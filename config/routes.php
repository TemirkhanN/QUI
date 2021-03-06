<?php

/** Route - RegEx that matches string in url address . Required!
 *  Action - controllerName/methodName that will be called if route matches url. Required!
 *  NOTE! method name can be set dynamically by defining between () in RegEx and passing that value so 'action'=>'controllerName/{param_order_index}
 *  Params - variables matched in route between () . params index shall match order in route.
 *  NOTE! for example: in route /(\d+)/(one|two)* second parameter(one|two) shall be defined like so 'params'=>[1=>'second_param']
 *  NOTE! params can be accessed from App::$app->routedParam('param_name')
 *  Full - set true if should be checked full request url(not only PHP_URL_PATH)
 */


return [
    ['route' => '^/?$', 'action' => 'main'],
    ['route' => '^/redirect$', 'action' => 'main/outgoingLink'],
    ['route' => '^/wiki/?$', 'action' => 'wiki/index'],
    ['route' => '^/wiki/([a-z]+)/?$', 'action' => 'wiki/{1}']
];