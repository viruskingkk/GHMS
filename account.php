<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
	header("Location: index.php?login");
	exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'My Account');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");
$view->addScript("include/js/jquery.validate.js");

$member = sc_get_member_data($_SESSION['Center_Username']);


if(isset($_POST['email'])&& filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
if($_POST['web_site']!='' && !filter_var($_POST['web_site'], FILTER_VALIDATE_URL)){
$_web_site=$member['row']['web_site'];}
else{$_web_site=$_POST['web_site'];}

if($_POST['tel']!='' && !filter_var($_POST['tel'], FILTER_VALIDATE_INT)){
$_tel=$member['row']['tel'];}

if($_POST['company']!='' && !filter_var($_POST['company'])){
$_company=$member['row']['company'];}

if($_POST['password'] == ''){$pass = $member['row']['password'];}
else {$pass = sc_password($_POST['password'], $member['row']['name']);}
	
	$SQL->query("UPDATE member SET password = '%s', email = '%s', company = '%s', tel = '%s', web_site = '%s' WHERE name = '%s'",array($pass,$_POST['email'],$_POST['company'],$_POST['tel'],$_web_site,$_SESSION['Center_Username'])
	);
	header("Location: account.php?ok");
}
?>

<script type="text/javascript">
$(function(){
	$("#form1").validate({
		rules:{
			authpassword:{equalTo: "#password"},
		},
		messages:{
			authpassword:{equalTo: "Please enter the same password as above"}
		},
	});
});
</script>

<div class="main">
<?php if(isset($_GET['ok'])){?>
<div class="alert alert-success"><span class="prompt">Modified successfully!</span></div>
<?php } ?>
<?php if(isset($_GET['Password'])){?>
<div class="alert alert-success"><span class="prompt">Password is not the same</span></div>
<?php } ?>
	<h2 class="subtitle">My Account</h2>
<div class="row-fluid">
	<div class="span3 text-center">
		<img src="include/avatar.php?id=<?php echo $member['row']['name']; ?>" class="avatar">                       
		<p><a href="avatar.php">Modify avatar</a></p>
	</div>
	<div class="span9">
		<form id="form1" name="form1" action="account.php" method="POST" class="form-horizontal">
			<div class="control-group">
					<label class="control-label">Account：</label>
					<div class="controls"><?php echo htmlspecialchars($member['row']['name']); ?></div>
				</div>
			<div class="control-group">
				<label class="control-label" for="username">Reset YourPassword：</label>
				<div class="controls">
					<input name="password" type="password" class="input-large" id="password" maxlength="30" required="required">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="authpassword">Confirm Your New Password：</label>
				<div class="controls">	
					<input name="authpassword" type="password"  class="input-large" id="authpassword" maxlength="30" required="required" >
				</div>
			</div>
          
		  <div class="control-group">
				<label class="control-label" for="email">* E-mail：</label>
				<div class="controls">	
					<input name="email" type="text" id="email" class="input-large" maxlength="255" value="<?php echo $member['row']['email']; ?>" /> 
				</div>
			</div>
            
			<div class="control-group">
				<label class="control-label" for="company">Company：</label>
				<div class="controls">	
					<input name="company" type="text" id="company" class="input-large" maxlength="255" value="<?php echo $member['row']['company']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="tel">TEL：</label>
				<div class="controls">	
					<input name="tel" type="text" id="tel" class="input-large" maxlength="20" value="<?php echo $member['row']['tel']; ?>" />
				</div>   
			</div>
            
			<div class="control-group">
				<label class="control-label" for="web_site">Website：</label>
				<div class="controls">	
					<input name="web_site" type="text" id="web_site" class="input-large" maxlength="255" value="<?php echo $member['row']['web_site']; ?>" />
				</div>
			</div>
            
			<div class="control-group">
				<label class="control-label">Registration Date：</label>
				<div class="controls"><?php echo $member['row']['joined']; ?></div>
			</div>
			<div class="control-group">				
            <label class="control-label">Last active：</label>
			  <div class="controls"><?php echo $member['row']['last_login']; ?></div>
			</div>                        
			<div class="control-group">
				<div class="controls">
					<input name="button" type="submit" id="botton" class="btn btn-success btn-large" value="Confirm" />
				</div>
			</div>
		  <div class="clearfix"></div>
	</form>
</div>
</div>
<?php
	$view->render();
?>