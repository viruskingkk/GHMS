<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
	header("Location: index.php?login");
	exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Folder');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/upload.js");

if(!is_dir('include/file/' . $_SESSION['Center_Username'] . '/')){
	@mkdir('include/file/' . $_SESSION['Center_Username'] .'/');
}

if(isset($_FILES['qqfile']) || isset($_GET['qqfile'])){
	require_once('include/upload.php');
	
	function check(){
		global $center;
		$dir = 'include/file/'.$_SESSION['Center_Username'];
		$filequantity = count(glob($dir . "/*.*"));
		if($filequantity >= $center['file']['max_files']){
			throw new Exception('The number of files have reached the maximum of！');
		}
	};
	
	$uploader = new FileUploader($center['file']['limitedext'], $center['file']['max_size'] * 1024, 'qqfile', 'check');
	$result = $uploader->handleUpload('include/file/' . $_SESSION['Center_Username'] . '/');
	
	header("Content-Type: text/plain");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	die();
}

if((isset($_GET['rename'])) && ($_GET['rename']!='') && (isset($_POST['newname'])) && (sc_namefilter($_POST['newname'])!='')){
	$_GETrename=sc_namefilter($_GET['rename']);
	$file = 'include/file/' . $_SESSION['Center_Username'] . '/' . $_GETrename;
	$newname = 'include/file/' . $_SESSION['Center_Username'] . '/' . sc_namefilter($_POST['newname']) . '.' . pathinfo(sc_namefilter($_GETrename), PATHINFO_EXTENSION);
	rename($file, $newname);
	header("Location: file.php?renameok");
}
else if((isset($_GET['del'])) && ($_GET['del'] !='')){
	unlink('include/file/'.$_SESSION['Center_Username'].'/'.sc_namefilter($_GET['del']));
	header("Location: file.php");
}
?>
<script>
$(function(){
	$('.file li').on('click contextmenu',function(e){
		if(e.which == 3){
			e.preventDefault();
			var file = $(this).attr('data-file');
			$('.context-menu span a').each(function(){
				$(this).attr('href',$(this).attr('data-href')+encodeURIComponent(file));
			});
			$('.context-menu').css({
				top: e.pageY,
				left: e.pageX
			}).fadeIn(400);
		}
	});
	$('.context-menu span a').on('click',function(e){
		if($(this).attr('href').indexOf('del') > -1){
			if(!window.confirm('Erase?')){
				e.preventDefault();
			}
		}
	});
	$(':not(.context-menu)').on('click',function(){
		$('.context-menu').fadeOut(200);
	});
	$('#upload').fineUploader({
		request: {
			endpoint: 'file.php'
		},
		validation: {
			allowedExtensions: <?php echo json_encode($center['file']['limitedext']); ?>,
			sizeLimit: <?php echo $center['file']['max_size'] * 1000; ?>
		},
		messages: {
			typeError: '{file} Incorrect format。\nYou can only upload the following formats：{extensions}。',
			sizeError: '{file} Size is too large，The maximum upload size is{sizeLimit}。',
			noFilesError: 'Please select some file upload。',
			onleave: 'File upload unfinished，Are you sure you want to leave？',
			tooManyFilesError: 'You may not post too many files。Upload a maximum of five。'
		},
		failedUploadTextDisplay: {
			mode: 'custom',
			maxChars: 40,
			responseProperty: 'error',
			enableTooltip: true
		},
		text: {
			uploadButton: 'Choose File'
		}
	});
});
</script>
<div class="main">
	<h2>Folder</h2>
	<?php if(isset($_GET['upload'])){
		$dir = 'include/file/'.$_SESSION['Center_Username'];
		$filequantity = count(glob($dir . "/*.*"));
		if($filequantity >= $center['file']['max_files']){
	?>
  <div class="prompt error">The number of files have reached the maximum of！</div>
	<?php }else{ ?>
		<div class="remarks">File upload relevant provisions：
			<ol>
				<li>Prohibit illegal uploading files, pictures</li>
				<li>File size limit：<?php echo floor($center['file']['max_size']); ?> KB</li>
				<li>Allowed file types：<?php echo implode(",",$center['file']['limitedext']); ?></li>
			</ol>
		</div>
		<div id="upload"></div>
	<?php }
	} else {
		if(isset($_GET['uploading'])){
	?>
		<div class="prompt">Files uploaded successfully！</div>
	<?php }
	if(!isset($_GET['rename'])){
		$dir = 'include/file/'.$_SESSION['Center_Username'];
		$filequantity = count(glob($dir."/*.*"));
		
		if(!is_dir($dir.'/')) {      //檢查會員資料夾是否存在
			if(!mkdir($dir)){  //不存在的話就創建會員資料夾
				die ("Upload directory does not exist, and the creation fails");
			}
		}
		
		$handle = @opendir($dir) or die("Unable to open" . $dir);
		
		if($filequantity >= $center['file']['max_files']){
			echo '<div class="prompt error">The number of files have reached the maximum of！</div>';
		}
		
		if(isset($_GET['renameok'])){
			echo '<div class="prompt">Rename success！</div>';
		}
		echo "<strong>In " . $_SESSION['Center_Username'] . " folder archives</strong>，there have" . $filequantity . "Files，Limit is ".$center['file']['max_files'] ."！<br>";
	?>
		<ul class="file">
	<?php
		while($file = readdir($handle)){
			if($file != "." && $file != ".."){ 
			$myfile = $dir."/".$file;
			$extend = pathinfo($myfile, PATHINFO_EXTENSION);//取得副檔名
	?>
		<li data-file="<?php echo $file ?>">
			<img src="./images/file.png">
			<a href="<?php echo $myfile ?>" target="_blank"><?php echo $file ?></a><br>
			<span style="font-size:84%;">
			<?php
				$ext = array('Bytes','KB','MB','GB','TB','PB','EB','YB');
				$size = filesize($myfile);
				$i = floor(log($size,1000));
				echo sprintf('%.2f '.$ext[$i], ($size/pow(1000, floor($i))));
			?>
			</span>
		</li>
	<?php }
	}
	?>
		<br class="clearfix" />
	</ul>
	<div class="context-menu">
		<span><a data-href="file.php?del=">Delete</a></span>
		<span><a data-href="file.php?rename=">Rename</a></span>
	</div>
	<?php
	clearstatcache();
	closedir($handle);
	}
	else if(isset($_GET['rename'])){
		$_oldname=sc_namefilter($_GET['rename']);
	?>
	<form name="form1" method="post" action="file.php?rename=<?php echo $_oldname; ?>">
		You are ready to rename：<?php echo htmlspecialchars($_GET["rename"]); ?>
		<div class="controls">
			<input name="newname" type="text" id="newname" class="input-medium" value="<?php echo substr($_oldname,0,strrpos($_oldname, '.')); ?>" maxlength="25">
		<span><?php echo '.' . pathinfo($_oldname, PATHINFO_EXTENSION); ?></span>
		</div>
		<input type="submit" name="button" id="button" value="Rename" />
		<a href="file.php">Cancel</a>
		</p>
	</form>
	<?php }
	} ?>
</div>
<?php
	$view->render();
?>