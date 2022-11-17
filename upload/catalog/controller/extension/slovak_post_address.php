<?php
class ControllerExtensionSlovakPostAddress extends Controller {

    function fillPoints(){

        $json_regions = array();

        if ($this->config->get('slovak_post_address_status')) {



            $url = 'https://www.posta.sk/public/forms/zoznam_post.xml';


            if ($this->config->get('slovak_post_address_url')) {
                $url = HTTPS_SERVER . 'catalog/controller/extension/zoznam_post.xml';
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //200 is fine


            if($retcode != 404){
                $xml = simplexml_load_file($url);


                foreach ($xml->POSTA as $posta_info){
                    $json_regions[] = $posta_info;
                }

                $json_regions =  json_encode($json_regions) ;


                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput($json_regions);
            }else{
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json_regions));
            }

        }
        else {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json_regions));
        }
    }


}
