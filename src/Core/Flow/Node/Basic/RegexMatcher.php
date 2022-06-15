<?php
namespace Nubesys\Flow\Core\Flow\Node\Basic;

use Nubesys\Flow\Core\Flow\Node\Node;

class RegexMatcher extends Node {

    protected function inputIn($p_params){
        
        //TODO: Usar properties y agripaciones con nombre para mas adelante
        $matches                = array();
        $result                 = preg_match("/" . $p_params['pattern'] . "/", $p_params['string'], $matches);

        if($result !== 0){

            $this->sendOut([
                "match"         => true,
                "matches"       => $matches
            ]);
        }else{

            $this->sendOut([
                "match"         => false,
                "matches"       => []
            ]);
        }
    }
}