<?php
namespace Dompdf;

/**
 * Autoloads Dompdf classes
 *
 * @package Dompdf
 */
class Autoloader
{
    const PREFIX = 'Dompdf';


    /**
     * Register the autoloader
     */
    public static function register()
    {
        spl_autoload_register([new self, 'autoload']);

    }//end register()


    /**
     * Autoloader
     *
     * @param string
     */
    public static function autoload($class)
    {
        if ($class === 'Dompdf\Cpdf') {
            include_once __DIR__."/../lib/Cpdf.php";
            return;
        }

        $prefixLength = strlen(self::PREFIX);
        if (0 === strncmp(self::PREFIX, $class, $prefixLength)) {
            $file = str_replace('\\', '/', substr($class, $prefixLength));
            $file = realpath(__DIR__.(empty($file) ? '' : '/').$file.'.php');
            if (file_exists($file)) {
                include_once $file;
            }
        }

    }//end autoload()


}//end class
