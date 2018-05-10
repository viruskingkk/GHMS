<?php
if(!session_id()) {
    session_start();
}

if(!isset($_COOKIE['login']) && isset($_SESSION['Center_Username'])){//如果已登入COOKIE不存在，但SESSION存在
	header('Location: index.php?logout');//直接消除SESSION並登出
}

global $center;

date_default_timezone_set("Asia/Taipei"); //時區設定
$center['site_name'] = "%s"; //網站名稱
$center['register'] = "%d"; //是否開啟註冊，0為關閉，1為開啟

$center['file']['limitedext'] = array("%s");//文件夾_允許上傳的檔案格式
$center['file']['max_files'] = "%d"; //文件夾_最多檔案數量
$center['file']['max_size'] = "%d"; //文件夾_檔案大小限制 單位 KB
$center['chat']['public'] = "%d"; //聊天室_發言間隔 單位 秒
$center['avatar']['max_size'] = "%d";//頭像_檔案大小限制 單位 KB

require_once('function.php');