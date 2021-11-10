<?php
namespace Nubesys\Flow\Core\App;

use Nubesys\Flow\Core\App\FlowApp;

class TcpFlowApp extends FlowApp
{
    private $server;

    public function __construct($p_container, $p_server) {

        parent::__construct($p_container);
        
        $this->server               = $p_server;

        
    }
}