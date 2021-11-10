<?php
namespace Nubesys\Flow\Core;

class Common
{
    protected $container;

    protected $localScope;
    protected $localScopeSetters;

    public function __construct($p_container){

        $this->container            = $p_container;

        $this->localScope           = new \Nubesys\Flow\Core\Register();
        $this->localScopeSetters    = array();
    }

    protected function setLocalScopeSetters($p_setters){

        $this->localScopeSetters    = $p_setters;
    }

    //SCOPE MAGIC FUNCTION
    public function __call($name, $arguments){

        //TODO: VER EL USO DEL GLOBAL VS LOCAL CONTAINER QUE SEA TRANSPARENTE
        $result             = null;

        switch($name){

            case "get" :
            
                if($this->localScope->has($arguments[0])){

                    $result     = $this->localScope->get($arguments[0]);
                }else{

                    $result     = $this->container->get('globalScope')->get($arguments[0]);
                }
                
                break;
            case "set" :

                if(\in_array(explode('.', $arguments[0])[0], $this->localScopeSetters)){

                    $result     = $this->localScope->set($arguments[0], $arguments[1]);
                }else{

                    $result     = $this->container->get('globalScope')->set($arguments[0],$arguments[1]);
                }
                                
                break;
            case "has" :
                
                if($this->localScope->has($arguments[0])){

                    $result     = true;
                }else{

                    $result     = $this->container->get('globalScope')->get($arguments[0]);
                }

                break;
        }

        return $result;
    }

    //SESSION

    //LOGGS

    //CACHE

    //FILES
    protected function fileGetJsonContent($p_path){

        if(file_exists($p_path)){

            return json_decode(file_get_contents($p_path), true);
        }else{

            return false;
        }
    }

    //HTTP

    //STRUCT
    public static function isValidJson($p_string){

        return ((is_string($p_string) && (is_object(json_decode($p_string)) || is_array(json_decode($p_string))))) ? true : false;
    }
    
}

?>