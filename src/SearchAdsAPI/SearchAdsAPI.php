<?php
/**
 * Created by PhpStorm.
 * User: mamyta
 * Date: 06/02/2017
 * Time: 0:12
 */

class SearchAdsAPI{

    private $pemfile;
    private $keyfile;

    private $account_apple_id;

    public function __construct(array $config = [])
    {
        $this->setApiKeys($config['pemfile'],$config['keyfile']);
        if($config['account_apple_id']){
            $this->setAppleAccountId($config['account_apple_id']);
        }
    }

    public function setApiKeys($pemfile,$keyfile){
        if (!file_exists($pemfile)) {
            throw new ('Required pem file');
        }
        if (!file_exists($keyfile)) {
            throw new ('Required key file');
        }
        $this->pemfile = $pemfile;
        $this->$keyfile = $keyfile;
    }
    public function setAppleAccountId($account_apple_id){
        return $this->account_apple_id = $account_apple_id;
    }


    public function request(array $config = [])
    {

        $config = array_merge([
            'isAddHeader' => true,
            'method' => 'GET',
        ], $config);


        if (!empty($config['json'])){
            $config['json'] = json_encode(json_decode($config['json'],TRUE));
        }


        $url = "https://api.searchads.apple.com/api/v1/".$config['path'];
        $curl = curl_init($url);

        $HTTPHEADER =array();

        if ($config['isAddHeader']){
            $HTTPHEADER[]='Authorization: orgId=' . $this->account_apple_id;
        }
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->pemfile);
        curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLKEY, $this->keyfile);
        if($config['method'] == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $config['json']);
            $HTTPHEADER[] = 'Content-Type:application/json';
        }
        if($config['method'] == 'PUT'){
            curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $config['json']);
            $HTTPHEADER[] = 'Content-Type:application/json';
        }
        if (!empty($HTTPHEADER))
            curl_setopt($curl, CURLOPT_HTTPHEADER,  $HTTPHEADER);

        $ans = curl_exec($curl);

        return $ans;
    }
}