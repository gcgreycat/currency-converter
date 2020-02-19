<?php


namespace App\Controller;


use App\Entity\ConvertForm;
use App\Service\CbrDaily\CbrDaily;
use App\Utils\JsonApiDataResponse;
use App\Utils\JsonApiErrorResponse;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        if ($convertForm instanceof JsonResponse) {
            return $convertForm;
        }

        $fromRate = $cbrDaily->getRate($convertForm->getFrom());
        $toRate = $cbrDaily->getRate($convertForm->getTo());

        if (empty($fromRate)) {
            return $this->jsonError("We haven't data for '{$convertForm->getFrom()}' currency");
        }

        if (empty($toRate)) {
            return $this->jsonError("We haven't data for '{$convertForm->getTo()}' currency");
        }

        $result = $this->calcConvert($fromRate, $toRate, $convertForm->getAmount());

        return new JsonApiDataResponse([
            'result' => (float)number_format($result, 4, '.', ''),
        ]);
    }

    /**
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    private function jsonError(string $message, int $status = 400)
    {
        return new JsonApiErrorResponse($message, $status);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return ConvertForm|JsonResponse
     */
    private function getConvertForm(Request $request, ValidatorInterface $validator)
    {
        if ($request->headers->get('Content-Type') !== 'application/vnd.api+json') {
            return $this->jsonError('Wrong Content-Type');
        }

        $requestData = json_decode($request->getContent(), true);
        if (empty($requestData['data'])) {
            return $this->jsonError('Wrong data');
        }

        $convertForm = new ConvertForm($requestData['data']);

        $errors = $validator->validate($convertForm);

        if ($errors->count()) {
            $error = $errors->get(0);

            return $this->jsonError($error->getPropertyPath() . ': ' . $error->getMessage());
        }

        return $convertForm;
    }

    /**
     * @param array $from
     * @param array $to
     * @param float $amount
     * @return float|int
     */
    private function calcConvert(array $from, array $to, float $amount)
    {
        return $from['value'] * $to['nominal'] * $amount / $from['nominal'] / $to['value'];
    }
}