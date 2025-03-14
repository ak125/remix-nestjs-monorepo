<div id="MAIN-MENU-CONTAINER">
	
	<div id="MENU-ICON-CONTAINER" onclick="openNav()">
		&nbsp;
	</div>
	
	<div id="MINI-LOGO-ICON-CONTAINER">
		<img src="<?php echo $domain; ?>/assets/img/mini-logo-icon-gray.png" /><br /><br />&copy; <?php echo date("Y"); ?><br /><?php echo $domaincorename; ?>
	</div>

	
</div>

<div id="mySidenav" class="sidenav">
	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	<a href="<?php echo $domain; ?>/welcome">Dashboard</a>
	<?php
	if ($privilegeGranted == "SA")
	{
	?>
	<a href="<?php echo $domain; ?>/stock/">Stock management</a>
	<?php
	}
	?>
	<a href="<?php echo $domain; ?>/out">Log Out</a>
</div>