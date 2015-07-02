<?php

namespace app\core\file\worker;


class File {


    public static function deleteFile($file){
        if (file_exists($file)){
            unlink($file);
        }

        return;
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
            copy($from, $to);
        }

        $to = str_ireplace(FRONTEND_DIR, '', $to);

        return $to;

    }
}