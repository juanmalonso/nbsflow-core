<?php
namespace Nubesys\Flow\Core\Flow\Node\HttpRouter;

use Nubesys\Flow\Core\Flow\Node\Node;

class HttpRouterEntries extends Node {
    
    private $routerEntryIndex;
    private $routerParams;
    private $routerEntries;

    protected function inputIn($p_params){

        $this->routerEntryIndex         = 0;

        $this->routerParams             = $p_params;

        //TODO: CONFIG + PROPERTIES
        //TODO: properties layer (scope)

        $this->routerEntries            = $this->getRouterEntries();
        
        $this->nextEtry();
    }

    private function getRouterEntries(){
        $result                     = array();

        $entriesFinded              = $this->find("$..routes");

        if(count($entriesFinded)>0){
            
            foreach($entriesFinded as $routes){

                foreach($routes as $route){

                    $result[]       = $route;
                }
            }
        }else{

            $result                 = array();
        }
        
        return $result;
    }
    
    private function nextEtry(){

        if(count($this->routerEntries) > 0){

            if(isset($this->routerEntries[$this->routerEntryIndex])){
                
                //MATCH
                if(isset($this->routerEntries[$this->routerEntryIndex]["match"])){

                    $routerMatchData    = $this->routerEntries[$this->routerEntryIndex]["match"];

                    //TODO: Datos match en otro nodo para combinar con grupos
                    //TODO: Match de verbs, paths, host, rules

                    if(isset($routerMatchData['pathRegex'])){

                        $this->sendPathRegex([
                            "pattern"   => $routerMatchData['pathRegex'],
                            "string"    => $this->routerParams['uri']
                        ]);
                    }
                }else{

                    //TODO: GROUPS
                }
                
            }else{

                //TODO END SEND 404 NOT FOUND PAGE
                //DEPENDIENDO DE UI/API ETC

                $this->sendEnd("404");
            }
            
        }else{

            //TODO END SEND 404 NOT FOUND PAGE
            //DEPENDIENDO DE UI/API ETC

            $this->sendEnd("404");
        }
    }

    /*protected function inputEachResult($p_params){

        //var_dump($p_params);
    }*/

    protected function inputAllResult($p_params){
        
        $match = true;
        
        foreach($p_params as $entryResult){

            if(!$entryResult['match']){

                $match = false;
                break;
            }
        }

        if($match){

            //TODO: Dividir las acciones en otros nodos
            //TODO: acciones en otros nodos (redirect, flow, phpfile, rawfile, class method, etc.)

            if(isset($this->routerEntries[$this->routerEntryIndex]["flow"])){

                //TODO: Logica para el tipo de flow (web|xweb|api|file|etc) para armar el request

                $flowInstance                       = null;
                $flowData                           = $this->routerEntries[$this->routerEntryIndex]["flow"];
                
                foreach($this->container->get("classLoader")->getPrefixesPsr4() as $prefix=>$values){
        
                    if($prefix == $flowData["library"]){
                        
                        $flowSchemaFileContent       = $this->fileGetJsonContent($values[0] . "../../flows/" . $flowData["path"] . ".json");
        
                        $flowInstance                = new \Nubesys\Flow\Core\Flow\Flow($this->container, $flowSchemaFileContent, ["request", $this->routerParams]);
                        
                        break;
                    }
                }
                
                if(!is_null($flowInstance)){

                    $flowEndCallBack = function ($p_result) use ($flowInstance) {                      
                        
                        $this->sendEnd($p_result);
                    };
                
                    $flowInstance->start($this->routerParams, $flowEndCallBack);
                }

            }else{

                //TODO: implementar funciones tipo sendExit(), sendError(), sendRaw(), etc, etc

                $this->sendEnd("asd");
            }
        }else{

            $this->routerEntryIndex         += 1;

            $this->nextEtry();
        }
    }
    
    /*
    protected function inputIn($p_params){

        echo "NODE " . $this->get("reference") . " inputIn START \n";

        $p_params[] = "" . time();

        \Swoole\Coroutine::sleep(1);

        var_dump($p_params);

        echo "NODE " . $this->get("reference") . " inputIn END \n";

        $this->sendOut($p_params);
    }
    */
}