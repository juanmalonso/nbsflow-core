<?php
namespace Nubesys\Flow\Core\Flow\Port;

use Nubesys\Flow\Core\Common;

class Port extends Common {

    function __construct($p_container, $p_id){

        parent::__construct($p_container);

        $this->setLocalScopeSetters(["id","sources","targets","isStart","isEnd"]);

        echo "PORT " . $p_id . " CREATED \n";
        
        //ID
        $this->set("id", $p_id);

        $this->set("sources",array());
        $this->set("targets",array());
        $this->set("isStart",false);
        $this->set("isEnd",false);
    }

    /* ID MANAGER */
    public function getId(){

        return $this->get("id");
    }

    /* START END */
    public function setAsStart(){

        $this->set("isStart", true);
    }

    public function setAsEnd(){

        $this->set("isEnd", true);
    }

    public function isStart(){

        return $this->get("isStart");
    }

    public function isEnd(){

        return $this->get("isEnd");
    }

    /* SOURCES MANAGMENT */
    public function addSource(&$p_sourceNodeInstance, $p_sourcePortKey){

        $sources            = $this->get("sources");

        $sources[]          = array($p_sourceNodeInstance, $p_sourcePortKey, $p_sourceNodeInstance->getId());

        $this->set("sources", $sources);
    }

    public function getSources(){

        return $this->get("sources"); 
    }

    public function hasSources(){
        $result         = false;

        if(count($this->get("sources")) > 0){

            $result     = true;
        }

        return $result;
    }

    public function countSources(){

        return count($this->get("sources"));
    }

    /* TARGETS MANAGMENT */
    public function addTarget(&$p_targetNodeInstance, $p_targetPortKey){

        $targets            = $this->get("targets");

        $targets[]          = array($p_targetNodeInstance, $p_targetPortKey, $p_targetNodeInstance->getId());

        $this->set("targets", $targets);
    }

    public function getTargets(){

        return $this->get("targets");
    }

    public function hasTargets(){

        $result         = false;

        if(count($this->get("targets")) > 0){

            $result     = true;
        }

        return $result;
    }

    public function countTargets(){

        return count($this->get("targets"));
    }
}