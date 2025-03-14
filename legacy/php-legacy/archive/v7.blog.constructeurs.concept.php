<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$marque_alias=$_GET["marque_alias"];
$mdg_alias=$_GET["mdg_alias"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT MODELE_ID, MODELE_DISPLAY, MARQUE_ID, MARQUE_DISPLAY  
	FROM AUTO_MODELE 
	JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
	WHERE MODELE_ALIAS = '$mdg_alias' 
	AND MARQUE_ALIAS = '$marque_alias'";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['MODELE_DISPLAY']==1)
	{
	$marque_id = $result_selector['MARQUE_ID'];
	$mdg_id = $result_selector['MODELE_ID'];
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_item = "SELECT * 
	FROM AUTO_MODELE 
	JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
	WHERE MODELE_ID = '$mdg_id' 
	AND MARQUE_ID = '$marque_id'  
	AND MODELE_DISPLAY = 1 
	AND MARQUE_DISPLAY = 1";
$request_item = $conn->query($query_item);
$result_item = $request_item->fetch_assoc();
	// MARQUE DATA
	$marque_name = $result_item['MARQUE_NAME'];
	$marque_name_meta = $result_item['MARQUE_NAME_META'];
    $marque_alias = $result_item["MARQUE_ALIAS"];
    $marque_relfollow = $result_item["MARQUE_RELFOLLOW"];
    if($isMacVersion == false)
	{
	$marque_img = $result_marque['MARQUE_LOGO'];
	}
	else
	{
	$marque_img = str_replace(".webp",".png",$result_marque['MARQUE_LOGO']);
	}
	$marque_pic = $domain."/upload/constructeurs-automobiles/marques-logos/".$marque_img;
	// MDG DATA
	$mdg_name = $result_item['MODELE_NAME'];
    $mdg_alias = $result_item["MODELE_ALIAS"];
    		if($isMacVersion == false)
			{
				$mdg_pic = $result_modele_group['MODELE_PIC'];
			}
			else
			{
				$mdg_pic = str_replace(".webp",".png",$result_modele_group['MODELE_PIC']);
			}
			if(($mdg_pic=="no.webp")||($mdg_pic=="no.png")||($mdg_pic==""))
			{
				$mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/no.png";
			}
			else
			{
				$mdg_picture = $domain."/upload/constructeurs-automobiles/marques-modeles/".$marque_alias."/".$mdg_pic;
			}

    // SEO & CONTENT
    	// META
		$pagetitle="Pièces auto ".$result_item['MARQUE_NAME_META']." ".$result_item['MODELE_NAME']." à prix pas cher";
        $pagedescription = "Automecanik vous offre toutes les pièces et accessoires autos à prix pas cher pour ".$result_item['MARQUE_NAME_META']." ".$result_item['MODELE_NAME'];
        $pagekeywords = $result_item['MARQUE_NAME'].", ".$result_item['MODELE_NAME'];
        // CONTENT
        $pageh1 = " Choisissez votre ".$result_item['MARQUE_NAME']." ".$result_item['MODELE_NAME'];
        $pagecontent = "Un vaste choix de pièces détachées <b>".$result_item['MARQUE_NAME']." ".$result_item['MODELE_NAME']."</b> au meilleur tarif et de qualité irréprochable proposées par les grandes marques d'équipementiers automobile de première monte d'origine. Consultez notre catalogue de pièces auto <b>".$result_item['MARQUE_NAME']." ".$result_item['MODELE_NAME']."</b> proposé par Automecanik.com et choisissez des pièces compatibles tel que kit distribution, kit embrayage, alternateur, démarreur, échappement et autres pièces.";	
	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
		$pagecontent = content_cleaner($pagecontent);

	// ROBOT
    $relfollow = 1;
    if($marque_relfollow==1)
  	{
  		$pageRobots="index, follow";
    	$relfollow = 1;
  	}
  	else
  	{
  		$pageRobots="noindex, nofollow";
    	$relfollow = 0;
  	}
  	// CANONICAL
  	$canonicalLink = $domain."/".$blog."/".$constructeurs."/".$marque_alias."/".$mdg_alias;
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
	$first_parent_arianelink = $constructeurs;
	$first_parent_arianetitle = $constructeurs_title;
	$parent_arianelink = $marque_alias;
	$parent_arianetitle = $marque_name;
	$arianetitle = $mdg_name;
?>
<?php 
// parametres relatifs à la page
$arianefile="concept";
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

				<h1><?php echo $pageh1; ?></h1>
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

<?php
$query_modele = "SELECT *
		FROM AUTO_MODELE
		WHERE ( MODELE_ID = $mdg_id OR MODELE_PARENT = $mdg_id )
		AND MODELE_MARQUE_ID = $marque_id 
		AND MODELE_DISPLAY = 1
		ORDER BY MODELE_NAME ASC";
$request_modele = $conn->query($query_modele);
if ($request_modele->num_rows > 0) 
{
?>
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

    		<div class="row">
				<div class="col-12 pb-3">
					<p>
					<?php echo $pagecontent; ?>
					</p>
				</div>
			</div>
			<?php
			while($result_modele = $request_modele->fetch_assoc())
			{
			$this_modele_name = $result_modele['MODELE_NAME'];
			$this_modele_alias = $result_modele['MODELE_ALIAS'];
			$this_modele_id = $result_modele['MODELE_ID'];
			?>
			<div class="row pt-3">
				<div class="col-12">

					<h2><?php echo $marque_name; ?> <?php echo $this_modele_name; ?></h2>
					<div class="divh2"></div>

				</div>
				<div class="col-12 pb-3">
			
					<div class="row p-0 m-0">
					<?php
					$query_motorisation = "SELECT *
							FROM AUTO_TYPE
							WHERE TYPE_MODELE_ID = $this_modele_id 
							AND TYPE_DISPLAY = 1
							ORDER BY TYPE_FUEL, TYPE_NAME ASC";
					$request_motorisation = $conn->query($query_motorisation);
					if ($request_motorisation->num_rows > 0) 
					{
					while($result_motorisation = $request_motorisation->fetch_assoc())
					{
					$this_type_id = $result_motorisation["TYPE_ID"];
					$this_type_name = $result_motorisation["TYPE_NAME"];
					$this_type_alias = $result_motorisation["TYPE_ALIAS"];
					$this_type_date = "";
					if(empty($result_motorisation['TYPE_YEAR_TO']))
				    {
				    $this_type_date="du ".$result_motorisation['TYPE_MONTH_FROM']."/".$result_motorisation['TYPE_YEAR_FROM'];
				    }
				    else
				    {
				    $this_type_date="de ".$result_motorisation['TYPE_YEAR_FROM']." à ".$result_motorisation['TYPE_YEAR_TO'];
				    }
					$this_type_nbch = $result_motorisation['TYPE_POWER_PS'];
					$this_type_puissance = $result_motorisation['TYPE_POWER_KW'];
					$this_type_carosserie = $result_motorisation['TYPE_BODY'];
					$this_type_fuel = $result_motorisation['TYPE_FUEL'];
					$this_type_cylindre = $result_motorisation['TYPE_LITER'];
					// LINK TO SITE
					$this_link_to_motorisation = $domain."/constructeurs/".$marque_alias."-".$marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
					?>
					<div class="col-md-6" style="background: #f6fbfb; border-bottom: 2px solid #fff; padding:4px;">

						<a target="_blank" href="<?php echo $this_link_to_motorisation ; ?>"><?php echo $marque_name; ?> <?php echo $this_modele_name; ?> <?php echo $this_type_name." ".$this_type_nbch." ch ".$this_type_date; ?></a>

					</div>
					<div class="col-md-2" style="background: #f6fbfb; border-bottom: 2px solid #fff; padding:4px;">

						<?php echo $this_type_nbch; ?> ch (<?php echo $this_type_puissance; ?> kw)

					</div>
					<div class="col-md-2" style="background: #f6fbfb; border-bottom: 2px solid #fff; padding:4px;">

						<?php echo $this_type_fuel; ?>

					</div>
					<div class="col-md-2" style="background: #f6fbfb; border-bottom: 2px solid #fff; padding:4px;">

						<?php echo $this_type_carosserie; ?>

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

	</div>
</div>
<?php
}
?>

<?php
$query_cross_gamme_car = "SELECT DISTINCT CGC_PG_ID, PG_ALIAS, PG_NAME, PG_NAME_META, PG_PIC, PG_IMG,  
	CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_POWER_PS, 
	TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, 
	MODELE_ID, MODELE_ALIAS, MODELE_NAME, MODELE_NAME_META, 
	MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META_TITLE   
	FROM __CROSS_GAMME_CAR_NEW 
	JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
	JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
	JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
	JOIN PIECES_GAMME ON PG_ID = CGC_PG_ID
	JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	WHERE CGC_MARQUE_ID = $marque_id 
	AND CGC_MODELE_ID = $mdg_id
	AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)
	AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
	AND CGC_LEVEL = 2
	ORDER BY MC_MF_ID, MC_SORT, CGC_ID";
$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
if ($request_cross_gamme_car->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-12">

				<h2>CATALOGUE PIÈCES AUTO <?php echo $marque_name; ?> <?php echo $mdg_name; ?> LES PLUS VENDUS</h2>
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
						$this_type_id = $result_cross_gamme_car['CGC_TYPE_ID'];
						$this_marque_name_site = $result_cross_gamme_car['MARQUE_NAME'];
						$this_marque_name_meta = $result_cross_gamme_car['MARQUE_NAME_META'];
						$this_marque_name_meta_title = $result_cross_gamme_car['MARQUE_NAME_META_TITLE'];
						$this_marque_alias = $result_cross_gamme_car['MARQUE_ALIAS'];
						$this_marque_id = $result_cross_gamme_car['MARQUE_ID'];
						$this_modele_name_site = $result_cross_gamme_car['MODELE_NAME'];
						$this_modele_name_meta = $result_cross_gamme_car['MODELE_NAME_META'];
						$this_modele_alias = $result_cross_gamme_car['MODELE_ALIAS'];
						$this_modele_id = $result_cross_gamme_car['MODELE_ID'];
						$this_type_name_site = $result_cross_gamme_car['TYPE_NAME'];
						$this_type_name_meta = $result_cross_gamme_car['TYPE_NAME_META']; // Changer à TYPE_NAME_META
						$this_type_alias = $result_cross_gamme_car['TYPE_ALIAS'];
						$this_type_date = "";
						if(empty($result_cross_gamme_car['TYPE_YEAR_TO']))
						{
						$this_type_date="du ".$result_cross_gamme_car['TYPE_MONTH_FROM']."/".$result_cross_gamme_car['TYPE_YEAR_FROM'];
						}
						else
						{
						$this_type_date="de ".$result_cross_gamme_car['TYPE_MONTH_FROM']."/".$result_cross_gamme_car['TYPE_YEAR_FROM']." à ".$result_cross_gamme_car['TYPE_MONTH_TO']."/".$result_cross_gamme_car['TYPE_YEAR_TO'];
						}
						$this_type_nbch = $result_cross_gamme_car['TYPE_POWER_PS'];
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
						$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
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
						//$addon_content_seo_gamme_car = $addon_content_seo_gamme_car."<br><a>".$addon_title_seo_gamme_car."</a>";
						$addon_content_seo_gamme_car = $addon_content_seo_gamme_car.".<br>".$addon_title_seo_gamme_car.".";
						/////////////////////////////// addon_content_seo_gamme_car //////////////////////////////////////
						// Changement des variables standards
						$addon_content_seo_gamme_car=str_replace("#Gamme#",$this_pg_name_site,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace("#VMarque#",$this_marque_name_site,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace("#VModele#",$this_modele_name_site,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace("#VType#",$this_type_name_site,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace("#VAnnee#",$this_type_date,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace("#VNbCh#",$this_type_nbch,$addon_content_seo_gamme_car);
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
						$comp_switch_3_pg_id_debut=($this_type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
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
						$PrixPasCherTab=(($this_pg_id%100)+$this_type_id)%$PrixPasCherLength;
						$PrixPasCherTab2=(($this_pg_id%100)+$this_type_id+1)%$PrixPasCherLength;
						$addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$addon_content_seo_gamme_car);
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
						$comp_switch_debut = ($this_type_id+1) % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
						FROM __SEO_ITEM_SWITCH 
						WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1 
						ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
						// TITLE
						$comp_switch_debut_2 = $this_type_id % $request_seo_item_switch_num_rows;
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
						$LinkGammeCar_pg_id_debut=($this_type_id+$this_pg_id+2) % $request_seo_gamme_car_switch_num_rows;
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
						$LinkGammeCar_pg_id_debut_2=($this_type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2 
						ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut_2,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$LinkGammeCar_pg_id_value_2 = $result_seo_gamme_car_switch["SGCS_CONTENT"];
						}

						$LinkGammeCar_pg_id_link_no=$LinkGammeCar_pg_id_value." les ".$this_pg_name_site." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch et ".$LinkGammeCar_pg_id_value_2;

						$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_no,$addon_content_seo_gamme_car);

						//$LinkGammeCar_pg_id_link_full="<a href='".$LinkGammeCar_pg_id_link."'>";

						//$addon_content_seo_gamme_car=str_replace("<a>",$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
						}
						/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
						}
						else
						{
						//$addon_content_seo_gamme_car = "Achetez ".$this_pg_name_meta." ".$this_marque_name_meta." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date.", d'origine à prix bas."."<br><a href='".$LinkGammeCar_pg_id_link."'>".$this_pg_name_meta." ".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date."</a>";
						$addon_content_seo_gamme_car = "Achetez ".$this_pg_name_meta." ".$this_marque_name_meta." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date.", d'origine à prix bas."."<br>".$this_pg_name_meta." ".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date;
						}
						?>
						<div class="item">
							<div class="pad15">

								<div class="container-fluid multicarouselwhiteBloc">
									<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="w-100 img-fluid lazy anim"/>
									<p>
										<b><?php echo $this_pg_name_site." pour ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site; ?></b>
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