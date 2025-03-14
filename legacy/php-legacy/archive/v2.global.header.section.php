<div class="headerleftbordermobileSearch p-0 d-block d-lg-none">
			
	<a class="headerSearchmobile" data-toggle="collapse" href="#mobileSearch" role="button" aria-expanded="false" aria-controls="mobileSearch"><img src="/assets/img/icon-search.png" alt="recherche" class="mw-100" /></a>

</div>
<div class="headerleftbordermobileCart p-0 d-block d-lg-none">
			
	<a href="<?php echo $domain; ?>/panier.html" class="headerCartmobile"><img src="/assets/img/icon-cart.png" alt="mon panier" class="mw-100" />
	<span>(<?php echo @count($_SESSION['amcnkCart']['id_article']); ?>)</span></a>

</div>

<div class="container-fluid globalmobileheader d-block d-lg-none">
<div class="container-fluid mywidecontainer p-0">

<nav class="navbar navbar-expand-lg navbar-light p-0 m-0">
  

  <a class="navbar-brand" href="<?php echo $domain; ?>"><img src="<?php  echo $domain; ?>/assets/img/automecanik-logo.png" alt="<?php  echo $domainwebsitename; ?>"></a>
  
  <button class="navbar-toggler MOBILE-MENU-ICON p-0 m-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span><img src="<?php  echo $domain; ?>/assets/img/menu-icon-gray-white.png" alt="menu"></span>
  </button>


  <div class="collapse navbar-collapse" id="navbarSupportedContent" style="padding-top: 4px;">
    <ul class="navbar-nav">
      
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>">Accueil</a>
		  		</div>
		  	</div>
		</li>
        
        <?php
		if($arianefile=="home")
		{
		?>
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a class="" data-toggle="collapse" href="#catalog" role="button" aria-expanded="false" aria-controls="catalog">Catalogue Produit</a>
		  		</div>
		  	</div>
		</li>
        <?php
		}
		?>
      	
      	<?php
		if(!isset($_SESSION['myaklog'])) // le client est deja connecté
		{
		?>
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>/inscription.html">S'inscrire</a>
		  		</div>
		  	</div>
		</li>
		<?php
		}
		?>
      
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/" target="_blank">blog automobile</a>
		  		</div>
		  	</div>
		</li>

	    <li class="nav-item">
		  	
	    	<div class="row">
				<div class="col-6 MOBILE-MENU-ACTION text-center">
					<a href="tel:<?php echo $domainwebsiteteltocall; ?>">
					<img src="<?php  echo $domain; ?>/assets/img/icon-header-tel.png" width="35" height="35" alt="Assistance">
					<br><span><?php echo $domainwebsitetel; ?></span>
					</a>
				</div>
				<div class="col-6 MOBILE-MENU-ACTION text-center">
					<?php
					if(isset($_SESSION['myaklog'])) // le client est deja connecté
					{
					?>
					<a href="<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/">
					<img src="<?php  echo $domain; ?>/assets/img/icon-header-account.png" width="35" height="35" alt="mon compte">
					<br><span><?php echo $_SESSION['myakciv']." ".$_SESSION['myakprenom']." ".$_SESSION['myaknom']; ?></span>
					</a>
					<?php
					}
					else
					{
					?>
					<a href="<?php echo $domain; ?>/connexion.html">
					<img src="<?php  echo $domain; ?>/assets/img/icon-header-account.png" width="35" height="35" alt="mon compte">
					<br><span>Mon Compte</span>
					</a>
					<?php
					}
					?>
				</div>
			</div>

		</li>
    </ul>
  </div>
</nav>

</div>
</div>

<?php 
//if($isSmartPhoneVersion == true)
//{
?>
<div class="collapse container-fluid globalsearchformobile" id="mobileSearch">
	

			<form  action="<?php echo $domain; ?>/search/"  method="POST" role="form">
			<div class="row">
				<div class="col-10 col-xl-10 pr-0">
					
					<input type="text" name="quest" placeholder="Réf. d'origine ou commercial de votre pièce" autocomplete="off" />
					
				</div>
				<div class="col-2 col-xl-2 pl-0">
					
					<input type="submit" class="headerSearchRefSubmit" value="" />
					
				</div>
			</div>
			</form>	

</div>

<div class="container-fluid globalh1formobile d-block d-lg-none">
	
		<span class="containerh1mobile">
			<?php
			if($marque_logo!="")
			{
				?>
				<img src="<?php echo $domain; ?>/upload/constructeurs-automobiles/icon/<?php echo $marque_logo; ?>" />
				<?php
			}
			?>
			<span class="h1mobile"><?php echo $pageh1; ?></span>
		</span>

</div>
<?php 
//}
?>

