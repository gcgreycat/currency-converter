<?php


namespace App\Utils;


use Symfony\Component\HttpFoundation\Request;

class JsonApiRequestValidator
{
    /**
     * @param Request $request
     * @return bool
     * @throws JsonApiRequestException
     */
    public function validate(Request $request): bool
    {
        if ($request->headers->get('Content-Type') !== 'application/vnd.api+json') {
            throw new JsonApiRequestException('Wrong Content-Type');
        }

        $requestData = json_decode($request->getContent(), true);
        if (empty($requestData['data'])) {
            throw new JsonApiRequestException('Wrong data');
        }

        return true;
    }
}