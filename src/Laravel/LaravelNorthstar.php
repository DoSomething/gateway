<?php

namespace DoSomething\Northstar\Laravel;

use DoSomething\Northstar\Northstar;
use DoSomething\Northstar\Exceptions\InternalException;
use DoSomething\Northstar\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Validation\ValidationException as LaravelValidationException;
use Illuminate\Support\MessageBag;

class LaravelNorthstar extends Northstar
{
    /**
     * The class name of the OAuth framework bridge. This allows us to interact with Laravel
     * without writing any custom framework integration code, but can be overridden in the
     * client constructor's `$config` array.
     *
     * @var string
     */
    protected $bridge = \DoSomething\Northstar\Laravel\LaravelOAuthBridge::class;

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
