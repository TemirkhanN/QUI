<?php

namespace qui;


/**
 * Class AutoLoader
 * @package qui
 *
 * Backend classes autoload system.
 */
class AutoLoader
{

    /**
     * @param string $className
     * @return bool
     * @throws \RuntimeException
     */
    public static function autoloadClass($className)
    {
        $classPath = self::calculatePathToClass($className);

        if (self::validToRequire($classPath)) {

            require_once(ROOT_DIR . '/' . $classPath);

            return true;
        }

        throw new \RuntimeException('Unable to autoload class ' . $className . '<br>');
    }


    /**
     * @param string $className
     * @return string
     */
    private static function calculatePathToClass($className)
    {
        $className = ltrim($className, '\\'); //remove first backslash if it exists
        $className = str_replace('_', '-', $className); //replace underline with dash in classes that lay in folders like "my-folder"
        $className = str_replace('\\', '/', $className ); // replace backslash with slash just to be sure
        $className = preg_replace('#^\\\?#', 'backend/', $className); //remove basic prefix of classes
        return $className.'.php';
    }


    /**
     * @param string $classPath
     *
     * @return bool
     */
    private static function validToRequire($classPath = '')
    {
        $pathToFile = realpath(ROOT_DIR.'/'.$classPath);

        $isInProjectDir = preg_match('#'.str_replace('\\', '\\\\', ROOT_DIR).'#', $pathToFile);

        return $pathToFile && $isInProjectDir && file_exists($pathToFile);

    }
}

spl_autoload_register('qui\AutoLoader::autoloadClass');
