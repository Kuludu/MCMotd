<?php
header("Content-type: image/png");
if (!empty($_GET["ip"])) {
    $ip = htmlspecialchars($_GET["ip"]);
    include 'function.php';
    if(empty($_GET["text"])){
        $text = null;
    }else{
        $text = htmlspecialchars($_GET["text"]);
    }
    if(empty($_GET["tlx"])){
        $text_loc_x = 540;
    }else{
        $text = htmlspecialchars($_GET["tlx"]);
    }
    if(empty($_GET["tly"])){
        $text_loc_y = 240;
    }else{
        $text = htmlspecialchars($_GET["tly"]);
    }
    if(empty($_GET["port"])){
        $port = 25565;
    }else{
        $port = htmlspecialchars($_GET["port"]);
    }
    img::paintImage($ip, $text,$port,$text_loc_x,$text_loc_y);
}

class img{
    function paintImage($ip, $text, $port,$text_loc_x, $text_loc_y) {
        $query = functions::QueryMinecraft($ip,$port);
        if($query === FALSE){
            self::error();
            return FALSE;
        }
        $motd = $query["HostName"];
        $online = '在线人数:' . $query["Players"];
        $max = $query["MaxPlayers"];
        $ver = $query["Version"];

        $im = imagecreatefrompng('background.png');
        $white = imagecolorallocate($im, 255, 255, 255);
        $yellow = imagecolorallocate($im, 255, 255, 0);
        $green = imagecolorallocate($im, 4, 255, 32);
        $path = dirname(__FILE__);
        $font = $path . '/font.ttf';

        imagettftext($im, 30, 0, 900, 290, $green, $font, '游戏版本:'.$ver);
        imagettftext($im, 40, 0, 10, 50, $white, $font, $online . '/' . $max);
        imagettftext($im, 60, 10, 160, 230, $yellow, $font, $motd);
        imagettftext($im, 30, 0, 540, 240, $green, $font, $text);
        imagepng($im);
        imagedestroy($im);
    }
    
    function error(){
        $err_msg = "抱歉，无法获取数据！";
        $moe_msg = "(╯' - ')╯┻━┻ <-服务器";
        $im = imagecreatefrompng('background.png');
        $red = imagecolorallocate($im, 255, 0, 0);
        $green = imagecolorallocate($im, 4, 255, 32);
        $path = dirname(__FILE__);
        $font = $path . '/font.ttf';

        imagettftext($im, 100, 0, 0, 170, $red, $font, $err_msg);
        imagettftext($im, 40, 0, 300, 260, $green, $font, $moe_msg);
        imagepng($im);
        imagedestroy($im);
    }
}