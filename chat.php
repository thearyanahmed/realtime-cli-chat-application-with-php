#!/usr/bin/env php

<?php 


require 'vendor/autoload.php';

use Pubnub\Pubnub;


$pubnub = new Pubnub(
    "pub-c-a658d87b-d656-445e-94d3-5e5edeaa3bf9",  
    "sub-c-9b4155ec-1af5-11e8-acdc-3a42756f9040",  
    "sec-c-NTQ3MmFjZTQtYmVmNy00MGI4LWFkMGMtZjdlZWYwM2NkNTJk",   
    false   
);



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

	fwrite(STDOUT, ' > ');

	while(true) {
		

		$message = trim(fgets(STDIN));

		$pubnub->publish($room,[

			'body' => $message,
			'username' => $username
			
			]);
	}

	pcntl_wait($status);

} else {

	$pubnub->subscribe($room,function($payload) use ($username){
		
		$timestamps = date('d-m-y H:i:s');

		if($username != $payload['message']['username']) {
			
			fwrite(STDOUT, "\r> ");

		}

		fwrite(STDOUT, "[ " .$timestamps ." ] " . $payload['message']['username'] . " > " . $payload['message']['body'] . "\n");
		
		fwrite(STDOUT, "\r> ");
		
		return true;
	});	
}
