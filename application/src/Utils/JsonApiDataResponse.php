<?php


namespace App\Utils;


class JsonApiDataResponse extends JsonApiResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct(['data' => $data], $status, $headers, $json);
    }
}