<?php

// Error Reporting Turned ON
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Do a inclusion of Mailchimp Module
require_once  'Mailchimp.php';

// Initiate Class with API key, Server Id and List Id 
$mailchimp    = new \Anlprz\MailChimp\Mailchimp ( 'd82db1asdfafe560fa6d-us99', 'us99', 'asdfewrw31' );

// Add Merged Fields. "NAME" is default and is Required one to set up.
$mailchimp->setMergeFields("NAME", "Megan Fox");
$mailchimp->setMergeFields("FNAME", "Megan")->setMergeFields( array( "LNAME" => "Fox" ) );

// Run Final Subscription to customer with Email
$result       = $mailchimp->subscribe( 'myNameTestmantwo@yourcompany.digital' );

// For subscribing into certain Groups / interest
$mailchimp->setGroupId( 'c8ef0de3d3' );

$result       = $mailchimp->subscribeGroup( 'myNameTestmantwo@yourcompany.digital' );


?>
