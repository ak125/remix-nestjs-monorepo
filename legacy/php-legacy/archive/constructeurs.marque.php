<?php 
session_start();
// parametres relatifs à la page
$typefile="level1";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$marque_id=$_GET["marque_id"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT MARQUE_DISPLAY 
	FROM AUTO_MARQUE 
	WHERE MARQUE_ID = $marque_id";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['MARQUE_DISPLAY']==1)
	{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_marque = "SELECT MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, 
	MARQUE_RELFOLLOW, MARQUE_LOGO 
	FROM AUTO_MARQUE 
	WHERE MARQUE_ID = $marque_id  
    AND MARQUE_DISPLAY = 1";
$request_marque = $conn->query($query_marque);
$result_marque = $request_marque->fetch_assoc();
	// PG DATA
	$marque_name_site = $result_marque['MARQUE_NAME'];
	$marque_name_meta = $result_marque['MARQUE_NAME_META'];
	$marque_name_meta_title = $result_marque['MARQUE_NAME_META_TITLE'];
    $marque_alias = $result_marque["MARQUE_ALIAS"];
    $marque_relfollow = $result_marque["MARQUE_RELFOLLOW"];
    // LOGO
    //$marque_logo = $result_marque["MARQUE_LOGO"];
    if($isMacVersion == false)
	{
		$marque_logo = $result_marque['MARQUE_LOGO'];
	}
	else
	{
		$marque_logo = str_replace(".webp",".png",$result_marque['MARQUE_LOGO']);
	}
    // SEO & CONTENT
    $query_seo = "SELECT SM_TITLE, SM_DESCRIP, SM_KEYWORDS, SM_H1, SM_CONTENT 
    	FROM __SEO_MARQUE 
		WHERE SM_MARQUE_ID = $marque_id ";
	$request_seo = $conn->query($query_seo);
	if ($request_seo->num_rows > 0) 
	{
		$result_seo = $request_seo->fetch_assoc();
		// META
		$pagetitle = strip_tags($result_seo['SM_TITLE']);
			// Traitement Marqueur Title
		    $pagetitle=str_replace("#VMarque#",$marque_name_meta_title,$pagetitle);
		    $PrixPasCherTab=$marque_id%$PrixPasCherLength;
		    $pagetitle=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$pagetitle);
        $pagedescription = strip_tags($result_seo['SM_DESCRIP']);
        $pagekeywords = strip_tags($result_seo["SM_KEYWORDS"]);
        // CONTENT
        //$pageh1 = strip_tags($result_seo['SM_H1']);
	    $pageh1 = "Pièces auto ".$marque_name_site;
        $pagecontent = $result_seo['SM_CONTENT'];	
	}
	else
	{
      	//META
      	$pagetitle = "Pièces détachées auto ".$marque_name_meta_title." neuves & d'origine";
	    $pagedescription = "Achetez pour votre ".$marque_name_meta." des pièces détachées & accessoires auto de qualité à un prix pas cher de toutes les marques d'équipementiers de pièces automobile.";
	    $pagekeywords = $marque_name_meta;
	    // CONTENT
	    $pageh1 = "Pièces auto ".$marque_name_site;
		$pagecontent = "Automecanik vous propose tous les modèles du constructeur automobile <b>".$marque_name_site."</b>, sélectionnez le modèle de votre voiture <b>".$marque_name_site."</b> et ensuite choisissez l'année et la motorisation de votre véhicule pour accéder au catalogue de pièce détachée compatible avec votre voiture.";
	}

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
  	$canonicalLink = $domain."/".$Auto."/".$marque_alias."-".$marque_id.".html";
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
	$arianetitle = $marque_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="constructeurs.marque";
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

<link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="manifest" href="/favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://ajax.googleapis.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/style.marque.min.css" rel="stylesheet" media="all"> 
</head>
<body>

<?php
require_once('global.header.section.php');
?>

<!-- SEARCH CAR -->
<div class="container-fluid globalthirdheader">
<div class="container-fluid mywidecontainer">
<?php
$thisYear = date("Y");
//$formCarMarqueid=$marque_id;
?>
	<div class="row pt-3">
		<div class="col-md-1 pr-md-3 headerSearchCarTitle text-center pr-md-0">
			<img src="/assets/img/icon-catalog-car.png" alt="choisir véhicule" class="img-fluid"> Sélect. véhicule
		</div>
		<div class="col-sm-3 col-md-2 headerSearchCar">

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
				<option value="<?php echo $this_marque_id; ?>" <?php if($this_marque_id==$marque_id) echo ' selected="selected"'; ?> class="favorite"><?php echo $this_marque_name_site; ?></option>
				<?php
				}
				else
				{
				?>
				<option value="<?php echo $this_marque_id; ?>" <?php if($this_marque_id==$marque_id) echo ' selected="selected"'; ?>><?php echo $this_marque_name_site; ?></option>
				<?php					
				}
				}
				}
				?>
			</select>

		</div>
		<div class="col-sm-3 col-md-2 headerSearchCar">

			<?php
			// Annee MIN
			$query_get_car_min_year = "SELECT DISTINCT MIN(MODELE_YEAR_FROM) AS minYear   
			FROM AUTO_MODELE 
			WHERE MODELE_DISPLAY = 1 AND MODELE_MARQUE_ID = $marque_id ";
			$request_get_car_min_year = $conn->query($query_get_car_min_year);
			if ($request_get_car_min_year->num_rows > 0) 
			{
			$result_get_car_min_year = $request_get_car_min_year->fetch_assoc();
			$minYear=$result_get_car_min_year["minYear"];
			}
			// Annee MAX
			$query_get_car_max_year = "SELECT DISTINCT MAX(COALESCE(MODELE_YEAR_TO,'$thisYear')) AS maxYear   
			FROM AUTO_MODELE 
			WHERE MODELE_DISPLAY = 1 AND MODELE_MARQUE_ID = $marque_id ";
			$request_get_car_max_year = $conn->query($query_get_car_max_year);
			if ($request_get_car_max_year->num_rows > 0) 
			{
			$result_get_car_max_year = $request_get_car_max_year->fetch_assoc();
			$maxYear=$result_get_car_max_year["maxYear"];
			}
			?>
			<select name="form-year" id="form-year">
				<option value="0">Année</option>
				<?php
				while($maxYear>=$minYear)
		        {
		        	if(($maxYear==2020)||($maxYear==2010)||($maxYear==2000))
		            {
		                ?>
		                <option value="<?php echo $maxYear; ?>"  class="favorite"><?php echo $maxYear; ?></option>
		                <?php
		            }
		            else
		            {
		                ?>
		                <option value="<?php echo $maxYear; ?>"><?php echo $maxYear; ?></option>
		                <?php
		            }
		        $maxYear--;
		        }
		        ?>
			</select>

		</div>
		<div class="col-sm-3 col-md-2 headerSearchCar">

			<select name="form-model" id="form-model" disabled="disabled">
				<option>Modèle</option>
			</select>

		</div>
		<div class="col-sm-3 col-md-2 headerSearchCar">

			<select name="form-type" id="form-type" onChange="MM_jumpMenu('parent',this,0)" disabled="disabled">
				<option>Motorisation</option>
			</select>

		</div>
		<div class="col-md-3 headerSearchMine pl-md-3">

			<form  action="<?php echo $domain; ?>/searchmine/"  method="POST" role="form">
			<div class="row">
				<div class="col-10 col-md-9 col-xl-10 pr-0">
					
					<input type="text" name="MINE" placeholder="Recherche par type mine" />
					<input type="hidden" name="ASK2PAGE" value="1" />
					
				</div>
				<div class="col-2 col-md-3 col-xl-2 pl-0">
					
					<input type="submit" class="headerSearchMineSubmit" value="" />
					
				</div>
			</div>
			</form>	

		</div>
	</div>

