#!/usr/bin/env php

<?php 


require 'vendor/autoload.php';

use Pubnub\Pubnub;


$pubnub = new Pubnub(
    "pubnub-public-key",  
    "pubnub-subscriber-key-ithnk",  
    "pubnub-secret-key-maybe?",   
    false   
);

function banner() {

	echo "[+]-------------------------------------[+]\n";
	echo "[+]\t Real Time Chat Application \t[+]\n";
	echo "[+]\t  HyperTEXT PreProcessor\t[+]\n";
	echo "[+]\t \t \t \t \t[+]\n";
	echo "[+]\t \t@thearyanahmed\t \t[+]\n";
	echo "[+]\t \t \t \t \t[+]\n";
	echo "[+]-------------------------------------[+]\n";
	echo "\n";
}

function getEncryptionLevel() {

	fwrite(STDOUT, 'Set Encryption Level (1-10) : ');

	$level = trim(fgets(STDIN));

	if (!is_int($level) && !($level > 0 && $level <= 10)) {
		getEncryptionLevel();
	}

	return $level;
}


banner();


$encryptionLevelBase = "JGVuY3J5cHRpb25MZXZlbCA9IGdldEVuY3J5cHRpb25MZXZlbCgpOw==";

$makeHashBase = "ZnVuY3Rpb24gbWFrZUhhc2goJHN0cmluZywkbGV2ZWwpDQp7CQ0KICAgIGZvciAoJGk9MDsgJGkgPCAkbGV2ZWw7ICRpKyspIHsgDQoJCSRzdHJpbmcgPSBzdHJyZXYoJHN0cmluZyk7DQoJCSRzdHJpbmcgPSBAY29udmVydF91dWVuY29kZSgkc3RyaW5nKTsNCgl9DQoNCglyZXR1cm4gJHN0cmluZzsNCn0NCg==";

$deHashBase = "ZnVuY3Rpb24gZGVIYXNoKCRzdHJpbmcsJGxldmVsKQ0Kew0KCWZvciAoJGk9MDsgJGkgPCAkbGV2ZWw7ICRpKyspIHsgDQoJCSRzdHJpbmcgPSBAY29udmVydF91dWRlY29kZSgkc3RyaW5nKTsNCgkJJHN0cmluZyA9IHN0cnJldigkc3RyaW5nKTsNCgl9DQoNCglyZXR1cm4gJHN0cmluZzsNCn0NCg==";

eval(base64_decode($encryptionLevelBase));
eval(base64_decode($makeHashBase));
eval(base64_decode($deHashBase));

fwrite(STDOUT, 'Join room : ');

$room = trim(fgets(STDIN));

$hereNow = $pubnub->hereNow($room,false,true);


function connectAs() {

	global $hereNow;

	fwrite(STDOUT, 'Connect as : ');

	$username = trim(fgets(STDIN));
		
	foreach ($hereNow['uuids'] as $user) {
		
		if($user['state']['username'] === $username){

			fwrite(STDOUT, "Username taken\n");

			$username = connectAs();
		}
	}
	
	return $username;
};

$username = connectAs();

$pubnub->setState($room,['username' => $username]);

fwrite(STDOUT, "Connected to '{$room}' as '{$username}' \n");

$pid = pcntl_fork();

if($pid == -1) {

	exit(1);

} elseif($pid) {

	fwrite(STDOUT, "\r> ");

	while(true) {
		

		$message = trim(fgets(STDIN));

		$message = makeHash($message,$encryptionLevel);

		$pubnub->publish($room,[

			'body' => $message,
			'username' => $username
			
			]);
	}

	pcntl_wait($status);

} else {

	$pubnub->subscribe($room,function($payload) use ($username,$encryptionLevel){
		
		$timestamps = date('g-i:a');

		if($username != $payload['message']['username']) {
			
			fwrite(STDOUT, "\r> ");

		}

		$message = deHash($payload['message']['body'],$encryptionLevel);

		fwrite(STDOUT, "\r>[" .$timestamps ."] " . $payload['message']['username'] . " > " . $message . "\n");
		
		fwrite(STDOUT, "\r> ");
		
		return true;
	});	
}
