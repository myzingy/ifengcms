<?php

$code = $_GET["code"];
$useropenid = getUserOpenid($code);
//echo $useropenid;
$BACK_URL = $_GET["state"];
$url_to = $BACK_URL.'?openid='.$useropenid;
header('location:'.$url_to);

function getUserOpenid($code){
    //$appid = "wxc23b7df9375cc300";
    //$appsecret = "d21884f6ad9f6408c0b69d6d0d970f76";
    $appid = "wxeac5ee619fe202cb";
    $appsecret = "57982c3f587b987211eb0dbb62e4739c";
    $access_token = "";
    //Get access_token
    $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
    $access_token_json = https_request($access_token_url);
    $access_token_array = json_decode($access_token_json,true);
    $access_token = $access_token_array['access_token'];
    $openid = $access_token_array['openid'];
    return $openid;
}

function https_request($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl,  CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)){
        return 'ERROR'.curl_error($curl);
    }
    curl_close($curl);
    return $data;
}

?>
