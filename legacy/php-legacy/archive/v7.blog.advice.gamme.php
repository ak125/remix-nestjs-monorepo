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
<!-- favicon -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.blog.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('v7.blog.header.section.php');
?>

<div class="container-fluid containerBanner">
    <div class="container-fluid mymaxwidth">

    	<div class="row">
			<div class="col-12">

				<div class="containerariane">
					<?php
					// fichier de recuperation et d'affichage des parametres de la base de données
					require_once('config/ariane.conf.php');
					?>
				</div>

			</div>
		</div>

    </div>
</div>

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-md-8 text-left itemdata">

				<div class="row">
					<div class="col-12 pb-3 text-center">

						<img data-src="<?php echo $ba_wall_link; ?>" src="/upload/loading-min.gif"
						alt="<?php echo $ba_h1; ?>"
						class="mw-100  img-fluid lazy" />
						<h1><?php echo $pageh1; ?></h1>
						<p class="date">
						Publié le <?php echo date_format(date_create($result_item['BA_CREATE']), 'd/m/Y à H:i'); ?>
						&nbsp; | &nbsp; 
						Modifié le <?php echo date_format(date_create($result_item['BA_UPDATE']), 'd/m/Y à H:i'); ?>
						</p>

					</div>
					<div class="col-12 pb-3">

						<p><?php echo $pagecontent; ?></p>

					</div>
					<?php 
					if(($result_item['BA_CTA_LINK']!=NULL)&&($result_item['BA_CTA_LINK']!=''))
					{
					?>
						<div class="col-12 pb-3 text-center">
						<a class="buyNow" target="_blank" href="<?php echo $result_item['BA_CTA_LINK']; ?>"><i class="pe-7s-cart"></i><?php echo $result_item['BA_CTA_ANCHOR']; ?><br>maintenant</a>
						</div>
					<?php 
					}
					?>
					<div class="col-12 pb-3">

						<p class="sommary">
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
						<a href="<?php echo $this_ba2_marker_get; ?>"><?php echo $result_h2['BA2_H2']; ?></a><br>
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
								&nbsp; &nbsp; &nbsp; &nbsp; <a href="<?php echo $this_ba3_marker_get; ?>"><?php echo $result_h3['BA3_H3']; ?></a><br>
								<?php
							}
							}
						}
						}
						?>
						</p>

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
						<div class="col-12">

							<h2 id="<?php echo $this_ba2_marker; ?>"><?php echo $result_h2['BA2_H2']; ?></h2>
							<div class="divh2"></div>

						</div>
						<div class="col-12 pb-3">
							<?php
							if($ba2_wall!="no.jpg")
							{
							?>
							<img src="<?php echo $ba2_wall_link; ?>" alt="<?php echo $result_h2['BA2_H2']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
							<?php
							}
							?>
							<p><?php echo strip_tags($result_h2['BA2_CONTENT'],'<a><b><ul><li><br>'); ?></p>
							<?php 
							if(($result_h2['BA2_CTA_LINK']!=NULL)&&($result_h2['BA2_CTA_LINK']!=''))
							{
							?>
							<center><a class="buyNow" target="_blank" href="<?php echo $result_h2['BA2_CTA_LINK']; ?>"><i class="pe-7s-cart"></i><?php echo $result_h2['BA2_CTA_ANCHOR']; ?><br>maintenant</a></center>
							<?php 
							}
							?>
						</div>

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
							<div class="col-12">

								<h3 id="<?php echo $this_ba3_marker; ?>"><?php echo $result_h3['BA3_H3']; ?></h3>

							</div>
							<div class="col-12 pb-3">
								<?php
								if($ba3_wall!="no.jpg")
								{
								?>
								<img src="<?php echo $ba3_wall_link; ?>" alt="<?php echo $result_h3['BA3_H3']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
								<?php
								}
								?>
								<p><?php echo strip_tags($result_h3['BA3_CONTENT'],'<a><b><ul><li><br>'); ?></p>
								<?php 
								if(($result_h3['BA3_CTA_LINK']!=NULL)&&($result_h3['BA3_CTA_LINK']!=''))
								{
								?>
								<center><a class="buyNow" target="_blank" href="<?php echo $result_h3['BA3_CTA_LINK']; ?>"><i class="pe-7s-cart"></i><?php echo $result_h3['BA3_CTA_ANCHOR']; ?><br>maintenant</a></center>
								<?php 
								}
								?>
							</div>
						<?php 
						}
						} 
						?>

					<?php 
					}
					} 
					?>
				</div>

			</div>
			<div class="col-md-4">

				<?php
				//require_once('v7.blog.side.section.php');
				?>
				<div class="container-fluid sideBars">
				<?php
				$query_side = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_CREATE, BA_UPDATE, 
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
				$request_side = $conn->query($query_side);
				if ($request_side->num_rows > 0) 
				{
				?>
				<div class="row">
					<div class="col-12">

						<h2>On vous propose</h2>
						<div class="sideh2"></div>

					</div>
					<div class="col-12">

						<div class="row">
						<?php
						while($result_side = $request_side->fetch_assoc())
						{
							$this_ba_id = $result_side['BA_ID'];
							$this_ba_h1 = $result_side['BA_H1'];
							$this_ba_alias = $result_side['BA_ALIAS'];
							$this_pg_name_site = $result_side['PG_NAME'];
							$this_pg_alias = $result_side['PG_ALIAS'];
							?>
							<?php
							// photo article blog
							$this_ba_wall = $result_side['BA_WALL'];
							if($this_ba_wall=="no.jpg")
							{
							// image standard de la gamme
							if($isMacVersion == false)
							{
								$this_pg_img = $result_side['PG_IMG'];
							}
							else
							{
								$this_pg_img = str_replace(".webp",".jpg",$result_side['PG_IMG']);
							}
							$this_ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
							}
							else
							{
							// image de l'article
							$this_ba_wall_link = $domain."/upload/blog/conseils/mini/".$this_ba_wall;
							}
							?>
							<div class="col-4 itemsBloc">

								<img data-src="<?php echo $this_ba_wall_link; ?>" src="/upload/loading-min.gif"
									alt="<?php echo $this_ba_h1; ?>"
									width="400" height="250" 
									class="mw-100 img-fluid lazy" />

							</div>
							<div class="col-8 itemsBloc">

								<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>" class="sideread"><?php echo $this_ba_h1; ?></a>
								<b><?php echo $this_pg_name_site; ?></b><br>
								<i>Publié le <?php echo date_format(date_create($result_side['BA_UPDATE']), 'd/m/Y'); ?></i>

							</div>
							<?php
						}
						?>
						</div>

					</div>
				</div>
				<?php
				}
				?>
				</div>

			</div>
		</div>

	</div>
