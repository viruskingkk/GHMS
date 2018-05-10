<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
	header("Location: index.php?login");
	exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Knowledge Center');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
?>
<div class="main">
<h2>Knowledge Center</h2>
</div>

<li class="dropdown">
1.<a href="" data-target="#" data-toggle="dropdown">StoryTeller preview時，為何Table無法在Story Frame中出現? ▼</a>
<ul class="dropdown-menu">
<li>請檢查Table是否設定為paragraph，而不是inline</li>
</ul></li><br>

<li class="dropdown">
2.<a href="" data-target="#" data-toggle="dropdown">StoryTeller如何在Repeater上增加條件過濾條件? ▼</a>
<ul class="dropdown-menu">
<li>(1)可在Data link裡增加一組[]，裡面放置過濾條件</li>
<li>(2)在Filter Column中，放置過濾條件</li>
</ul></li><br>

<li class="dropdown">
3.<a href="" data-target="#" data-toggle="dropdown">StoryTeller為何無法進行preview，錯誤訊息為Error in substitution (Name: '', ID: '3221225479')?▼</a>
<ul class="dropdown-menu">
<li>(1)檢查是否有變數定義在Job中，而在StoryTeller中使用於Substitution。</li>
<li>(2)是否Xpath取得了多個node，並直接用於Substitution中，未搭配repeater進行控制</li>            
</ul></li><br>

<li class="dropdown">
4.<a href="" data-target="#" data-toggle="dropdown">如何進行版面上物件微調整? ▼</a>
<ul class="dropdown-menu">
<li>(1)在點選欲移動物件後，按住Alt鍵後，即可透過方向鍵或滑鼠進行微調整。</li>
<li>(2)在StoryTeller工具列上，取消View / Snap to Grid</li>
</ul></li><br>

<li class="dropdown">
5.<a href="" data-target="#" data-toggle="dropdown">如何deploy一個export檔案在Control Center中運行? ▼</a>
<ul class="dropdown-menu">
<li>(1)建立一個Application Domain。</li>
<li>(2)在Application Domain下建立StreamServer application</li>
<li>(3)export檔案，部署在上面運行。</li>
</ul></li><br>

<li class="dropdown">
6.<a href="" data-target="#" data-toggle="dropdown">如何使用XMLIN event讀入/定義我的XML檔案? ▼</a>
<ul class="dropdown-menu">
<li>(1)開啟XMLIN後，File / Open Sample，從Resource set中，選定要定義的XML檔案開啟</li>
<li>(2)使用Tools / Pattern Tool，定義Pattern</li>
<li>(3)使用Tools / Extract Message，將其他未定義的XML tag讓其自動定義。</li>
<li>(4)File / Save</li>
</ul></li><br>   

<li class="dropdown">
7.<a href="" data-target="#" data-toggle="dropdown">如何建立一個project在Design Center中? ▼</a>
<ul class="dropdown-menu">
<li>(1)開啟Design Center，選File / New / Project</li>
<li>(2)Project Name，輸入該專案名稱</li>
<li>(3)Default code page，若沒有特殊需求，請維持預設狀態</li>
<li>(4)Default Resource Set，可使用預設名稱，或自行修改</li>
<li>(5)Project folder / directory，指定存放位置</li>
<li>(6)OK</li>
</ul></li><br>

<li class="dropdown">
8.<a href="" data-target="#" data-toggle="dropdown">如何在Script中宣告全域變數? ▼</a>
<ul class="dropdown-menu">
<li>(1)變數宣告使用$符號</li>
<li>(2)變數名稱第一個字元需為字母或底線</li>
</ul></li><br> 

<li class="dropdown">
9.<a href="" data-target="#" data-toggle="dropdown">如何取得已定義在Event的field?? ▼</a>
<ul class="dropdown-menu">
<li>請使用&符號，將欲使用的message field名稱相連即可。(ex: &ContactCode)</li>
</ul></li><br> 

<li class="dropdown">
10.<a href="" data-target="#" data-toggle="dropdown">如何讓輸出的檔名可動態變更? ▼</a>
<ul class="dropdown-menu">
<li>(1)開啟Runtime物件(Physical Layer)</li>
<li>(2)選擇一個Process物件，並右鍵點選Connector Settings</li>
<li>(3)在File sheet中，File屬性可以設定變數</li>
</ul></li><br>       

<li class="dropdown">
11.<a href="" data-target="#" data-toggle="dropdown">如何找到哪些物件在StoryTeller中有使用Arial字型?▼</a>
<ul class="dropdown-menu">
<li>(1)開啟StoryTeller Process，選Edit / Find</li>
<li>(2)在Find欄位上，選擇Font，並在右方輸入Arial後，點選Find按鈕</li>
</ul></li><br>

<li class="dropdown">
12.<a href="" data-target="#" data-toggle="dropdown">在StoryTeller編輯版面上，我如何Hightlight所設計的repeater位置?▼</a>
<ul class="dropdown-menu">
<li>(1)選View / Toolbars / View</li>
<li>(2)點選Hightlight，右側下箭頭，選擇repeaters</li>
</ul></li><br>

<li class="dropdown">
13.<a href="" data-target="#" data-toggle="dropdown">如何在StoryTeller偵測物件的Y位置?▼</a>
<ul class="dropdown-menu">
<li>請於該物件的After Script使用StGetProperty(“Height”);</li>
</ul></li><br>

<li class="dropdown">
14.<a href="" data-target="#" data-toggle="dropdown">為何我的log紀錄會重複跑兩次??▼</a>
<ul class="dropdown-menu">
<li>請檢查是否有使用多管道輸出設定在Connector Selector上。</li>
</ul></li><br>

<li class="dropdown">
15.<a href="" data-target="#" data-toggle="dropdown">如何建立TOC在document上?▼</a>
<ul class="dropdown-menu">
<li>(1)在Runtime中，Documnet End裡設定Edit Process Sort Definition…</li>
<li> (2)使用Two stages的方式或Post-processor產生</li>
</ul></li><br>         

<li class="dropdown">
16.<a href="" data-target="#" data-toggle="dropdown">如何產生TotalPages在每個頁面上?▼</a>
<ul class="dropdown-menu">
<li>(使用Two stages的方式或Post-processor產生</li>
</ul></li><br>

<li class="dropdown">
17.<a href="" data-target="#" data-toggle="dropdown">在我已經import Global project的resource set了，但為何我無法使用?▼</a>
<ul class="dropdown-menu">
<li>請開啟任一欲使用Global resource的Message物件，在中間的圖形上，右鍵點選Add Resource Set，將Global resource set加入</li>
</ul></li><br>
<?php
	$view->render();
?>