<?php
namespace Nubesys\Flow\Core\Flow;

use Nubesys\Flow\Core\Common;

class Flow extends Common {

    private $schema;
    private $endCallBack;

    private $timeLines;

    function __construct($p_container, $p_schema, $p_flowScope = array()){

        parent::__construct($p_container);

        $this->setLocalScopeSetter("flow");
        $this->setLocalScopeSetter("schema");
        $this->setLocalScopeSetter("endCallBack");
        $this->setLocalScopeSetter("nodes");
        $this->setLocalScopeSetter("ends");
        $this->setLocalScopeSetter("resultChannel");
        $this->setLocalScopeSetter("queueChannel");
        $this->setLocalScopeSetter("type");
        $this->setLocalScopeSetter("properties");
        $this->setLocalScopeSetter("mapping");

        $this->set("schema", $p_schema);
        $this->set("flow", $p_flowScope);

        if($this->has("schema.type")){

            $this->set("type", $this->get("schema.type"));
        }else{

            $this->set("type", "data");
        }

        //TODO VER UNA CLASE GRAPH PARA LOS GRAFICOS DE CONCURRENCIA
        $this->timeLines                        = array();

        $this->setResponseObject();

        $this->buildFlow();
    }

    public function startTimeLine($p_key){

        $this->timeLines[$p_key]                = array();
        $this->timeLines[$p_key]['start']       = microtime(true);

        echo $p_key . ", START, " . $this->timeLines[$p_key]['start'] . "\r\n";
    }

    public function endTimeLine($p_key){
        
        if(!isset($this->timeLines[$p_key]['end'])){

            $this->timeLines[$p_key]['end']     = microtime(true);

            echo $p_key . ", END, " . $this->timeLines[$p_key]['end'] . "\r\n";
        }
    }

    public function endAllTimeLines(){

        foreach($this->timeLines as $timeLineKey=>$timeLineValue){

            if(!isset($timeLineValue['end'])){

                $this->endTimeLine($timeLineKey);
            }
        }
    }

    public function getTimesLineGraph(){

        $result     = array();
        $minTime    = 0;
        $maxTime    = 0;

        foreach($this->timeLines as $timeLineKey=>$timeLineValue){

            if($minTime == 0){

                $minTime = $timeLineValue['start'];
            }else if($timeLineValue['start'] < $minTime){

                $minTime = $timeLineValue['start'];
            }

            if($timeLineValue['end'] > $maxTime){

                $maxTime = $timeLineValue['end'];
            }
        }

        $totalTime = $maxTime - $minTime;

        foreach($this->timeLines as $timeLineKey=>$timeLineValue){

            $timeLineStartTime              = $timeLineValue['start'] - $minTime;
            $result[$timeLineKey]['start']  = round(($timeLineStartTime * 100) / $totalTime);

            $timeLineEndTime                = $timeLineValue['end'] - $minTime;
            $result[$timeLineKey]['end']    = round(($timeLineEndTime * 100) / $totalTime);
        }

        return $result;
    }

    public function pushResult($p_data){
        $result = "!EOFLOW";
        
        $this->get("resultChannel")->push($p_data);

        if($this->get("resultChannel")->isFull()){

            $result = array();

            while(!$this->get("resultChannel")->isEmpty()){

                $result[] = $this->get("resultChannel")->pop();
            }
        }

        if(count($result) == 1){

            return $result[0];
        }else{

            return $result;
        }
    }

    protected function buildFlow(){

        //PROPERTIES
        if($this->has("schema.properties")){

            $this->set("properties", $this->get("schema.properties"));
        }

        //MAPPING
        if($this->has("schema.mapping")){

            $this->set("mapping", $this->get("schema.mapping"));
        }

        //NODES
        if($this->has("schema.nodes")){

            foreach($this->get("schema.nodes") as $node){
                
                if(isset($node['reference']) && isset($node['classPath'])){

                    $classPathTmp                                   = $node['classPath'];
                    
                    if(class_exists($classPathTmp)){

                        $this->set("nodes." . $node['reference'], new $classPathTmp($this->container, $this, $node));
                    }else{

                        //TODO: CLASS NOT EXISTS
                    }
                }else{

                    //TODO: NO PARAMS NODE
                }
            }
        }

        //PORTS
        $portsInstances                         = array();
        $resultChannelSize                      = 0;

        //STARTS CONNECTIONS
        if($this->has("schema.connections")){

            foreach($this->get("schema.connections") as $connection){

                if(!isset($portsInstances[$connection['node']])){

                    $portsInstances[$connection['node']]    = array();
                }

                if(!isset($portsInstances[$connection['node']][$connection['port']])){

                    $portId                                 = $connection['node'] . "_" . $connection['port'];

                    $portsInstances[$connection['node']][$connection['port']]   = new \Nubesys\Flow\Core\Flow\Port\Port($this->container, $portId);
                }

                //SOURCES
                if(isset($connection['sources'])){

                    foreach($connection['sources'] as $source){
                        
                        if(isset($source['start'])){

                            $portsInstances[$connection['node']][$connection['port']]->setAsStart();
                        }else{

                            $sourceNodeInstance             = $this->get("nodes." . $source['node']);

                            $portsInstances[$connection['node']][$connection['port']]->addSource($sourceNodeInstance, $source['port']);
                        }
                    }
                }

                //TARGETS
                if(isset($connection['targets'])){

                    foreach($connection['targets'] as $target){

                        if(isset($target['end'])){

                            $portsInstances[$connection['node']][$connection['port']]->setAsEnd();

                            $resultChannelSize              += 1;
                        }else{

                            $targetNodeInstance             = $this->get("nodes." . $target['node']);
                            
                            $portsInstances[$connection['node']][$connection['port']]->addTarget($targetNodeInstance, $target['port']);
                        }
                    }
                }
            }
        }
        
        foreach($portsInstances as $nodeReference=>$ports){
            
            foreach($ports as $portKey=>$portInstance){
                
                $this->get("nodes.".$nodeReference)->addPort($portKey, $portInstance);
            }
        }

        $this->set("resultChannel", new \Swoole\Coroutine\Channel($resultChannelSize));
    }

    public function start($p_stream, &$p_endCallBack){
        
        $this->set("endCallBack", $p_endCallBack);
        
        foreach($this->get("nodes") as $nodeKey=>$nodeInstance){
            
            foreach($nodeInstance->get("ports") as $portKey=>$portInstance){
                
                if($portInstance->isStart()){

                    go(function () use ($nodeKey, $portKey, $p_stream) {
                        
                        $this->get("nodes." . $nodeKey)->__input($portKey, $p_stream);
                    });
                }
            }
        }
    }

    public function end($p_stream){
        
        $this->endAllTimeLines();
        
        foreach($this->getTimesLineGraph() as $key=>$values){

            echo str_pad($key, 50) . str_repeat("░", $values['start']) . str_repeat("▓", $values['end'] - $values['start']) . "\r\n";
        }

        call_user_func($this->get("endCallBack"), $p_stream);
    }
}
