<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\AppCommon;

class FlowAppCommon extends AppCommon
{
    protected $primaryFlow;
    
    public function __construct($p_container){

        parent::__construct($p_container);
    }

    protected function loadPrimaryFlow(){

        if($this->has("env.NBS_FLOW_LIBRARY") && $this->has("env.NBS_FLOW_PATH")){

            foreach($this->container->get("classLoader")->getPrefixesPsr4() as $prefix=>$values){

                if($prefix == $this->get("env.NBS_FLOW_LIBRARY")){

                    $primaryFlowDefinition              = $this->fileGetJsonContent($values[0] . "../json/" . $this->get("env.NBS_FLOW_PATH") . ".json");

                    var_dump($primaryFlowDefinition);
                }
            }
        }
    }
}

?>