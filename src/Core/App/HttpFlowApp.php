<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\FlowApp;

class HttpFlowApp extends FlowApp
{
    private $server;

    public function __construct($p_container, $p_server) {

        parent::__construct($p_container);
        
        $this->server                           = $p_server;

        $this->loadPrimaryFlow();
    }

    public function onRequest($request, $response){

        echo "ON REQUEST \r\n";
        
        $params = array();
        $params['method']                       = strtoupper($request->getMethod());
        $params['headers']                      = $request->header;
        $params['protocol']                     = $request->server['server_protocol'];
        $params['uri']                          = $request->server['request_uri'];
        $params['host']                         = $request->header['host'];
        
        if(!is_null($request->cookie)){

            $params['cookies']                  = $request->cookie;
        }

        if(!is_null($request->get)){
            
            $params['get']                      = $request->get;
        }

        if($request->server['request_uri'] != "/"){

            $params['url']                      = $this->parseUri($request->server['request_uri']);
        }

        if(!is_null($request->post)){
            
            $params['post']                     = $request->post;
        }

        if(!is_null($request->files)){

            $params['files']                    = $request->files;
        }
        
        if($request->rawContent() != ""){

            if($this->isValidJson($request->rawContent())){

                $params['json']                 = json_decode($request->rawContent());
            }else{

                $params['raw']                  = $request->rawContent();
            }
        }

        $flowEndCallBack = function ($p_result) use ($response) {
            
            //TODO: USAR EL OBJETO RESPONSE
            $body                               = "";
            if($p_result->has("type")){

                //HEADERS
                if($p_result->has("headers")){

                    //TODO: HEADERS
                }

                //WEB
                if($p_result->get("type") == "web"){

                    $response->header("Content-Type", "text/html; charset=UTF-8");

                    if($p_result->has("body")){

                        $body                   = $p_result->get("body");
                    }else{

                        //TODO: NO BODY
                    }
                }

                //API


                //FILES

            }else{

                //TODO: BAD RESPONSE TYPE
            }

            $response->end($body);
        };

        \Nubesys\Flow\Core\Colors::echo("Access " . $params['protocol'] . " - " . $params['host'] . " - " . $params['uri'], "green_bg+black+bold");
        echo "\r\n";
        
        $this->get("primaryFlow")->start($params, $flowEndCallBack);
        
    }

    public function onServerStart($server){

        echo "ON START \r\n";
    }

    public function onWorkerStart($server, $workerId){

        echo "ON WORKER START $workerId \r\n";
    }
}