<?php

namespace DoSomething\Gateway\Server\Commands;

use JOSE_JWK;
use Illuminate\Console\Command;
use DoSomething\Gateway\Common\RestApiClient;

class PublicKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load public key for Gateway server middleware.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = config('services.northstar.url');
        $path = config('services.northstar.key');
        $http = new RestApiClient($url);

        // Print an error if a URL isn't set in config file.
        if (empty($url)) {
            $this->error('Set a Northstar URL in config/services.php! <https://git.io/...>');
        }

        $discoveryUrl = $url.'/.well-known/openid-configuration';

        $this->comment('Reading configuration from '.$discoveryUrl.'...');
        $configuration = $http->get($discoveryUrl);
        $jwksUri = $configuration['jwks_uri'];

        $this->comment('Reading public key from '.$jwksUri.'...');
        $jwks = $http->get($jwksUri);

        $components = $jwks['keys'][0];
        $jwk = JOSE_JWK::decode($components);

        file_put_contents($path, (string) $jwk);
        $this->info('Wrote public key to ' . $path . '.');
    }
}
