<?php
namespace Nubesys\Flow\Core\Flow\Response;

use Nubesys\Flow\Core\Register;

class FlowResponse extends Register
{
    
    public function __construct() {

        parent::__construct();
        
        $this->setType("data");
        //setStatus("KO");
    }
    
    public function setType($p_type){

        $this->set("type", $p_type);
    }

    public function getType(){

        return $this->get("type");
    }

    public function setStatus($p_status){

        $this->set("status", $p_type);
    }

    public function getStatus(){

        return $this->get("status");
    }

    public function setData($p_data){

        $this->set("data", $p_data);
    }

    public function getData(){

        return $this->get("data");
    }

    public function setInfo($p_info){

        $this->set("info", $p_info);
    }

    public function getInfo(){

        return $this->get("info");
    }

    public function setDebug($p_debug){

        $this->set("debug", $p_debug);
    }

    public function getDebug(){

        return $this->get("debug");
    }

}