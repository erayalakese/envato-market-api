<?php
namespace erayalakese;

/**
 * Envato Market API to verify and download Envato purchases
 *
 * @author Eray Alakese <erayalakese@gmail.com>
 * @version 1.0.0
 * @license GPL v2
 */
class Envato_Market_API
{
	private $personal_token;
	private $api_url = "https://api.envato.com/v1/market";
	private $download_url;

	function verify_purchase_code($code)
	{
		//(strlen($code) != 36) ? return false; : '';

		$url = sprintf("%s/private/user/verify-purchase:%s.json", $this->api_url, $code);

		$x = $this->request($url);
		//var_dump($x);

		return TRUE;
	}

	function download_item($code)
	{
		if(!$this->verify_purchase_code($code)) die("Invalid purchase code");
		$url = sprintf("%s/private/user/download-purchase:%s.json", $this->api_url, $code);

		// Find download url
		$x = $this->request($url, true);
		$this->download_url = $x->{"download-purchase"}->download_url;

		// Start download
		$this->download();
		

	}

	function request($url, $decode_json=false)
	{
		if(function_exists('curl_version')) :
			// Use cURL if possible
			$CURL = curl_init();
			curl_setopt($CURL, CURLOPT_URL, $url);
			curl_setopt($CURL, CURLOPT_HEADER, 1);
			curl_setopt($CURL, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->personal_token)); 
			curl_setopt($CURL, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($CURL);
			curl_close($CURL);
		else :
			// Use file_get_contents instead
			$context = stream_context_create(array(
					'http'=>array(
					    'method'=>"GET",
					    'header'=>"Authorization: Bearer ".$this->personal_token
					  )
				));

			$data = file_get_contents($url, false, $context);
		endif;

		if($decode_json)
			return json_decode($data);
		else
			return $data;
	}

	function download()
	{
		header('Location: '.$this->download_url);
	}

	function set_personal_token($token)
	{
		$this->personal_token = $token;
	}
}