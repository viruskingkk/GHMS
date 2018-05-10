<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Member Login');

if(isset($_SESSION['Center_Username'],$_SESSION['Center_UserGroup'])){
	header('Location: member.php');
}

if (isset($_POST['username'])) {
	$temp_var = sc_login($_POST['username'],$_POST['password']);
	if($temp_var > 0){
        header("Location: member.php?login");
		}
	else{	
	 header("Location: index.php?login");
	}
	die("");
}

else 
 if(isset($_GET['logout'])) {
	sc_loginout();
}



switch(true){
	case isset($_GET['reg']):
?>
	<div class="prompt">Registration success!</div>
<?php
	break;
	case isset($_GET['out']):
?>
	<div class="prompt">You have been signed out!</div>
<?php
	break;
	case isset($_GET['getpassword']):
?>
	<div class="prompt">Password Reset success!</div>
<?php
	break;
	case isset($_GET['login']):
?>
	<div class="prompt error">Account does not exist or account password error</div>
<?php
	break;
}
?>
<div class="login-form">
	<h2 class="page-header">Member Login</h2>
    <form class="form-horizontal" action="index.php" method="post">

            
            <div class="control-group">
		<label class="control-label" for="username">Account：</label>
		<div class="controls">
		<input id="username" name="username" type="text">
			</div>
		</div>               
<div class="control-group">
			<label class="control-label" for="password">Password：</label>
			<div class="controls">
				<input id="password" name="password" type="password">
			</div>
		</div>
        	<div class="control-group">
			<div class="controls">
				<input type="submit" value="Sign in" class="btn btn-primary btn-large">
				<a href="register.php" class="btn btn-info">register</a>
				<a href="getpassword.php"></a>
			</div>
		</div>

<?php
	$view->render();
?>