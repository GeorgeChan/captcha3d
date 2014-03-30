<?php
header ("Content-type: image/png");
require "captcha3d.php";
//  $captcha,$fontsize,$color,$width,$high,$thickness,$direction,$thicknessShow,$interval,$angle,$font
$png = new captcha3d(null,30,null,130,40,2,1,0,3,0,null);
imagepng($png->render());

?>