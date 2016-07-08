<?php

namespace DoSomething\Northstar;

use DoSomething\Northstar\Common\RestApiClient;
use DoSomething\Northstar\Resources\NorthstarClient;
use DoSomething\Northstar\Resources\NorthstarUser;
use DoSomething\Northstar\Resources\NorthstarUserCollection;
use DoSomething\Northstar\Resources\NorthstarClientCollection;

class Northstar extends RestApiClient
{
    use AuthorizesWithNorthstar;

    /**
     * Create a new Northstar API client.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $base_url = $config['url'];

        // Set required fields for OAuth authentication trait.
        $this->authorizationServerUrl = $config['url'];
        $this->grant = ! empty($config['grant']) ? $config['grant'] : 'client_credentials';
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->scope = isset($config['scope']) ? $config['scope'] : ['user'];

        if (! empty($config['repository'])) {
            $this->repository = $config['repository'];
        }

        parent::__construct($base_url);
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
     * @param string $type - '_id', 'email', 'mobile'
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
     * @return array - keys
     */
    public function getAllClients()
    {
        $response = $this->get('v1/keys');

        return new NorthstarClientCollection($response);
    }

    /**
     * Send a POST request to create a new API key.
     * Requires an `admin` scoped API key.
     *
     * @param array $input - key values
     * @return Northstar
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
     * @return Northstar
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
     * @return Northstar
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
