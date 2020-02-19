<?php


namespace App\Tests;


use Symfony\Component\Filesystem\Filesystem;

class Helper
{
    const KERNEL_DIR = __DIR__ . '/..';
    const XML_URL = __DIR__ . '/data/test_xml_daily.xml';
    const XML_FOLDER = '/var/test_cbr_dailies';
    const LOCK_FOLDER = '/var/test_lock_stores';

    public static function removeTestFolders()
    {
        $fs = new Filesystem();
        foreach ([self::KERNEL_DIR . self::XML_FOLDER, self::KERNEL_DIR . self::LOCK_FOLDER] as $folder) {
            if (file_exists($folder)) {
                $fs->remove($folder);
            }
        }
    }
}