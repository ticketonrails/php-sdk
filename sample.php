<?php
require_once('tor.php');

$API_KEY = "YOUR API KEY";
$PROJECT_DOMAIN = "YOUR PROJECT DOMAIN";

$api = new TorApi($API_KEY, $PROJECT_DOMAIN);

// new ticket
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

// list tickets
$tickets_list = $api->get_tickets();
print_r($tickets_list);

// ticket detail
if (sizeof($tickets_list) > 0) {
	$first_ticket = $api->get_ticket($tickets_list["tickets"][0]["id"]);
	print_r($first_ticket);
}

?>