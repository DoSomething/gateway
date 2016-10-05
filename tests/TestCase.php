<?php

use Illuminate\Database\Capsule\Manager as DB;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Set up the test case.
     *
     * @return void
     */
    public function setUp()
    {
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
}
