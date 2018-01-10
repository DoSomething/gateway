<?php

use Carbon\Carbon;
use Lcobucci\JWT\Builder;
use Illuminate\Http\Request;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Illuminate\Database\Capsule\Manager as DB;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Location of the example private key.
     *
     * @var string
     */
    protected $signer = __DIR__ . '/Server/example-private.key';

    /**
     * Location of the example public key.
     *
     * @var string
     */
    protected $key = __DIR__ . '/Server/example-public.key';

    /**
     * Set up the test case.
     *
     * @return void
     */
    public function setUp()
    {
        // Reset mocked time, if set.
        Carbon::setTestNow(null);

        // Reset any mocked headers.
        unset($_SERVER['HTTP_X_REQUEST_ID']);

        $this->setUpDatabase();
        $this->migrateTables();
    }

    /**
     * Set up the database for testing.
     *
     * @return void
     */
    protected function setUpDatabase()
    {
        $database = new DB;

        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    /**
     * Migrate the tables for the database setup.
     *
     * @return void
     */
    protected function migrateTables()
    {
        // @TODO: maybe try and use the migrations defined in
        // Laravel/Migrations for quick setup?
        DB::schema()->create('clients', function ($table) {
            $table->string('client_id')->unique();
            $table->string('access_token', 1024)->nullable();
            $table->integer('access_token_expiration')->nullable();
        });
    }

    /**
     * "Freeze" time so we can make assertions based on it.
     *
     * @param string $time
     * @return Carbon
     */
    public function mockTime($time = 'now')
    {
        Carbon::setTestNow((string) new Carbon($time));

        return Carbon::getTestNow();
    }

    /**
     * Create a request with the given authorization header.
     *
     * @param $authorization
     * @return Request
     */
    public function createRequest($authorization)
    {
        $request = new Request();
        $request->headers->set('Authorization', $authorization);

        return $request;
    }

    /**
     * Create a request with a signed JWT header.
     *
     * @param string $key
     * @param string $client
     * @param Carbon $issuedAt
     * @param array $contents
     * @return Request
     */
    public function createJwtRequest($key, $client, $issuedAt, $contents = [])
    {
        $token = (new Builder())
            ->setIssuer('https://northstar-phpunit.dosomething.org')
            ->setAudience($client)
            ->setId(bin2hex(random_bytes(40)), true)
            ->setIssuedAt($issuedAt->getTimestamp())
            ->setNotBefore($issuedAt->getTimestamp())
            ->setExpiration($issuedAt->getTimestamp())
            ->setExpiration($issuedAt->addHour()->getTimestamp())
            ->setSubject(isset($contents['sub']) ? $contents['sub'] : null)
            ->set('role', isset($contents['role']) ? $contents['role'] : null)
            ->set('scopes', isset($contents['scopes']) ? $contents['scopes'] : null)
            ->sign(new Sha256(), new Key('file://' . $key))
            ->getToken();

        return $this->createRequest('Bearer ' . (string) $token);
    }

    /**
     * Mock a header on the current PHP request.
     *
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function withRequestHeader($header, $value)
    {
        $serverVariable = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        $_SERVER[$serverVariable] = $value;

        return $this;
    }
}
