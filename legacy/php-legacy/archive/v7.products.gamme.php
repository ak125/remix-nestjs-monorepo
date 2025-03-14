<?php 
session_start();
// parametres relatifs à la page
$typefile="level1";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
require_once('config/v7.model.pg.php');
// Get Datas
$pg_id=$_GET["pg_id"];
?>
<?php
// REDIRECTION
if($pg_id==3940)
{
	$linkRedirect = $domain."/pieces/corps-papillon-158.html";
	header("Status: 301 Moved Permanently", false, 301);
	header("Location: ".$linkRedirect);
	exit();
}
?>
<?php
// QUERY SELECTOR
$query_gamme_privileg = get_gamme_privileg($pg_id);
if (!empty($query_gamme_privileg)) 
{
	if($query_gamme_privileg['PG_DISPLAY']==1)
	{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_gamme_by_id = get_gamme_by_id($pg_id);
	// PG DATA
	$pg_name_site = $query_gamme_by_id['PG_NAME'];
	$pg_name_meta = $query_gamme_by_id['PG_NAME_META'];
    $pg_alias = $query_gamme_by_id["PG_ALIAS"];
    $pg_relfollow = $query_gamme_by_id["PG_RELFOLLOW"];
    // MF DATA
    $mf_id = $query_gamme_by_id["MF_ID"];
    $mf_name_site = $query_gamme_by_id["MF_NAME"];
    $mf_name_meta = $query_gamme_by_id["MF_NAME_META"];
    // WALL
    if($isMacVersion == false)
	{
		$pg_pic = $query_gamme_by_id['PG_IMG'];
	}
	else
	{
		$pg_pic = str_replace(".webp",".jpg",$query_gamme_by_id['PG_IMG']);
	}
    $pg_wall = $query_gamme_by_id["PG_WALL"];
    // SEO & CONTENT
    $query_seo = "SELECT SG_TITLE, SG_DESCRIP, SG_KEYWORDS, SG_H1, SG_CONTENT 
    	FROM __SEO_GAMME 
		WHERE SG_PG_ID = $pg_id";
	$request_seo = $conn->query($query_seo);
	if ($request_seo->num_rows > 0) 
	{
		$result_seo = $request_seo->fetch_assoc();
		// META
		$pagetitle = strip_tags($result_seo['SG_TITLE']);
        $pagedescription = strip_tags($result_seo['SG_DESCRIP']);
        $pagekeywords = strip_tags($result_seo["SG_KEYWORDS"]);
        // CONTENT
        $pageh1 = strip_tags($result_seo['SG_H1']);
        $pagecontent = $result_seo['SG_CONTENT'];	
	}
	else
	{
		// META
		$pagetitle=$pg_name_meta." neuf & à prix bas";
      	$pagedescription="Votre ".$pg_name_meta." au meilleur tarif, de qualité & à prix pas cher pour toutes marques et modèles de voitures.";
      	$pagekeywords = $pg_name_meta;
        // CONTENT
        $pageh1="Choisissez ".$pg_name_site." pas cher pour votre véhicule";
      	$pagecontent="Le(s) <b>".$pg_name_site."</b> commercialisés sur ".$pg_name_site." sont disponibles pour tous les modèles de véhicules et dans plusieurs marques d'équipementiers de pièces détachées automobile.<br>Identifier la marque, l'année, le modèle et la motorisation de votre véhicule sélectionnez le <b>".$pg_name_site."</b> compatible avec votre voiture.<br>Nous commercialisons des <b>".$pg_name_site."</b>  de différentes qualités : qualité d'origine, première monte et équivalente à l'origine avec des prix pas cher.";
	}

	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
		$pagecontent = strip_tags($pagecontent,"<a><b><br><strong><u>");
		$pagecontent = content_cleaner($pagecontent);

	// ROBOT
    $relfollow = 1;
    if($pg_relfollow==1)
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
  	$canonicalLink = $domain."/".$Piece."/".$pg_alias."-".$pg_id.".html";
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
	$arianetitle = $pg_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="products.gamme";
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
<link href="<?php echo $domain; ?>/assets/css/v10.style.z.min.css" rel="stylesheet" media="all">
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
$query_blog = "SELECT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_UPDATE, 
PG_NAME, PG_ALIAS, PG_IMG, PG_WALL
FROM __BLOG_ADVICE 
JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
WHERE BA_PG_ID = $pg_id 
ORDER BY BA_UPDATE DESC, BA_CREATE DESC LIMIT 1";
$request_blog = $conn->query($query_blog);
if ($request_blog->num_rows > 0) 
{
?>
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

    	<?php
		$result_blog = $request_blog->fetch_assoc();
		$this_ba_id = $result_blog['BA_ID'];
		$this_ba_h1 = $result_blog['BA_H1'];
		$this_ba_alias = $result_blog['BA_ALIAS'];
		$this_ba_preview = $result_blog['BA_PREVIEW'];
		$this_pg_name_site = $result_blog['PG_NAME'];
		$this_pg_alias = $result_blog['PG_ALIAS'];
		?>
		<div class="row">
			<div class="col-sm-6 col-md-4 col-lg-3 text-center text-sm-left align-self-center">

				<?php
				// photo article blog
				$this_ba_wall = $result_blog['BA_WALL'];
				if($this_ba_wall=="no.jpg")
				{
				$this_ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$pg_pic;
				}
				else
				{
				// image de l'article
				$this_ba_wall_link = $domain."/upload/blog/conseils/mini/".$this_ba_wall;
				}
				?>
				<img data-src="<?php echo $this_ba_wall_link; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_ba_h1; ?>" width="225" height="165" class="mw-100 img-fluid lazy"/>

			</div>
			<div class="col-sm-6 col-md-8 col-lg-9 align-self-center">

				<h2><?php echo $this_ba_h1; ?></h2>
				<div class="divh2"></div>
				<i>Publié le <?php echo date_format(date_create($result_blog['BA_UPDATE']), 'd/m/Y'); ?></i>
				<p>
					<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>" class="blog-z-read-more" target="_blank"><?php echo $this_ba_preview; ?></a> <?php echo strip_tags($pagecontent,'<b><stong>'); ?>
				</p>

			</div>
		</div>

    </div>
</div>
<?php
}
else
{
	$query_conseil = "SELECT SGC_TITLE, SGC_CONTENT  
		FROM __SEO_GAMME_CONSEIL 
		WHERE SGC_PG_ID = $pg_id 
		ORDER BY SGC_ID";
	$request_conseil = $conn->query($query_conseil);
	if ($request_conseil->num_rows > 0) 
	{
	?>
	<div class="container-fluid containerwhitePage">
	    <div class="container-fluid mymaxwidth">

			<div class="row">
				<div class="col-12">

					<h2>Conseils sur les <?php echo $pg_name_site; ?></h2>
					<div class="divh2"></div>
					<p>
						<?php
						$sgc_count=1;
						while($result_conseil = $request_conseil->fetch_assoc())
						{
							echo "<b><u>".$sgc_count.". ".$result_conseil["SGC_TITLE"]."</u></b>";
							echo "<br>";
							echo "<br>";
							$this_sgc_content = strip_tags($result_conseil["SGC_CONTENT"],'<b><a><br>'); 
							echo $this_sgc_content = content_cleaner($this_sgc_content);
							echo "<br>";
							echo "<br>";
							$sgc_count++;
						}
						?>
					</p>

				</div>
			</div>

	    </div>
	</div>
	<?php 
	}
}
?>	

<?php
$query_same_family = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_PIC, PG_IMG 
					FROM PIECES_GAMME 
					JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
					WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)  
					AND MC_MF_ID = $mf_id AND MC_PG_ID != $pg_id 
					ORDER BY MC_SORT";
$request_same_family = $conn->query($query_same_family);
if ($request_same_family->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Catalogue <?php echo $mf_name_site; ?></h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="McSameFamily"  data-interval="1000">
				<div class="MultiCarousel-inner">

					<?php
					while($result_same_family = $request_same_family->fetch_assoc())
					{
						$this_pg_id = $result_same_family['PG_ID'];
						$this_pg_name_site = $result_same_family['PG_NAME'];
						$this_pg_name_meta = $result_same_family['PG_NAME_META'];
						$this_pg_alias = $result_same_family['PG_ALIAS'];
						if($isMacVersion == false)
						{
							$this_pg_img = $result_same_family['PG_IMG'];
						}
						else
						{
							$this_pg_img = str_replace(".webp",".jpg",$result_same_family['PG_IMG']);
						}
						if(empty($this_pg_img))
						{
						  $this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/no.png";
						}
						else
						{
						  $this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
						}
						// LINK TO GAMME
						$LinkGamme_pg_id_link = $domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id.".html";
						// SEO TITLE ET DESCRIPTION DE LA PAGE Z
						$query_seo_z = "SELECT SG_TITLE, SG_DESCRIP  
					    	FROM __SEO_GAMME 
							WHERE SG_PG_ID = $this_pg_id";
						$request_seo_z = $conn->query($query_seo_z);
						if ($request_seo_z->num_rows > 0) 
						{
							$result_seo_z = $request_seo_z->fetch_assoc();
							// META
							$addon_title_seo_gamme = strip_tags($result_seo_z['SG_TITLE']);
					        $addon_content_seo_gamme = strip_tags($result_seo_z['SG_DESCRIP']);	
						}
						else
						{
							// META
							$addon_title_seo_gamme = $this_pg_name_meta." neuf & à prix bas";
					      	$addon_content_seo_gamme = "Votre ".$this_pg_name_meta." au meilleur tarif, de qualité & à prix pas cher pour toutes marques et modèles de voitures.";
						}
						// seo content
						$addon_seo_gamme = $addon_content_seo_gamme."<br><a href='".$LinkGamme_pg_id_link."'>".$addon_title_seo_gamme."</a>";
					?>
					<div class="item">
						<div class="pad15">

							<div class="container-fluid multicarouselwhiteBloc">
								<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="img-fluid lazy anim"/>
								<p>
									<b><?php echo $result_same_family["PG_NAME"]; ?></b>
									<br>
									<?php echo content_cleaner($addon_seo_gamme); ?>.
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
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Les motorisations les plus consultées</h2>
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

								$LinkGammeCar_pg_id_link_full=$LinkGammeCar_pg_id_value." les <a href='".$LinkGammeCar_pg_id_link."'>".$pg_name_site." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site." ".$this_type_nbch." ch</a> et ".$LinkGammeCar_pg_id_value_2;

								$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
							}
						/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
						}
						else
						{
							$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$this_marque_alias."-".$this_marque_id."/".$this_modele_alias."-".$this_modele_id."/".$this_type_alias."-".$this_type_id.".html";
							$PrixPasCher[$PrixPasCherTab] = "";
							$addon_title_seo_gamme_car = $this_type_nbch." ch ".$this_type_date;
				    		$addon_content_seo_gamme_car = "Achetez <a href='".$LinkGammeCar_pg_id_link."'>".$pg_name_meta." ".$this_marque_name_meta." ".$this_modele_name_site." ".$this_type_name_site."</a> ".$this_type_nbch." ch ".$this_type_date.", d'origine à prix bas.";			
				    	}	
						?>
						<div class="item">
							<div class="pad15">

								<div class="container-fluid multicarouselgrayBloc">
									<img data-src="<?php echo $this_modele_group_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_marque_name_meta.' '.$result_cross_gamme_car['MODELE_NAME']; ?>" width="270" height="135" class="w-100 img-fluid lazy anim"/>
									<p>
										<b><?php echo $pg_name_site." ".$PrixPasCher[$PrixPasCherTab]." ".$this_marque_name_site." ".$this_modele_name_site." ".$this_type_name_site.", ".$addon_title_seo_gamme_car; ?></b>
										<br>
										<?php echo $addon_content_seo_gamme_car; ?>.
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
$query_equipementier = "SELECT DISTINCT PM_ID, PM_NAME, SEG_CONTENT, PM_LOGO
	FROM __SEO_EQUIP_GAMME
	JOIN PIECES ON PIECE_PM_ID = SEG_PM_ID AND PIECE_PG_ID = SEG_PG_ID
	JOIN PIECES_MARQUE ON PM_ID = PIECE_PM_ID
	WHERE PIECE_PG_ID = $pg_id 
	AND PIECE_DISPLAY = 1
	AND SEG_CONTENT IS NOT NULL 
	AND PM_DISPLAY = 1
	ORDER BY PM_SORT, SEG_ID";
$request_equipementier = $conn->query($query_equipementier);
if ($request_equipementier->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>&Eacute;quipementiers  <?php echo $pg_name_site; ?></h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="McPm"  data-interval="1000">
				<div class="MultiCarousel-inner">

					<?php
					while($result_equipementier = $request_equipementier->fetch_assoc())
					{
						if($isMacVersion == false)
						{
							$this_pm_img = $result_equipementier['PM_LOGO'];
						}
						else
						{
							$this_pm_img = str_replace(".webp",".png",$result_equipementier['PM_LOGO']);
						}
					$this_pm_pic = $domain."/upload/equipementiers-automobiles/".$this_pm_img;
					?>
					<div class="item">
						<div class="pad15">

							<div class="container-fluid multicarouselwhiteBloc">
								<img data-src="<?php echo $this_pm_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_equipementier['PM_NAME']; ?>" title="<?php echo $result_equipementier['PM_NAME']; ?>" width="100" height="80" class="mw-100 img-fluid lazy"/>
								<p>
									<b><?php echo $pg_name_site; ?> <?php echo $result_equipementier['PM_NAME']; ?></b>
									<br>
									<?php echo content_cleaner(strip_tags($result_equipementier['SEG_CONTENT'])); ?>.
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
$query_information = "SELECT SGI_CONTENT 
	FROM __SEO_GAMME_INFO 
	WHERE SGI_PG_ID = $pg_id 
	ORDER BY SGI_ID";
$request_information = $conn->query($query_information);
if ($request_information->num_rows > 0) 
{
?>
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-12">

				<h2>Informations sur les <?php echo $pg_name_site; ?></h2>
				<div class="divh2"></div>
				<p>
					<?php
					while($result_information = $request_information->fetch_assoc())
					{
						echo "- ".$result_information["SGI_CONTENT"]."<br>";
					}
					?>
				</p>

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
function MM_jumpMenu(targ,selObj,restore){eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'"),restore&&(selObj.selectedIndex=0)}$(function(){$("#form-marq").change(function(){$("#form-year").load("_form.get.car.gamme.year.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid="+$("#form-marq").val()),document.getElementById("form-year").disabled=!1,$("#form-model").load("_form.get.car.gamme.modele.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid=0&formCarMarqueYear=0"),$("#form-type").load("_form.get.car.gamme.type.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-model").disabled=!0,document.getElementById("form-type").disabled=!0}),$("#form-year").change(function(){$("#form-model").load("_form.get.car.gamme.modele.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()),document.getElementById("form-model").disabled=!1,$("#form-type").load("_form.get.car.gamme.type.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0"),document.getElementById("form-type").disabled=!0}),$("#form-model").change(function(){$("#form-type").load("_form.get.car.gamme.type.php?formGammeid=<?php echo $pg_id; ?>&formCarMarqueid="+$("#form-marq").val()+"&formCarMarqueYear="+$("#form-year").val()+"&formCarModelid="+$("#form-model").val()),document.getElementById("form-type").disabled=!1})});
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
<?php $pageDisabled = "z"; include("412.page.php"); ?>
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