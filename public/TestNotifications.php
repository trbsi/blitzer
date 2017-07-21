<?php

require_once('PushNotifications.php');

// Message payload
$msg_payload = array (
'mtitle' => 'Test push notification title',
'mdesc' => 'Test push notification body',
);

// For Android
$regId = 'APA91bFj7EluPtNldew4A3dXKPVuiIs0BgdbXYM2FTTfJanrejChxCx-uxoK6mnXV7pWw1m0QrSeteJKf-PNSqJzv5Vn7JOMAhHfA5kWad07oS6zduRCwDyGk7atoSbwOp4NFDnM0W8S';

// For iOS
$deviceToken = 'b54768dcc082e3e03da2f6ff5cb5f2ac6fad0fbeb664c465abd610599e5677e3';
if(isset($_GET["token"])) {
	$deviceToken = $_GET["token"];
}

// For WP8
$uri = 'http://s.notify.live.net/u/1/sin/HmQAAAD1XJMXfQ8SR0b580NcxIoD6G7hIYP9oHvjjpMC2etA7U_xy_xtSAh8tWx7Dul2AZlHqoYzsSQ8jQRQ-pQLAtKW/d2luZG93c3Bob25lZGVmYXVsdA/EKTs2gmt5BG_GB8lKdN_Rg/WuhpYBv02fAmB7tjUfF7DG9aUL4';

// Replace the above variable values
if($_GET['os'] == 'android') {
	$r = PushNotifications::android($msg_payload, $regId);
}

//	PushNotifications::WP8($msg_payload, $uri);

if($_GET['os'] == 'ios') {
	$r = PushNotifications::iOS($msg_payload, $deviceToken);
}
var_dump($r);



?>