</div>

<?php
$query_cross_gamme_car = "SELECT DISTINCT CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_POWER_PS, 
	TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_YEAR_TO, 
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
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Les <?php echo $pg_name_site; ?> des véhicules les plus vendus</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="McCgcCar"  data-interval="1000">
				<div class="MultiCarousel-inner">

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
						if(empty($result_cross_gamme_car['TYPE_YEAR_TO']))
					    {
					    $this_type_date="du ".$result_cross_gamme_car['TYPE_MONTH_FROM']."/".$result_cross_gamme_car['TYPE_YEAR_FROM'];
					    }
					    else
					    {
					    $this_type_date="de ".$result_cross_gamme_car['TYPE_YEAR_FROM']." à ".$result_cross_gamme_car['TYPE_YEAR_TO'];
					    }
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
						//$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
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

								$LinkGammeCar_pg_id_link_full=$LinkGammeCar_pg_id_value." les ".$pg_name_site." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch et ".$LinkGammeCar_pg_id_value_2;

								$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
							}
						/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
						}
						else
						{
							//$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
							$PrixPasCher[$PrixPasCherTab] = "";
							$addon_title_seo_gamme_car = $this_type_nbch." ch ".$this_type_date;
				    		$addon_content_seo_gamme_car = "Achetez ".$pg_name_meta." ".$this_marque_name_meta." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch ".$this_type_date.", d'origine à prix bas.";			
				    	}	
						?>
						<div class="item">
							<div class="pad15">

								<div class="container-fluid multicarouselgrayforgrayBloc">
									<img data-src="<?php echo $this_modele_group_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_marque_name_meta.' '.$result_cross_gamme_car['MODELE_NAME']; ?>" width="270" height="135" class="w-100 img-fluid lazy anim"/>
									<p>
										<b><?php echo $pg_name_site." ".$PrixPasCher[$PrixPasCherTab]." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site.", ".$addon_title_seo_gamme_car; ?></b>
										<br>
										<?php echo content_cleaner($addon_content_seo_gamme_car); ?>.
									</p>
								</div>

							</div>
						</div>
					<?php
					}
					?>
				</div>
		        <span class="leftLst"><</span>
		        <span class="rightLst">></span>
				</div>

			</div>
		</div>

    </div>
