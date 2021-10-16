<?php
namespace Nubesys\Flow\Core;

class Common
{
    protected $container;

    public function __construct($p_container){

        $this->container    = $p_container;
    }

    //SCOPE MAGIC FUNCTION
    public function __call($name, $arguments){

        //TODO: VER EL USO DEL GLOBAL VS LOCAL CONTAINER QUE SEA TRANSPARENTE
        $result             = null;

        switch($name){

            case "get" :
                $result     = $this->container->get('globalScope')->get($arguments[0]);
                break;
            case "set" :
                $result     = $this->container->get('globalScope')->set($arguments[0], $arguments[1]);
                break;
            case "has" :
                $result     = $this->container->get('globalScope')->has($arguments[0]);
                break;
        }

        return $result;
    }

    //LOGGS

    //CACHE

    //FILES
    protected function fileGetJsonContent($p_path){

        if(file_exists($p_path)){

            return json_decode(file_get_contents($p_path));
        }else{

            return false;
        }
    }

    //HTTP

    //
}

?>