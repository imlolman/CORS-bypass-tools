<?php

if(isset($_GET['url'])){

    if (isset($_SERVER['HTTP_ORIGIN'])) {

        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");

        header('Access-Control-Allow-Credentials: true');

    }

    echo file_get_contents($_GET['url']);

    die();

}else{
  echo '<pre>'.file_get_contents('usage.txt').'</pre>';
}

?>