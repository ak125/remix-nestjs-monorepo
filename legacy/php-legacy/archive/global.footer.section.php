<div class="container-fluid globalfooter">
<div class="container-fluid mywidecontainer">
	
	<div class="row">
		<div class="col-md-9 pl-0 text-justify">
			Pièces détachées auto à un prix pas cher sur Automecanik.com &nbsp; 
			<a href="https://www.automecanik.com/blog-pieces-auto/comment-choisir-le-bon-produit" target="_blank">Achat de pièces de voiture en ligne</a>
			<?php
			if(($typefile=="standard")&&($arianefile=="home"))
			{
			?>
			<br><br>
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
						<a href='<?php echo $domain; ?>/<?php echo $result_footer_first_menu["FM_ALIAS"]; ?>'><?php echo $result_footer_first_menu["FM_TITLE"]; ?></a>
					<?php
					}
				}
				?>
				<br>
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
						<a href='<?php echo $domain; ?>/<?php echo $result_footer_second_menu["FM_ALIAS"]; ?>'><?php echo $result_footer_second_menu["FM_TITLE"]; ?></a>
					<?php
					}
				}
				?>
			<?php
			}
			?>
			<u><br><br>&copy; <?php echo date("Y"); ?> <?php echo $ownername; ?></u>
		</div>
		<div class="col-md-3 pr-0 text-right">
			<span>inscription newsletter</span>
			<br>
			Découvrez comme il est simple et rapide de rester informé sur les dernières nouveautés.
			<br><br>
			<a href="" class="globalsubscribelink">inscription immédiate</a>
		</div>
	</div>

</div>
</div>