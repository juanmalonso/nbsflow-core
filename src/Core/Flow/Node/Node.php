<?php
namespace Nubesys\Flow\Core\Flow\Node;

use Nubesys\Flow\Core\Common;

class Node extends Common {
    
    function __construct($p_container, &$p_flow, $p_options){
        
        parent::__construct($p_container);

        $this->setLocalScopeSetter("flow");
        $this->setLocalScopeSetter("reference");
        $this->setLocalScopeSetter("id");
        $this->setLocalScopeSetter("ports");
        $this->setLocalScopeSetter("portsChannels");
        $this->setLocalScopeSetter("portsCalls");
        $this->setLocalScopeSetter("properties");
        $this->setLocalScopeSetter("mapping");
        $this->setLocalScopeSetter("manifest");

        //ID
        $this->set("id", uniqid("node-", true));

        //FLOW REFERENCE
        $this->set("flow", $p_flow);

        //REFERENCE
        if(isset($p_options['reference'])){

            $this->set("reference", $p_options['reference']);
        }
        
        //PROPERTIES
        if(isset($p_options['properties'])){

            $this->set("properties", $p_options['properties']);
        }

        //MAPPING
        if(isset($p_options['mapping'])){

            $this->set("mapping", $p_options['mapping']);
        }

        $this->loadManifest();

        $this->set("ports", array());
        $this->set("portsChannels", array());
        $this->set("portsCalls", array());

        echo "NODE " . $this->get("reference") . " " . $this->getId() . " CREATED \n";
    }

    /* MANIFEST */
    protected function loadManifest(){

        $this->set("manifest", array());
    }

    /* ID MANAGER */
    public function getId(){

        return $this->get("id");
    }

    /* PORTS MANAGMENT */

    public function addPort($p_portKey, &$p_portInstance){

        $this->set("portsCalls." . $p_portKey, 0);
        $this->set("ports." . $p_portKey, $p_portInstance);
        
        if($p_portInstance->hasTargets()){

            $this->set("portsChannels." . $p_portKey, new \Swoole\Coroutine\Channel($p_portInstance->countTargets()));
        }

        if($p_portInstance->hasSources()){

            $this->set("portsChannels." . $p_portKey, new \Swoole\Coroutine\Channel($p_portInstance->countSources()));
        }
    }

    /* MESSAGE MANAGMENT */
    public function __input($p_portKey, $p_stream, $p_sourceNodeId = false){
        var_dump($this->get("reference") . "->__input->" . $p_portKey);
        if($this->has("ports." . $p_portKey)){

            $methodsToCalls = array();

            //INPUT EACH
            $localInputEachMethodName     = "inputEach" . ucfirst($p_portKey);
            var_dump("INPUT EACH? " . $localInputEachMethodName);
            if(method_exists($this, $localInputEachMethodName)){

                $methodsToCalls[$localInputEachMethodName] = $p_stream;
            }

            if($this->get("ports." . $p_portKey)->hasSources()){

                if($this->has("portsChannels." . $p_portKey)){

                    $this->get("portsChannels." . $p_portKey)->push($p_stream);
                    
                    if($this->get("portsChannels." . $p_portKey)->isFull()){

                        $data               = array();

                        while(!$this->get("portsChannels." . $p_portKey)->isEmpty()){

                            $data[]         = $this->get("portsChannels." . $p_portKey)->pop();
                        }

                        //INPUT FIRST
                        $localInputFirstMethodName        = "inputFirst" . ucfirst($p_portKey);
                        var_dump("INPUT FIRST? " . $localInputFirstMethodName);
                        if(method_exists($this, $localInputFirstMethodName)){

                            $methodsToCalls[$localInputFirstMethodName] = $data[0];
                        }

                        //INPUT LAST
                        $localInputLastMethodName         = "inputLast" . ucfirst($p_portKey);
                        var_dump("INPUT LAST? " . $localInputLastMethodName);
                        if(method_exists($this, $localInputLastMethodName)){

                            $methodsToCalls[$localInputLastMethodName] = $data[count($data) - 1];
                        }

                        //INPUT ALL
                        $localInputAllMethodName          = "inputAll" . ucfirst($p_portKey);
                        var_dump("INPUT ALL? " . $localInputAllMethodName);
                        if(method_exists($this, $localInputAllMethodName)){

                            $methodsToCalls[$localInputAllMethodName] = $data;
                        }
                    }
                }
            }
            
            if(count($methodsToCalls) == 0){

                //INPUT
                $localInputMethodName           = "input" . ucfirst($p_portKey);
                
                if(method_exists($this, $localInputMethodName)){

                    $methodsToCalls[$localInputMethodName] = $p_stream;
                }
            }

            //TODO: APLICAR DATA MAPPING a NIVEL DE PRUERTO->METODO
            //TODO: APLICAR DATA TRANSFORM a NIVEL DE PRUERTO->METODO

            foreach($methodsToCalls as $methodName=>$params){

                $portsCallsIncrement            = $this->get("portsCalls." . $p_portKey) + 1;

                $this->set("portsCalls." . $p_portKey, $portsCallsIncrement);

                $this->get("flow")->startTimeLine($this->get("reference") . "->" . $methodName . "[" . $this->get("portsCalls." . $p_portKey) . "]");

                $this->$methodName($params);

                $this->get("flow")->endTimeLine($this->get("reference") . "->" . $methodName . "[" . $this->get("portsCalls." . $p_portKey) . "]");
            }
        }else{

            //TODO : NO HAY PORT
        }
    }


