<?php
/*
AutoTags 1.0
Tag suggestion Plugin for Wordpress 2.5 (or newer)
Copyright (C) 2008 VividVisions.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require (dirname(__FILE__).'/../../..//wp-config.php');

Function autoTags_post($content) 
{
    $request_url = "http://asia.search.yahooapis.com/cas/v1/ke";
    $appid = "AvxtDlHV34F4YdHvpkccsOReXanb_wvJA7qa2S.KznL5YoPWnppRSdMYrJI3KH8-";
    $ch = curl_init();
    $curl_opts = array(
    CURLOPT_URL  =>  $request_url,
    CURLOPT_POST  =>  true,
    CURLOPT_POSTFIELDS =>  array('appid'=>$appid,'format'=>'json', 'content'=>$content),
    CURLOPT_RETURNTRANSFER  =>  true
    );
    curl_setopt_array($ch, $curl_opts);
    $ret = curl_exec($ch);
    curl_close($ch);
    $list = json_decode($ret);
    foreach($list as $item)
    {
       $out[] = $item->token;
    }
    $response = array('memes'=>array('dimensions'=>array('topic'=>$out)));
	$response = json_encode($response);
	return $response;
}

header("Content-Type: text/javascript; charset=" . get_option('blog_charset'));
$response = autoTags_post($_POST['text']);
echo $response;
?>
