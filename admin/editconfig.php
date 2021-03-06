<?php
set_include_path('../include/');
$includepath = true;

require_once('../Connections/SQL.php');
require_once('../config.php');
require_once('view.php');

if(!isset($_SESSION['Center_Username']) or $_SESSION['Center_UserGroup'] != 'admin'){
    header("Location: ../index.php");
	exit;
}

if(isset($_POST['site_name'])){
	if(isset($_POST['register'])){
		$register=1;
	}else{
		$register=0;
	}
	$config='../config.php';
	$config_sample='../config-sample.php';
	$put_config = vsprintf(file_get_contents($config_sample),array(
		$_POST['site_name'],
		$register,
		implode('","',explode(',',$_POST['limitedext'])),
		abs($_POST['max_files']),
		abs($_POST['max_size']),
		abs($_POST['public']),
		abs($_POST['avatar_max_size'])
	));
	file_put_contents($config,$put_config);
	$_GET['ok']=true;
	require('../config.php');
}

$view = new View('../view/new_theme.html','../include/admin_nav.php',$center['site_name'],'系統設定',true);
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("../include/js/channel.js");
$view->addScript("../include/js/jquery.validate.js");
?>
<script type="text/javascript">
$(function(){
	$("#form1").validate({
		rules:{
			site_name:{required:true},
			html_tag:{required:true},
			public:{required:true,min:0},
			limitedext:{required:true},
			max_files:{required:true,min:0},
			max_size:{required:true,min:0}
		},
	});
});
</script>
<div class="main">
<?php if(isset($_GET['ok'])){?>
	<div class="alert alert-success">編輯成功！</div>
<?php } ?>
<h2 class="subtitle">系統設定</h2>
<form id="form1" name="form1" class="form-horizontal" method="post" action="editconfig.php">
	<fieldset>
		<legend>主要</legend>
		<div class="control-group">
			<label class="control-label" for="site_name">網站名稱：</label>
			<div class="controls">
				<input id="site_name" name="site_name" class="input-xlarge" type="text" value="<?php echo $center['site_name']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="register">開啟註冊：</label>
			<div class="controls">
				<input id="register" name="register" type="checkbox" value="1"<?php if($center['register']){echo ' checked="checked"';} ?>> <label class="checkbox inline" for="register">開啟</label>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>聊天室</legend>
		<div class="control-group">
			<label class="control-label" for="public">發言間隔：</label>
			<div class="controls">
				<input id="public" name="public" class="input-mini" type="text" value="<?php echo $center['chat']['public']; ?>"> 秒
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>文件夾</legend>
		<div class="control-group">
			<label class="control-label" for="limitedext">允許上傳的檔案格式：</label>
			<div class="controls">
				<input id="limitedext" name="limitedext" class="input-xxlarge" type="text" value="<?php echo implode(",", $center['file']['limitedext']); ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="max_files">最多檔案數量：</label>
			<div class="controls">
				<input id="max_files" name="max_files" class="input-mini" type="text" value="<?php echo $center['file']['max_files']; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="max_size">檔案大小限制：</label>
			<div class="controls">
				<input id="max_size" name="max_size" class="input-mini" type="text" value="<?php echo $center['file']['max_size']; ?>"> KB
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>頭像</legend>
		<div class="control-group">
			<label class="control-label" for="avatar_max_size">檔案大小限制：</label>
			<div class="controls">
				<input id="avatar_max_size" name="avatar_max_size" class="input-mini" type="text" value="<?php echo $center['avatar']['max_size']; ?>"> KB
			</div>
		</div>
	</fieldset>
	<div class="control-group">
		<div class="controls">
			<input name="button" type="submit" id="button" class="btn btn-info btn-large" value="開始搜尋" />
		</div>
	</div>
</form>
</div>
<?php
$view->render();
?>