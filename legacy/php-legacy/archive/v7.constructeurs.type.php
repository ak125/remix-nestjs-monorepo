<?php 
session_start();
// parametres relatifs à la page
$typefile="level2";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$marque_id=$_GET["marque_id"];
$modele_id=$_GET["modele_id"];
$type_id=$_GET["type_id"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT TYPE_DISPLAY 
	FROM AUTO_TYPE 
	JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
	JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
	WHERE TYPE_ID = $type_id
	AND TYPE_MODELE_ID = $modele_id 
	AND TYPE_MARQUE_ID = $marque_id 
	AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['TYPE_DISPLAY']==1)
	{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_motorisation = "SELECT TYPE_ALIAS, TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, TYPE_BODY, TYPE_FUEL, 
	TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, TYPE_RELFOLLOW,  
	MODELE_NAME, MODELE_NAME_META, MODELE_ALIAS, MODELE_RELFOLLOW, 
	MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, MARQUE_RELFOLLOW, MARQUE_LOGO 
	FROM AUTO_TYPE 
	JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
	JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
	WHERE TYPE_ID = $type_id
	AND TYPE_MODELE_ID = $modele_id 
	AND TYPE_MARQUE_ID = $marque_id 
	AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1";
$request_motorisation = $conn->query($query_motorisation);
$result_motorisation = $request_motorisation->fetch_assoc();
	// MARQUE DATA
	$marque_name_site = $result_motorisation['MARQUE_NAME'];
	$marque_name_meta = $result_motorisation['MARQUE_NAME_META'];
	$marque_name_meta_title = $result_motorisation['MARQUE_NAME_META_TITLE'];
    $marque_alias = $result_motorisation["MARQUE_ALIAS"];
    $marque_relfollow = $result_motorisation["MARQUE_RELFOLLOW"];
    //$marque_logo = $result_motorisation["MARQUE_LOGO"];
    if($isMacVersion == false)
	{
		$marque_logo = $result_motorisation['MARQUE_LOGO'];
	}
	else
	{
		$marque_logo = str_replace(".webp",".png",$result_motorisation['MARQUE_LOGO']);
	}
    // MODELE DATA
	$modele_name_site = $result_motorisation['MODELE_NAME'];
	$modele_name_meta = $result_motorisation['MODELE_NAME_META'];
    $modele_alias = $result_motorisation["MODELE_ALIAS"];
    $modele_relfollow = $result_motorisation["MODELE_RELFOLLOW"];
    // TYPE DATA
    $type_name_site = $result_motorisation['TYPE_NAME'];
	$type_name_meta = $result_motorisation['TYPE_NAME_META'];
	$type_alias = $result_motorisation['TYPE_ALIAS'];
	$type_relfollow = $result_motorisation['TYPE_RELFOLLOW'];
	$type_date = "";
	if(empty($result_motorisation['TYPE_YEAR_TO']))
    {
    $type_date="du ".$result_motorisation['TYPE_MONTH_FROM']."/".$result_motorisation['TYPE_YEAR_FROM'];
    }
    else
    {
    $type_date="de ".$result_motorisation['TYPE_YEAR_FROM']." à ".$result_motorisation['TYPE_YEAR_TO'];
    }
	$type_nbch = $result_motorisation['TYPE_POWER_PS'];
	$type_carosserie = $result_motorisation['TYPE_BODY'];
	$type_fuel = $result_motorisation['TYPE_FUEL'];

    // SEO & CONTENT
      	//META
      	$pagetitle = "Pièces ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." #CompSwitch#";
      			// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
				$comp_switch_value="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT STS_ID   
						FROM __SEO_TYPE_SWITCH 
						WHERE STS_ALIAS = 1";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 1 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$pagetitle=str_replace($comp_switch_marker,$comp_switch_value,$pagetitle);
				//}
	    $pagedescription = "Catalogue pièces détachées pour ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date." neuves #CompSwitch#";
	    		// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
				$comp_switch_value="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT STS_ID   
						FROM __SEO_TYPE_SWITCH 
						WHERE STS_ALIAS = 2";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = ($type_id+1) % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 2 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$pagedescription=str_replace($comp_switch_marker,$comp_switch_value,$pagedescription);
				//}
	    $pagekeywords = $marque_name_meta.", ".$modele_name_meta.", ".$type_name_meta.", ".$type_nbch." ch, ".$type_date;
	    // CONTENT
	    //$pageh1 = $marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_fuel." ".$type_nbch." ch ".$type_date;
	    $pageh1 = $marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
		$pagecontent = "#CompSwitch_1# pour le modèle  <b>".$marque_name_site." ".$modele_name_site." ".$type_carosserie." </b> <strong>".$type_date."</strong> de motorisation <strong>".$type_name_site." ".$type_nbch."</strong> ch.";
				// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch_1#";
				$comp_switch_value="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT STS_ID   
						FROM __SEO_TYPE_SWITCH 
						WHERE STS_ALIAS = 10";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 10 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$pagecontent=str_replace($comp_switch_marker,$comp_switch_value,$pagecontent);
				//}
		$pagecontent2 = "#CompSwitch_2# du catalogue sont compatibles au modèle de la voiture <strong>".$marque_name_site." ".$modele_name_site." ".$type_name_site."</strong> que vous avez sélectionné. Choisissez les pièces correspondantes à votre recherche dans les gammes disponibles et choisissez un article proposé par #CompSwitch_3#.";
				// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch_2#";
				$comp_switch_value="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT STS_ID   
						FROM __SEO_TYPE_SWITCH 
						WHERE STS_ALIAS = 11";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 11 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$pagecontent2=str_replace($comp_switch_marker,$comp_switch_value,$pagecontent2);
				//}
					// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch_3#";
				$comp_switch_value="";
				//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
				//{
					$query_seo_item_switch = "SELECT STS_ID   
						FROM __SEO_TYPE_SWITCH 
						WHERE STS_ALIAS = 12";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// CONTENT
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 12 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$pagecontent2=str_replace($comp_switch_marker,$comp_switch_value,$pagecontent2);
				//}


	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
		$pagecontent = content_cleaner($pagecontent);
		$pagecontent2 = content_cleaner($pagecontent2);

	// ROBOT
    $relfollow = 1;
    if(($marque_relfollow==1)&&($modele_relfollow==1)&&($type_relfollow==1))
  	{
  		$pageRobots="index, follow";
    	$relfollow = 1;
    	// NB FAMILY
    	$query_count_family = "SELECT DISTINCT MC_MF_ID FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_DISPLAY = 1
			JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID AND PG_DISPLAY = 1 AND PIECES_GAMME.PG_LEVEL IN (1,2)
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			WHERE RTP_TYPE_ID = $type_id";
		$request_count_family = $conn->query($query_count_family);
		if ($request_count_family->num_rows < 3) 
		{
			$pageRobots="noindex, nofollow";
    		$relfollow = 0;
    	}
    	else
    	{
    	// NB GAMME
    		$query_count_gamme = "SELECT DISTINCT PG_ID FROM PIECES_RELATION_TYPE
				JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_DISPLAY = 1
				JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID AND PG_DISPLAY = 1 AND PIECES_GAMME.PG_LEVEL IN (1,2)
				WHERE RTP_TYPE_ID = $type_id";
			$request_count_gamme = $conn->query($query_count_gamme);
			if ($request_count_gamme->num_rows < 5) 
			{
				$pageRobots="noindex, nofollow";
	    		$relfollow = 0;
	    	}
    	}
  	}
  	else
  	{
  		$pageRobots="noindex, nofollow";
    	$relfollow = 0;
  	}
  	// CANONICAL
  	$canonicalLink = $domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
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
    $parent_arianelink = $Auto."/".$marque_alias."-".$marque_id.".html";
	$parent_arianetitle = $marque_name_site;
    $arianetitle = $modele_name_site." ".$type_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="constructeurs.type";
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
<!-- PRECFETCHING JS -->
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://ajax.googleapis.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="preload" as="font" href="/assets/fonts/Pe-icon-7-stroke.woff?d7yf1v" type="font/woff" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts-2/rubik-v14-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts-2/rubik-v14-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts-2/rubik-v14-latin-600.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts-2/rubik-v14-latin-500.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts-2/rubik-v14-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v10.fonts.min.css" rel="stylesheet" media="all">
<style>body {font-family:'Rubik',normal;}</style>
<link href="<?php echo $domain; ?>/assets/css/v10.style.ty.min.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('v7.global.header.section.php');
?>
<div class="container-fluid containerBanner">
    <div class="container-fluid mymaxwidth">

    	<div class="row d-flex flex-row-reverse">
			<div class="col-12 col-sm align-self-center">

				<h1><?php echo $pageh1; ?></h1>
				<div class="containerariane">
					<?php
					// fichier de recuperation et d'affichage des parametres de la base de données
					require_once('config/ariane.conf.php');
					?>
				</div>

			</div>
			<div class="col-12 col-sm containerSeekCarBox">

				<div class="container-fluid containerSeekCar">
				<div class="row">
				<div class="col-12 pb-3">
				Sélectionnez votre véhicule
				</div>
				<div class="col-12 col-sm-6 col-md-12 p-1 pb-0">
				<select name="form-marq" id="form-marq">
				<option value="0">Constructeur</option>
				<?php
				$query_get_car_marque = "SELECT MARQUE_ID, MARQUE_NAME, MARQUE_TOP   
				FROM AUTO_MARQUE 
				WHERE MARQUE_DISPLAY = 1 
				ORDER BY MARQUE_SORT";
				$request_get_car_marque = $conn->query($query_get_car_marque);
				if ($request_get_car_marque->num_rows > 0) 
				{
				while($result_get_car_marque = $request_get_car_marque->fetch_assoc())
				{
				$this_marque_id = $result_get_car_marque['MARQUE_ID'];
				$this_marque_name_site = $result_get_car_marque['MARQUE_NAME'];
				if($result_get_car_marque['MARQUE_TOP']==1)
				{
				?>
				<option value="<?php echo $this_marque_id; ?>" class="favorite"><?php echo $this_marque_name_site; ?></option>
				<?php
				}
				else
				{
				?>
				<option value="<?php echo $this_marque_id; ?>"><?php echo $this_marque_name_site; ?></option>
				<?php					
				}
				}
				}
				?>
				</select>
				</div>
				<div class="col-12 col-sm-6 col-md-12 p-1 pb-0">
				<select name="form-year" id="form-year" disabled="disabled">
				<option>Année</option>
				</select>
				</div>
				<div class="col-12 col-sm-6 col-md-12 p-1 pb-0">
				<select name="form-model" id="form-model" disabled="disabled">
				<option>Modèle</option>
				</select>				
				</div>
				<div class="col-12 col-sm-6 col-md-12 p-1 pb-0">
				<select name="form-type" id="form-type" onChange="MM_jumpMenu('parent',this,0)" disabled="disabled">
				<option>Motorisation</option>
				</select>				
				</div>
				<div class="col-12 containermineSeekCar">
				<form  action="<?php echo $domain; ?>/searchmine/"  method="POST" role="form">
				<div class="row">
				<div class="col-12 pb-3">
				Recherche par type mine
				</div>
				<div class="col-9 pr-0">
				<input type="text" name="MINE" />
				<input type="hidden" name="ASK2PAGE" value="2" />
				<input type="hidden" name="PGMINE" value="<?php echo $pg_id; ?>" />
				</div>
				<div class="col-3 pl-0">
				<input type="submit" class="mineSeekCar" value="" />
				</div>
				</div>
				</form>	
				</div>
				</div>
				</div>

			</div>
		</div>

    </div>
</div>

<?php
$query_catalog_family = "SELECT DISTINCT MF_ID, IF(MF_NAME_SYSTEM IS NULL, MF_NAME, MF_NAME_SYSTEM) AS MF_NAME, 
	MF_DESCRIPTION, MF_PIC 
	FROM PIECES_RELATION_TYPE
	JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID
	JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
	JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	JOIN CATALOG_FAMILY ON MF_ID = MC_MF_ID
	WHERE RTP_TYPE_ID = $type_id 
	AND PIECE_DISPLAY = 1 AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) AND MF_DISPLAY = 1
	ORDER BY MF_SORT";
