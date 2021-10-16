<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\FlowAppCommon;

class HttpFlowApp extends FlowAppCommon
{
    private $server;

    public function __construct($p_container, $p_server) {

        parent::__construct($p_container);
        
        $this->server               = $p_server;

        $this->loadPrimaryFlow();
    }

    public function onRequest($request, $response){

        echo "ON REQUEST \r\n";

        $response->write("HELLO WORLD \r\n");
        
        $response->write($this->server->getWorkerId() . "\r\n");
        $response->write($this->server->getWorkerPid() . "\r\n");
                
        $response->write(json_encode($this->container->get("classLoader")->getPrefixesPsr4()));
        $response->end();
    }

    public function onServerStart($server){

        echo "ON START \r\n";
    }

    public function onWorkerStart($server, $workerId){

        echo "ON WIRKER START $workerId \r\n";
    }
}