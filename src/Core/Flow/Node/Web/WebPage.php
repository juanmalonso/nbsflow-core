<?php
namespace Nubesys\Flow\Core\Flow\Node\Web;

use Nubesys\Flow\Core\Flow\Node\Node;
use Nubesys\Flow\Core\Flow\Response\FlowHttpWebResponse;

class WebPage extends Node {

    //V1 de web page

    private $jssources;
    private $csssources;
    private $scriptsblocks;

    private $response;

    protected function inputIn($p_params){
        $this->setParams($p_params);

        $this->initPageAssets();

        $pageFileName                   = "";
        if($this->has("params.url.0")){

            if($this->get("params.url.0") == "pages"){

                foreach($this->get("params.url") as $index=>$value){

                    if(is_numeric($index)){

                        if($index > 0){

                            $pageFileName .= "/" . $value;
                        }
                    }
                }
            }else{

                $pageFileName                   = $this->get("params.url.0");
            }

        }else{

            $pageFileName                   = "/index";
        }
        
        //TODO: Inicializar los httpresponseobject de forma abstracta
        $this->response = new FlowHttpWebResponse();
        $this->response->setBody($this->renderPageCode($pageFileName));

        $this->sendOut($this->response);
    }

    protected function renderPageCode($p_contentFileName = "/index", $p_templateFileName = "default"){
        
        $result                 = "";

        //TEMPLATE FILE
        $templateFileName       = $p_templateFileName;
        if($this->has("properties.template")){

            $templateFileName   = $this->get("properties.template");
        }

        //CONTENT FILE
        $contentFileName        = $p_contentFileName;
        if($this->has("properties.contentPath")){

            $contentFileName   = $this->get("properties.contentFile");
        }

        //FILES PATH
        if($this->has("config.files.path")){

            //TODO: CACHEAR LOS TEMPLATES

            $filesDirectory     = $this->get("config.files.path");

            $templateFilePath   = $filesDirectory . "web/templates/" . $templateFileName . ".phtml";

            if(file_exists($templateFilePath)){

                $result             = file_get_contents($templateFilePath);

                $contentFilePath    = $filesDirectory . "web/contents" . $contentFileName . ".phtml";
                
                if(file_exists($contentFilePath)){

                    $content        = file_get_contents($contentFilePath);

                    $result         = str_replace("{{content}}", $content, $result);

                }else{

                    //TODO: NO content file
                    //TODO: Content desde properties
                }
            }else{

                //TODO: NO template file
            }
        }else{

            //TODO: NO files directory
        }
        var_dump($this->csssources->all());
        $csssourcesCode             = "";
        foreach($this->csssources->all() as $csssource){

            $csssourcesCode         .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $csssource . "\">";
        }
        $result                     = str_replace("{{csssources}}", $csssourcesCode, $result);

        $jssourcesCode              = "";
        foreach($this->jssources->all() as $jssource){

            $jssourcesCode          .= "<script type=\"text/javascript\" src=\"" . $jssource . "\"></script>";
        }
        $result                     = str_replace("{{jssources}}", $jssourcesCode, $result);

        $scriptsblocksCode          = "";
        foreach($this->scriptsblocks->all() as $scriptsblock){

            $scriptsblocksCode      .= "<script type=\"text/javascript\" >" . $scriptsblock . "</script>";
        }
        $result                     = str_replace("{{scriptsblocks}}", $scriptsblocksCode, $result);                

        return $result;
    }

    protected function setParams($p_params){

        //TODO: Abstraer esta funcionalidad en los puertos start de los nodos WEB PAGE y API
        $this->setLocalScopeSetter("params");
        $this->set("params", $p_params);
    }
    
    protected function initPageAssets(){

        $this->jssources            = new \Nubesys\Flow\Core\Register();
        $this->csssources           = new \Nubesys\Flow\Core\Register();
        $this->scriptsblocks        = new \Nubesys\Flow\Core\Register();
        $this->content              = "";
        
        if($this->has("properties.jssources")){

            foreach ($this->get("properties.jssources") as $value) {
                
                $this->addJsSource($value);
            }
        }
        
        if($this->has("properties.csssources")){

            foreach ($this->get("properties.csssources") as $value) {
                
                $this->addCssSource($value);
            }
        }

        if($this->has("properties.scriptsblocks")){

            foreach ($this->get("properties.scriptsblocks") as $value) {
                
                $this->addScriptBlock($value);
            }
        }

    }

    protected function addJsSource($p_source){

        $this->jssources->push($p_source);
    }

    protected function addCssSource($p_source){

        $this->csssources->push($p_source);
    }

    protected function addScriptBlock($p_code){

        $this->scriptsbloks->push($p_code);
    }
}