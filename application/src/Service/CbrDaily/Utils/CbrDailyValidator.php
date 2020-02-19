<?php


namespace App\Service\CbrDaily\Utils;


use Exception;

class CbrDailyValidator
{
    /**
     * @param string $xmlData
     * @return bool
     * @throws Exception
     */
    public function validate(string $xmlData)
    {
        $data = simplexml_load_string($xmlData);

        if (empty($data)) {
            throw new Exception('Can\'t load xml for validation');
        }

        $attributes = $data->attributes();

        return $data->getName() === 'ValCurs'
            && (string)$attributes->Date
            && (string)$attributes->name === 'Foreign Currency Market'
            && count($data->children());
    }
}