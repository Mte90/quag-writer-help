<?php
require ('http.php');
require ('oauth_client.php');

$client = new oauth_client_class;

// Edit those configurations!
$client -> client_id = 'a62fad7dea514a5384e69591e9c6ca';
$client -> client_secret = '7239f1a2385c68da8b214d469d0fce';
$client -> redirect_uri = admin_url().'options-general.php?page=qwh_main';
$client -> dialog_url = 'https://www.quag.com/oauth2/authorize/?client_id={CLIENT_ID}&response_type=code&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
$client -> access_token_url = 'https://www.quag.com/oauth2/token/';
$client -> scope = 'user_resource thread_resource';
$api_url = "http://www.quag.com/v1/a_threads_by_interest/";
$api_params = array('q' => 'seo');

// Dont't edit! Standard OAuth2 parameters.
$client -> request_token_url = '';
$client -> append_state_to_redirect_uri = '';
$client -> authorization_header = true;
$client -> url_parameters = false;
$client -> token_request_method = 'GET';
$client -> signature_method = 'HMAC-SHA1';
$client -> oauth_version = '2.0';

if (strlen($client -> client_id) == 0 || strlen($client -> client_secret) == 0)
    die('Please go to Quag API Apps page http://www.quag.com/account/clients , ' . 'create an application, set the client_id to App ID/API Key and client_secret with App Secret');

if (($success = $client -> Process())) {
    if (strlen($client -> access_token))
        $success = $client -> CallAPI($api_url . '?' . http_build_query($api_params), 'GET', array(), array('FailOnAccessError' => false, 'AsArray' => true), $results);
}
$success = $client -> Finalize($success);

if ($client -> exit)
    exit ;
if ($success) {
    if (is_array($results)) {
        echo '
    <div id="a_threads_by_interest_container">
        <h1>a_threads_by_interest: <b>' . $api_params['q'] . '</b> <a href="http://www.quag.com" target="_blank"><img alt="quag" src="http://www.quag.com/m/images/logo-quag.png"/></a></h1>
        <div class="overflow_container">';
        if (sizeof($results['threads']['internal'])) {
            foreach ($results['threads']['internal'] as $internalThread) {
                echo '
            <div class="thread">
                <div class="image">
                    <a href="' . $internalThread['author']['resource_uri'] . '" target="_blank">
                        <img src="' . $internalThread["author"]['avatar_url'] . '" alt="' . $internalThread["author"]["username"] . '\'s avatar" />
                        <span class="username">' . $internalThread["author"]["username"] . '</span>
                    </a>
                </div>
                <div class="data">
                    <div>';
                foreach ($internalThread['quags'] as $quag)
                    echo '    
                        <span class="quag">' . $quag['quag'] . '</span>';
                echo '    
                    </div>
                    <div>
                        <a href="' . $internalThread['resource_uri'] . '" target="_blank">' . $internalThread['title'] . '</a>
                    </div>                     
                    <div>
                        <span class="summary">' . $internalThread['summary'] . '</span>
                    </div>         
                </div>
                <div class="clearer">&nbsp;</div>
            </div>';
            }
        }
        echo '     
        </div>
    </div>
    
    <style type="text/css">
        body{
            background: none repeat scroll 0 0 #FBFBFB;
        }
        #a_threads_by_interest_container{     
            border: 1px solid #D8D8D8;
            border-radius: 15px 15px 15px 15px;
            width:600px;
            background: none repeat scroll 0 0 white;   
            font-family: Helvetica,sans-serif;
            color:#333;
        }
        #a_threads_by_interest_container .overflow_container{  
            overflow:auto;
            height:320px;
        }
        #a_threads_by_interest_container a {
            color: #2A92A1;
            font-weight: 500;
            text-decoration: none;
        }    
        #a_threads_by_interest_container a:hover {
            text-decoration: underline;
        }  
        #a_threads_by_interest_container div.clearer{
            clear:both;
            border-bottom:1px solid #dddddd;
            margin:auto;
            width:400px;
            height:5px;
            line-height:5px;
            margin-bottom:5px;
        }            
        #a_threads_by_interest_container .image {
           width:60px;
           text-align:center; 
           float:left;
           margin:5px;
        }    
        #a_threads_by_interest_container .image img {
           border-radius: 100px 100px 100px 100px;
           border:1px solid #dddddd;
           width:50px;
        }                 
        #a_threads_by_interest_container .data {
           float:left;
           margin:5px;
           width:500px;
        }                    
        #a_threads_by_interest_container h1{
           font-size:18px;
           font-weight: 500;
           text-decoration: none;
           border-radius: 15px 15px 0px 0px;
           background-color: #29909E;
           color:white;
           padding:10px;
           margin: 0 0 5px;
           border-bottom: 1px solid #D8D8D8;
        }  
        #a_threads_by_interest_container h1 img{
           float:right;
           height:20px;
        }       
        #a_threads_by_interest_container .username{
           font-size:10px;
           font-weight: 300;
           border-radius: 4px 4px 4px 4px;
           background-color: #29909E;
           color:white;
           padding:3px;
           text-align:center;
           display:block;
        }
        #a_threads_by_interest_container .quag {
            background: none repeat scroll 0 0 #EBF2F4;
            border-radius: 4px 4px 4px 4px;
            color: #2A92A1;
            display: inline-block;
            font-size: 12px;
            line-height: 15px;
            margin: 0;
            padding: 3px 6px;
            position: relative;
            text-align: left;            
        }
        #a_threads_by_interest_container .summary{
           font-size:13px;
           line-height: 18px;
        }
    </style>';
    }
} else {
    echo HtmlSpecialChars($client -> error);
}
?>