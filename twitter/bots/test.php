<?php
defined('IS_VALID') or define('IS_VALID', 1);
require_once __DIR__ . '/../../main.php';

// Load composer
require_once LIB_DIR . '/twitter/vendor/autoload.php';

global $environment;

$consumerKey = $environment->twitter->apps->{1}->apiKey;
$consumerSecret = $environment->twitter->apps->{1}->apiSecret;
$accessToken = $environment->twitter->apps->{1}->accessToken;
$accessTokenSecret = $environment->twitter->apps->{1}->accessTokenSecret;

// ENTER HERE YOUR CREDENTIALS (see readme.txt)
$twitter = new \Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
dump('$twitter'); dump($twitter);

$statuses = $twitter->load(\Twitter::ME_AND_FRIENDS);
dump('$statuses'); dump($statuses);

$statuses = $twitter->load(\Twitter::ME, 20, array('screen_name' => 'OntologyNetwork'));
dump('$statuses'); dump($statuses);
