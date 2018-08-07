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
// dump('$twitter'); dump($twitter);

// $statuses = $twitter->load(\Twitter::ME_AND_FRIENDS);
// dump('$statuses'); dump($statuses);

// $statuses = $twitter->load(\Twitter::ME, 1, array('screen_name' => 'bitshares'));
// dump('$statuses'); dump($statuses);

$latest_tweet = \BossBaby\Telegram::get_user_feeds('bitshares', 1);
dump('$latest_tweet'); dump($latest_tweet);
die;

// create curl resource 
// $ch = curl_init(); 
// curl_setopt($ch, CURLOPT_URL, "https://twitter.com/bitshares"); 
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
// $output = curl_exec($ch); 
// curl_close($ch);
// // echo $output;

// $output = trim($output);
// file_put_contents(__DIR__ . '/twitter.html', $output);

require_once LIB_DIR . '/simplehtmldom/simple_html_dom.php';
// js-stream-tweet
// $dom = str_get_html(trim($output));
// dump($dom);

// js-tweet-text-container
// $dom = file_get_html(__DIR__ . '/twitter.html');
$html = new simple_html_dom();
$html->load(file_get_contents(__DIR__ . '/twitter.html'));
// $result = $html->save();

foreach($html->find('div[class=js-tweet-text-container]', null, true) as $div) {
    // $div->href = 'http://www.example.com' . $link->href;
    dump($div);
    die;
}

// foreach($html->find('p[class=tweet-text]') as $p) {
//     dump($p);
//     die;
// }

# Create a DOM parser object
// $dom = new DOMDocument();
// @$dom->loadHTML(file_get_contents(__DIR__ . '/twitter.html'));
// foreach($dom->getElementsByTagName('p') as $link) {
//         # Show the <a href>
//         echo $link->getAttribute('href');
//         echo "<br />";
// }

// $doc = new DOMDocument();
// libxml_use_internal_errors(true);
// $doc->loadHTML(file_get_contents(__DIR__ . '/twitter.html')); // loads your HTML
// $xpath = new DOMXPath($doc);
// // returns a list of all links with rel=nofollow
// $nlist = $xpath->query("//div[@class='js-tweet-text-container']");
// dump($nlist);
