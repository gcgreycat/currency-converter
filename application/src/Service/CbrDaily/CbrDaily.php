<?php


namespace App\Service\CbrDaily;


use App\Service\CbrDaily\Utils\CbrDailyDownloader;
use App\Service\CbrDaily\Utils\CbrDailyParser;
use Exception;

class CbrDaily
{
    /**
     * @var array
     */
    private $currencyData;

    /**
     * @var CbrDailyParser
     */
    private $cbrDailyParser;

    /**
     * @var CbrDailyDownloader
     */
    private $cbrDailyDownloader;



    /**
     * CbrDaily constructor.
     * @param CbrDailyParser $cbrDailyParser
     * @param CbrDailyDownloader $cbrDailyDownloader
     * @throws Exception
     */
    public function __construct(
        CbrDailyParser $cbrDailyParser,
        CbrDailyDownloader $cbrDailyDownloader
    )
    {
        $this->cbrDailyParser = $cbrDailyParser;
        $this->cbrDailyDownloader = $cbrDailyDownloader;
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param float $amount
     * @return float|int
     * @throws ConvertException
     * @throws Exception
     */
    public function convert(string $fromCurrency, string $toCurrency, float $amount)
    {
        $fromRate = $this->getRate($fromCurrency);
        $toRate = $this->getRate($toCurrency);

        if (empty($fromRate)) {
            throw new ConvertException("We haven't data for '{$fromCurrency}' currency");
        }

        if (empty($toRate)) {
            throw new ConvertException("We haven't data for '{$toCurrency}' currency");
        }

        if (empty($fromRate['nominal']) || empty($toRate['value'])) {
            throw new ConvertException('Wrong rate data');
        }

        return $fromRate['value'] * $toRate['nominal'] * $amount / $fromRate['nominal'] / $toRate['value'];
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
    private function getCurrencyData(): array
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
    private function loadCurrencyFromFile(): array
    {
        $fileName =$this->cbrDailyDownloader->downloadLastXml();
        return $this->parseXml($fileName);
    }

    /**
     * @param string $fileName
     * @return array
     * @throws Exception
     */
    private function parseXml(string $fileName): array
    {
        $xmlData = file_get_contents($fileName);
        return $this->cbrDailyParser->parse($xmlData);
    }
}