<?php
function CroppedThumbnail($imgSrc,$thumbnail_width,$thumbnail_height) { //$imgSrc is a FILE - Returns an image resource.
//getting the image dimensions  
list($width_orig, $height_orig) = getimagesize($imgSrc);   
$myImage = imagecreatefromjpeg($imgSrc);
$ratio_orig = $width_orig/$height_orig;

if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
   $new_height = $thumbnail_width/$ratio_orig;
   $new_width = $thumbnail_width;
} else {
   $new_width = $thumbnail_height*$ratio_orig;
   $new_height = $thumbnail_height;
}

$x_mid = $new_width/2;  //horizontal middle
$y_mid = $new_height/2; //vertical middle

$process = imagecreatetruecolor(round($new_width), round($new_height)); 

imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height); 
imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

imagedestroy($process);
imagedestroy($myImage);
return $thumb;
}
//Create the thumbnail
//$newimagename = date("YmdHis").".jpg";
$newimagename = $_GET['i'];
// reconstruction de l'image
$domain='https://4714711e034622730849-4d36b3a7f81d82c66bad0a46c55c8159.ssl.cf3.rackcdn.com/products.art/';
$newimagename=str_replace(".ak.", "/", $newimagename);
$newimagename=$domain.$newimagename;
// fin reconstruction de l'image
$newThumb = CroppedThumbnail($newimagename,$_GET['w'],$_GET['h']);
// And display the image...
header('Content-type: image/jpeg');
imagejpeg($newThumb);
?>