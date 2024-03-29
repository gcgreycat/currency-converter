<?php


namespace App\tests\Service\CbrDaily;


use App\Service\CbrDaily\CbrDaily;
use App\Service\CbrDaily\Utils\CbrDailyDownloader;
use App\Service\CbrDaily\Utils\CbrDailyParser;
use App\Service\CbrDaily\Utils\CbrDailyValidator;
use App\Tests\Helper;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class CbrDailyTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Helper::removeTestFolders();
    }


    /**
     * @dataProvider providerForRates
     *
     * @param string $currency
     * @param array|null $rate
     * @throws Exception
     */
    public function testGetRate(string $currency, array $rate = null)
    {
        $parameterBag = $this->getParameterBag();
        $cbrDailyParser = new CbrDailyParser();
        $cbrDailyValidator = new CbrDailyValidator();
        $cbrDailyDownloader = new CbrDailyDownloader(
            $cbrDailyValidator,
            $parameterBag->get('app.cbr_xml_daily_save_folder'),
            $parameterBag->get('app.cbr_xml_daily_lock_folder'),
            $parameterBag->get('app.cbr_xml_daily_url')
        );

        $cbrDaily = new CbrDaily(
            $cbrDailyParser,
            $cbrDailyDownloader
        );

        $this->assertEquals($rate, $cbrDaily->getRate($currency));
    }

    public function providerForRates()
    {
        return [
            [
                'currency' => 'USD',
                'rate' => [
                    'nominal' => 1,
                    'value' => 65.5000,
                ],
            ]
        ];
    }

    /**
     * @return ParameterBag
     */
    private function getParameterBag()
    {
        return new ParameterBag([
            'kernel.project_dir' => Helper::KERNEL_DIR,
            'app.cbr_xml_daily_url' => Helper::XML_URL,
            'app.cbr_xml_daily_save_folder' => Helper::KERNEL_DIR . Helper::XML_FOLDER,
            'app.cbr_xml_daily_lock_folder' => Helper::KERNEL_DIR . Helper::LOCK_FOLDER,
        ]);
    }
}