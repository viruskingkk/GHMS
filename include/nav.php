<?php if(isset($_SESSION['Center_Username'])){ ?>
	<li><a href="member.php">My Account Details</a></li>
	<li><a href="chat.php">Support online</a></li>
	<li class="dropdown">
		<a href="forum.php" data-target="#" data-toggle="dropdown">Support Center ▼</a>
		<ul class="dropdown-menu">
			
			<li><a href="forum.php?newpost">New Discussions</a></li>
			<li><a href="mypost.php">My Discussions</a></li>
            <li><a href="Knowledge.php">Knowledge Center</a></li>
            <li><a href="Under_Contruction.php">Online Blog</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="file.php" data-target="#" data-toggle="dropdown">Folder ▼</a>
		<ul class="dropdown-menu">
			<li><a href="file.php">Folder</a></li>        
			<li><a href="file.php?upload">Flie Upload</a></li>
		</ul>
	</li>
 <li class="dropdown">
 		<a href="" data-target="#" data-toggle="dropdown">Resources ▼</a>
			<ul class="dropdown-menu">
				<li><a href="Under_Contruction.php">Alerts & Advisories</a></li>
                <li><a href="Under_Contruction.php">Training</a></li>
			</ul>
		</li>
	<?php if($_SESSION['Center_UserGroup']=="admin"){?>
		<li><a href="admin/index.php">System mananger</a></li>
	<?php } ?>
	<li><a href="index.php?logout">Logout</a></li>
<?php }else{ ?>
	<li><a href="index.php">Login</a></li>
<p>
  <?php } ?>
</p>
