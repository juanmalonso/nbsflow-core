<?php
namespace Nubesys\Flow\Core\Flow\Node\Basic;

use Nubesys\Flow\Core\Flow\Node\Node;

class InOut extends Node {
    
    protected function inputIn($p_params){

        echo "NODE " . $this->get("reference") . " inputIn START \n";

        $p_params[] = "" . time();

        \Swoole\Coroutine::sleep(1);

        var_dump($p_params);

        echo "NODE " . $this->get("reference") . " inputIn END \n";

        $this->sendOut($p_params);
    }
}