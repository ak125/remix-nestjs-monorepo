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
			<div class="col-12 pt-3 pb-3">
				<span class="containerh1">
					<h1><?php echo $pageh1; ?></h1>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				<p class="text-justify contenth2"><?php echo $pagecontent; ?></p>
			</div>
			<div class="col-12 pt-3 pb-3">



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
			while($result_modele = $request_modele->fetch_assoc())
			{
			$this_modele_name = $result_modele['MODELE_NAME'];
			$this_modele_alias = $result_modele['MODELE_ALIAS'];
			$this_modele_id = $result_modele['MODELE_ID'];
			?>
			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2">
					<h2><?php echo $marque_name; ?> <?php echo $this_modele_name; ?></h2>
				</span>
			</div>
			<div class="col-12 p-3">
			
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
			}
			?>



			</div>
			</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

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
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>CATALOGUE PIÈCES AUTO <?php echo $marque_name; ?> <?php echo $mdg_name; ?> LES PLUS VENDUS</h2>
		</span>
	</div>
	</div>
	<div class="row p-0 m-0">
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
			<div class="col-12 col-md-6 blocToPCar">

				<div class="container-fluid p-3 pb-0 mh57">
					<span><?php echo $this_pg_name_site." pour ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site; ?></span>
				</div>
				<div class="container-fluid regularContentBordered2 mh237 p-3">
					<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="img-fluid lazy"/> <?php echo content_cleaner($addon_content_seo_gamme_car); ?>
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