<?php

namespace core\app;


class AppLog
{

    private static $errors;

    /**
     * @param \Exception $error information
     */
    public static function noteError($error)
    {

        $trace = $error->getTrace();
        $loggedError['file'] = $trace[0]['file'];
        $loggedError['line'] = $trace[0]['line'];
        $loggedError['message'] = $error->getMessage();
        $loggedError['args'] = $trace[0]['args'];

        self::$errors[] = $loggedError;

    }


    protected static function showError($error = [])
    {

        echo 'Error occurred in ' . $error['file'] . ' on line ' . $error['line'] . '<br>';
        echo $error['message'];

        echo '<br> at ';
        foreach ($error['args'] as $arg) {
            if (!is_array($arg)) {
                echo $arg . ' ';
            }
        }

        echo '<br><br>';

    }


    public static function showErrors()
    {
        if (self::$errors !== null): ?>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= \App::t('error') ?></h3>
                </div>
                <div class="panel-body">
                    <? array_walk(self::$errors, 'self::showError'); ?>
                </div>
            </div>
        <? endif;

    }


    public static function errorsExist()
    {
        return !empty(self::$errors);
    }


}