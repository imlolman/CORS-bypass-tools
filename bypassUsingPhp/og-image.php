<?php

if(count($_GET) == 0){
  $data = '<pre>
  Usage: /og-image.php => Get OpenGraph Image Data with Different Resolution and Quality.
        Get Params:
            url (required)        => URL to extract OpenGraph API Image     | Example => url=https://github.com/imlolman/Font-Awesome-Offline-Quick-Icon-Search
            resolution (optional) => Resolution in Which you want Image     | Example => resolution=500x300
            quality (optional)    => Ranges from 1 to 100  (default=100)    | Example => quality=10
            crop (optional)       => true/false  (default=false)            | Example => crop=false


        Example => /og-image.php?url=https://github.com/imlolman/Font-Awesome-Offline-Quick-Icon-Search&resolution=500x300&quality=10&crop=false



  </pre>';
  echo $data;

  die();
}

$quality = 100;
$resolution = null;
$crop = false;

if(isset($_GET['quality'])){
  $quality = $_GET['quality'];
}
if(isset($_GET['resolution'])){
  $resolution = [(int)(explode('x',$_GET['resolution'])[0]),(int)(explode('x',$_GET['resolution'])[1])];
}
if(isset($_GET['crop'])){
  if($_GET['crop'] == '1'){
    $crop = true;
  }
}

if(isset($_GET['url'])){
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
    }
    $re = '/<meta property="og:image" content="(.*?)" \/>/m';
    $str = file_get_contents($_GET['url']);
    preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE);
    $img_link = $matches[1][0];

    $ran = rand(5000,6000);
    $img = 'temp/downloaded'.$ran.'.png';  
    file_put_contents($img, file_get_contents($img_link));
    
    if($resolution != null){
      $imgData = resize_image($img, $resolution[0], $resolution[1], $crop);
    }else{
      list($width, $height) = getimagesize($img);
      $imgData = resize_image($img, $width, $height, $crop);
    }

    $destination = 'temp/format'.$ran.'.png';
    imagepng($imgData,$destination,10-($quality/10));
    unlink($img);
    $img = $destination;
   
    
    $image = file_get_contents($img);
    unlink($img);
    
    header('Content-type: image/png;');
    header("Content-Length: " . strlen($image));
    echo $image;
    die();
  }

function resize_image($file, $w, $h, $crop=false) {
  list($width, $height) = getimagesize($file);
  $r = $width / $height;
  if ($crop) {
      if ($width > $height) {
          $width = ceil($width-($width*abs($r-$w/$h)));
      } else {
          $height = ceil($height-($height*abs($r-$w/$h)));
      }
      $newwidth = $w;
      $newheight = $h;
  } else {
      if ($w/$h > $r) {
          $newwidth = $h*$r;
          $newheight = $h;
      } else {
          $newheight = $w/$r;
          $newwidth = $w;
      }
  }
  $exploding = explode(".",$file);
  $ext = end($exploding);
  switch($ext){
      case "png":
          $src = imagecreatefrompng($file);
      break;
      case "jpeg":
      case "jpg":
          $src = imagecreatefromjpeg($file);
      break;
      case "gif":
          $src = imagecreatefromgif($file);
      break;
      default:
          $src = imagecreatefromjpeg($file);
      break;
  }
  $dst = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  return $dst;
}
?>