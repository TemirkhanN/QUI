<?php

namespace core\helper;

use core\app\AppLog;


/**
 * Class FileSystem
 * @package core\helper
 *
 *
 * Any file manipulation such as delete, copy, require flow here
 */
class FileSystem {


    /**
     * @param string $filePath path to file that shall be required
     * @return bool|mixed returns false if somehow troubled to require file
     */
    public static function requireFile($filePath = '')
    {
        try {
            if (file_exists($filePath)) {

                return require_once $filePath;

            } else {

                throw new  \Exception('File ' . basename($filePath) . ' doesn\'t exist');

            }
        } catch (\Exception $error) {

            AppLog::noteError($error);
            return false;

        }
    }


    /**
     * @param string $from source absolute path
     * @param string $to destination where file going to be moved
     * @return mixed|string destination relative path from document_root
     */
    public static function copyFileToFolder($from = '', $to = '')
    {
        $destinationDir = pathinfo($to, PATHINFO_DIRNAME);
        if(!file_exists($to)){
            if(!is_dir($destinationDir)){
                mkdir($destinationDir, 0755, true);
            }

            try {
                if(copy($from, $to) == false){
                    throw new \Exception("Couldn't copy file from ". $from . " " . $to);
                }
            } catch(\Exception $e){
                AppLog::noteError($e);
            }
        }

        $to = str_ireplace(FRONTEND_DIR, '', $to);

        return $to;

    }


    /**
     * @param string $path  absolute path to file
     * @return bool
     */
    public static function deleteFile($path){
        if (file_exists($path)){
            return unlink($path);
        }

        return false;
    }


    /**
     * @param string $path absolute path to folder
     * @return bool
     */
    public static function deleteFolder($path)
    {
        if(is_dir($path)){
            $files = glob($path.'/*');

            if($files) {

                foreach ($files as $file) {

                    if(is_dir($path)){
                        self::deleteFolder($file);
                    } else{
                        self::deleteFile($file);
                    }

                }

            }

            return rmdir($path);
        }

        return false;

    }


}