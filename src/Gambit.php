<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\GambitCampaign;
use DoSomething\Gateway\Resources\GambitCampaignCollection;

class Gambit extends RestApiClient
{
    /**
     * Unknown signup source.
     */
    const SIGNUP_SOURCE_FALLBACK = 'unknown';


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

    /**
     * Send a Post request Gambit signup endpoing.
     *
     * To notify Gambit that signup has been created.
     *
     * @param string $id - Signup
     * @return boolean
     */
    public function createSignup($id, $source = self::SIGNUP_SOURCE_FALLBACK)
    {
        $payload = [
            'id' => $id,
            'source' => $source,
        ];
        $response = $this->post('v1/signup/', $payload);

        if (is_null($response) || empty($response['success'])) {
            return false;
        }

        $result = $response['success'];
        if (empty($result['code']) || $result['code'] !== 200) {
            // Todo: log error.
            return false;
        }

        return true;
    }
}
