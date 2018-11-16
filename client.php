<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/16
 * Time: 11:01
 * Email: songyongzhan@qianbao.com
 */


$client=stream_socket_client('tcp://127.0.0.1:9999',$errno,$error);

if(!$client){
  throw new Exception($error.' Errno '.$errno);
}

$message='hello'.microtime();


fwrite($client,$message,strlen($message));


echo fread($client,1024);


fclose($client);


