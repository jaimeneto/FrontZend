<?php

class FrontZend_Module_Container
{
    public function init()
    {
        $methods = get_class_methods($this);

        foreach($methods as $method) {
            if (substr($method, 0, 3) == 'new') {
                $name = substr($method, 3);
                FrontZend_Container::set($name, get_class($this));
            }
        }
    }

    public function get($name)
    {
        $methodName = "new{$name}";
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
    }

}