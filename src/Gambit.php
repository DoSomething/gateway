<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\GambitCampaign;

class Gambit extends RestApiClient
{

    /**
     * Configuration array.
     *
     * @var string
     */
    protected $config;

    /**
     * Create a new Gambit API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Save configuration.
        $this->config = $config;
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a GET request to return all users matching a given
     * query from Northstar.
     *
     * @param array $inputs - Filter, search, or pagination queries
     * @return NorthstarUserCollection
     */
    // public function getAllUsers($inputs = [])
    // {
    //     $response = $this->get('v1/users', $inputs);

    //     return new NorthstarUserCollection($response);
    // }

    /**
     * Send a GET request to return a user with that id.
     *
     * @param string $id - ID
     * @return NorthstarUser
     */
    public function getCampaign($id)
    {
        $response = $this->get('v1/campaigns/' . $id);

        if (is_null($response)) {
            return null;
        }

        return new GambitCampaign($response['data']);
    }

}
