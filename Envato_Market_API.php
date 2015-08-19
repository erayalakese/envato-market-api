<?php
class Envato_Market_API
{
	private $personal_token = "GTTTePxFvxlTacMrB5I3qqPtCd4D0Po4";
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
		$context = stream_context_create(array(
				'http'=>array(
				    'method'=>"GET",
				    'header'=>"Authorization: Bearer ".$this->personal_token
				  )
			));

		$x = file_get_contents($url, false, $context);

		if($decode_json)
			return json_decode($x);
		else
			return $x;
	}

	function download()
	{
		header('Location: '.$this->download_url);
	}

	/*function download()
	{
		$url = $this->download_url;
		$file_name = explode("?", pathinfo($url)["basename"])[0];

		$target = fopen($file_name, 'w');

		if($f = fopen($url, "r"))
		{
		    while(!feof($f)) {
		        $buffer = fread($f, 2048);
		        fwrite($target, $buffer, 2048);
		    }
		}
		fclose($f);
		fclose($target);
	}*/
}