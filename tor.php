<?php
//check if curl installed
if (!function_exists('curl_init'))
	throw new TorApiException("lib curl missing");

class TorApi
{
	var $api_key = "";
	var $api_token = "";
	var $api_version = "1";
	var $server_url = "";
	var $project_domain = "";

	public function __construct($api_key, $project_domain) {
		$this->api_key = $api_key;
		$this->project_domain = $project_domain;
		$this->server_url = "http://api.ticketonrails.com";
		$this->api_token = md5($project_domain.md5($api_key));
	}

	public function request($url, $method, $params, $attachment = null) {
		$request_url = $this->server_url . "/v" . $this->api_version . $url;
		$params["token"] = $this->api_token;

		$ch = curl_init();
		switch($method) {
			case "GET":
				curl_setopt($ch, CURLOPT_URL, $request_url."?".http_build_query($params, '', '&'));
				break;
			case "POST":
				if ($attachment) {
					$postParams = array();
					foreach ($params as $fieldName => $fieldValue) {
						$postParams[$fieldName] = $fieldValue;
					}
					$postParams["attachment"] = "@".$attachment;
					curl_setopt($ch, CURLOPT_URL, $request_url);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
				} else {
					curl_setopt($ch, CURLOPT_URL, $request_url);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
				}
				break;
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE); 
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE); 

		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);

		curl_close($ch);
		
		//check status code or response for errors
		if($httpcode >= 400) {
			$json = json_decode($result, TRUE);
			throw new TorApiException(sprintf("%s", $json["error"]));
		}
		else {
			return json_decode($result, TRUE);
		}
	}

	public function new_ticket($values = null) {
		$params = array();
		$ticket = array();
		$attachment = null;
		if($values){
			# sending only what is necessary
			$ticket_params = array("email", "from_name", "subject", "body", "html", "date", "labels");
			foreach ($ticket_params as $param) {
				if (array_key_exists($param, $values)) {
					$ticket[$param] = $values[$param];
				}
			}
			if (array_key_exists("attachment", $values)) {
				$attachment = $values["attachment"];
			}
		}
		$params["ticket"] = json_encode($ticket);
		$response = $this->request("/tickets", "POST", $params, $attachment);
		return $response;
	}
}

class TorApiException extends Exception { }
?>