<?php


namespace App\Tests\Service\CbrDaily\Utils;


use App\Service\CbrDaily\Utils\CbrDailyValidator;
use Exception;
use PHPUnit\Framework\TestCase;

class CbrDailyValidatorTest extends TestCase
{
    /**
     * @dataProvider provideDataForValidation
     *
     * @param string $xml
     * @param bool $result
     * @param bool $exception
     * @throws Exception
     */
    public function testValidation(string $xml, bool $result, bool $exception)
    {
        if ($exception) {
            $this->expectException(Exception::class);
        }

        $validator = new CbrDailyValidator();
        $this->assertTrue(true);
        $validateResult = $validator->validate($xml);

        if (!$exception) {
            $this->assertEquals($result, $validateResult);
        }
    }

    public function provideDataForValidation()
    {
        return [
            [
                'xml' => '',
                'result' => false,
                'exception' => true,
            ],
            [
                'xml' => '<?xml version="1.0" encoding="windows-1251"?><html><body></body></html>',
                'result' => false,
                'exception' => false,
            ],
            [
                'xml' => '<?xml version="1.0" encoding="windows-1251"?>
<ValCurs Date="01.01.1970" name="Foreign Currency Market">
    <Valute></Valute>
</ValCurs>',
                'result' => true,
                'exception' => false
            ],
        ];
    }
}