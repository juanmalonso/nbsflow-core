<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\App;

class FlowApp extends App
{
    protected $primaryFlow;
    
    public function __construct($p_container){

        parent::__construct($p_container);

        $this->setLocalScopeSetter("primaryFlow");
    }

    protected function loadPrimaryFlow(){

        if($this->has("env.NBS_FLOW_LIBRARY") && $this->has("env.NBS_FLOW_PATH")){

            foreach($this->container->get("classLoader")->getPrefixesPsr4() as $prefix=>$values){

                if($prefix == $this->get("env.NBS_FLOW_LIBRARY")){

                    $flowSchemaFileContent                              = $this->fileGetJsonContent($values[0] . "../../flows/" . $this->get("env.NBS_FLOW_PATH") . ".json");

                    $this->set("primaryFlow", new \Nubesys\Flow\Core\Flow\Flow($this->container, $flowSchemaFileContent));
                }
            }
        }
    }
}

?>