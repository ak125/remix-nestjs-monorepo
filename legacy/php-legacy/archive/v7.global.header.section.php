<div class="container-fluid menutop d-none d-lg-block">
    <div class="container-fluid mymaxwidth">

    		<span>Pièces auto à prix pas cher</span>
    		<a href="tel:<?php echo $domainwebsiteteltocall; ?>"><?php echo $domainwebsitetel; ?></a>
			&nbsp; &nbsp; | &nbsp; &nbsp;
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
				 &nbsp; &nbsp; | &nbsp; &nbsp;
				<a href="<?php echo $domain; ?>/inscription.html">s'inscrire</a>
			<?php
			}
			?>
    </div>
</div>

<div class="container-fluid stickymenu">
    <div class="container-fluid mymaxwidth p-0">

		<div class="container-fluid mobilestickymenu">
		  	<a href="<?php echo $domain; ?>"><?php echo $domainname; ?></a>
		</div>

		<div class="nav">
		<nav class="w-100">
		<a href="javascript:void(0);" class="mobile-menu-trigger"><img src="/assets/img/menu.svg" width="27" /></a>
		<a onclick="openMyQuickCart()" class="mobile-menu-trigger-cart"><img src="/assets/img/cart.svg" width="24" /><span><?php echo @count($_SESSION['amcnkCart']['id_article']); ?></span></a>

			<?php
			if(isset($_SESSION['myaklog'])) // le client est deja connecté
			{
				$usrLinkaccount = $domain."/".$domainClient2022."/";
			}
			else
			{
				$usrLinkaccount = $domain."/connexion.html";
			}
			?>

		<a href="<?php echo $usrLinkaccount; ?>" class="mobile-menu-trigger-user"><img src="/assets/img/user.svg" width="27" /></a>
		<a onclick="openMyQuickSearch()" class="mobile-menu-trigger-search"><img src="/assets/img/search.svg" width="24" /></a>
	    <ul class="menu menu-bar">
	      <li>
	        <a href="<?php echo $domain; ?>" class="menu-link menu-bar-link menu-bar-link-logo"><?php echo $domainname; ?></a>
	      </li>
	      <li>
	        <a href="javascript:void(0);" class="menu-link menu-bar-link" aria-haspopup="true">catalogue produit</a>
	        <ul class="mega-menu mega-menu--multiLevel">
		        <?php
				$query_catalog_family = "SELECT DISTINCT MF_ID, IF(MF_NAME_SYSTEM IS NULL, MF_NAME, MF_NAME_SYSTEM) AS MF_NAME, 
				MF_DESCRIPTION, MF_PIC, MF_SORT  
				FROM PIECES_GAMME 
				JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
				JOIN CATALOG_FAMILY ON MF_ID = MC_MF_ID
				WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 AND MF_DISPLAY = 1
				ORDER BY MF_SORT";
				$request_catalog_family = $conn->query($query_catalog_family);
				while($result_catalog_family = $request_catalog_family->fetch_assoc())
				{
				$this_mf_id = $result_catalog_family['MF_ID'];
				$this_mf_name_site = $result_catalog_family['MF_NAME'];
				?>
		          <li>
		            <a href="javascript:void(0);" class="menu-link mega-menu-link" aria-haspopup="true"><?php echo $this_mf_name_site; ?></a>
						<ul class="menu menu-list">
							<?php
							$query_catalog_gamme = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_PIC, PG_IMG, MC_SORT 
							FROM PIECES_GAMME 
							JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
							WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 
							AND MC_MF_ID = $this_mf_id
							ORDER BY MC_SORT";	
							$request_catalog_gamme = $conn->query($query_catalog_gamme);
							while($result_catalog_gamme = $request_catalog_gamme->fetch_assoc())
							{
							$this_pg_name = $result_catalog_gamme['PG_NAME'];
							$thislinktoPage = $domain."/".$Piece."/".$result_catalog_gamme['PG_ALIAS']."-".$result_catalog_gamme['PG_ID'].".html";
							?>
							<li>
							<span data-obfsq="<?php echo base64_encode($thislinktoPage); ?>" class="menu-link menu-list-link obfsq"><?php echo $this_pg_name; ?></span>
							</li>
							<?php
							}
							?>

						</ul>
		          </li>
		          <li class="mobile-menu-back-item">
		            <a href="javascript:void(0);" class="menu-link mobile-menu-back-link">Menu Principal</a>
		          </li>
				<?php
				}
				?>
	        </ul>
	      </li>
	      <li>
	        <a href="<?php echo $domain; ?>/<?php echo $blog; ?>/" target="_blank" class="menu-link menu-bar-link">blog automobile</a>
	      </li>
	      <li class="mobile-menu-header">
	        <a href="<?php echo $domain; ?>" class="">
	          <span>AUTOMECANIK LOGO</span>
	        </a>
	      </li>
	      <div class="stickymenuannex w-100">

	        <a onclick="openMyQuickCart()" class="stickymenuannex-cart"><img src="/assets/img/cart.svg" width="30" class="mw-100" /><span><?php echo @count($_SESSION['amcnkCart']['id_article']); ?></span></a>

	        <?php
			if(isset($_SESSION['myaklog'])) // le client est deja connecté
			{
			?>
				<a href="<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/" class="stickymenuannex-user"><img src="/assets/img/user.svg" width="34" class="mw-100" /></a>
			<?php
			}
			else
			{
			?>
				<a href="<?php echo $domain; ?>/connexion.html" class="stickymenuannex-user"><img src="/assets/img/user.svg" width="34" class="mw-100" /></a>
			<?php
			}
			?>

	        <!--a href="" class="stickymenuannex-search"><img src="/assets/img/search.svg" width="30" class="mw-100" /></a-->

	        <form method="post" action="<?php echo $domain; ?>/find/" class="stickymenuannex-search">
	        	<input type="search" name="quest" autocomplete="off">
	        </form>

	      </div>
	    </ul>
		</nav>
		</div>

    </div>
