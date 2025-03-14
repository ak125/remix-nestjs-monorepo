<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$pg_alias=$_GET["pg_alias"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT PG_ID, PG_DISPLAY 
	FROM PIECES_GAMME 
	WHERE PG_ALIAS = '$pg_alias'";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['PG_DISPLAY']==1)
	{
	$pg_id = $result_selector['PG_ID'];
	if($pg_id==620)
	{
		$linkRedirect = $domain."/blog-pieces-auto/conseils/emetteur-d-embrayage";
		header("Status: 301 Moved Permanently", false, 301);
		header("Location: ".$linkRedirect);
		exit();
	}
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_item = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_CONTENT, BA_CTA_ANCHOR, BA_CTA_LINK,  
	BA_WALL, BA_CREATE, BA_UPDATE, BA_TITLE, BA_DESCRIP, BA_KEYWORDS, 
	PG_NAME, PG_ALIAS, PG_NAME_META, PG_RELFOLLOW, PG_PIC, PG_IMG, PG_WALL, PG_WALL, 
	MF_ID, MF_NAME, MF_NAME_META
	FROM __BLOG_ADVICE
	JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
	INNER JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	INNER JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
	WHERE BA_PG_ID = $pg_id 
	ORDER BY BA_UPDATE DESC, BA_CREATE DESC LIMIT 1";
$request_item = $conn->query($query_item);
$result_item = $request_item->fetch_assoc();
	// PG DATA
	$pg_name_site = $result_item['PG_NAME'];
	$pg_name_meta = $result_item['PG_NAME_META'];
    $pg_alias = $result_item["PG_ALIAS"];
    $pg_relfollow = $result_item["PG_RELFOLLOW"];
    // MF DATA
    $mf_id = $result_item["MF_ID"];
    $mf_name_site = $result_item["MF_NAME"];
    $mf_name_meta = $result_item["MF_NAME_META"];
    // ITEM DATA
    $ba_id = $result_item['BA_ID'];
	$ba_h1 = $result_item['BA_H1'];
	$ba_alias = $result_item['BA_ALIAS'];
	$ba_preview = $result_item['BA_PREVIEW'];
	$ba_content = $result_item['BA_CONTENT'];
    // WALL
    $ba_wall = $result_item['BA_WALL'];
		if($ba_wall=="no.jpg")
		{
			// image standard de la gamme
			if($isMacVersion == false)
			{
				$this_pg_img = $result_item['PG_IMG'];
			}
			else
			{
				$this_pg_img = str_replace(".webp",".jpg",$result_item['PG_IMG']);
			}
		$ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
		$ba_wall_link_social = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
		}
		else
		{
		// image de l'article
		$ba_wall_link = $domain."/upload/blog/conseils/large/".$ba_wall;
		$ba_wall_link_social = $domain."/upload/blog/conseils/medium/".$ba_wall;
		}
    // SEO & CONTENT
		// META
		$pagetitle = $result_item['BA_TITLE'];
        $pagedescription = $result_item['BA_DESCRIP'];
        $pagekeywords = $result_item["BA_KEYWORDS"];
        // CONTENT
        $pageh1 = $ba_h1;
        $pagecontent = strip_tags($ba_content);	
	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
		$pagecontent = content_cleaner($pagecontent);

	// ROBOT
    $relfollow = 1;
    /*if($pg_relfollow==1)
  	{
  		$pageRobots="index, follow";
    	$relfollow = 1;
  	}
  	else
  	{
  		$pageRobots="noindex, nofollow";
    	$relfollow = 0;
  	}*/
  	$pageRobots="index, follow";
    $relfollow = 1;
  	// CANONICAL
  	$canonicalLink = $domain."/".$blog."/".$entretien."/".$pg_alias;
		// NOT CANONICAL TO NOINDEX
		if ($relfollow == 1) 
		{
			$accessLink = $domain.$_SERVER['REQUEST_URI'];
			if (strcmp($canonicalLink, $accessLink) !== 0) 
			{
				$pageRobots="noindex, follow";
			}
		}
	// ARIANE
	$parent_arianelink = $entretien;
	$parent_arianetitle = $entretien_title;
	$arianetitle = $pg_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="gamme";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="title" content="<?php  echo $pagetitle; ?>" />
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="keywords" content="<?php  echo $pagekeywords; ?>"/>
<meta name="robots" content="<?php echo $pageRobots; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Social network -->
<meta property="og:title" content="<?php  echo $pageh1; ?>">
<meta property="og:image" content="<?php echo $ba_wall_link_social; ?>">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/style.blog.advice.gamme.min.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('blog.global.header.section.php');
?>

<div class="container-fluid containerPage">
<div class="container-fluid containerinPage">

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-sm-8 col-lg-9 d-none d-md-block">
	<!-- COL LEFT -->
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('config/ariane.conf.php');
		?>
	<!-- / COL LEFT -->
	</div>
	<div class="col-sm-4 col-lg-3 text-right">
	<!-- COL RIGHT -->
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('global.social.share.php');
		?>
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-md-8 col-lg-9">
	<!-- COL LEFT -->

			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh1">
					<h1><?php echo $pageh1; ?></h1>
					Publié le <?php echo date_format(date_create($result_item['BA_CREATE']), 'd/m/Y à H:i'); ?>
					&nbsp; | &nbsp; 
					Modifié le <?php echo date_format(date_create($result_item['BA_UPDATE']), 'd/m/Y à H:i'); ?>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				<p class="text-justify contenth2"><?php echo $pagecontent; ?></p>
			</div>
			<div class="col-12 pb-3 text-center">
				<img data-src="<?php echo $ba_wall_link; ?>" src="/upload/loading-min.gif"
				alt="<?php echo $ba_h1; ?>"
				class="mw-100 lazy" />
			</div>
			<div class="container-fluid p-3 text-center">
				<?php 
				if(($result_item['BA_CTA_LINK']!=NULL)&&($result_item['BA_CTA_LINK']!=''))
				{
				?>
				<center>
				<?php $thislinktoPage = $result_item['BA_CTA_LINK']; ?>
				<span class="qcd" target="_blank" data-qcd="<?php echo base64_encode($thislinktoPage); ?>"><?php echo $result_item['BA_CTA_ANCHOR']; ?><br>maintenant</span>
				</center>
				<?php 
				}
				?>
			</div>
			<div class="col-12 pt-3 pb-3">
				<?php
				$query_h2 = "SELECT *
						FROM __BLOG_ADVICE_H2
						WHERE BA2_BA_ID = $ba_id
						ORDER BY BA2_ID ASC";
				$request_h2 = $conn->query($query_h2);
				if ($request_h2->num_rows > 0) 
				{
				while($result_h2 = $request_h2->fetch_assoc())
				{
				$this_ba2_id = $result_h2['BA2_ID'];
				$this_ba2_marker_get = $canonicalLink."#".url_title($result_h2['BA2_H2']);
				?>
				<a class="sommary" href="<?php echo $this_ba2_marker_get; ?>"><?php echo $result_h2['BA2_H2']; ?></a><br>
				<?php
					$query_h3 = "SELECT *
							FROM __BLOG_ADVICE_H3
							WHERE BA3_BA2_ID = $this_ba2_id
							ORDER BY BA3_ID ASC";
					$request_h3 = $conn->query($query_h3);
					if ($request_h3->num_rows > 0) 
					{
					while($result_h3 = $request_h3->fetch_assoc())
					{
						$this_ba3_marker_get = $canonicalLink."#".url_title($result_h3['BA3_H3']);
						?>
						&nbsp; &nbsp; &nbsp; &nbsp; <a class="sommary" href="<?php echo $this_ba3_marker_get; ?>"><?php echo $result_h3['BA3_H3']; ?></a><br>
						<?php
					}
					}
				}
				}
				?>
			</div>
			</div>
			<?php
			$query_h2 = "SELECT *
					FROM __BLOG_ADVICE_H2
					WHERE BA2_BA_ID = $ba_id
					ORDER BY BA2_ID ASC";
			$request_h2 = $conn->query($query_h2);
			if ($request_h2->num_rows > 0) 
			{
			while($result_h2 = $request_h2->fetch_assoc())
			{
			$ba2_id = $result_h2['BA2_ID'];
			// WALL
		    $ba2_wall = $result_h2['BA2_WALL'];
			// image H2
			$ba2_wall_link = $domain."/upload/blog/conseils/large/".$ba2_wall;
			//SOMMAIRE
			$this_ba2_marker = url_title($result_h2['BA2_H2']);
			?>
			<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
			<div class="row" id="<?php echo $this_ba2_marker; ?>">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2">
					<h2><?php echo $result_h2['BA2_H2']; ?></h2>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				
				<p class="text-justify contenth2">
					<?php
					if($ba2_wall!="no.jpg")
					{
					?>
					<img src="<?php echo $ba2_wall_link; ?>" alt="<?php echo $result_h2['BA2_H2']; ?>" class= "w-100" />
					<br>
					<br>
					<?php
					}
					?>
					<?php echo strip_tags($result_h2['BA2_CONTENT'],'<a><b><ul><li><br>'); ?>
				</p>

				<?php 
				if(($result_h2['BA2_CTA_LINK']!=NULL)&&($result_h2['BA2_CTA_LINK']!=''))
				{
				?>
				<center>
				<?php $thislinktoPage = $result_h2['BA2_CTA_LINK']; ?>
				<span class="qcd" data-qcd="<?php echo base64_encode($thislinktoPage); ?>"><?php echo $result_h2['BA2_CTA_ANCHOR']; ?><br>maintenant</span>
				</center>
				<?php 
				}
				?>

				<?php
				$query_h3 = "SELECT *
						FROM __BLOG_ADVICE_H3
						WHERE BA3_BA2_ID = $ba2_id
						ORDER BY BA3_ID ASC";
				$request_h3 = $conn->query($query_h3);
				if ($request_h3->num_rows > 0) 
				{
				while($result_h3 = $request_h3->fetch_assoc())
				{
				// WALL
			    $ba3_wall = $result_h3['BA3_WALL'];
				// image H2
				$ba3_wall_link = $domain."/upload/blog/conseils/large/".$ba3_wall;
				//SOMMAIRE
				$this_ba3_marker = url_title($result_h3['BA3_H3']);
				?>
						<h3 class="blog" id="<?php echo $this_ba3_marker; ?>"><?php echo $result_h3['BA3_H3']; ?></h3>
						<p class="text-justify contenth3">
						<?php
						if($ba3_wall!="no.jpg")
						{
						?>
						<img src="<?php echo $ba3_wall_link; ?>" alt="<?php echo $result_h3['BA3_H3']; ?>" class="w-100" />
						<br>
						<br>
						<?php
						}
						?>
						<?php echo strip_tags($result_h3['BA3_CONTENT'],'<a><b><ul><li><br>'); ?>
						</p>

						<?php 
						if(($result_h3['BA3_CTA_LINK']!=NULL)&&($result_h3['BA3_CTA_LINK']!=''))
						{
						?>
				<center>
				<?php $thislinktoPage = $result_h3['BA3_CTA_LINK']; ?>
				<span class="qcd" data-qcd="<?php echo base64_encode($thislinktoPage); ?>"><?php echo $result_h3['BA3_CTA_ANCHOR']; ?><br>maintenant</span>
				</center>
						<?php 
						}
						?>
				<?php
				}
				}
				?>

			</div>
			</div>
			<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
			<?php
			}
			}
			?>

	<!-- / COL LEFT -->
	</div>
	<div class="col-md-4 col-lg-3">
	<!-- COL RIGHT -->
			<?php
			$query_cross = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_CREATE, BA_UPDATE, 
				PG_NAME, PG_ALIAS, PG_PIC, PG_IMG, PG_WALL
				FROM __BLOG_ADVICE_CROSS
				JOIN __BLOG_ADVICE ON BA_ID = BAC_BA_ID_CROSS
				JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
				INNER JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
				INNER JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
				WHERE BAC_BA_ID = $ba_id 
				AND BA_ID != $ba_id
				AND BA_PG_ID > 0
				ORDER BY MC_SORT";
			$request_cross = $conn->query($query_cross);
			if ($request_cross->num_rows > 0) 
			{
			?>
			<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2panel">
					<h2>on vous propose</h2>
				</span>
			</div>
			</div>
			<div class="row p-0 m-0">
				<?php
				while($result_cross = $request_cross->fetch_assoc())
				{
					$this_ba_id = $result_cross['BA_ID'];
					$this_ba_h1 = $result_cross['BA_H1'];
					$this_ba_alias = $result_cross['BA_ALIAS'];
					$this_ba_preview = $result_cross['BA_PREVIEW'];
					$this_pg_name_site = $result_cross['PG_NAME'];
					$this_pg_alias = $result_cross['PG_ALIAS'];
					?>
					<?php
					// photo article blog
					$this_ba_wall = $result_cross['BA_WALL'];
					if($this_ba_wall=="no.jpg")
					{
					// image standard de la gamme
					if($isMacVersion == false)
					{
						$this_pg_img = $result_cross['PG_IMG'];
					}
					else
					{
						$this_pg_img = str_replace(".webp",".jpg",$result_cross['PG_IMG']);
					}
					$this_ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
					}
					else
					{
					// image de l'article
					$this_ba_wall_link = $domain."/upload/blog/conseils/mini/".$this_ba_wall;
					}
					?>
					<div class="col-12 blocToBlogItemDark">

						<div class="container-fluid p-3 pb-0">
							<i><?php echo $this_ba_h1; ?></i>
							<br>
							<b><?php echo $this_pg_name_site; ?></b>
							<span> | <?php echo date_format(date_create($result_cross['BA_UPDATE']), 'd/m/Y'); ?></span>
						</div>
						<div class="container-fluid p-3 mh167 text-center">
							<img data-src="<?php echo $this_ba_wall_link; ?>" src="/upload/loading-min.gif" 
							alt="<?php echo $this_ba_h1; ?>"
							class="w-100 lazy" />
						</div>
						<div class="container-fluid regularContentDark p-3">
							<?php echo content_cleaner($this_ba_preview); ?>
						</div>
						<div class="container-fluid regularContentBordered text-center pb-3">
							<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>" class="blog-read-more">Lire plus</a>
						</div>

					</div>
					<?php
				}
				?>
			</div>
			<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
			<?php
			}
			?>
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<style type="text/css">
	.mh57{ min-height: 57px; }
	.mh127{ min-height: 127px; }
	.mh167{ min-height: 167px; }
	.mh187{ min-height: 187px; }
	.mh237{ min-height: 237px; }
	.mh257{ min-height: 257px; }
	.mh277{ min-height: 277px; }
	.blocToPCar{ background: #fafbfc; border: 4px solid #fff; border-bottom: 11px solid #fff; padding: 0px; }
	.blocToPCar span{ font-size: 20px; font-weight: 400; text-align: justify; }
	</style>
	<?php
	$query_cross_gamme_car = "SELECT DISTINCT CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_POWER_PS, 
		MODELE_ID, MODELE_ALIAS, MODELE_NAME, MODELE_PIC, 
		MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META    
		FROM __CROSS_GAMME_CAR_NEW 
		JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
		WHERE CGC_PG_ID = $pg_id 
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1
		AND CGC_LEVEL = 1 
		GROUP BY TYPE_MODELE_ID 
		ORDER BY CGC_ID, MODELE_NAME, TYPE_NAME";
	$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
	if ($request_cross_gamme_car->num_rows > 0) 
	{
	?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2">
					<h2>Les <?php echo $pg_name_site; ?> des véhicules les plus vendus</h2>
				</span>
			</div>
	</div>
	<div class="row p-0 m-0">
		<?php
		while($result_cross_gamme_car = $request_cross_gamme_car->fetch_assoc())
		{
			$this_type_id = $result_cross_gamme_car['CGC_TYPE_ID'];
			$this_marque_name_site = $result_cross_gamme_car['MARQUE_NAME'];
			$this_marque_name_meta = $result_cross_gamme_car['MARQUE_NAME_META'];
			$this_marque_alias = $result_cross_gamme_car['MARQUE_ALIAS'];
			$this_marque_id = $result_cross_gamme_car['MARQUE_ID'];
			$this_modele_name_site = $result_cross_gamme_car['MODELE_NAME'];
			$this_modele_alias = $result_cross_gamme_car['MODELE_ALIAS'];
			$this_modele_id = $result_cross_gamme_car['MODELE_ID'];
			$this_type_name_site = $result_cross_gamme_car['TYPE_NAME'];
			$this_type_alias = $result_cross_gamme_car['TYPE_ALIAS'];
			$this_type_date = "";
			$this_type_nbch = $result_cross_gamme_car['TYPE_POWER_PS'];
			if(empty($result_cross_gamme_car['MODELE_PIC']))
			{
			  $this_modele_group_pic = $domain."/upload/constructeurs-automobiles/marques-modeles/no.png";
			}
			else
			{
				if($isMacVersion == false)
				{
					$this_modele_img = $result_cross_gamme_car['MODELE_PIC'];
				}
				else
				{
					$this_modele_img = str_replace(".webp",".jpg",$result_cross_gamme_car['MODELE_PIC']);
				}
				$this_modele_group_pic = $domain."/upload/constructeurs-automobiles/marques-modeles/".$this_marque_alias."/".$this_modele_img;
			}
			// LINK TO CAR
			$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
			// SEO TITLE ET DESCRIPTION DE LA PAGE P
			$query_seo_gamme_car = "SELECT SGC_TITLE, SGC_DESCRIP   
				FROM __SEO_GAMME_CAR 
				WHERE SGC_PG_ID = $pg_id 
				ORDER BY SGC_ID DESC LIMIT 1";
			$request_seo_gamme_car = $conn->query($query_seo_gamme_car);
			if ($request_seo_gamme_car->num_rows > 0) 
			{
			$result_seo_gamme_car = $request_seo_gamme_car->fetch_assoc();
			$addon_title_seo_gamme_car="#CompSwitchTitle#";
			$addon_content_seo_gamme_car=strip_tags($result_seo_gamme_car['SGC_DESCRIP']);
			/////////////////////////////// addon_content_seo_gamme_car //////////////////////////////////////
			// Changement des variables standards
				$addon_content_seo_gamme_car=str_replace("#Gamme#",$pg_name_site,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#VMarque#",$this_marque_name_site,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#VModele#",$this_modele_name_site,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#VType#",$this_type_name_site,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#VAnnee#",$this_type_date,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#VNbCh#",$this_type_nbch,$addon_content_seo_gamme_car);
		        $addon_content_seo_gamme_car=str_replace("#MinPrice#","",$addon_content_seo_gamme_car);
		    // Changement #CompSwitch_3_PG_ID#
		        $comp_switch_3_pg_id_marker="#CompSwitch_3_".$pg_id."#";
				$comp_switch_3_pg_id_value="";
				if(strpos($addon_content_seo_gamme_car, $comp_switch_3_pg_id_marker))
				{
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 3";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$comp_switch_3_pg_id_debut=($this_type_id+$pg_id+3) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 3 
							ORDER BY SGCS_ID LIMIT $comp_switch_3_pg_id_debut,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$comp_switch_3_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}
					$addon_content_seo_gamme_car=str_replace($comp_switch_3_pg_id_marker,$comp_switch_3_pg_id_value,$addon_content_seo_gamme_car);
				}
			// Changement #PrixPasCher#	
				$PrixPasCherTab=(($pg_id%100)+$this_type_id)%$PrixPasCherLength;
				$PrixPasCherTab2=(($pg_id%100)+$this_type_id+1)%$PrixPasCherLength;
		        $addon_content_seo_gamme_car=preg_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$addon_content_seo_gamme_car,1);
		        $addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab2],$addon_content_seo_gamme_car);
		    // Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
		        $comp_switch_marker_2="#CompSwitchTitle#";
				$comp_switch_value="";
				$comp_switch_value_2="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT SIS_ID   
						FROM __SEO_ITEM_SWITCH 
						WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 1";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = ($this_type_id+1) % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 1 
							ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
						// TITLE
						$comp_switch_debut_2 = $this_type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 1 
							ORDER BY SIS_ID LIMIT $comp_switch_debut_2,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value_2 = $result_seo_item_switch["SIS_CONTENT"];
					}
					$addon_content_seo_gamme_car=str_replace($comp_switch_marker,$comp_switch_value,$addon_content_seo_gamme_car);
					$addon_content_seo_gamme_car = content_cleaner($addon_content_seo_gamme_car);
					$addon_title_seo_gamme_car=str_replace($comp_switch_marker_2,$comp_switch_value_2,$addon_title_seo_gamme_car);
					$addon_title_seo_gamme_car = content_cleaner($addon_title_seo_gamme_car);
				//}
			// Changement #LinkGammeCar_PG_ID#
				$LinkGammeCar_pg_id_marker="#LinkGammeCar_".$pg_id."#";
				$LinkGammeCar_pg_id_value="";
				$LinkGammeCar_pg_id_value_2="";
				if(strstr($addon_content_seo_gamme_car, $LinkGammeCar_pg_id_marker))
				{
					// LinkGammeCar_PG_ID_1
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 1";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$LinkGammeCar_pg_id_debut=($this_type_id+$pg_id+2) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 1 
							ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$LinkGammeCar_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}
					// LinkGammeCar_PG_ID_2
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 2";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$LinkGammeCar_pg_id_debut_2=($this_type_id+$pg_id+3) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 2 
							ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut_2,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$LinkGammeCar_pg_id_value_2 = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}

					/*$LinkGammeCar_pg_id_link_full=$LinkGammeCar_pg_id_value." les <a href='".$LinkGammeCar_pg_id_link."'>".$pg_name_site." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch</a> et ".$LinkGammeCar_pg_id_value_2;*/


					$LinkGammeCar_pg_id_link_full=$LinkGammeCar_pg_id_value." les ".$pg_name_site." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch et ".$LinkGammeCar_pg_id_value_2;

					$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
				}
			/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
			}
			?>
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3 blocToPCar">

				<div class="container-fluid p-3 pb-0 mh127">
					<span><?php echo $pg_name_site." ".$PrixPasCher[$PrixPasCherTab]." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site.", ".$addon_title_seo_gamme_car; ?></span>
				</div>
				<?php
				if($isSmartPhoneVersion == false)
				{
				?>
				<div class="container-fluid p-3 mh167 text-center">
					<img data-src="<?php echo $this_modele_group_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_marque_name_meta.' '.$result_cross_gamme_car['MODELE_NAME']; ?>" width="270" height="135" class="w-100 img-fluid lazy"/>
				</div>
				<?php
				}
				?>
				<div class="container-fluid regularContentBordered p-3 mh187">
					<?php echo $addon_content_seo_gamme_car; ?>.
				</div>

			</div>
			<?php
		}
		?>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
	<?php
	}
	?>


