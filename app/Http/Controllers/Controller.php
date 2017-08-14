<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function ObjectToArray($obj) {
        $arr = [];
        foreach($obj as $key=>$value) {
            if(typeOf($value) == "Object") {
                $arr[$key] = ObjectToArray($value);
            } else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }
    
    public function postParam($key) {
        $value = '';
        if(array_key_exists('sex', $_POST)) {
            $value = urldecode($_POST[$key]);
        }
        return $value;
    }
    
    public function getParam($key) {
        $value = '';
        if(array_key_exists('sex', $_GET)) {
            $value = urldecode($_POST[$key]);
        }
        return $value;
    }
}