</div>

<div id="myquickcart" class="myquickcartcontent 100vh">
  <strong>Mon Panier</strong>
  <a href="javascript:void(0)" class="closebtn" onclick="closeMyQuickCart()"><i class="pe-7s-close pe-3x"></i></a>
  <div class="container-fluid mymaxwidth pt-3">
<?php 
////////////////////////////////// QUICK CART //////////////////////////////////
$amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']);
if($amcnkCart_count>0)
{
$amcnkCart_total_amount = 0;
$amcnkCart_total_consigne = 0;
for($i = 0; $i < $amcnkCart_count; $i++) 
{ 
$piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
$piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
?>
    <?php 
    $query_piece = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_NAME, 
    PIECE_PM_ID, PM_NAME, 
    (PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, (PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC, 
    PIECE_HAS_IMG, PIECE_SORT 
    FROM PIECES 
    JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
    JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
    WHERE PIECE_ID = $piece_id_this AND PIECE_DISPLAY = 1 
    ORDER BY PIECE_SORT";
    $request_piece = $conn->query($query_piece);
    if ($request_piece->num_rows > 0) 
    {
	    $result_piece = $request_piece->fetch_assoc();
	    // PHOTO
		if($result_piece['PIECE_HAS_IMG']==1)
		{
			$query_item_list_img = "SELECT CONCAT('rack/',PMI_FOLDER,'/',PMI_NAME,'.webp') AS PIECE_IMG 
				FROM PIECES_MEDIA_IMG WHERE PMI_PIECE_ID = $piece_id_this AND PMI_DISPLAY = 1";
			$request_item_list_img = $conn->query($query_item_list_img);
			if($request_item_list_img->num_rows)
			{
				$result_item_list_img = $request_item_list_img->fetch_assoc();
				$photo_link = $result_item_list_img['PIECE_IMG'];
			}
			else
			{
	            $photo_link = "upload/articles/no.png";
			}
		}
		else
		{
	        $photo_link = "upload/articles/no.png";
		}
	    ?>
	    <div class="row">
	    <div class="col-3 align-self-center pr-0">
	    	<img src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" class="img-fluid lazy mw-100" />
	    </div>
		<div class="col-9 align-self-center">  
		    <b><?php echo $result_piece['PIECE_NAME']; ?><br><?php echo $result_piece['PM_NAME']; ?> 
		    réf <?php echo $result_piece['PIECE_REF']; ?></b><br>
		    <span>
		    	<?php echo $piece_qte_this; ?> x <?php echo number_format($result_piece['PVTTC'], 2, '.', ' '); ?> 
		    	<?php echo $GlobalSiteCurrencyChar; ?>
		    </span>
		    <?php 
	        if($result_piece['PCTTC']>0)
	        {
	            ?>
	            <br><i>+ Consigne de <?php echo number_format($result_piece['PCTTC'], 2, '.', ''); ?> <?php echo $GlobalSiteCurrencyChar; ?> TTC</i>
	            <?php
	        }
	        ?>
	    </div>
	    </div>
		<?php
    $amcnkCart_total_amount = $amcnkCart_total_amount + ($result_piece['PVTTC']*$piece_qte_this);
    $amcnkCart_total_consigne = $amcnkCart_total_consigne + ($result_piece['PCTTC']*$piece_qte_this);
	}
    $amcnkCart_total = $amcnkCart_total_amount+$amcnkCart_total_consigne;
}
?>
		<div class="row">
		<div class="col-12 pb-3 pt-3">
		Sous Total TTC : 
		<b><?php echo number_format($amcnkCart_total_amount, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?></b>
		<?php
		if($amcnkCart_total_consigne>0)
		{
		?>
		<br>Consigne &nbsp;TTC :
		<b><?php echo number_format($amcnkCart_total_consigne, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?></b>
		<?php
		}
		?>
		</div>
		<div class="col-12 pt-2">
			<button class="mycartcontinue mb-1" onclick="closeMyQuickCart()" >Continuer mes achats</button>
	    	<button class="mycartvaliate" onclick="window.location.href='<?php echo $domain; ?>/panier.html'">valider ma commande</button>
		</div>
		</div>
<?php
}
else
{
?>
		<div class="row">
	    <div class="col-12 text-center">
	    	Votre panier est vide
	    </div>
		<div class="col-12 pt-2">
			<button class="mycartcontinue mb-1" onclick="closeMyQuickCart()" >Continuer mes achats</button>
		</div>
	    </div>
<?php
}
////////////////////////////////// QUICK CART //////////////////////////////////
?>
  </div>
</div>

<div id="myquicksearch" class="myquicksearchcontent 100vh">
  <a href="javascript:void(0)" class="closebtn" onclick="closeMyQuickSearch()"><i class="pe-7s-close"></i></a>
  <div class="container-fluid myquicksearchcontentin">
	
		<div class="row">
			<div class="col-12 pt-3 pb-3">
				Recherche par référence
			</div>
			<div class="col-12 pt-3">
				
				<form  action="<?php echo $domain; ?>/find/"  method="POST" role="form">
				<div class="row">
					<div class="col-9 col-sm-10 pr-0">
						<input type="text" name="quest" id="quest" placeholder="Réf. d'origine ou commercial de votre pièce" autocomplete="off" />
					</div>
					<div class="col-3 col-sm-2">
						<input type="submit" class="submit" value="" />
					</div>
				</div>
				</form>	

			</div>
		</div>

  </div>
</div>