$request_catalog_family = $conn->query($query_catalog_family);
if ($request_catalog_family->num_rows > 0) 
{
?>
<div class="container-fluid containercatalogPage">
    <div class="container-fluid mymaxwidth">

    	<div class="row">
			<div class="col-12">

				<h2>Catalogue <?php echo $pageh1; ?></h2>
				<div class="divh2"></div>

			</div>
	    	<?php
			while($result_catalog_family = $request_catalog_family->fetch_assoc())
			{
			$mf_id = $result_catalog_family['MF_ID'];
			$mf_name_site = $result_catalog_family['MF_NAME'];
			$this_mf_pic = $domain."/upload/articles/familles-produits/".$result_catalog_family['MF_PIC'];
				$query_catalog_gamme = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_PIC, PG_IMG
					FROM PIECES_RELATION_TYPE
					JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID
					JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
					JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
					WHERE RTP_TYPE_ID = $type_id AND PIECE_DISPLAY = 1 AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) 
					AND MC_MF_ID = $mf_id
					ORDER BY MC_SORT";
				$request_catalog_gamme = $conn->query($query_catalog_gamme);
				if ($request_catalog_gamme->num_rows > 0) 
				{
				?>
				<div class="col-12 col-sm-6 col-lg-4 catalogPageCol">
					<div class="container-fluid catalogPageBloc">

						<img data-src="<?php echo $this_mf_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_mf_name; ?>" width="225" height="165" class="img-fluid lazy"/><br>
						<b><?php echo $mf_name_site; ?></b>
						<?php
						while($result_catalog_gamme = $request_catalog_gamme->fetch_assoc())
						{
						$this_pg_name = $result_catalog_gamme['PG_NAME'];
						$this_pg_pic = $result_catalog_gamme['PG_IMG'];
						$thislinktoPage = $domain."/".$Piece."/".$result_catalog_gamme['PG_ALIAS']."-".$result_catalog_gamme['PG_ID']."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
						?>
						<br>
						<a href="<?php echo $thislinktoPage; ?>"><?php echo $this_pg_name; ?></a>
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

    </div>
</div>
<?php
}
?>

<?php
$query_cross_gamme_car = "SELECT DISTINCT CGC_PG_ID, PG_ALIAS, PG_NAME, PG_NAME_META, PG_PIC, PG_IMG 
		FROM __CROSS_GAMME_CAR_NEW 
		JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
		JOIN PIECES_GAMME ON PG_ID = CGC_PG_ID
		JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
		WHERE CGC_TYPE_ID = $type_id 
		AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 3
		ORDER BY MC_MF_ID, MC_SORT LIMIT 48";
$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
if ($request_cross_gamme_car->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>CATALOGUE PIÈCES AUTO <?php echo $marque_name_site; ?> LES PLUS vendus</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="McCgcGamme"  data-interval="1000">
				<div class="MultiCarousel-inner">

					<?php
					while($result_cross_gamme_car = $request_cross_gamme_car->fetch_assoc())
					{
						$this_pg_id = $result_cross_gamme_car['CGC_PG_ID'];
						$this_pg_name_site = $result_cross_gamme_car['PG_NAME'];
						$this_pg_name_meta = $result_cross_gamme_car['PG_NAME_META'];
						$this_pg_alias = $result_cross_gamme_car['PG_ALIAS'];
						if($isMacVersion == false)
						{
							$this_pg_img = $result_cross_gamme_car['PG_IMG'];
						}
						else
						{
							$this_pg_img = str_replace(".webp",".jpg",$result_cross_gamme_car['PG_IMG']);
						}
						if(empty($this_pg_img))
						{
						  $this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/no.png";
						}
						else
						{
						  $this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
						}
						// LINK TO CAR
						$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
						// SEO TITLE ET DESCRIPTION DE LA PAGE P
						$query_seo_gamme_car = "SELECT SGC_TITLE, SGC_DESCRIP   
							FROM __SEO_GAMME_CAR 
							WHERE SGC_PG_ID = $this_pg_id 
							ORDER BY SGC_ID DESC LIMIT 0,1";
						$request_seo_gamme_car = $conn->query($query_seo_gamme_car);
						if ($request_seo_gamme_car->num_rows > 0) 
						{
						$result_seo_gamme_car = $request_seo_gamme_car->fetch_assoc();
						$addon_title_seo_gamme_car=strip_tags($result_seo_gamme_car['SGC_TITLE']);
							$addon_title_seo_gamme_car=str_replace("#CompSwitch#","#CompSwitchTitle#",$addon_title_seo_gamme_car);
						$addon_content_seo_gamme_car=strip_tags($result_seo_gamme_car['SGC_DESCRIP']);
						$addon_content_seo_gamme_car = $addon_content_seo_gamme_car."<br><a>".$addon_title_seo_gamme_car."</a>";
						/////////////////////////////// addon_content_seo_gamme_car //////////////////////////////////////
						// Changement des variables standards
							$addon_content_seo_gamme_car=str_replace("#Gamme#",$this_pg_name_site,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#VMarque#",$marque_name_site,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#VModele#",$modele_name_site,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#VType#",$type_name_site,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#VAnnee#",$type_date,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#VNbCh#",$type_nbch,$addon_content_seo_gamme_car);
					        $addon_content_seo_gamme_car=str_replace("#MinPrice#","",$addon_content_seo_gamme_car);
					    // Changement #CompSwitch_3_PG_ID#
					        $comp_switch_3_pg_id_marker="#CompSwitch_3_".$this_pg_id."#";
							$comp_switch_3_pg_id_value="";
							if(strpos($addon_content_seo_gamme_car, $comp_switch_3_pg_id_marker))
							{
								$query_seo_gamme_car_switch = "SELECT SGCS_ID   
									FROM __SEO_GAMME_CAR_SWITCH 
									WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3";
								$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
								if ($request_seo_gamme_car_switch->num_rows > 0) 
								{
									$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
									// CONTENT
									$comp_switch_3_pg_id_debut=($type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
									$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
										FROM __SEO_GAMME_CAR_SWITCH 
										WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3 
										ORDER BY SGCS_ID LIMIT $comp_switch_3_pg_id_debut,1";
									$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
									$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
									$comp_switch_3_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
								}
								$addon_content_seo_gamme_car=str_replace($comp_switch_3_pg_id_marker,$comp_switch_3_pg_id_value,$addon_content_seo_gamme_car);
							}
						// Changement #PrixPasCher#	
							$PrixPasCherTab=(($this_pg_id%100)+$type_id)%$PrixPasCherLength;
							$PrixPasCherTab2=(($this_pg_id%100)+$type_id+1)%$PrixPasCherLength;
					        $addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab2],$addon_content_seo_gamme_car);
					        //$addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab2],$addon_content_seo_gamme_car);
					    // Changement #CompSwitch#
					        $comp_switch_marker="#CompSwitch#";
					        $comp_switch_marker_2="#CompSwitchTitle#";
							$comp_switch_value="";
							$comp_switch_value_2="";
							//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
							//{
								$query_seo_item_switch = "SELECT SIS_ID   
									FROM __SEO_ITEM_SWITCH 
									WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1";
								$request_seo_item_switch = $conn->query($query_seo_item_switch);
								if ($request_seo_item_switch->num_rows > 0) 
								{
									$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
									// CONTENT
									$comp_switch_debut = ($type_id+1) % $request_seo_item_switch_num_rows;
									$query_seo_item_switch = "SELECT SIS_CONTENT   
										FROM __SEO_ITEM_SWITCH 
										WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1 
										ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
									$request_seo_item_switch = $conn->query($query_seo_item_switch);
									$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
									$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
									// TITLE
									$comp_switch_debut_2 = $type_id % $request_seo_item_switch_num_rows;
									$query_seo_item_switch = "SELECT SIS_CONTENT   
										FROM __SEO_ITEM_SWITCH 
										WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1 
										ORDER BY SIS_ID LIMIT $comp_switch_debut_2,1";
									$request_seo_item_switch = $conn->query($query_seo_item_switch);
									$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
									$comp_switch_value_2 = $result_seo_item_switch["SIS_CONTENT"];
								}
								$addon_content_seo_gamme_car=str_replace($comp_switch_marker,$comp_switch_value,$addon_content_seo_gamme_car);
								$addon_content_seo_gamme_car=str_replace($comp_switch_marker_2,$comp_switch_value_2,$addon_content_seo_gamme_car);
							//}
						// Changement #LinkGammeCar_PG_ID#
							$LinkGammeCar_pg_id_marker="#LinkGammeCar_".$this_pg_id."#";
							$LinkGammeCar_pg_id_value="";
							$LinkGammeCar_pg_id_value_2="";
							if(strstr($addon_content_seo_gamme_car, $LinkGammeCar_pg_id_marker))
							{
								// LinkGammeCar_PG_ID_1
								$query_seo_gamme_car_switch = "SELECT SGCS_ID   
									FROM __SEO_GAMME_CAR_SWITCH 
									WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1";
								$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
								if ($request_seo_gamme_car_switch->num_rows > 0) 
								{
									$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
									// CONTENT
									$LinkGammeCar_pg_id_debut=($type_id+$this_pg_id+2) % $request_seo_gamme_car_switch_num_rows;
									$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
										FROM __SEO_GAMME_CAR_SWITCH 
										WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1 
										ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut,1";
									$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
									$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
									$LinkGammeCar_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
								}
								// LinkGammeCar_PG_ID_2
								$query_seo_gamme_car_switch = "SELECT SGCS_ID   
									FROM __SEO_GAMME_CAR_SWITCH 
									WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2";
								$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
								if ($request_seo_gamme_car_switch->num_rows > 0) 
								{
									$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
									// CONTENT
									$LinkGammeCar_pg_id_debut_2=($type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
									$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
										FROM __SEO_GAMME_CAR_SWITCH 
										WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2 
										ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut_2,1";
									$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
									$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
									$LinkGammeCar_pg_id_value_2 = $result_seo_gamme_car_switch["SGCS_CONTENT"];
								}

								$LinkGammeCar_pg_id_link_no=$LinkGammeCar_pg_id_value." les ".$this_pg_name_site." et ".$LinkGammeCar_pg_id_value_2;

								$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_no,$addon_content_seo_gamme_car);

								$LinkGammeCar_pg_id_link_full="<a href='".$LinkGammeCar_pg_id_link."'>";

								$addon_content_seo_gamme_car=str_replace("<a>",$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
							}
						/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
						}
						else
						{
							$addon_content_seo_gamme_car = "Achetez ".$this_pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date.", d'origine à prix bas."."<br><a href='".$LinkGammeCar_pg_id_link."'>".$this_pg_name_meta." ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date."</a>";
						}
					?>
						<div class="item">
							<div class="pad15">

								<div class="container-fluid multicarouselwhiteBloc">
									<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="img-fluid lazy anim"/>
									<p>
										<b><?php echo $this_pg_name_site." pour ".$marque_name_site." ".$modele_name_site." ".$type_name_site; ?></b>
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
function MM_jumpMenu(targ,selObj,restore){eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'"),restore&&(selObj.selectedIndex=0)}$(function(){$("#form-marq").change(function(){$("#form-year").load("_form.get.car.year.php?formCarMarqueid="+$("#form-marq").val()),document.getElementById("form-year").disabled=!1,$("#form-model").load("_form.get.car.modele.php?formCarMarqueid=0&formCarMarqueYear=0"),$("#form-type").load("_form.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-model").disabled=!0,document.getElementById("form-type").disabled=!0}),$("#form-year").change(function(){$("#form-model").load("_form.get.car.modele.php?formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()),document.getElementById("form-model").disabled=!1,$("#form-type").load("_form.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-type").disabled=!0}),$("#form-model").change(function(){$("#form-type").load("_form.get.car.type.php?formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()+"&formCarModelid="+$("#form-model").val()),document.getElementById("form-type").disabled=!1})});
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
<?php $pageDisabled = "ty"; include("412.page.php"); ?>
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
<?php $pageDisabled = "ty"; include("410.page.php"); ?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
?>