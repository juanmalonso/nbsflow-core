<?php
namespace Nubesys\Flow\Core\Flow\Response;

use Nubesys\Flow\Core\Flow\Response\FlowResponse;

class FlowHttpResponse extends FlowResponse
{
    
    public function __construct() {

        parent::__construct();
    }
    
    public function setHeader($p_name, $p_value){
        
        //TODO: ver el tema de los key
        $this->set("header." . $p_name, $p_value);
    }

    public function getHeaders(){

        return $this->get("header");
    }
}