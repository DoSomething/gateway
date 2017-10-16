<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Resources\NorthstarClient;
use DoSomething\Gateway\Resources\NorthstarUser;
use DoSomething\Gateway\Resources\NorthstarUserCollection;
use DoSomething\Gateway\Resources\NorthstarClientCollection;

class Northstar extends RestApiClient
{
    use AuthorizesWithNorthstar;

    /**
     * Create a new Northstar API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        $base_url = $config['url'];

        // Set required fields for OAuth authentication trait.
        $this->authorizationServerUri = $config['url'];
        $this->grant = $config['grant'];
        $this->config = $config;

        if (! empty($config['bridge'])) {
            $this->bridge = $config['bridge'];
        }

        parent::__construct($base_url, $overrides);
    }

    /**
     * Send a GET request to return all users matching a given
     * query from Northstar.
     *
     * @param array $inputs - Filter, search, or pagination queries
     * @return NorthstarUserCollection
     */
    public function getAllUsers($inputs = [])
    {
        $response = $this->get('v1/users', $inputs);

        return new NorthstarUserCollection($response);
    }

    /**
     * Send a GET request to return a user with that id.
     *
     * @param string $type - 'id', 'email', 'mobile'
     * @param string $id - ID, email, id, phone
     * @return NorthstarUser
     */
    public function getUser($type, $id)
    {
        $response = $this->get('v1/users/'.$type.'/'.$id);

        if (is_null($response)) {
            return null;
        }

        return new NorthstarUser($response['data']);
    }

    /**
     * Send a POST request to create/update a user in Northstar.
     * Requires an `admin` scoped API key.
     *
     * @param array $input - Fields to update in profile
     * @return NorthstarUser|null
     */
    public function createUser($input)
    {
        $response = $this->post('v1/users', $input);

        return new NorthstarUser($response['data']);
    }

    /**
     * Send a PUT request to update a user in Northstar.
     *
     * @param string $id - Northstar User ID
     * @param array $input - Fields to update in profile
     * @return mixed
     */
    public function updateUser($id, $input)
    {
        $response = $this->put('v1/users/_id/'.$id, $input);

        return new NorthstarUser($response['data']);
    }

    /**
     * Send a DELETE request to delete a user from Northstar.
     * Requires an `admin` scoped API key.
     *
     * @param $id - Northstar user ID
     * @return bool - Whether user was successfully deleted
     */
    public function deleteUser($id)
    {
        $success = $this->delete('v1/users/_id/'.$id);

        return $success;
    }

    /**
     * Send a GET request to return all Northstar keys.
     * Requires an `admin` scoped API key.
     *
     * @param array $inputs - Pagination queries
     * @return NorthstarClientCollection
     */
    public function getAllClients($inputs = [])
    {
        $response = $this->get('v2/clients', $inputs);

        return new NorthstarClientCollection($response);
    }

    /**
     * Send a POST request to create a new API key.
     * Requires an `admin` scoped API key.
     *
     * @param array $input - key values
     * @return NorthstarClient
     */
    public function createNewClient($input)
    {
        $response = $this->post('v2/clients', $input);

        return new NorthstarClient($response['data']);
    }

    /**
     * Send a GET request to get the specified key.
     * Requires an `admin` scoped API key.
     *
     * @param string $client_id - API key
     * @return NorthstarClient
     */
    public function getClient($client_id)
    {
        $response = $this->get('v2/clients/'.$client_id);

        return new NorthstarClient($response['data']);
    }

    /**
     * Send a POST request to generate new keys to northstar
     * Requires an `admin` scoped API key.
     *
     * @param string $client_id - API key
     * @param array $input - key values
     * @return NorthstarClient
     */
    public function updateClient($client_id, $input)
    {
        $response = $this->put('v2/clients/'.$client_id, $input);

        return new NorthstarClient($response['data']);
    }

    /**
     * Send a DELETE request to delete an API key from Northstar.
     * Requires an `admin` scoped API key.
     *
     * @param string $client_id - API key
     * @return bool - Whether user was successfully deleted
     */
    public function deleteClient($client_id)
    {
        return $this->delete('v2/clients/'.$client_id);
    }

    /**
     * Get the available scopes for API keys & their descriptions.
     *
     * @return array - key/value array of scopes & descriptions
     */
    public function scopes()
    {
        return $this->get('v2/scopes');
    }
}
