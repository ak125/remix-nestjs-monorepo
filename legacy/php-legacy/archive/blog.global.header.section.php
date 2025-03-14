<div class="headerleftbordermobileCart p-0 d-block d-lg-none">
			
	<a href="<?php echo $domain; ?>/panier.html" class="headerCartmobile"><img src="/assets/img/icon-cart.png" class="mw-100" />
	<span>(<?php echo @count($_SESSION['amcnkCart']['id_article']); ?>)</span></a>

</div>

<div class="container-fluid globalmobileheader d-block d-lg-none">
<div class="container-fluid mywidecontainer p-0">

<nav class="navbar navbar-expand-lg navbar-light p-0 m-0">
  

  <a class="navbar-brand" href="<?php echo $domain; ?>"><img src="<?php  echo $domain; ?>/assets/img/automecanik-logo.png" alt="<?php  echo $domainwebsitename; ?>"></a>
  
  <button class="navbar-toggler MOBILE-MENU-ICON p-0 m-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span><img src="<?php  echo $domain; ?>/assets/img/menu-icon-gray-white.png"></span>
  </button>


  <div class="collapse navbar-collapse" id="navbarSupportedContent" style="padding-top: 4px;">
    <ul class="navbar-nav">
      
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/">Accueil</a>
		  		</div>
		  	</div>
		</li>

	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a class="" data-toggle="collapse" href="#catalogMontage" role="button" aria-expanded="false" aria-controls="catalogMontage"><?php echo $entretien_title; ?></a>
		  		</div>
		  	</div>
		</li>
      
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/comment-choisir-le-bon-produit">Comment choisir le bon produit</a>
		  		</div>
		  	</div>
		</li>
      
	    <li class="nav-item">
	    	<div class="row">
				<div class="col-12 COL-MOBILE">
		  			<a href="<?php echo $domain; ?>" target="_blank">Retournez sur Automecanik.com</a>
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
					<a href="<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/" target="_blank">
					<img src="<?php  echo $domain; ?>/assets/img/icon-header-account.png" width="35" height="35" alt="mon compte">
					<br><span><?php echo $_SESSION['myakciv']." ".$_SESSION['myakprenom']." ".$_SESSION['myaknom']; ?></span>
					</a>
					<?php
					}
					else
					{
					?>
					<a href="<?php echo $domain; ?>/connexion.html" target="_blank">
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

<div class="container-fluid globalfirstheader d-none d-lg-block">
<div class="container-fluid mywidecontainer">
	
	<div class="row">
		<div class="col-md-2 logoborder">
			&nbsp;
		</div>
		<div class="col-md-6 linksfirstheader">

			<a href="<?php echo $domain; ?>" target="_blank">Retournez sur Automecanik.com</a>		

		</div>
		<div class="col-md-4 pr-0 linksfirstheader text-right">
			
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

<div class="container-fluid globalsecondheader  d-none d-lg-block">
<div class="container-fluid mywidecontainer">
	
	<div class="row">
		<div class="col-md-2 logomenu">
			<a href="<?php echo $domain; ?>" target="_blank"><img src="/assets/img/automecanik.png" class="mw-100" /></a>
		</div>
		<div class="col-md-3 linkssecondheader">

			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/">Accueil</a> &nbsp;

		</div>
		<div class="col-md-7 linkssecondheader pr-0 text-right">
			
			<a class="" data-toggle="collapse" href="#catalogMontage" role="button" aria-expanded="false" aria-controls="catalogMontage"><?php echo $entretien_title; ?></a> &nbsp;
			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $constructeurs; ?>"><?php echo $constructeurs_title; ?></a> &nbsp;
			<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $guide; ?>"><?php echo $guide_title; ?></a>

			<?php /*a href="<?php echo $domain; ?>/<?php echo $blog; ?>/comment-choisir-le-bon-produit">Comment choisir le bon produit</a */ ?>

		</div>
	</div>

</div>
</div>


<div class="collapse headerCatalog" id="catalogMontage">
<div class="container-fluid mywidecontainer">

	<a class="closeCatalog d-block d-lg-none" data-toggle="collapse" href="#catalogMontage" role="button" aria-expanded="false" aria-controls="catalogMontage">X</a>
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
				$query_catalog_gamme = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_CREATE, BA_UPDATE, 
				PG_NAME, PG_ALIAS, PG_PIC, PG_IMG, PG_WALL 
				FROM __BLOG_ADVICE
				JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
				INNER JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
				INNER JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
				WHERE BA_PG_ID > 0 
				AND MF_ID = $mf_id
				ORDER BY MC_SORT, BA_UPDATE DESC, BA_CREATE DESC";	
				$request_catalog_gamme = $conn->query($query_catalog_gamme);
				if ($request_catalog_gamme->num_rows > 0) 
				{
				?>
					<div class="col-lg-4 col-xl-3 headerCatalogFamily p-0">

					<a class="headerCatalogFamilyTitle" data-toggle="collapse" href="#catalogMontage<?php  echo $mf_id; ?>" role="button" aria-expanded="false" aria-controls="catalogMontage<?php  echo $mf_id; ?>"><?php  echo $mf_name_site; ?><span></span></a>
					
					<div class="collapse headerCatalogFamilyContent" id="catalogMontage<?php  echo $mf_id; ?>">
					<?php
					while($result_catalog_gamme = $request_catalog_gamme->fetch_assoc())
					{
					$this_ba_id = $result_catalog_gamme['BA_ID'];
					$this_ba_h1 = $result_catalog_gamme['BA_H1'];
					$this_ba_alias = $result_catalog_gamme['BA_ALIAS'];
					$this_ba_preview = $result_catalog_gamme['BA_PREVIEW'];
					$this_pg_name_site = $result_catalog_gamme['PG_NAME'];
					$this_pg_alias = $result_catalog_gamme['PG_ALIAS'];
					$thislinktoPage = $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias;
					?>		
					<a href="<?php echo $thislinktoPage; ?>"><span>&gt;</span> <?php echo $this_ba_h1; ?></a>
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
			<!-- / CATALOGUE -->


	    </div>
	</div>

</div>      
</div>

<?php
switch ($arianefile)
{
    case 'home':
    {
		?>
		<div class="container-fluid blog-home-search">
		<div class="container-fluid mysmallcontainer">

					
			<div class="row">
				<div class="col-12 pb-3">
					<h1 class="blog"><?php echo $pageh1; ?></h1>
					<?php echo $pagecontent; ?>
				</div>
				<?php /*div class="col-12 pt-3">

					<form  action="<?php echo $domain; ?>/<?php echo $blog; ?>/search/"  method="POST" role="form">
					<div class="row">
						<div class="col-12 col-sm-9 pb-3">
							
							<input type="text" name="quest" placeholder="Chercher dans le support..."/>
							
						</div>
						<div class="col-12 col-sm-3 pb-3">
							
							<input type="submit" class="blogSearchSubmit" value="rechercher" />
							
						</div>
					</div>
					</form>		

				</div */ ?>
			</div>


		</div>
		</div>
		<?php
	break;
	}
	case 'advice':
    {
		?>
		<div class="container-fluid blog-entretien">
		<div class="container-fluid mysmallcontainer">

					
			<div class="row">
				<div class="col-12 pb-3">
					<h1 class="blog"><?php echo $pageh1; ?></h1>
					<?php echo $pagecontent; ?>
				</div>
			</div>


		</div>
		</div>
		<?php
	break;
	}
	case 'constructeurs':
    {
		?>
		<div class="container-fluid blog-entretien">
		<div class="container-fluid mysmallcontainer">

					
			<div class="row">
				<div class="col-12 pb-3">
					<h1 class="blog"><?php echo $pageh1; ?></h1>
					<?php echo $pagecontent; ?>
				</div>
			</div>


		</div>
		</div>
		<?php
	break;
	}
	case 'guide':
    {
		?>
		<div class="container-fluid blog-entretien">
		<div class="container-fluid mysmallcontainer">

					
			<div class="row">
				<div class="col-12 pb-3">
					<h1 class="blog"><?php echo $pageh1; ?></h1>
					<?php echo $pagecontent; ?>
				</div>
			</div>


		</div>
		</div>
		<?php
	break;
	}
	default :
    {
        ?>
		<div class="container-fluid globalthirdheader">
		<div class="container-fluid mywidecontainer nocarform">
		</div>
		</div>
		<?php
    break;
    }
}
?>