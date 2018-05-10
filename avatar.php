<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
	header("Location: index.php?login");
	exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Modify avatar');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");

$user = $SQL->query("SELECT avatar FROM member WHERE name = '%s'", array($_SESSION['Center_Username']));
$row_user = $user->fetch_assoc();
$totalRows_user = $user->num_rows;
$upload_error = null;

if((@$_GET['step'] == 2) && (!isset($_GET['no'])) && isset($_FILES["fileField"])) {
	try {
		//檢查頭像資料夾是否存在
		if(!is_dir("images/avatar")) {
			//不存在的話就創建頭像資料夾
			if(!mkdir("images/avatar")){
				die("Avatar folder does not exist and creation failed");
			}
		}
		if($_FILES["fileField"]["name"] != "" && is_uploaded_file($_FILES["fileField"]["tmp_name"])){
			if((!isset($_FILES["fileField"]["error"]) > 0)){
				throw new Exception("File upload failed");
			}
			
			if($center['avatar']['max_size'] <= $_FILES["fileField"]["size"] / 1000){
				throw new Exception("File size exceeds limit");
			}
			
			$limitedext = array("jpeg","jpg","gif","png");//允許的副檔名
			$extend = pathinfo($_FILES['fileField']['name'], PATHINFO_EXTENSION);//檔案副檔名
			$new_name= $_SESSION['Center_Username'] . "_" . substr(sc_keygen(),0,8). "." . $extend;//檔案亂數名
			$file="../images/avatar/" .$new_name;
			
			if(!in_array($extend,$limitedext)){
				throw new Exception("This file format is not allowed");
			}
			
			move_uploaded_file($_FILES["fileField"]["tmp_name"],"images/avatar/".$new_name);//複製檔案
			if($row_user['avatar']!="../images/default_avatar.png"){
				unlink(str_replace('../','',$row_user['avatar']));//刪除舊頭像
			}
			$SQL->query("UPDATE member SET avatar = '%s' WHERE name = '%s'",array(
				$file,
				$_SESSION['Center_Username']
			));
			header("Location: avatar.php?ok");
		}
		else {
			throw new Exception("You have not uploaded any photo");
		}
	}
	catch(Exception $e){
		$upload_error = $e->getMessage();
	}
}
?>
<div class="main">
	<h2>Modify avatar</h2>
<?php
if($upload_error !== null){
?>
  <div class="prompt error"><?php echo $upload_error; ?></div>
<?php }
if(isset($_GET['no'])) { ?>
	<div class="prompt">Modify avatar failure！</div>
<?php }
if(isset($_GET['ok'])) {
?>
	<div class="prompt">Modify avatar success！</div>
<?php }
if(!isset($_GET['step'])) {
?>
	<form action="avatar.php?step=2" method="post" enctype="multipart/form-data" name="form1">
		<p>Your current avatar：</p>
		<img src="include/avatar.php?id=<?php echo $_SESSION['Center_Username'] ?>" class="avatar">
		<div class="controls">
			<input name="fileField" type="file" id="fileField" />
			<input type="submit" name="button" id="button" value="Upload" />
		</div>
		<p>Upload an avatar picture format only allows jpg.gif.png and the file size should not exceed <?php echo $center['avatar']['max_size']; ?> KB，Recommend size 100x100 ~ 200x200(px)。</p>
	</form>
<?php } ?>
</div>
<?php
$view->render();
?>