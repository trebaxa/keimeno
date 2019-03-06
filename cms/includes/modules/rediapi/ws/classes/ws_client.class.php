<?php

class SoapHeaderAPIToken {
    public $apikey;
    public $appid;

    /**
     *
     * @param apikey
     */
    public function __construct($apikey, $appid) {
        $this->apikey = $apikey;
        $this->appid = $appid;
    }
}

class ws_client {

    var $client = NULL;
    var $result = "";

    function __construct() {
        #'http://www.dev.trebaxa.com/shop/ws/redimero.wsdl' => "NULL"

    }

    function connect($ws_config) {
        $this->connected = false;
        if ($ws_config->location() == "" || $ws_config->api_key() == "" && $ws_config->api_appid() == "")
            return;
        $wsu = 'http://schemas.xmlsoap.org/ws/2002/07/utility';
        $usernameToken = new SoapHeaderAPIToken($ws_config->api_key(), $ws_config->api_appid());
        $soapHeaders[] = new SoapHeader($wsu, 'APIToken', $usernameToken);
        $this->client = new SoapClient(NULL, array(
            'location' => $ws_config->location(),
            'uri' => $ws_config->location(),
            'trace' => 1,
            'exceptions' => 0));
        $this->client->__setSoapHeaders($soapHeaders);
        $this->connected = true;
    }

    public function interpreter() {
        if ($this->connected == false)
            return;
        $this->result = $this->client->__soapCall(strval($_REQUEST['cmd']), array($_GET));
        $this->output();
    }

    public function call($cmd, $params) {
        if ($this->connected == false)
            return;
        $this->result = $this->client->__soapCall(strval($cmd), array($params));
        if (is_soap_fault($this->result)) {
            echo "FehlerCode: ", $this->result->faultcode, "<br>";
            echo "Beschreibung: ", $this->result->faultstring, "<br>";
            echo "Sender: ", $this->result->faultactor, "<br>";
            die();
        }
        else {
            #  echo $this->arr_to_xml($this->result);
            return $this->result;
        }
    }

    protected function output() {
        if (is_soap_fault($this->result)) {
            echo "FehlerCode: ", $this->result->faultcode, "<br>";
            echo "Beschreibung: ", $this->result->faultstring, "<br>";
            echo "Sender: ", $this->result->faultactor, "<br>";
        }
        else {
            #  echo $this->arr_to_xml($this->result);
            echo $this->result;
        }
    }

    protected function arr_to_xml($arr) {
        $XMLPARSER = new ArrayToXML();
        $xml_string = $XMLPARSER->toXml($arr, 'data', null);
        unset($XMLPARSER);
        return $xml_string;
    }


}

?>