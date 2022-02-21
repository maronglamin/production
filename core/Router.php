<?php

class Router
{

    public static function route ($url)
    {
        
        // controllers
        $controller = (isset($url[0]) && $url[0] != '') ? ucwords($url[0]) : DEFAULT_CONTROLLER;
        $controller_name = $controller;
        array_shift($url);

        // action 
        $action = (isset($url[0]) && $url[0] != '') ? $url[0] : 'index';
        $action_name = $controller;
        array_shift($url);

        // params
        $query_params = $url;

        $dispatch = new $controller($controller_name, $action);

        if (method_exists($controller, $action)) {
            call_user_func_array([$dispatch, $action], $query_params);
        } else {
            die('That Method does not exists in the  conroller\"' . $controller_name .'\"');
        }
    }

}