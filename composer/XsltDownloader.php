<?php

use PhpZip\ZipFile;

require 'vendor/autoload.php';

class XsltDownloader
{
    public static function run()
    {
        $xslZipUrl = getenv('XSLT_URL');
        if (empty($xslZipUrl)) {
            return;
        }

        $destPath = __DIR__.'/../var/xslt';
        if (is_dir($destPath)) {
            return;
        }

        echo 'Downloading XSLT files.'.PHP_EOL;
        $zip = file_get_contents($xslZipUrl);

        echo 'Downloaded XSLT files.'.PHP_EOL;
        mkdir($destPath, 0777, true);

        echo 'Save XSLT files on '.$destPath.PHP_EOL;
        self::decompress($zip, $destPath);
    }

    private static function decompress($zip, $destination)
    {
        $zipFile = new ZipFile();
        $zipFile->openFromString($zip)
                ->extractTo($destination);

        $zipFile->close();
    }
}

XsltDownloader::run();
