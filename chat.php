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