</div>
</div>

<?php
require_once('global.footer.section.php');
?>

</body>
</html>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) {
	var classname = document.getElementsByClassName("qcd");
	for (var i = 0; i < classname.length; i++) {
		//click gauche
		classname[i].addEventListener('click', myFunction, false);
		//click droit
		classname[i].addEventListener('contextmenu', myRightFunction, false);
	}
});
//fonction du click gauche
var myFunction = function(event) {
	var attribute = this.getAttribute("data-qcd");               
			if(event.ctrlKey) {                   
				 var newWindow = window.open(decodeURIComponent(window.atob(attribute)), '_blank');                    
				 newWindow.focus();               
			} else {                    
				 document.location.href= decodeURIComponent(window.atob(attribute));
			}
	};
//fonction du click droit
var myRightFunction = function(event) {
	var attribute = this.getAttribute("data-qcd");               
		if(event.ctrlKey) {                   
			 var newWindow = window.open(decodeURIComponent(window.atob(attribute)), '_blank');                    
			 newWindow.focus();               
		} else {      
			 window.open(decodeURIComponent(window.atob(attribute)),'_blank');	
		}
} 
</script>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(r){function e(){for(var e,t,n=0;n<l.length;n++)e=l[n],0<=(t=e.getBoundingClientRect()).top&&0<=t.left&&t.top<=(r.innerHeight||document.documentElement.clientHeight)&&function(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}(l[n],function(){l.splice(n,n)})}for(var l=new Array,t=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,c=0,o=[],i=l.length;c<i;c++)l[c].currentStyle.f&&o.push(l[c]);r.removeRule(0),t=o}return t}("img.lazy"),n=0;n<t.length;n++)l.push(t[n]);e(),function(e,t){r.addEventListener?this.addEventListener(e,t,!1):r.attachEvent?this.attachEvent("on"+e,t):this["on"+e]=t}("scroll",e)}(this);
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.min.php');
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 200                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
	}
	else
	{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
412
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 412                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
	}
}
else
{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           410                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
410
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
?>