<div class="container-fluid globalfirstheader d-none d-lg-block">
<div class="container-fluid mywidecontainer">
	
	<div class="row">
		<div class="col-lg-2 logoborder d-none d-lg-block">
			&nbsp;
		</div>
		<div class="col-12 col-lg-6 headerSearchRef">

			<form  action="<?php echo $domain; ?>/search/"  method="POST" role="form">
			<div class="row">
				<div class="col-10 col-xl-10 pr-0">
					
					<input type="text" name="quest" id="quest" placeholder="Réf. d'origine ou commercial de votre pièce" autocomplete="off" />
					<div id="questList"></div>
					
				</div>
				<div class="col-2 col-xl-2 pl-0">
					
					<input type="submit" class="headerSearchRefSubmit" value="" />
					
				</div>
			</div>
			</form>		

		</div>
		<div class="col-lg-4 pr-0 linksfirstheader text-right d-none d-lg-block">
			
			<a href="tel:<?php echo $domainwebsiteteltocall; ?>"><?php echo $domainwebsitetel; ?></a>
			 | 
			<?php
			if(isset($_SESSION['myaklog'])) // le client est deja connecté
			{
			?>
				<a href="<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/"><?php echo $_SESSION['myakciv']." ".$_SESSION['myakprenom']." ".$_SESSION['myaknom']; ?></a>
			<?php
			}
			else
			{
			?>
				<a href="<?php echo $domain; ?>/connexion.html">entrer</a>
				 | 
				<a href="<?php echo $domain; ?>/inscription.html">s'inscrire</a>
			<?php
			}
			?>

		</div>
	</div>

</div>
</div>

<div class="container-fluid globalsecondheader d-none d-lg-block">
<div class="container-fluid mywidecontainer">
	
	<div class="row">
		<div class="col-md-2 logomenu">
			<a href="<?php echo $domain; ?>"><img src="/assets/img/automecanik.png" class="mw-100"/></a>
		</div>
		<div class="col-md-10">
				
				<div class="row">
				<div class="col-md-8 linkssecondheader">

					<a href="<?php echo $domain; ?>">Accueil</a> &nbsp;
			        <?php
					if(($arianefile=="home"))
					{
					?>
					<a class="" data-toggle="collapse" href="#catalog" role="button" aria-expanded="false" aria-controls="catalog">Catalogue Produit</a> &nbsp;
			        <?php
					}
					?>
					<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/" target="_blank">blog automobile</a>

				</div>
				<div class="col-md-4 pr-0 text-right">
					
					<a href="/panier.html" class="headerCart"><img src="/assets/img/icon-cart.png" class="mw-100" /><br>
					<span>Mon panier (<?php echo @count($_SESSION['amcnkCart']['id_article']); ?>)</span></a>

				</div>
				</div>

		</div>
	</div>

</div>
</div>


<?php
if(($arianefile=="home"))
{
?>
<div class="collapse headerCatalog" id="catalog">
<div class="container-fluid mywidecontainer">

	<a class="closeCatalog d-block d-lg-none" data-toggle="collapse" href="#catalog" role="button" aria-expanded="false" aria-controls="catalog">X</a>
	<div class="row">
		<div class="col-lg-2 headerCatalogLogo d-none d-lg-block">
			&nbsp;
		</div>
		<div class="col-lg-10 headerCatalogContainer">


			<!-- CATALOGUE -->
			<?php
			$query_catalog_family = "SELECT DISTINCT MF_ID, IF(MF_NAME_SYSTEM IS NULL, MF_NAME, MF_NAME_SYSTEM) AS MF_NAME, 
			MF_DESCRIPTION, MF_PIC 
			FROM PIECES_GAMME 
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			JOIN CATALOG_FAMILY ON MF_ID = MC_MF_ID
			WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 AND MF_DISPLAY = 1
			ORDER BY MF_SORT";
			$request_catalog_family = $conn->query($query_catalog_family);
			if ($request_catalog_family->num_rows > 0) 
			{
			?>
				<div class="row p-0 m-0">
				<?php
				while($result_catalog_family = $request_catalog_family->fetch_assoc())
				{
				$mf_id = $result_catalog_family['MF_ID'];
				$mf_name_site = $result_catalog_family['MF_NAME'];
				$query_catalog_gamme = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_PIC, PG_IMG 
					FROM PIECES_GAMME 
					JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
					WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 
					AND MC_MF_ID = $mf_id
					ORDER BY MC_SORT";	
				$request_catalog_gamme = $conn->query($query_catalog_gamme);
				if ($request_catalog_gamme->num_rows > 0) 
				{
				?>
					<div class="col-lg-4 col-xl-3 headerCatalogFamily p-0">

					<a class="headerCatalogFamilyTitle" data-toggle="collapse" href="#catalog<?php  echo $mf_id; ?>" role="button" aria-expanded="false" aria-controls="catalog<?php  echo $mf_id; ?>"><?php  echo $mf_name_site; ?><span></span></a>
					
					<div class="collapse headerCatalogFamilyContent" id="catalog<?php  echo $mf_id; ?>">
					<?php
					while($result_catalog_gamme = $request_catalog_gamme->fetch_assoc())
					{
					$this_pg_name = $result_catalog_gamme['PG_NAME'];
					$thislinktoPage = $domain."/".$Piece."/".$result_catalog_gamme['PG_ALIAS']."-".$result_catalog_gamme['PG_ID'].".html";
					?>		
					<a href="<?php echo $thislinktoPage; ?>"><span>&gt;</span> <?php echo $this_pg_name; ?></a>
					<?php
					}
					?>
					</div>

					</div>
				<?php
				}
				}
				?>
				</div>
			<?php
			}
			?>
			<!-- / CATALOGUE -->


	    </div>
	</div>

</div>      
</div>
<?php
}
?>