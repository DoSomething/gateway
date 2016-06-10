<?php

namespace DoSomething\Northstar;

use GuzzleHttp\Message\Response;
use Illuminate\Support\MessageBag;
use DoSomething\Northstar\Exceptions\InternalException;
use DoSomething\Northstar\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Validation\ValidationException as LaravelValidationException;

class LaravelNorthstarClient extends NorthstarClient
{
    /**
     * Send a Northstar API request, and translates any Northstar exceptions
     * into their built-in Laravel equivalents.
     *
     * @param string $method
     * @param string $path
     * @param array $options
     * @param bool $withAuthorization
     * @return Response|void
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
