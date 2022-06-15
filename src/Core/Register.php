<?php
namespace Nubesys\Flow\Core;

use ArrayAccess;

class Register implements ArrayAccess
{
    private $container = array();

    public function __construct($p_container = array()) {
        
        $this->container = $p_container;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function get($key){
        
        if(!stristr($key, '.') === FALSE){
            
            return $this->getDot($key);
        }else{

            return isset($this->container[$key]) ? $this->container[$key] : null;
        }
    }

    public function getDot($keyDot){

        $dot = new \Adbar\Dot($this->container);

        return $dot->get($keyDot);
    }

    public function has($key){

        if(!stristr($key, '.') === FALSE){

            return $this->hasDot($key);
        }else{

            return isset($this->container[$key]) ? true : false;
        }
    }

    public function hasDot($keyDot){

        $dot = new \Adbar\Dot($this->container);

        return $dot->has($keyDot);
    }

    public function remove($p_key){

        if($this->has($p_key)){

            unset($this->container[$p_key]);
        }
    }

    public function set($key, $value){

        if (is_null($key)) {
            
            $this->container[] = $value;
        } else {

            if(!stristr($key, '.') === FALSE){

                $this->setDot($key, $value);
            }else{
                
                $this->container[$key] = $value;
            }
        }
    }

    public function setDot($keyDot, $value){

        $dot = new \Adbar\Dot($this->container);

        $dot->set($keyDot, $value);

        $this->setAll($dot->all());
    }

    public function setAll($value){

        $this->container = $value;
    }

    public function all(){

        return $this->container;
    }

    public function find($p_path){

        return (new \Flow\JSONPath\JSONPath(json_decode(json_encode($this->container),true)))->find($p_path)->getData();
    }

    public function push($value){

        $this->container[] = $value;
    }
    
    public function prepend($key, $value){}
    
    public function keys(){

        return array_keys($this->container);
    }
}