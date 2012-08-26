<?php
require_once('tor.php');

$API_KEY = "YOUR API KEY";
$PROJECT_DOMAIN = "YOUR PROJECT DOMAIN";

$api = new TorApi($API_KEY, $PROJECT_DOMAIN);

$ticket = array(
		"email" => "your@email.address",
		"from_name" => "John Doe",
		"subject" => "lorem ipsum...",
		"body" => "lorem ipsum...",
		"html" => false,
		"date" => time(),
		"labels" => array("label 1", "label 2"),
		"attachment" => "attachment.txt"
	);

$ticket_result = $api->new_ticket($ticket);
print_r($ticket_result);
?>