<?php


namespace App\Tests\Service\CbrDaily\Utils;


use App\Service\CbrDaily\Utils\CbrDailyParser;
use Exception;
use PHPUnit\Framework\TestCase;

class CbrDailyParserTest extends TestCase
{
    /**
     * @dataProvider provideXmlForParsing
     *
     * @param string $xml
     * @param array $result
     * @param bool $exception
     * @throws Exception
     */
    public function testCorrectXmlParsing(string $xml, array $result, bool $exception)
    {
        if ($exception) {
            $this->expectException(Exception::class);
        }

        $parser = new CbrDailyParser();
        $parsed = $parser->parse($xml);

        if (!$exception) {
            $this->assertEquals($result, $parsed);
        }
    }

    public function provideXmlForParsing()
    {
        return [
            // correct
            [
                'xml' => '<?xml version="1.0" encoding="windows-1251"?>
<ValCurs Date="01.01.1970" name="Foreign Currency Market">
    <Valute ID="R01235">
        <NumCode>840</NumCode>
        <CharCode>USD</CharCode>
        <Nominal>1</Nominal>
        <Name>Доллар США</Name>
        <Value>65,5000</Value>
    </Valute>
    <Valute ID="R01239">
        <NumCode>978</NumCode>
        <CharCode>EUR</CharCode>
        <Nominal>1</Nominal>
        <Name>Евро</Name>
        <Value>69,1000</Value>
    </Valute>
</ValCurs>',
                'result' => [
                    'USD' => [
                        'nominal' => 1,
                        'value' => 65.5000
                    ],
                    'EUR' => [
                        'nominal' => 1,
                        'value' => 69.1000
                    ]
                ],
                'exception' => false,
            ],
            // incorrect
            [
                'xml' => '',
                'result' => [],
                'exception' => true,
            ]
        ];
    }
}