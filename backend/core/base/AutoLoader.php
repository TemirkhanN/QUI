<?php

namespace app\core\base;


/**
 * Class AutoLoader
 * @package app\core\base
 *
 * Backend classes autoload system.
 */
class AutoLoader
{

    const PROJECT_ROOT = ROOT_DIR;
    const NAMESPACE_PREFIX = 'app';


    public static function autoloadClass($className = '')
    {
        $className = ltrim($className, '\\'); //remove first backslash if it exists
        $className = str_replace('_', '-', $className); //replace underline with dash in classes that lay in folders like "my-folder"
        $className = str_replace('\\', '/', $className ); // replace backslash with slash just to be sure
        $className = preg_replace('#^\\\?'.self::NAMESPACE_PREFIX.'#', 'backend', $className); //remove basic prefix of classes
        $className = $className.'.php';

        if (self::validToRequire($className)) {

            return require_once self::PROJECT_ROOT . '/' . $className;

        }

        echo 'Unable to autoload class ' . $className . '<br>';
    }


    /**
     * @param string $classPath
     *
     * @return bool
     */
    private static function validToRequire($classPath = '')
    {

        $pathToFile = realpath(self::PROJECT_ROOT.'/'.$classPath);

        return $pathToFile && preg_match('#'.str_replace('\\', '\\\\\\', ROOT_DIR).'#', $pathToFile);

    }



}


spl_autoload_register('\app\core\base\AutoLoader::autoloadClass');