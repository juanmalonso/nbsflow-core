<?php
namespace Nubesys\Flow\Core;

class Common
{
    protected $container;

    protected $localScope;
    protected $localScopeSetters;
    protected $localScopeProperties;

    public function __construct($p_container, $p_initialLocalScope = array()){

        $this->container            = $p_container;

        $this->localScope           = new \Nubesys\Flow\Core\Register($p_initialLocalScope);
        $this->localScopeSetters    = array();
    }

    protected function setLocalScopeSetter($p_setter){

        $this->localScopeSetters[]          = $p_setter;
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
            case "find" :

                $result         = array_merge($this->localScope->find($arguments[0]), $this->container->get('globalScope')->find($arguments[0]));

                break;
        }

        return $result;
    }

    //SESSION

    //LOGS

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
    protected static function isValidJson($p_string){

        return ((is_string($p_string) && (is_object(json_decode($p_string)) || is_array(json_decode($p_string))))) ? true : false;
    }

    //URI PARSER
    protected static function parseUri($p_uri){

        $result = array();

        $uriPartes                          = explode("/", $p_uri);

        if($uriPartes[0] == ""){

            array_shift($uriPartes);
        }

        if($uriPartes[count($uriPartes) - 1] == ""){

            array_pop($uriPartes);
        }

        for($p = 0; $p < count($uriPartes); $p++){

            if(strpos($uriPartes[$p], ":") !== false){

                $paramPartes                = explode(":", $uriPartes[$p]);

                if(strpos($paramPartes[1], ",") !== false){

                    $result[$paramPartes[0]]    = explode(",", $paramPartes[1]);
                }else{

                    $result[$paramPartes[0]]    = $paramPartes[1];
                }
                
            }else{

                $result[$p]                 = $uriPartes[$p];
            }
        }

        return $result;
    }
    
}

?>