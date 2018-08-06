<?php
namespace BossBaby;

class Twitter
{
    public static function get_user_feeds($username = '', $count = 1)
    {
        if (!$username or !$count) return [];

        // require_once LIB_DIR . '/twitter/vendor/autoload.php';
        // require_once LIB_DIR . '/twitter/src/twitter.class.php';

        global $environment;
        
        $consumerKey = $environment->twitter->apps->{1}->apiKey;
        $consumerSecret = $environment->twitter->apps->{1}->apiSecret;
        $accessToken = $environment->twitter->apps->{1}->accessToken;
        $accessTokenSecret = $environment->twitter->apps->{1}->accessTokenSecret;
        $environment->twitter_instance = new \Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        
        $arr = $environment->twitter_instance->load(\Twitter::ME, $count, array('screen_name' => trim($username), 'exclude_replies' => true, 'include_rts' => true));
        return $arr;
    }
}