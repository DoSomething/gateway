<?php

use DoSomething\Gateway\Common\RestApiClient;
use DoSomething\Gateway\Exceptions\ValidationException;
use GuzzleHttp\Psr7\Response;

class RestApiClientTest extends TestCase
{
    /**
     * Test that we can instantiate a RestApiClient.
     */
    public function testRestApiClientForCustomResource()
    {
        $apiUrl = 'https://api.xavierinstitute.edu';
        $restClient = new RestApiClient($apiUrl);

        $this->assertEquals((string) $restClient->getBaseUri(), $apiUrl);
    }

    /**
     * Test that making a normal GET request works.
     */
    public function testMakeGetRequest()
    {
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['teacher' => 'Charles Xavier'])),
        ]);

        $this->assertEquals($client->get('classes/1'), ['teacher' => 'Charles Xavier']);
    }

    /**
     * Test that requesting a missing resource returns null.
     */
    public function testHandlesMissingRequest()
    {
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(404),
        ]);

        $this->assertNull($client->get('classes/abc'));
    }

    /**
     * Test that making a normal POST request works.
     */
    public function testMakePostRequest()
    {
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(201, ['Content-Type' => 'application/json'], json_encode(['teacher' => 'Charles Xavier'])),
        ]);

        $this->assertEquals($client->post('teachers'), ['teacher' => 'Charles Xavier']);
    }

    /**
     * Test that requesting a missing resource returns null.
     */
    public function testHandlesValidationError()
    {
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(422, ['Content-Type' => 'application/json'], json_encode([
                'error' => [
                    'code' => 422,
                    'message' => '',
                    'fields' => [
                        'teacher' => ['The provided teacher is not employed here.'],
                    ],
                ],
            ])),
        ]);

        $this->setExpectedException(ValidationException::class);
        $client->post('classes', ['teacher' => 'Erik Lehnsherr']);
    }

    /**
     * Test that making a normal PUT request works.
     */
    public function testMakePutRequest()
    {
        $body = ['teacher' => 'Charles Xavier', 'job' => 'Professor'];
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
        ]);

        $this->assertEquals($client->put('teachers/1', ['job' => 'Professor']), $body);
    }

    /**
     * Test that making a normal DELETE request works.
     */
    public function testMakeDeleteRequest()
    {
        $client = new MockClient('https://api.xavierinstitute.edu', [
            new Response(201, ['Content-Type' => 'application/json'], json_encode([
                'success' => [
                    'code' => 200,
                    'message' => 'Deleted.',
                ],
            ])),
        ]);

        $this->assertEquals($client->delete('teachers/1'), true);
    }
}
