<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/11/16
 * Time: 11:01
 * Email: songyongzhan@qianbao.com
 */


$master = array();
$socket = stream_socket_server("tcp://127.0.0.1:9999", $errno, $errstr);
if (!$socket) {
  echo "$errstr ($errno)<br />\n";
} else {
  $master[] = $socket;


  while (TRUE) {


    $read = $master;


    $mod_fd = stream_select($read, $_w, $_e, 30000);

    if ($mod_fd === FALSE) {
      break;
    }
    //printf("\n=====================\n%s\n=====================\n", $mod_fd);
    for ($i = 0; $i < $mod_fd; $i++) {


      if (isset($read[$i]) && $read[$i] === $socket) {

        $conn = stream_socket_accept($socket);
        fwrite($conn, "Hello! The time is " . date("n/j/Y g:i a") . "\n");
        $master[] = $conn;

      } else if(isset($read[$i])) {

        $sock_data = fread($read[$i], 1024);
        var_dump($sock_data);


        if (strlen($sock_data) === 0) { // connection closed
          $key_to_del = array_search($read[$i], $master, TRUE);
          fclose($read[$i]);

          unset($master[$key_to_del]);

        } else if ($sock_data === FALSE) {

          echo "Something bad happened";
          $key_to_del = array_search($read[$i], $master, TRUE);
          unset($master[$key_to_del]);

        } else {

          echo "The client has sent :";
          var_dump($sock_data);
          fwrite($read[$i], "You have sent :[" . $sock_data . "]\n");
          fclose($read[$i]);
          unset($master[array_search($read[$i], $master)]);

        }



      }


    }


  }
}
