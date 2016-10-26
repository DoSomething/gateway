<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\GambitCampaign;
use DoSomething\Gateway\Resources\GambitCampaignCollection;

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
     * Send a GET request to return all campaigns.
     *
     * @return GambitCampaignCollection
     */
    public function getAllCampaigns()
    {
        $response = $this->get('v1/campaigns');

        return new GambitCampaignCollection($response);
    }

    /**
     * Send a GET request to return a campaign with that id.
     *
     * @param string $id - ID
     * @return GambitCampaign
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