</div>
</div>
<!-- / SEARCH CAR -->

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
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<img class="img-fluid" src="<?php echo $domain; ?>/upload/constructeurs-automobiles/icon/<?php echo $marque_logo; ?>"  alt="<?php echo $marque_name_meta; ?>"/>
			<h1><?php echo $pageh1; ?></h1>
		</span>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<?php
	/*$query_cross_gamme_car = "SELECT DISTINCT CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, 
		TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, 
		MODELE_ID, MODELE_ALIAS, MODELE_NAME, MODELE_NAME_META, MODELE_PIC,  
		MDG_NAME, MDG_PIC, 
		MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE   
		FROM __CROSS_GAMME_CAR 
		JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MODELE_GROUP ON MDG_ID = MODELE_MDG_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MDG_MARQUE_ID
		WHERE CGC_MARQUE_ID = $marque_id 
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MDG_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 1
		ORDER BY CGC_ID";*/
	$query_cross_gamme_car = "SELECT DISTINCT CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, 
		TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, 
		MODELE_ID, MODELE_ALIAS, MODELE_NAME, MODELE_NAME_META, MODELE_PIC,  
		MDG_NAME, MDG_PIC, 
		MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE   
		FROM __CROSS_GAMME_CAR_NEW 
		JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MODELE_GROUP ON MDG_ID = MODELE_MDG_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MDG_MARQUE_ID
		WHERE CGC_MARQUE_ID = $marque_id 
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MDG_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 2
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
			<h2>LES VOITURES LES PLUS CONSULTÉES DE LA MARQUE <?php echo $marque_name_site; ?></h2>
		</span>
	</div>
	<div class="col-12 pt-3 pb-3">
		<p class="text-justify contenth2"><?php echo $pagecontent; ?></p>
	</div>
	</div>
	<div class="row p-0 m-0">
		<?php
		while($result_cross_gamme_car = $request_cross_gamme_car->fetch_assoc())
		{
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
			$this_type_name_meta = $result_cross_gamme_car['TYPE_NAME_META']; // Cahnger à TYPE_NAME_META
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
			$LinkToGammeCar = $domain."/".$Auto."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
			// SEO TITLE ET DESCRIPTION DE LA PAGE MOTORISATION
			$addon_title_seo_gamme_car="Pièces auto ".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." #CompSwitch#";
			/////////////////////////////// addon_title_seo_gamme_car //////////////////////////////////////
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
						$comp_switch_debut = $this_type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 1 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$addon_title_seo_gamme_car=str_replace($comp_switch_marker,$comp_switch_value,$addon_title_seo_gamme_car);
				//}
			/////////////////////////////// fin addon_title_seo_gamme_car //////////////////////////////////
			$addon_content_seo_gamme_car="Catalogue pièces détachées pour <a href='".$LinkToGammeCar."'>".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date."</a> neuves #CompSwitch#";
			/////////////////////////////// addon_content_seo_gamme_car //////////////////////////////////////
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
						$comp_switch_debut = ($this_type_id+1) % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT STS_CONTENT   
							FROM __SEO_TYPE_SWITCH 
							WHERE STS_ALIAS = 2 
							ORDER BY STS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["STS_CONTENT"];
					}
					$addon_content_seo_gamme_car=str_replace($comp_switch_marker,$comp_switch_value,$addon_content_seo_gamme_car);
				//}
			/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
			?>
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3 blocToPCar">

				<div class="container-fluid p-3 pb-0 mh57">
					<span><?php echo content_cleaner($addon_title_seo_gamme_car); ?></span>
				</div>
				<div class="container-fluid p-3 mh167 text-center">
					<img data-src="<?php echo $this_modele_group_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_marque_name_meta.' '.$result_cross_gamme_car['MODELE_NAME']; ?>" width="270" height="135" class="w-100 img-fluid lazy"/>
				</div>
				<div class="container-fluid regularContentBordered p-3 mh127">
					<?php echo content_cleaner($addon_content_seo_gamme_car); ?>.
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

	<?php
	/*$query_cross_gamme_car = "SELECT DISTINCT CGC_PG_ID, PG_ALIAS, PG_NAME, PG_NAME_META, PG_PIC, PG_IMG,  
		CGC_TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_POWER_PS, 
		TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, 
		MODELE_ID, MODELE_ALIAS, MODELE_NAME, MODELE_NAME_META, 
		MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META_TITLE   
		FROM __CROSS_GAMME_CAR 
		JOIN AUTO_TYPE ON TYPE_ID = CGC_TYPE_ID
		JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
		JOIN AUTO_MARQUE ON MARQUE_ID = MODELE_MARQUE_ID
		JOIN PIECES_GAMME ON PG_ID = CGC_PG_ID
		JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
		WHERE CGC_MARQUE_ID = $marque_id 
		AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 1
		ORDER BY MC_MF_ID, MC_SORT LIMIT 48";*/
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
		AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 1
		GROUP BY CGC_PG_ID 
		ORDER BY MC_MF_ID, MC_SORT, CGC_ID";
	$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
	if ($request_cross_gamme_car->num_rows > 0) 
	{
	?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>CATALOGUE PIÈCES AUTO <?php echo $marque_name_site; ?> LES PLUS VENDUS</h2>
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
			$addon_content_seo_gamme_car = $addon_content_seo_gamme_car."<br><a>".$addon_title_seo_gamme_car."</a>";
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

					$LinkGammeCar_pg_id_link_full="<a href='".$LinkGammeCar_pg_id_link."'>";

					$addon_content_seo_gamme_car=str_replace("<a>",$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
				}
			/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
			}
			else
			{
				$addon_content_seo_gamme_car = "Achetez ".$this_pg_name_meta." ".$this_marque_name_meta." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date.", d'origine à prix bas."."<br><a href='".$LinkGammeCar_pg_id_link."'>".$this_pg_name_meta." ".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date."</a>";
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

<button class="btn goBacktoTop" onclick="topFunction()" id="myBtnTop" title="Vers le haut">
TOP
</button>


</body>
</html>
<!--<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.min.php');
?>
<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
function MM_jumpMenu(targ,selObj,restore){eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'"),restore&&(selObj.selectedIndex=0)}$(function(){$("#form-marq").change(function(){$("#form-year").load("_form.get.car.year.php?formCarMarqueid="+$("#form-marq").val()),document.getElementById("form-year").disabled=!1,$("#form-model").load("_form.get.car.modele.php?formCarMarqueid=0&formCarMarqueYear=0"),$("#form-type").load("_form.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-model").disabled=!0,document.getElementById("form-type").disabled=!0}),$("#form-year").change(function(){$("#form-model").load("_form.get.car.modele.php?formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()),document.getElementById("form-model").disabled=!1,$("#form-type").load("_form.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-type").disabled=!0}),$("#form-model").change(function(){$("#form-type").load("_form.get.car.type.php?formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()+"&formCarModelid="+$("#form-model").val()),document.getElementById("form-type").disabled=!1})});
</script>
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
<?php $pageDisabled = "mq"; include("412.page.php"); ?>
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
<?php include("410.page.php"); ?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
?>