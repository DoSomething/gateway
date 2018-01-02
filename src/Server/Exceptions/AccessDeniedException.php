<?php

namespace DoSomething\Gateway\Server\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AccessDeniedException extends Exception
{
    /**
     * Create a new "access denied" exception.
     *
     * @return void
     */
    public function __construct($message, $hint = null)
    {
        $this->message = $message;
        $this->hint = $hint;

        parent::__construct($message);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $contents = [
            'error' => 'access_denied',
            'message' => $this->message,
        ];

        if ($this->hint) {
            $contents['hint'] = $this->hint;
        }

        // Return a properly formatted error according
        // to RFC 6749, Section 5.2. <goo.gl/EeUqjz>
        return new JsonResponse($contents, 401, [
            'WWW-Authenticate' => 'Bearer realm="OAuth"',
        ]);
    }
}
