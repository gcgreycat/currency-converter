<?php


namespace App\Service\CbrDaily\Utils;


use DOMDocument;
use Exception;
use LibXMLError;

class CbrDailyValidator
{
    /**
     * @var LibXMLError[]
     */
    private $errors;

    /**
     * @param string $xmlData
     * @return bool
     * @throws Exception
     */
    public function validate(string $xmlData): bool
    {
        $this->errors = [];

        if (empty($xmlData)) {
            throw new Exception('Can\'t load xml for validation');
        }

        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadXML($xmlData);
        $result = true;

        if (!$doc->schemaValidate(__DIR__ . '/cbr_xml_daily.xsd')) {
            $this->errors = libxml_get_errors();
            libxml_clear_errors();
            $result = false;
        }

        libxml_use_internal_errors(false);
        return $result;
    }

    /**
     * @return LibXMLError[]
     */
    public function getLastErrors(): array
    {
        return $this->errors;
    }
}