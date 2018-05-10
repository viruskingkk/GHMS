<?php 
/**************************************************** 
特點：無需mysql支持；速度快；無需設置路徑，放在哪級目錄下，就搜索該目錄和子目 
錄；可以搜索一切文本類型的檔；顯示檔相關內容；關鍵字自動高亮顯示。 
修改內容：增加了自動分頁和風格設置檔。 
搜索框代碼（請單獨存為html檔，如果放在search.php相同目錄下，無需修改）： 
<FORM method="post" action="search.php"><input type="text" name="key" size=40 value=""> 
<input type="submit" value="檢索"></FORM> 
****************************************************/ 
require ("search.inc"); 
function get_msg($path) { 
global $key, $i; 
$handle = opendir($path); 
while ($filename = readdir($handle)) { 
//echo $path."/".$filename."<br>"; 
$newpath = $path."/".$filename; 
$check_type = preg_match("/\.html?$/", $filename); 
if (is_file($newpath) && $check_type) { 
$fp = fopen($newpath, "r"); 
$msg = fread($fp, filesize($newpath)); 
fclose($fp); 
match_show($key, $msg, $newpath, $filename); 
} 
if (is_dir($path."/".$filename) && ($filename != ".") && ($filename != "..")) { 
//echo "<BR><BR><BR>".$newpath."<BR><BR><BR>"; 
get_msg($path."/".$filename); 
} 
} 
closedir($handle); 
return $i; 
} 
function match_show($key, $msg, $newpath, $filename) { 
global $ar, $i; 
$key = chop($key); 
if($key) { 
$msg = preg_replace("/<style>.+<\/style>/is", "", $msg); 
$msg = str_replace(" ", "", $msg); 
$msg = preg_replace("/<[^>]+>/", "", $msg); 
$value = preg_match("/.*$key.*/i", $msg, $res); 
if($value) { 
$res[0] = preg_replace("/$key/i", "<FONT COLOR=\"red\">$key</FONT>", $res[0]); 
$i++; 
$link = $newpath; 
$ar[] = "$i.◆<a href=\"$link\">$filename</a><BR><BR>" . $res[0]."<BR><br>"; 
} 
}else { 
echo "請輸入關鍵字"; 
exit; 
} 
} 
$i = get_msg("."); 
if (empty($page)) $page=1; 
$maxresult=($page*20); 
$resultcount = count($ar); 
if ($resultcount%20==0) $maxpageno=$resultcount/20; 
else $maxpageno=floor($resultcount/20)+1; 
if ($page>$maxpageno) { $page=$maxpageno; $pagemax=$resultcount-1; $pagemin=max(0,$result_count-20);} 
elseif ($page==1) {$pagemin=0; $pagemax=min($result_count-1,20-1); } 
else { $pagemin=min($resultcount-1,20*($page-1)); $pagemax=min($resultcount-1,$pagemin+20-1); } 
$maxresult=min($maxresult,$resultcount); 
echo "<p align=\"center\">"; 
echo "檢索結果"; 
echo "</p><hr>"; 
for ($i=max(0,$maxresult-20); $i<$maxresult; $i++) { 
print $ar[$i]; 
} 
echo "<hr><p align=\"center\">"; 
echo " 已經搜尋到了 $resultcount 條資訊"; 
$nextpage=$page+1; 
$previouspage=$page-1; 
echo " --- [ <a href=&acute;search.php?key=$key&page=$nextpage&acute; target=&acute;_self&acute;>搜尋下 20 個結果</a> ]"; 
echo " [ <a href=&acute;search.php?key=$key&page=$previouspage&acute; target=&acute;_self&acute;>返回上 20 個結果</a> ]"; 
exit; 