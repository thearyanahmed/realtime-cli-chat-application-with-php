#!/usr/bin/env php

<?php 


require 'vendor/autoload.php';

use Pubnub\Pubnub;


$pubnub = new Pubnub(
    "pub-c-a658d87b-d656-445e-94d3-5e5edeaa3bf9",  
    "sub-c-9b4155ec-1af5-11e8-acdc-3a42756f9040",  
    "sec-c-NTQ3MmFjZTQtYmVmNy00MGI4LWFkMGMtZjdlZWYwM2NkNTJk",   
    false    ## SSL_ON?
);

$connectAs = function() {

	fwrite(STDOUT, 'Connect as : ');
	
	return trim(fgets(STDIN));
};


fwrite(STDOUT, 'Join room : ');

$room = trim(fgets(STDIN));

$username = $connectAs();

fwrite(STDOUT, "\n Connected to '{$room}' as '{$username}' \n");

$pid = pcntl_fork();

if ($pid == -1) {

	exit(1);
} else if ($pid) {


	pcntl_wait($status);
} else {


	$pubnub->subscribe($room , function($payload) {
	    var_dump($payload);  
	    return true;         
	});
}