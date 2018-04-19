<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TMoney {
	private $URL = "https://prodapi-app.tmoney.co.id/api";
	private $TMONEY_TOKEN = null;
	private $TMONEY_USER = null;

	public function __construct()
	{
		// generate access token first.
		$client = new Client;
		$content = $client->request('POST','https://api.mainapi.net/token',
                [
                	'body' => 'grant_type=client_credentials',
                	'headers'  => [
		                'Authorization' => 'Basic '. env('TMONEY_AUTH', '')
		            ]
            	]
        );

        if($content->getStatusCode() == 200)
        {
        	$content = json_decode($content->getBody(), true);
			$this->TMONEY_TOKEN = $content['access_token'];

			// sign in
			$this->signIn();
        }
        else
        {
        	return false;
        }
	}

	public function signIn()
	{
		// signature
		$hash = env('TMONEY_USERNAME') . env('TMONEY_DATETIME') . env('TMONEY_TERMINAL') . env('TMONEY_APIKEY');
		$signature = hash_hmac("sha256", $hash, env('TMONEY_PRIVATE_KEY'));

		// sign in.
		$client = new Client;
		$content = $client->request('POST',$this->URL . "/sign-in",
                [
                	'headers'  => [
		                'Authorization' => 'Bearer '. $this->TMONEY_TOKEN,
		                'Accept'        => 'application/json',
		            ],
		            'form_params' => [
		            	'userName' 	 => env('TMONEY_USERNAME'),
		            	'password'   => env('TMONEY_PASSWORD'),
		            	'terminal'   => env('TMONEY_TERMINAL'),
		            	'apiKey'	 => env('TMONEY_APIKEY'),
		            	'datetime'   => env('TMONEY_DATETIME'),
		            	'signature'  => $signature
		            ]
            	]
        );

        if($content->getStatusCode() == 200)
        {
        	$content = json_decode($content->getBody(), true);

        	$this->TMONEY_USER = $content;
        }
        else
        {
        	return false;
        }
	}

	public function topupPrepaid($productCode, $billNumber, $amount)
	{
		// Inquiry Topup Prepaid.
		$client = new Client;
		$content = $client->request('POST',$this->URL . "/topup-prepaid",
                [
                	'headers'  => [
		                'Authorization' => 'Bearer '. $this->TMONEY_TOKEN,
		                'Accept'        => 'application/json',
		            ],
		            'form_params' => [
		            	'transactionType' 	 => 1,
		            	'terminal'   => env('TMONEY_TERMINAL'),
		            	'apiKey'	 => env('TMONEY_APIKEY'),
		            	'idTmoney'   => $this->TMONEY_USER['user']['idTmoney'],
		            	'idFusion'   => $this->TMONEY_USER['user']['idFusion'],
		            	'token'   => $this->TMONEY_USER['user']['token'],
		            	'productCode'   => $productCode,
		            	'billNumber'   => $billNumber,
		            	'amount'   => $amount,
		            	'pin'   => env('TMONEY_PIN')
		            ]
            	]
        );

        if($content->getStatusCode() == 200)
        {
        	$inquiry_result = json_decode($content->getBody(), true);

			if($inquiry_result['resultCode'] !== 0) {
				return $inquiry_result;
			}

        	// payment prepaid
        	$content = $client->request('POST',$this->URL . "/topup-prepaid",
                [
                	'headers'  => [
		                'Authorization' => 'Bearer '. $this->TMONEY_TOKEN,
		                'Accept'        => 'application/json',
		            ],
		            'form_params' => [
		            	'transactionType' 	 => "2",
		            	'terminal'   => env('TMONEY_TERMINAL'),
		            	'apiKey'	 => env('TMONEY_APIKEY'),
		            	'idTmoney'   => $this->TMONEY_USER['user']['idTmoney'],
		            	'idFusion'   => $this->TMONEY_USER['user']['idFusion'],
		            	'token'   => $this->TMONEY_USER['user']['token'],
		            	'productCode'   => $productCode,
		            	'billNumber'   => $billNumber,
		            	'amount'   => $amount,
		            	'pin'   => env('TMONEY_PIN'),
		            	'transactionID' => $inquiry_result['transactionID'],
		            	'refNo' => $inquiry_result['refNo']
		            ]
            	]
        	);

        	return json_decode($content->getBody(), true);
        }
        else
        {
        	return false;
        }
	}
}