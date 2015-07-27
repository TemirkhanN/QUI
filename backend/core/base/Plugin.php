<?php

namespace app\core\base;

/**
 * Interface Plugin
 * @package app\core\base
 *
 * Interface for every creating plugin. Class shall have public static init() function
 * that will be executed from App class automatically
 *
 * Why we need that?
 *
 * Example below shows init function from Rating plugin.
 * It adds custom routes to be accessible from url and creates necessary tables and connections
 *
 * public static function init()
 * {
 *      $routeToApi = ['route'=>'^/API/rating_plugin.php\?(getRating|setRating)+$', 'full' => true, 'action'=>'rating/{0}'];
 *      UrlManager::addRule($routeToApi);
 *      $postKey = self::$prefix.'targets';
 *      if(!empty($_POST[$postKey]) && is_array($_POST[$postKey])){
 *          self::createRatingTable();
 *          self::$targets = $_POST[$postKey];
 *      }
 * }
 */
interface Plugin {

    public static function init();

}