<?php


namespace App\Tests\Controller;


use App\Tests\Helper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ConvertControllerTest extends WebTestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Helper::removeTestFolders();
    }

    /**
     * @dataProvider provideCorrectRequest
     *
     * @param string $request
     * @param string $response
     */
    public function testCorrectResponses(string $request, string $response)
    {
        $client = static::createClient();

        $client->catchExceptions(true);

        $client->request(
            'POST',
            '/convert',
            [],
            [],
            ['CONTENT_TYPE' => 'application/vnd.api+json'],
            $request
        );

        $this->assertEquals($response, $client->getResponse()->getContent());
    }

    /**
     * @dataProvider provideErrorRequest
     *
     * @param string $request
     * @param string $response
     */
    public function testErrorResponse(string $request, string $response)
    {
        $client = static::createClient();

        $client->catchExceptions(true);

        $client->request(
            'POST',
            '/convert',
            [],
            [],
            ['CONTENT_TYPE' => 'application/vnd.api+json'],
            $request
        );

        $this->assertEquals($response, $client->getResponse()->getContent());
    }

    /**
     * @dataProvider provideErrorRequest
     *
     * @param string $request
     * @param string $response
     */
    public function testErrorExceptions(string $request, string $response)
    {
        $client = static::createClient();

        $client->catchExceptions(false);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(json_decode($response, true)['errors']['detail']);

        $client->request(
            'POST',
            '/convert',
            [],
            [],
            ['CONTENT_TYPE' => 'application/vnd.api+json'],
            $request
        );
    }

    public function provideCorrectRequest(): array
    {
        return [
            [
                'request' => '{"data":{"from": "USD","to": "EUR","amount": "1"}}',
                'response' => '{"data":{"result":0.9479}}',
            ],
            [
                'request' => '{"data":{"from": "EUR","to": "USD","amount": "1"}}',
                'response' => '{"data":{"result":1.055}}',
            ],
            [
                'request' => '{"data":{"from": "USD","to": "RUB","amount": "1"}}',
                'response' => '{"data":{"result":65.5}}',
            ],
            [
                'request' => '{"data":{"from": "EUR","to": "RUB","amount": "1"}}',
                'response' => '{"data":{"result":69.1}}',
            ],
        ];
    }

    public function provideErrorRequest(): array
    {
        return [
            [
                'request' => '{"data":{"from": "AUD","to": "RUB","amount": "1"}}',
                'response' => '{"errors":{"status":400,"detail":"We haven\u0027t data for \u0027AUD\u0027 currency"}}',
            ],
            [
                'request' => '{"data":{"from": "AUZ","to": "RUB","amount": "1"}}',
                'response' => '{"errors":{"status":400,"detail":"from: This value is not a valid currency."}}',
            ],
            [
                'request' => '{"data":{"from": "USD","to": "RUB","amount": "w1"}}',
                'response' => '{"errors":{"status":400,"detail":"amount: This value should be positive."}}',
            ],
        ];
    }
}