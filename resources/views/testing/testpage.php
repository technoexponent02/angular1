<?php
ini_set('display_errors', 1);
require_once(url('twitter-api-php-master/TwitterAPIExchange.php'));

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "3570429192-9YB4eSDvI1oTIZcpaCdpPWLiHeNOGptqggzg20n",
    'oauth_access_token_secret' => "qHeKfWIJg87O4rnYGWnpQlLl19g7mPfl6BBsbfMCpq51M",
    'consumer_key' => "nScLNNLmtUHvCCC1Neftiq3k8",
    'consumer_secret' => "lLNF3wrfvmi1UM3MVO8nRbXdYY4nKIKwn8MGZsLkiIWAIedxih"
);

/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
$url = 'https://api.twitter.com/1.1/blocks/create.json';
$requestMethod = 'POST';

/** POST fields required by the URL above. See relevant docs as above **/
$postfields = array(
    'screen_name' => 'usernameToBlock', 
    'skip_status' => '1'
);

/** Perform a POST request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
/*
$url = 'https://api.twitter.com/1.1/followers/ids.json';
$getfield = '?screen_name=J7mbo';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(); */

             ?>
