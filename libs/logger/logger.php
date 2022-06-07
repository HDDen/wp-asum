<?php
namespace logger;

use \ZipArchive as ZipArchive;

// Логи. Замена echo
$logdata = '';
$logfile = __DIR__ .'/log.txt';

if (defined('MADM_LOGGER_PATH') && (MADM_LOGGER_PATH != '')){
    $logfile = MADM_LOGGER_PATH;
}

if (defined('WP_PLUGIN_DIR')){
    $logfile = WP_PLUGIN_DIR . '/wp-asum/log.txt';
}

/*

short snippet

$logdata = print_r('',true);
$logfile = $_SERVER['DOCUMENT_ROOT'] . '/www/log.txt';
date_default_timezone_set( 'Europe/Moscow' );
$date = date('d/m/Y H:i:s', time());
//file_put_contents($logfile, $date.': '.$logdata.PHP_EOL, FILE_APPEND | LOCK_EX);

*/

// Сами ф-ии
// Старая вызывалась в новой, во втором методе упаковки

if (!function_exists('writeLog')){
    function writeLog($logdata = '', $newstarted = false){
        date_default_timezone_set( 'Europe/Moscow' );
        global $logfile;
        // Контроль размера файла. Если он больше определенного размера, помещаем в архив и пересоздаем.
        $maxLogSize = 1000000; // мегабайт

        if ($newstarted){
            $actualLogSize = filesize($logfile);

            if ($actualLogSize >= $maxLogSize){
                $date = date('d-m-Y_H-i-s', time());

                $zipped = false;
                if (class_exists('ZipArchive')){
                    $zip_file = dirname($logfile).'/_log_'.$date.'.zip';
                    $zip = new \ZipArchive;

                    if ($zip->open($zip_file, ZIPARCHIVE::CREATE)!==TRUE)
                    {
                        exit("cannot open <$zip_file>\n");
                    }
                    $zip->addFile($logfile,'log.txt');
                    $zip->close();
                    $zipped = true;
                }

                // Второй метод сжатия - если не отработал первый.
                $gzipped = false;
                if ( !$zipped ){

                    $bkp_to = dirname($logfile);
                    $bkp_name = '_log_'.$date.'.tar.gz';

                    $toarchive = shell_exec('tar -zcvf '.$bkp_to.'/'.$bkp_name.' '.$logfile.' ');
                    //$toarchive = shell_exec('tar -zcvf file.tar.gz /path/to/filename ');

                    $newlogdata = 'Прошли стадию паковки в гз'.PHP_EOL;
                    $newlogdata .= var_export($toarchive, true);
                    //old_writeLog($newlogdata);

                    $gzipped = true;
                }

                if ( $zipped || $gzipped ){
                    unlink($logfile);
                }
            }
        }

        $date = date('d/m/Y H:i:s', time());
        file_put_contents($logfile, $date.': '.$logdata.PHP_EOL, FILE_APPEND | LOCK_EX);

    }
}


?>