    public function __return($p_portKey, $p_stream){
        
        if($this->has("ports." . $p_portKey)){

            $methodsToCalls = array();

            //RETURN EACH
            $localReturnEachMethodName     = "returnEach" . ucfirst($p_portKey);
                
            if(method_exists($this, $localReturnEachMethodName)){

                $methodsToCalls[$localReturnEachMethodName] = $p_stream;
            }

            if($this->get("ports." . $p_portKey)->hasTargets()){

                if($this->has("portsChannels. " . $p_portKey)){

                    $this->get("portsChannels. " . $p_portKey)->push($p_stream);
                                        
                    if($this->get("portsChannels. " . $p_portKey)->isFull()){

                        $data               = array();

                        while(!$this->get("portsChannels. " . $p_portKey)->isEmpty()){

                            $data[]         = $this->get("portsChannels. " . $p_portKey)->pop();
                        }

                        //RETURN FIRST
                        $localReturnFirstMethodName        = "returnFirst" . ucfirst($p_portKey);
                    
                        if(method_exists($this, $localReturnFirstMethodName)){

                            $methodsToCalls[$localReturnFirstMethodName] = $data[0];
                        }

                        //RETURN LAST
                        $localReturnLastMethodName         = "returnLast" . ucfirst($p_portKey);
                    
                        if(method_exists($this, $localReturnLastMethodName)){

                            $methodsToCalls[$localReturnLastMethodName] = $data[count($data) - 1];
                        }

                        //RETURN ALL
                        $localReturnAllMethodName          = "returnAll" . ucfirst($p_portKey);
                    
                        if(method_exists($this, $localReturnAllMethodName)){

                            $methodsToCalls[$localReturnAllMethodName] = $data;
                        }
                    }
                }
            }

            if(count($methodsToCalls) == 0){

                //RETURN
                $localReturnMethodName              = "return" . ucfirst($p_portKey);
            
                if(method_exists($this, $localReturnMethodName)){

                    $methodsToCalls[$localReturnMethodName] = $p_stream;
                }
            }

            //TODO: APLICAR DATA MAPPING a NIVEL DE PRUERTO->METODO
            //TODO: APLICAR DATA TRANSFORM a NIVEL DE PRUERTO->METODO

            foreach($methodsToCalls as $methodName=>$params){

                $portsCallsIncrement            = $this->get("portsCalls." . $p_portKey) + 1;
                $this->set("portsCalls." . $p_portKey, $portsCallsIncrement);

                $this->get("flow")->startTimeLine($this->get("reference") . "->" . $methodName . "[" . $this->get("portsCalls." . $p_portKey) . "]");

                $this->$methodName($params);

                $this->get("flow")->endTimeLine($this->get("reference") . "->" . $methodName . "[" . $this->get("portsCalls." . $p_portKey) . "]");
            }
        }else{

            //TODO : NO HAY PORT
        }
    }

    public function __call($p_methodName, $p_params){
        
        $data           = $p_params[0];
        
        //TODO: Hacer que los metodos magicos sean un array del common (has, get, set, appred, preppend, count, etc)
        if(in_array($p_methodName, ['get', 'has', 'set', 'find'])){
            
            //REQUEST PARAMS ACCESS
            if(substr($p_params[0], 0, 7) == 'request'){
                
                return call_user_func_array(array($this->get("flow"), $p_methodName), $p_params);
            }else{

                return parent::__call($p_methodName, $p_params);
            }
        
        }else if(substr($p_methodName, 0, 4) == 'send'){
            
            //LOGICA SEND
            $portKey = lcfirst(substr($p_methodName, 4));
            
            if($this->has("ports." . $portKey)){
                
                if($this->get("ports." . $portKey)->isEnd()){
                    
                    var_dump("END PORT CALLED");
        
                    $result = $this->get("flow")->pushResult($data);
        
                    if($result !== "!EOFLOW"){
        
                        var_dump("!EOFLOW");
        
                        $this->get("flow")->end($result);
                    }
                }
                
                if($this->get("ports." . $portKey)->hasTargets()){
        
                    foreach($this->get("ports." . $portKey)->getTargets() as $target){
                        
                        go(function () use ($data, $target) {
        
                            $target[0]->__input($target[1], $data, $this->getId());
                        });
                    }
                }
            }else{

                //TODO : NO HAY PORT
            }
        }else if(substr($p_methodName, 0, 9) == 'returnAll'){
            
            //LOGICA RETURN ALL
            $portKey = lcfirst(substr($p_methodName, 9));

            if($this->has("ports." . $portKey)){

                if($this->get("ports." . $portKey)->hasSources()){

                    foreach($this->get("ports." . $portKey)->getSources() as $source){
                        
                        go(function () use ($data, $source) {
        
                            $source[0]->__return($source[1], $data);
                        });
                    }
                }
            }else{

                //TODO : NO HAY PORT
            }
        }else if(substr($p_methodName, 0, 6) == 'return'){
            var_dump("LOGICA RETURN PORT ID", lcfirst(substr($p_methodName, 6)));
            //LOGICA RETURN
            $portKey = lcfirst(substr($p_methodName, 6));

            if($this->has("ports." . $portKey)){

                $sourceNodeId = "";

                foreach(debug_backtrace(0, 6) as $backStack){

                    if(isset($backStack['function']) && $backStack['function'] == "__input"){

                        $sourceNodeId = $backStack['args'][2];
                        break;
                    }
                }
                
                if($this->get("ports." . $portKey)->hasSources()){

                    foreach($this->get("ports." . $portKey)->getSources() as $source){
                        
                        if($source[2] == $sourceNodeId){

                            go(function () use ($data, $source) {

                                $source[0]->__return($source[1], $data);
                            });
                        }
                    }
                }
            }else{

                //TODO : NO HAY PORT
            }
        }
    }
}