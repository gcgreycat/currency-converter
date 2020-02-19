<?php


namespace App\Service\CbrDaily;


use App\Service\CbrDaily\Utils\CbrDailyParser;
use App\Service\CbrDaily\Utils\CbrDailyValidator;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class CbrDaily
{
    /**
     * @var array
     */
    private $currencyData;

    /**
     * @var string
     */
    private $folderPath;

    /**
     * @var CbrDailyParser
     */
    private $cbrDailyParser;

    /**
     * @var CbrDailyValidator
     */
    private $cbrDailyValidator;

    /**
     * @var string
     */
    private $xmlUrl;

    /**
     * @var LockFactory
     */
    private $factory;


    /**
     * CbrDaily constructor.
     * @param ParameterBagInterface $parameterBag
     * @param CbrDailyParser $cbrDailyParser
     * @param CbrDailyValidator $cbrDailyValidator
     * @throws Exception
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        CbrDailyParser $cbrDailyParser,
        CbrDailyValidator $cbrDailyValidator
    )
    {
        $this->folderPath = $parameterBag->get('kernel.project_dir') . $parameterBag->get('app.cbr_xml_daily_save_folder');
        $this->xmlUrl = $parameterBag->get('app.cbr_xml_daily_url');
        if (empty($this->xmlUrl)) {
            throw new Exception('Empty parameter app.cbr_xml_daily_url');
        }
        $this->cbrDailyParser = $cbrDailyParser;
        $this->cbrDailyValidator = $cbrDailyValidator;

        $lockPath = $parameterBag->get('kernel.project_dir') . $parameterBag->get('app.cbr_xml_daily_lock_folder');
        if (!file_exists($lockPath)) {
            mkdir($lockPath);
        }
        $store = new FlockStore($lockPath);
        $this->factory = new LockFactory($store);
    }

    /**
     * @param string $currency
     * @return mixed|null
     * @throws Exception
     */
    public function getRate(string $currency)
    {
        $rates = $this->getCurrencyData();
        return empty($rates[$currency]) ? null : $rates[$currency];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getCurrencyData()
    {
        if (is_null($this->currencyData)) {
            $this->currencyData = $this->loadCurrencyFromFile();
            $this->currencyData['RUB'] = [
                'nominal' => 1,
                'value' => 1,
            ];
        }
        return $this->currencyData;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function loadCurrencyFromFile()
    {
        $date = $this->getDateTime();

        $fileName = $this->getFileName($date);

        if (!file_exists($fileName) || !is_file($fileName)) {
            $lock = $this->factory->createLock('cbr-daily-xml-download');
            $lock->acquire(true);
            /** Recheck if still not exists - download it */
            if (!file_exists($fileName) || !is_file($fileName)) {
                $xmlData = $this->downloadXmlFile();
                $this->saveXmlFile($xmlData);
            }

            $lock->release();
        }
        return $this->parseXml($fileName);
    }

    /**
     * @param string $fileName
     * @return array
     * @throws Exception
     */
    private function parseXml(string $fileName)
    {
        $xmlData = file_get_contents($fileName);
        return $this->cbrDailyParser->parse($xmlData);
    }

    /**
     * @return false|string
     * @throws Exception
     */
    private function downloadXmlFile()
    {
        $xmlData = file_get_contents($this->xmlUrl);
        if (!$this->cbrDailyValidator->validate($xmlData)) {
            throw new Exception('Wrong xml content');
        }

        return $xmlData;
    }

    /**
     * @param string $xmlData
     * @throws Exception
     */
    private function saveXmlFile(string $xmlData)
    {
        $data = simplexml_load_string($xmlData);

        if (empty($data)) {
            throw new Exception('Can\'t load xml for saving');
        }

        $attributes = $data->attributes();
        $date = DateTime::createFromFormat('d.m.Y', (string)$attributes->Date);

        $fileName = $this->getFileName($date);

        if (!file_exists($this->folderPath)) {
            if (!mkdir($this->folderPath)) {
                throw new Exception('Can\'t create cbr folder');
            }
        }
        if (!file_put_contents($fileName, $xmlData)) {
            throw new Exception('Error in file saving');
        }

        $currentDate = new DateTime();
        if ($currentDate->format('Y-m-d') !== $date->format('Y-m-d')) {
            $fileName = $this->getFileName($currentDate);
            if (!file_put_contents($fileName, $xmlData)) {
                throw new Exception('Error in file saving');
            }
        }
    }

    /**
     * @param DateTime $date
     * @return string
     */
    private function getFileName(DateTime $date)
    {
        return $this->folderPath . '/' . $date->format('Y-m-d') . '.xml';
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function getDateTime()
    {
        $date = new DateTime();

        $dayWeek = (int)$date->format('w');

        if (in_array($dayWeek, [0, 1])) {
            $subtractDays = (string)($dayWeek + 1);
            $date->sub(new DateInterval('P' . $subtractDays . 'D'));
        }

        return $date;
    }
}