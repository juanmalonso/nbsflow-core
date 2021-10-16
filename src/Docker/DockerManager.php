<?php
namespace Nubesys\Flow\Docker;

use Nubesys\Flow\Docker\Client;

class DockerManager {

    private $socketPath;

    public function __construct(string $socketPath)
    {
        $this->socketPath = $socketPath;
    }

    public function lsContainers(){
        $result = array();
        
        $client = new Client($this->socketPath);

        foreach ($client->dispatchCommand('v1.41/containers/json') as $container) {
            
            $containerTmp               = array();
            $containerTmp['id']         = $container['Id'];
            $containerTmp['name']       = $container['Names'][0];
            $containerTmp['state']      = $container['State'];
            $containerTmp['status']     = $container['Status'];

            $containerTmp['ports']      = array();

            foreach($container['Ports'] as $portRow){

                if(isset($portRow['IP'])){

                    $portTmp                    = array();
                    $portTmp['ip']              = $portRow['IP'];
                    $portTmp['public']          = $portRow['PublicPort'];
                    $portTmp['private']         = $portRow['PrivatePort'];

                    $containerTmp['ports'][]    = $portTmp;
                }
            }

            $containerTmp['cmd']            = $container['Command'];

            $containerTmp['image']          = array();
            $containerTmp['image']['id']    = $container['ImageID'];
            $containerTmp['image']['name']  = $container['Image'];
            
            $result[]                   = $containerTmp;
        }
        
        $client->__destruct();

        return $result;
    }
}

?>