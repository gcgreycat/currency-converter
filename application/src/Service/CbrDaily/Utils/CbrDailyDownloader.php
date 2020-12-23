<?php


namespace App\Service\CbrDaily\Utils;


use DateInterval;
use DateTime;
use Exception;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class CbrDailyDownloader
{
    /**
     * @var CbrDailyValidator
     */
    private $validator;

    /**
     * @var string
     */
    private $folderPath;

    /**
     * @var string
     */
    private $xmlUrl;

    /**
     * @var LockFactory
     */
    private $factory;

    /**
     * CbrDailyDownloader constructor.
     * @param CbrDailyValidator $validator
     * @param string $xmlFolder
     * @param string $lockFolder
     * @param string $xmlUrl
     */
    public function __construct(
        CbrDailyValidator $validator,
        string $xmlFolder,
        string $lockFolder,
        string $xmlUrl
    )
    {
        $this->validator = $validator;
        $this->folderPath = $xmlFolder;
        $this->xmlUrl = $xmlUrl;

        $lockPath = $lockFolder;
        if (!file_exists($lockPath)) {
            mkdir($lockPath);
        }
        $store = new FlockStore($lockPath);
        $this->factory = new LockFactory($store);
    }

    /**
     * @throws Exception
     */
    public function downloadLastXml(): string
    {
        $date = $this->getDateTime();

        $fileName = $this->getFileName($date);

        if (!file_exists($fileName) || !is_file($fileName)) {
            $lock = $this->factory->createLock('cbr-daily-xml-download');
            $lock->acquire(true);
            /** Recheck if still not exists - download it */
            if (!file_exists($fileName) || !is_file($fileName)) {
                $xmlData = $this->downloadXmlFile($this->xmlUrl);
                $this->saveXmlFile($xmlData);
            }

            $lock->release();
        }

        return $fileName;
    }

    /**
     * @param string $xmlUrl
     * @return false|string
     * @throws Exception
     */
    private function downloadXmlFile(string $xmlUrl)
    {
        $xmlData = file_get_contents($xmlUrl);
        if (!$this->validator->validate($xmlData)) {
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
    private function getFileName(DateTime $date): string
    {
        return $this->folderPath . '/' . $date->format('Y-m-d') . '.xml';
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    private function getDateTime(): DateTime
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