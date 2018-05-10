<?php
require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_SESSION['Center_Username'])){
	header("Location: index.php?n");
	exit;
}

$view = new View('view/new_theme.html','include/nav.php',$center['site_name'],'Member Center');
$view->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
$view->addScript("include/js/channel.js");
$view->addScript("include/js/notice.js");
?>

<div class="main">
<?php if((isset($_COOKIE['login']))&&(isset($_GET['login']))){?>
	<div class="alert alert-success"><span class="prompt">Login succuss!</span></div>
<?php } ?>
	<h2 class="subtitle">Member Center</h2>
	<p>Welcome to the <?php echo $center['site_name']; ?>！</p>
	<ul class="nav nav-pills">
		<li><a href="account.php">My Account</a></li>
		<li><a href="avatar.php">Modify Avatar</a></li>
		<li><a href="chat.php">Support Online</a></li>
		<li><a href="forum.php">Support Center</a></li>
		<li><a href="file.php">Folder</a></li>
      <li class="dropdown">
		<a href="" data-target="#" data-toggle="dropdown">Open a Support Ticket ▼</a>
		<ul class="dropdown-menu">
			<li><a href="ticket.php?newpost">New Ticket</a></li>
			<li><a href="ticketmypost.php">My Ticket</a></li>
            </ul>
                </li>
		<br class="clearfix" />
	</ul>
</div>
<?php
	$view->render();
?>