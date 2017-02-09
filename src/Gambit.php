<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\GambitCampaign;
use DoSomething\Gateway\Resources\GambitCampaignCollection;

class Gambit extends RestApiClient
{
    use AuthorizesWithGambit;

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
     * Default headers applied to every request.
     *
     * @var array
     */
    protected $defaultHeaders;

    /**
     * Create a new Gambit API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Save configuration.
        $this->config = $config;

        // Set response header.
        if (! empty($config['apiKey'])) {
            $this->apiKey = $config['apiKey'];
        }
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a GET request to return all campaigns.
     *
     * @param string $query - Filter campaigns. Following parameters allowed:
     *   - campaignbot: (boolean) Only campaigns with campaignbot enabled
     *
     * @see https://github.com/DoSomething/gambit/blob/develop/documentation/endpoints/campaigns.md
     *
     * @return GambitCampaignCollection
     */
    public function getAllCampaigns($query = [])
    {
        $response = $this->get('v1/campaigns', $query, false);

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
        $response = $this->get('v1/campaigns/' . $id, [], false);

        if (is_null($response)) {
            return null;
        }

        return new GambitCampaign($response['data']);
    }

    /**
     * Call a "message" action on the Campaign endpoint.
     *
     * @param string $id - ID
     * @param string $phone - Phone number, Northstar-compatible format
     * @param string $type - The campaign message type.
     *
     * @see  https://github.com/DoSomething/gambit/blob/develop/documentation/endpoints/campaigns.md#send-a-campaign-message
     *
     * @return bool
     */
    public function createCampaignMessage($id, $phone, $type)
    {
        $payload = [
            'phone' => $phone,
            'type' => $type,
        ];
        $response = $this->post('v1/campaigns/' . $id . '/message', $payload);

        return $this->responseSuccessful($response);
    }

    /**
     * Send a Post request Gambit signup endpoint.
     *
     * To notify Gambit that signup has been created.
     *
     * @param string $id - Signup
     * @return bool
     */
    public function createSignup($id, $source = self::SIGNUP_SOURCE_FALLBACK)
    {
        $payload = [
            'id' => $id,
            'source' => $source,
        ];
        $response = $this->post('v1/signups/', $payload);

        return $this->responseSuccessful($response);
    }
}
