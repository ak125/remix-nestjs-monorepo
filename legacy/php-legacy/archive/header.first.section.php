<?php include("../shopping_cart.function.php"); ?>
<?php
$reqlogDatas= mysql_query("SELECT * FROM 2027_xmassdoc_reseller_access_code WHERE login='$log' AND keylog='$mykey'");
$reslogDatas=mysql_fetch_array($reqlogDatas);
// privilege granted
$privilegeGranted = $reslogDatas["type"];
?>
<div class="container-fluid HEAD-CONTAINER">

	<div class="row">
		<div class="col MASSDOC-HEAD-CONTAINER-MAIN-TITLE text-left">
			
			<?php echo utf8_encode($reslogDatas["company"]); ?>
			<br>
			<span>Code : <b><?php echo utf8_encode($reslogDatas["type"]."-".$reslogDatas["id"]); ?></b></span>

		</div>
		<?php
		if ($privilegeGranted != "SA")
		{
		?>
		<div class="col-5 MASSDOC-HEAD-CONTAINER-MAIN-TITLE text-left d-none d-md-block pl-0">


							<form  action="<?php  echo $domain; ?>/search/"  method="GET" role="form">
							<div class="row">
								<div class="col-8">
									
									<input type="text" name="quest" class="REF-SEARCH-FORM-INPUT-TXT" placeholder="R&eacute;f. d'origine ou commercial de votre pi&egrave;ce"/>
									
								</div>
								<div class="col-4">
									
									<input type="submit" class="REF-SEARCH-FORM-INPUT-SUBMIT" value="" />
									
								</div>
							</div>
							</form>



		</div>
		<?php
		}
		if ($privilegeGranted == "SA")
		{
		?>
		<div class="col-3 col-sm-2 col-lg-1 HEAD-CONTAINER-MAIN-MY-ACCOUNT text-center">
			<a href="<?php  echo $domain; ?>/my/">
			<img src="<?php  echo $domain; ?>/assets/img/icon-header-account.png" width="35" height="35" alt="mon panier">
			<br><span>Mon compte</span>
			</a>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="col-3 col-sm-2 col-lg-1 HEAD-CONTAINER-MAIN-MY-CART text-center">
			<a href="<?php  echo $domain; ?>/cart/">
			<img src="<?php  echo $domain; ?>/assets/img/icon-header-cart.png" width="35" height="35" alt="mon panier">
			<br>
			<?php 
			$nbartonCart = count($_SESSION['panier']['id_article']); $prixCart = montant_panier();
			if($nbartonCart > 0) { ?><span><?php echo $nbartonCart." art // ".$prixCart." ".$Currency; ?> </span><?php } 
			else { ?><span>Mon panier</span><?php } ?>
			</a>
		</div>
		<div class="col-3 col-sm-2 col-lg-1 HEAD-CONTAINER-MAIN-MY-ACCOUNT text-center">
			<a href="<?php  echo $domain; ?>/my/">
			<img src="<?php  echo $domain; ?>/assets/img/icon-header-account.png" width="35" height="35" alt="mon panier">
			<br><span>Mon compte</span>
			</a>
		</div>
		<?php
		}
		?>
	</div>
	
</div> 
