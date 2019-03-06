<?php

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */

class Configuration {
    // For a full list of configuration parameters refer in wiki page (https://github.com/paypal/sdk-core-php/wiki/Configuring-the-SDK)
    public static function getConfig($gbl_config = null) {

        if ($gbl_config['pp_checkout_sandboxmode'] == 1)
            $mode = "sandbox";
        else
            $mode = "live";


        $config = array( // values: 'sandbox' for testing
                //       'live' for production

            "mode" => $mode // These values are defaulted in SDK. If you want to override default values, uncomment it and add your value.
                // "http.ConnectionTimeOut" => "5000",
            // "http.Retry" => "2",

            );
        return $config;
    }

    // Creates a configuration array containing credentials and other required configuration parameters.
    public static function getAcctAndConfig($gbl_config = null) {

        $config = array(
            // Signature Credential
            "acct1.UserName" => $gbl_config['pp_checkout_username'],
            "acct1.Password" => $gbl_config['pp_checkout_password'],
            "acct1.Signature" => $gbl_config['pp_checkout_signatur'],
            // Subject is optional and is required only in case of third party authorization
            //"acct1.Subject" => "",

            // Sample Certificate Credential
            // "acct1.UserName" => "certuser_biz_api1.paypal.com",
            // "acct1.Password" => "D6JNKKULHN3G5B8A",
            // Certificate path relative to config folder or absolute path in file system
            // "acct1.CertPath" => "cert_key.pem",
            // "acct1.AppId" => "APP-80W284485P519543T"
            );

        return array_merge($config, self::getConfig($gbl_config));
        ;
    }

}
