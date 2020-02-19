<?php


namespace App\Controller;


use App\Utils\JsonApiDataResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return new JsonApiDataResponse(['title' => 'Currency Converter']);
    }
}