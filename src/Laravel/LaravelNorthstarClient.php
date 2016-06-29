<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\NorthstarClient;
use DoSomething\Northstar\Exceptions\InternalException;
use DoSomething\Northstar\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Validation\ValidationException as LaravelValidationException;
use Illuminate\Support\MessageBag;

class LaravelNorthstarClient extends NorthstarClient
{
    /**
     * The class name of the OAuth repository. For Laravel, we default to the included repository
     * (although that can be overridden with the $config['repository'] option in the constructor).
     *
     * @var string
     */
    protected $repository = \DoSomething\Northstar\LaravelOAuthRepository::class;

    /**
     * Send a Northstar API request, and translates any Northstar exceptions
     * into their built-in Laravel equivalents.
     *
     * @param string $method
     * @param string $path
     * @param array $options
     * @param bool $withAuthorization
     * @return \GuzzleHttp\Psr7\Response|void
     */
    public function send($method, $path, $options = [], $withAuthorization = true)
    {
        try {
            return parent::send($method, $path, $options, $withAuthorization);
        } catch (ValidationException $e) {
            $messages = new MessageBag;

            foreach ($e->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    $messages->add($attribute, $error);
                }
            }

            throw new LaravelValidationException($messages);
        } catch (InternalException $e) {
            $message = 'Northstar returned an unexpected error for that request.';

            if (config('app.debug')) {
                $message = $e->getMessage();
            }

            throw new HttpException(500, $message);
        }
    }
}
