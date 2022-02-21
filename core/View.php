<?php

class View
{
    protected $_head,  $_body, $_site_title = SITE_TITLE, $_out_put_buffer, $_layout = DEFAULT_LAYOUT;

    public function __construct() 
    {

    }

    public function render($view_name)
    {
        $view_array = explode('/', $view_name);
        $view_string = implode(DS, $view_array);

        if (file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $view_string . '.php')) 
        {
            include(ROOT . DS . 'app' . DS . 'views' . DS . $view_string . '.php');
            include(ROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . $this->_layout . '.php');
        }
         else
        {
            die('This view \"' . $view_name . '\" does not exist.');
        }
    }

    public function content($type)
    {
        if ($type == 'head')
        {
            return $this->_head;
        }
         elseif ($type == 'body')
        {
            return $this->_body;
        }
        return false;
    }

    public function start($type) 
    {
        $this->_out_put_buffer = $type;
        ob_start();
    }

    public function end()
    {
        if ($this->_out_put_buffer == 'head')
        {
            $this->_head = ob_get_clean();
        } 
        elseif ($this->_out_put_buffer == 'body')
        {
            $this->_body = ob_get_clean();
        }
        else 
        {
            die('You must first run the start method');
        }
    }

    public function site_title()
    {
        return $this->_site_title;
    }

    public function set_site_title($title)
    {
        $this->_site_title = $title;
    }

    public function set_layout($path)
    {
        $this->_layout = $path;
    }

}