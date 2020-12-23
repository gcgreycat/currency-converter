<?php


namespace App\Controller;


use App\Entity\ConvertForm;
use App\Service\CbrDaily\CbrDaily;
use App\Service\CbrDaily\ConvertException;
use App\Utils\JsonApiDataResponse;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConvertController
{
    /**
     * @Route(path="/convert", methods={"POST"}, name="convert")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param CbrDaily $cbrDaily
     * @param LoggerInterface $logger
     * @return ConvertForm|JsonApiDataResponse|JsonResponse
     *
     * @throws Exception
     */
    public function index(Request $request, ValidatorInterface $validator, CbrDaily $cbrDaily, LoggerInterface $logger)
    {
        $convertForm = $this->getConvertForm($request, $validator);

        try {
            $result = $cbrDaily->convert($convertForm->getFrom(), $convertForm->getTo(), $convertForm->getAmount());
        } catch (ConvertException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        return new JsonApiDataResponse([
            'result' => (float)number_format($result, 4, '.', ''),
        ]);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return ConvertForm
     *
     * @throws BadRequestHttpException
     */
    private function getConvertForm(Request $request, ValidatorInterface $validator): ConvertForm
    {
        if ($request->headers->get('Content-Type') !== 'application/vnd.api+json') {
            throw new BadRequestHttpException('Wrong Content-Type');
        }

        $requestData = json_decode($request->getContent(), true);
        if (empty($requestData['data'])) {
            throw new BadRequestHttpException('Wrong data');
        }

        $convertForm = new ConvertForm($requestData['data']);

        $errors = $validator->validate($convertForm);

        if ($errors->count()) {
            $error = $errors->get(0);

            throw new BadRequestHttpException($error->getPropertyPath() . ': ' . $error->getMessage());
        }

        return $convertForm;
    }
}