<?php
namespace Nubesys\Flow\Core\Flow\Response;

use Nubesys\Flow\Core\Flow\Response\FlowHttpResponse;

class FlowHttpWebResponse extends FlowHttpResponse
{
    
    public function __construct() {

        parent::__construct();

        $this->setType("web");
    }
    
    public function setBody($p_body){
        
        //TODO: ver el tema de los key
        $this->set("body", $p_body);
    }

    public function getBody(){

        return $this->get("body");
    }
}