<html>
    <head>
        <title>测试</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <?php
        //腾讯
        $openid = '2395821C32127FC094C9EB52EF417205';
        $token = '434C7B9FAA53CC3C4E5DF828E301F56C';
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "https://graph.qq.com/user/get_user_info?access_token={$token}&oauth_consumer_key=1102364598&openid={$openid}");
        curl_setopt($ch, CURLOPT_URL, "https://graph.qq.com/oauth2.0/me?access_token={$token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result_str = curl_exec($ch);
        curl_close($ch);
        preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_arr);
        $rst = json_decode($result_arr[1], true);
        echo '<br>';
        echo $result_str;
        echo '<br>';
        echo $rst['client_id'];
        echo '<br>';
        echo $rst['openid'];
        echo '<br>';
        echo '434C7B9FAA53CC3C4E5DF828E301F56C';
        ?>
        <?php
        $token = "2.00muCWaCWEdE9Ce5425f629fQ5YSeB";
        $postData = array('access_token' => $token);
//        $postData = implode('&',$postData); 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.weibo.com/oauth2/get_token_info");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $return = curl_exec($ch);
        curl_close($ch);
        echo '<br>';
        print_r($return);
        ?>
    </body>
</html>