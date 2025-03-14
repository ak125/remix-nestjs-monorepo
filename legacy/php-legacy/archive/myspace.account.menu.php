<div class="row">
	<div class="col-12 pt-3">
		<span class="containerh2account">
			<h2>mon compte</h2>
		</span>
	</div>
</div>
<div class="row p-0 m-0">
	<div class="col-12 accountmenu">

		<li>
		<a href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/" rel="nofollow">Tableau de bord</a>
		</li>
		<!--li>
		<a href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/msg" rel="nofollow">Mes Messages</a>
		</li-->
		<li>
		<a href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/order" rel="nofollow">Mes Commandes</a>
		</li>

	</div>
</div>
<div class="row">
	<div class="col-12 pt-3">
		<span class="containerh2account">
			<h2>paramètres</h2>
		</span>
	</div>
</div>
<div class="row p-0 m-0">
	<div class="col-12 accountmenu">

		<!--li>
		<a href="< ?php  echo $domain; ?>/< ?php  echo $domainClient2022; ?>/address/facturation" rel="nofollow">Adresse de facturation</a>
		</li-->
		<!--li>
		<a href="< ?php  echo $domain; ?>/< ?php  echo $domainClient2022; ?>/address/livraison" rel="nofollow">Adresses de livraison</a>
		</li-->
		<li>
		<a href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/password" rel="nofollow">Changer mon mot de passe</a>
		</li>
		<li>
		<a href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/out" rel="nofollow">Déconnexion</a>
		</li>

	</div>
</div>

<div class="row">
	<div class="col-12 pt-3">
		<span class="containerh2account">
			<h2>automecanik</h2>
		</span>
	</div>
</div>
<div class="row p-0 m-0">
	<div class="col-12 accountmenu">

		<?php
		$query_footer_first_menu = "SELECT * 
				FROM ___FOOTER_MENU
				WHERE FM_LEVEL = 1 AND FM_RELFOLLOW = 0
				ORDER BY FM_ID ASC";
		$request_footer_first_menu = $conn->query($query_footer_first_menu);
		if ($request_footer_first_menu->num_rows > 0) 
		{
			while($result_footer_first_menu = $request_footer_first_menu->fetch_assoc())
			{
			?>
				<li><a href='<?php echo $domain; ?>/<?php echo $result_footer_first_menu["FM_ALIAS"]; ?>' rel="nofollow"><?php echo $result_footer_first_menu["FM_TITLE"]; ?></a></li>
			<?php
			}
		}
		?>

	</div>
</div>
<div class="row">
	<div class="col-12 pt-3">
		<span class="containerh2account">
			<h2>aide &amp; support</h2>
		</span>
	</div>
</div>
<div class="row p-0 m-0">
	<div class="col-12 accountmenu">
		
		<?php
		$query_footer_second_menu = "SELECT * 
				FROM ___FOOTER_MENU
				WHERE FM_LEVEL = 2 AND FM_RELFOLLOW = 0 
				ORDER BY FM_ID ASC";
		$request_footer_second_menu = $conn->query($query_footer_second_menu);
		if ($request_footer_second_menu->num_rows > 0) 
		{
			while($result_footer_second_menu = $request_footer_second_menu->fetch_assoc())
			{
			?>
				<li><a href='<?php echo $domain; ?>/<?php echo $result_footer_second_menu["FM_ALIAS"]; ?>' rel="nofollow"><?php echo $result_footer_second_menu["FM_TITLE"]; ?></a></li>
			<?php
			}
		}
		?>

	</div>
</div>