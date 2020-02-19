<?php


namespace App\Controller;


use App\Utils\JsonApiErrorResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorController
{
    public function __invoke(\Throwable $exception): Response
    {
        $status = 500;
        $message = 'Internal error';
        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage();
        }
        $headers = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];
        return new JsonApiErrorResponse($message, $status, $headers);
    }
}
