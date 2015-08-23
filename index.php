<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<form action="index.php">
    <input type="text" value="请输入服务器域名/IP" name="ip"/>
</form>
<?php
if(empty($_GET["ip"])){
    
}else{
    $ip = htmlspecialchars($_GET["ip"]);
    echo '<img src="image.php?ip='.$ip.'"></img>';
}

