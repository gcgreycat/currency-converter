<?php


namespace App\Utils;


use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApiResponse extends JsonResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        $headers['content-type'] = 'application/vnd.api+json';
        parent::__construct($data, $status, $headers, $json);
    }
}