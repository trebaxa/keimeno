<?php

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */

class paypal_lastschrift extends modules_class {

    var $PAYPAL_CHECKOUT = array();
    var $gbl_config_shop = array();

    /**
     * paypal_lastschrift::__construct()
     * 
     * @return
     */
    function __construct() {
        require_once ('PPBootStrap.php');
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = new graphic_class();
        $this->SELLFORM = array();
        if ($this->gbl_config['sf_rediapi'] != "" && class_exists('rediapi_class')) {
            $RC = new rediapi_class();
            $R = $RC->get_keypair($this->gbl_config['sf_rediapi']);
            $this->ws_config = new ws_clientconfig_class();
            $this->ws_config->set_api_id($R['r_apiid']);
            $this->ws_config->set_api_key($R['r_apikey']);
            $this->ws_config->set_location($R['r_serverurl']);
            $this->client = new ws_client();
            $this->client->connect($this->ws_config);
            $this->SELLFORM['invalidredi'] = $R['r_apiid'] == "";
            $this->gbl_config_shop = $this->client->call('get_shopconfig', array());
        }
    }

    /**
     * paypal_lastschrift::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('PAYPAL_CHECKOUT') != NULL) {
            $this->PAYPAL_CHECKOUT = array_merge($this->smarty->getTemplateVars('PAYPAL_CHECKOUT'), $this->PAYPAL_CHECKOUT);
            $this->smarty->clearAssign('PAYPAL_CHECKOUT');
        }
        $this->smarty->assign('PAYPAL_CHECKOUT', $this->PAYPAL_CHECKOUT);
    }

    /**
     * paypal_lastschrift::cronjob()
     * 
     * @return
     */
    function cronjob() {

    }

    /**
     * paypal_lastschrift::on_order()
     * 
     * @param mixed $o_obj
     * @return
     */
    public function on_order($o_obj = null) {
        $paymentDetails = $this->getPaymentDetails($o_obj);
        $setECReqDetails = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails[0] = $paymentDetails;
        $setECReqDetails->CancelURL = SSLSERVER . "index.php?page=" . $_REQUEST['page'] . "&section=ordercancel";
        $setECReqDetails->ReturnURL = SSLSERVER . 'index.php?page=100034&cmd=order_done&zahlweise=18&kid=' . $o_obj['kid'];
        $billingAgreementDetails = new BillingAgreementDetailsType('MerchantInitiatedBillingSingleAgreement');
        $billingAgreementDetails->BillingAgreementDescription = $o_obj['oid']; //TODO
        $setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);
        $setECReqType = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;
        $paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig($this->gbl_config_shop));

        try {
            /* wrap API method calls on the service object with a try catch */
            $setECResponse = $paypalService->SetExpressCheckout($setECReq);
            $token = $setECResponse->Token;

            if ($setECResponse->Ack == 'Success') {
                $token = $setECResponse->Token;
                header('Location: https://www.' . (($this->gbl_config_shop['pp_checkout_sandboxmode'] == 1) ? 'sandbox.' : '') .
                    'paypal.com/webscr?cmd=_express-checkout&token=' . $token);
            }
        }
        catch (Exception $ex) {
            //  include_once ("../Error.php");
            exit;
        }

        echoarr($setECResponse); //bleibt erstmal
        die(); //bleibt erstmal
    }

    /**
     * paypal_lastschrift::getPaymentDetails()
     * 
     * @param mixed $o_obj
     * @return
     */
    private function getPaymentDetails($o_obj) {
        $currencyCode = $o_obj['currency'];
        $paymentDetails = new PaymentDetailsType();
        $shippingTotal = new BasicAmountType($currencyCode, round($o_obj['post_brutto'], 2));
        $itemTotalValue = 0;
        $taxTotalValue = 0;
        $i = 0;
        $returnArray = $this->client->call('load_order_inhalt', array('oid' => $o_obj['oid']));
        foreach ($returnArray as $row) {
            $taxProValue = round($row['vk'] - $row['vknetto'], 2);
            $taxTotalValue += $taxProValue * round($row['menge'], 0);
            $itemAmount = new BasicAmountType($currencyCode, round($row['vknetto'], 2));
            $itemDetails = new PaymentDetailsItemType();
            $itemDetails->Name = $row['pname'];
            $itemDetails->Amount = $itemAmount;
            $itemDetails->Quantity = round($row['menge'], 0);
            $itemDetails->Tax = new BasicAmountType($currencyCode, $taxProValue);
            $paymentDetails->PaymentDetailsItem[$i] = $itemDetails;
            $i++;
        }

        $itemTotalValue = round($o_obj['brutto'], 2) - round($o_obj['post_brutto'], 2) - $taxTotalValue;
        $orderTotalValue = round($o_obj['brutto'], 2);


        //Payment details
        $address = $this->getCustomerAdress($o_obj);
        $paymentDetails->ShipToAddress = $address;
        $paymentDetails->ItemTotal = new BasicAmountType($currencyCode, $itemTotalValue);
        $paymentDetails->TaxTotal = new BasicAmountType($currencyCode, $taxTotalValue);
        $paymentDetails->OrderTotal = new BasicAmountType($currencyCode, $orderTotalValue);
        $paymentDetails->PaymentAction = 'Order';
        $paymentDetails->ShippingTotal = $shippingTotal;

        return $paymentDetails;

    }

    /**
     * paypal_lastschrift::getCustomerAdress()
     * 
     * @param mixed $o_obj
     * @return
     */
    private function getCustomerAdress($o_obj) {
        $address = new AddressType();
        $address->CityName = $o_obj['cust_ort'];
        $address->Name = $o_obj['cust_lastname'];
        $address->Street1 = $o_obj['cust_strasse'] . ' ' . $o_obj['cust_hausnr'];
        $address->StateOrProvince = '';
        $address->PostalCode = $o_obj['cust_plz'];
        $address->Country = $o_obj['cust_country'];
        $address->Phone = $o_obj['cust_tel'];
        return $address;
    }

    /**
     * paypal_lastschrift::on_order_done()
     * 
     * @param mixed $params
     * @return
     */
    public function on_order_done($params = null) {
        $BARequestType = new CreateBillingAgreementRequestType($_GET['token']);
        $createBillingAgreementReq = new CreateBillingAgreementReq();
        $createBillingAgreementReq->CreateBillingAgreementRequest = $BARequestType;
        $paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig($this->gbl_config_shop));
        $PayPalResult = $paypalService->CreateBillingAgreement($createBillingAgreementReq);
        $kundenObj = array('pp_lastschrift_ba_id' => $PayPalResult->BillingAgreementID);
        $params = array("kid" => $_GET['kid'], "customer" => $kundenObj);
        $this->client->call('update_customer', $params);
        header('Location:' . $this->gbl_config_shop['opt_site_domain'] . 'index.php?page=' . $_REQUEST['page'] . '&section=orderfine ');
    }
}

?>