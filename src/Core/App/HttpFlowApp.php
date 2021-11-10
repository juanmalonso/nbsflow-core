<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\FlowApp;

class HttpFlowApp extends FlowApp
{
    private $server;

    public function __construct($p_container, $p_server) {

        parent::__construct($p_container);
        
        $this->server               = $p_server;

        $this->loadPrimaryFlow();
    }

    public function onRequest($request, $response){

        echo "ON REQUEST \r\n";
        var_dump($request->server);
        $params = array();
        $params['METHOD']                   = strtoupper($request->getMethod());
        $params['HEADERS']                  = $request->header;
        $params['COOKIES']                  = $request->cookie;
        $params['URL']                      = array();
        $params['POST']                     = $request->post;
        
        if(!is_null($request->get)){
            
            $params['GET']                  = $request->get;
        }

        if(!is_null($request->post)){
            
            $params['POST']                  = $request->post;
        }

        if(!is_null($request->files)){

            $params['FILES']    = $request->files;
        }
        
        if($request->rawContent() != ""){

            if($this->isValidJson($request->rawContent())){

                $params['JSON']             = json_decode($request->rawContent());
            }else{

                $params['RAW']              = $request->rawContent();
            }
        }

        var_dump($params);
        
        $response->header("Content-Type", "text/plain");
        /*
        $data       = array("A","B","C");

        $flowEndCallBack = function ($p_result) use ($response) {

            $response->end(json_encode($p_result));
        };
    
        $this->get("primaryFlow")->start($data, $flowEndCallBack);
        */
        $response->end(json_encode("ASD"));
        /*
        $response->write("HELLO WORLD \r\n");
        
        $response->write($this->server->getWorkerId() . "\r\n");
        $response->write($this->server->getWorkerPid() . "\r\n");
                
        $response->write(json_encode($this->container->get("classLoader")->getPrefixesPsr4()));
        */
    }

    public function onServerStart($server){

        echo "ON START \r\n";
    }

    public function onWorkerStart($server, $workerId){

        echo "ON WORKER START $workerId \r\n";
    }
}