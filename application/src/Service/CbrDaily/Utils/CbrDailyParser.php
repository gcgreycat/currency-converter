<?php


namespace App\Service\CbrDaily\Utils;


use Exception;

class CbrDailyParser
{
    /**
     * @param string $xmlData
     * @return array
     * @throws Exception
     */
    public function parse(string $xmlData)
    {
        $result = [];

        $data = simplexml_load_string($xmlData);

        if (empty($data)) {
            throw new Exception('Can\'t load xml for parsing');
        }

        foreach ($data->children() as $key => $child) {
            if ($key !== 'Valute') {
                continue;
            }

            $charCode = (string)$child->CharCode;
            $nominal = (int)$child->Nominal;
            $value = $this->parseFloat($child->Value);
            $result[$charCode] = [
                'nominal' => $nominal,
                'value' => $value,
            ];
        }

        return $result;
    }

    /**
     * @param string $number
     * @return float
     */
    private function parseFloat(string $number)
    {
        return (float)str_replace(',', '.', str_replace('.', '', $number));
    }
}