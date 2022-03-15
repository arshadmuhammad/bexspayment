<?php


namespace MagArs\Bexs\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use MagArs\Bexs\Logger\Logger;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper {

    protected Logger $bexsLogger;

    protected string $accessToken;

    public function __construct(
        Context $context,
        Logger $bexsLogger
    ) {
        parent::__construct($context);
        $this->bexsLogger = $bexsLogger;
        $this->setAccessToken();
    }

    private function getConfig($path) {
        return $this->scopeConfig->getValue($path);
    }

    public function isActive() {
        return $this->getConfig('payment/bexs_api/active');
    }

    public function getAuthURL() {
        return $this->getConfig('payment/bexs_api/auth_url');
    }

    public function getAPIEndPoint() {
        return $this->getConfig('payment/bexs_api/api_end_point');
    }

    public function getClientId() {
        return $this->getConfig('payment/bexs_api/client_id');
    }

    public function getClientSecret() {
        return $this->getConfig('payment/bexs_api/client_secret');
    }

    public function getAudienceURL(): string {
        return 'https://forex-sandbox.bexs.com.br';
    }

    public function generateToken(){

        $curl = curl_init();

        $bodyParams = [];
        $bodyParams['client_id']       = $this->getClientId();
        $bodyParams['client_secret']   = $this->getClientSecret();
        $bodyParams['audience']   = $this->getAudienceURL();
        $bodyParams['grant_type']      = "client_credentials";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAuthURL().'/v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($bodyParams),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function setAccessToken() : void {
        $result = $this->generateToken();
        $this->accessToken = $result['access_token'];
    }

    public function getAccessToken() : string {
        return $this->accessToken;
    }

    public function createPayment($price = 1, $description, $consumer, $billing, $cartData){

        $exchangeData = $this->getExchangeRate('BRL', 'USD');

//        $consumer = [
//            "address" => [
//                "city" => "São Paulo",
//                "country" => "BRA",
//                "full_street_address" => "Av. Paulista",
//                "region" => "SP",
//                "zip_code" => "01311300"
//            ],
//            "email" => "email@domain.com",
//            "full_name" => "Your Name",
//            "external_id" => "costumer-external-id",
//            "national_id" => $national_id
//
//        ];

        $postData['checkout'] = true;
        $postData['confirm'] = true;
        $postData['installments'] = 1;
        $postData['due_date'] = date('Y-m-d');
        $postData['soft_descriptor'] = $description;
        $postData['amount'] = (float) $price;
        $postData['foreign_amount'] = $this->getForeignAmount($price,$exchangeData['quotes'][0]['rate']);
//        $postData['cart'] = [
//            [
//                "description" => "product discription",
//                "quantity" => 2,
//                "unit_price" => 1
//            ]
//        ];
        $postData['cart'] = $cartData;
        $postData['consumer'] = $consumer;
        $postData['billing'] = $billing;
        $postData['redirect_url'] = $this->_getUrl('');


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getAPIEndPoint().'v1/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '. $this->getAccessToken(),
                'Cache-Control: no-cache',
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function getForeignAmount($amount, $rate) {
        return (float) $amount / $rate;
    }

    public function getExchangeRate($from, $to) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getAPIEndPoint().'v1/exchange-rate?from='.$from.'&to='.$to,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '. $this->getAccessToken(),
                'Cache-Control: no-cache',
                'Content-Type: application/json'
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

}
