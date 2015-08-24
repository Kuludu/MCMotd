<?php

header("Content-type: image/png");
if (!empty($_GET["ip"])) {
    $ip = htmlspecialchars($_GET["ip"]);
    if (empty($_GET["text"])) {
        $text = null;
    } else {
        $text = htmlspecialchars($_GET["text"]);
    }
    if (empty($_GET["tlx"])) {
        $text_loc_x = 540;
    } else {
        $text = htmlspecialchars($_GET["tlx"]);
    }
    if (empty($_GET["tly"])) {
        $text_loc_y = 240;
    } else {
        $text = htmlspecialchars($_GET["tly"]);
    }
    if (empty($_GET["port"])) {
        $port = 25565;
    } else {
        $port = htmlspecialchars($_GET["port"]);
    }
    img::paintImage($ip, $text, $port, $text_loc_x, $text_loc_y);
}

class img {

    function paintImage($ip, $text, $port, $text_loc_x, $text_loc_y) {
        $query = self::QueryMinecraft($ip, $port);
        if ($query === FALSE) {
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

        imagettftext($im, 30, 0, 900, 290, $green, $font, '游戏版本:' . $ver);
        imagettftext($im, 40, 0, 10, 50, $white, $font, $online . '/' . $max);
        imagettftext($im, 60, 10, 160, 230, $yellow, $font, $motd);
        imagettftext($im, 30, 0, 540, 240, $green, $font, $text);
        imagepng($im);
        imagedestroy($im);
    }

    function error() {
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

    function QueryMinecraft($IP, $Port) {
        $Socket = Socket_Create(AF_INET, SOCK_STREAM, SOL_TCP);

        Socket_Set_Option($Socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 2, 'usec' => 0));
        Socket_Set_Option($Socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 2, 'usec' => 0));

        if ($Socket === FALSE || @Socket_Connect($Socket, $IP, (int) $Port) === FALSE) {
            return FALSE;
        }

        Socket_Send($Socket, "\xFE\x01", 2, 0);
        $Len = Socket_Recv($Socket, $Data, 512, 0);
        Socket_Close($Socket);

        if ($Len < 4 || $Data[0] !== "\xFF") {
            return FALSE;
        }

        $Data = SubStr($Data, 3);
        $Data = iconv('UTF-16BE', 'UTF-8', $Data);

        if ($Data[1] === "\xA7" && $Data[2] === "\x31") {
            $Data = Explode("\x00", $Data);
            return Array(
                'HostName' => $Data[3],
                'Players' => IntVal($Data[4]),
                'MaxPlayers' => IntVal($Data[5]),
                'Protocol' => IntVal($Data[1]),
                'Version' => $Data[2],
            );
        }

        $Data = Explode("\xA7", $Data);
        return Array(
            'HostName' => SubStr($Data[0], 0, -1),
            'Players' => isset($Data[1]) ? IntVal($Data[1]) : 0,
            'MaxPlayers' => isset($Data[2]) ? IntVal($Data[2]) : 0,
            'Protocol' => 0,
            'Version' => '1.3',
        );
    }

}