</div>
<?php
}
?>

<?php
require_once('v7.global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="<?php echo $domain; ?>/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
</script>
<script type="text/javascript">
$(document).ready(function () {
var itemsMainDiv = ('.MultiCarousel');
var itemsDiv = ('.MultiCarousel-inner');
var itemWidth = "";
$('.leftLst, .rightLst').click(function () {
var condition = $(this).hasClass("leftLst");
if (condition)
click(0, this);
else
click(1, this)
});
ResCarouselSize();
$(window).resize(function () {
ResCarouselSize();
});
//this function define the size of the items
function ResCarouselSize() {
var incno = 0;
var dataItems = ("data-items");
var itemClass = ('.item');
var id = 0;
var btnParentSb = '';
var itemsSplit = '';
var sampwidth = $(itemsMainDiv).width();
var bodyWidth = $('body').width();
$(itemsDiv).each(function () {
id = id + 1;
var itemNumbers = $(this).find(itemClass).length;
btnParentSb = $(this).parent().attr(dataItems);
itemsSplit = btnParentSb.split(',');
$(this).parent().attr("id", "MultiCarousel" + id);
if (bodyWidth >= 1200) {
incno = itemsSplit[3];
itemWidth = sampwidth / incno;
}
else if (bodyWidth >= 992) {
incno = itemsSplit[2];
itemWidth = sampwidth / incno;
}
else if (bodyWidth >= 768) {
incno = itemsSplit[1];
itemWidth = sampwidth / incno;
}
else {
incno = itemsSplit[0];
itemWidth = sampwidth / incno;
}
$(this).css({ 'transform': 'translateX(0px)', 'width': itemWidth * itemNumbers });
$(this).find(itemClass).each(function () {
$(this).outerWidth(itemWidth);
});
$(".leftLst").addClass("over");
$(".rightLst").removeClass("over");
});
}
//this function used to move the items
function ResCarousel(e, el, s) {
var leftBtn = ('.leftLst');
var rightBtn = ('.rightLst');
var translateXval = '';
var divStyle = $(el + ' ' + itemsDiv).css('transform');
var values = divStyle.match(/-?[\d\.]+/g);
var xds = Math.abs(values[4]);
if (e == 0) {
translateXval = parseInt(xds) - parseInt(itemWidth * s);
$(el + ' ' + rightBtn).removeClass("over");
if (translateXval <= itemWidth / 2) {
translateXval = 0;
$(el + ' ' + leftBtn).addClass("over");
}
}
else if (e == 1) {
var itemsCondition = $(el).find(itemsDiv).width() - $(el).width();
translateXval = parseInt(xds) + parseInt(itemWidth * s);
$(el + ' ' + leftBtn).removeClass("over");
if (translateXval >= itemsCondition - itemWidth / 2) {
translateXval = itemsCondition;
$(el + ' ' + rightBtn).addClass("over");
}
}
$(el + ' ' + itemsDiv).css('transform', 'translateX(' + -translateXval + 'px)');
}
//It is used to get some elements from btn
function click(ell, ee) {
var Parent = "#" + $(ee).parent().attr("id");
var slide = $(Parent).attr("data-slide");
ResCarousel(ell, Parent, slide);
}
});
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/v7.analytics.track.php');
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