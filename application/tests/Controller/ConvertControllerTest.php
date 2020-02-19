<?php


namespace App\Tests\Controller;


use App\Tests\Helper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    public function provideCorrectRequest()
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