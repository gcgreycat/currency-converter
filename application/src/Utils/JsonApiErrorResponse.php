<?php


namespace App\Utils;


class JsonApiErrorResponse extends JsonApiResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        $data = [
            'errors' => [
                'status' => $status,
                'detail' => $data,
            ],
        ];
        parent::__construct($data, $status, $headers, $json);
    }
}