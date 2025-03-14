<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$marque_alias=$_GET["marque_alias"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT MARQUE_ID, MARQUE_DISPLAY 
	FROM AUTO_MARQUE 
	WHERE MARQUE_ALIAS = '$marque_alias'";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['MARQUE_DISPLAY']==1)
	{
	$marque_id = $result_selector['MARQUE_ID'];
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_item = "SELECT * 
	FROM AUTO_MARQUE 
	WHERE MARQUE_ID = '$marque_id' 
	AND MARQUE_DISPLAY = 1";
$request_item = $conn->query($query_item);
$result_item = $request_item->fetch_assoc();
	// MARQUE DATA
	$marque_name_site = $result_item['MARQUE_NAME'];
	$marque_name_meta = $result_item['MARQUE_NAME_META'];
    $marque_alias = $result_item["MARQUE_ALIAS"];
    $marque_relfollow = $result_item["MARQUE_RELFOLLOW"];
    if($isMacVersion == false)
	{
	$this_marque_img = $result_marque['MARQUE_LOGO'];
	}
	else
	{
	$this_marque_img = str_replace(".webp",".png",$result_marque['MARQUE_LOGO']);
	}
	$this_marque_pic = $domain."/upload/constructeurs-automobiles/marques-logos/".$this_marque_img;
    // SEO & CONTENT
    $query_seo = "SELECT BSM_TITLE, BSM_DESCRIP, BSM_KEYWORDS, BSM_H1, BSM_CONTENT 
    	FROM __BLOG_SEO_MARQUE 
		WHERE BSM_MARQUE_ID = $marque_id ";
	$request_seo = $conn->query($query_seo);
	if ($request_seo->num_rows > 0) 
	{
		$result_seo = $request_seo->fetch_assoc();
		// META
		$pagetitle = strip_tags($result_seo['BSM_TITLE']);
	    $pagedescription = strip_tags($result_seo['BSM_DESCRIP']);
	    $pagekeywords = strip_tags($result_seo["BSM_KEYWORDS"]);
	    // CONTENT
		$pageh1 = strip_tags($result_seo['BSM_H1']);
		$pagecontent = strip_tags($result_seo['BSM_CONTENT'],"<a><b><br><strong><u>");
	}
	else
	{
		// META
		$pagetitle = "Pièces détachées ".$result_item['MARQUE_NAME_META']." à prix pas cher";
        $pagedescription = "Automecanik vous offre tous les conseilles d'achat de  toutes les pièces et accessoires autos à prix pas cher  du constructeur ".$result_item['MARQUE_NAME_META'];
        $pagekeywords = $result_item['MARQUE_NAME'];
        //H1
        $pageh1 = "Choisissez votre véhicule ".$result_item['MARQUE_NAME'];
        // CONTENT
        $query_seo_content = "SELECT SM_CONTENT 
	    	FROM __SEO_MARQUE 
			WHERE SM_MARQUE_ID = $marque_id";
		$request_seo_content = $conn->query($query_seo_content);
		if ($request_seo_content->num_rows > 0) 
		{
			$result_seo_content = $request_seo_content->fetch_assoc();
			$pagecontent = strip_tags($result_seo_content['SM_CONTENT'],"<a><b><br><strong><u>");
		}
		else
		{
	  		$pagecontent="Un vaste choix de pièces détachées <b>".$marque_name_site."</b> au meilleur tarif et de qualité irréprochable proposées par les grandes marques d'équipementiers automobile de première monte d'origine. Consultez notre catalogue de pièces auto <b>".$marque_name_site."</b> proposé par Automecanik.com et choisissez des pièces compatibles tel que kit distribution, kit embrayage, alternateur, démarreur, échappement et autres pièces.";
	  	}
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
  	$canonicalLink = $domain."/".$blog."/".$constructeurs."/".$marque_alias;
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
	$parent_arianelink = $constructeurs;
	$parent_arianetitle = $constructeurs_title;
	$arianetitle = $marque_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="marque";
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
	<div class="col-12">
	<!-- COL LEFT -->

			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh1">
					<h1><?php echo $pageh1; ?></h1>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				<p class="text-justify contenth2"><?php echo $pagecontent; ?></p>
			</div>
			</div>

			<div class="row">
			<?php
			$query_modele_group = "SELECT *
					FROM AUTO_MODELE_GROUP
					WHERE MDG_MARQUE_ID = $marque_id 
					AND MDG_DISPLAY = 1
					ORDER BY MDG_NAME ASC";
			$request_modele_group = $conn->query($query_modele_group);
			if ($request_modele_group->num_rows > 0) 
			{
			while($result_modele_group = $request_modele_group->fetch_assoc())
			{
			$this_mdg_name = $result_modele_group["MDG_NAME"];
			$this_mdg_alias = $result_modele_group["MDG_ALIAS"];
			if($isMacVersion == false)
			{
			$this_mdg_pic = $result_modele_group['MDG_PIC'];
			}
			else
			{
			$this_mdg_pic = str_replace(".webp",".png",$result_modele_group['MDG_PIC']);
			}
			if(($this_mdg_pic=="no.webp")||($this_mdg_pic=="no.png")||($this_mdg_pic==""))
			{
				$this_mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/no.png";
			}
			else
			{
				$this_mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/".$marque_alias."/".$this_mdg_pic;
			}
			?>
			<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3 blocToBlogItem">

					<div class="container-fluid p-3 pb-0 mh57">
						<i><?php echo $marque_name_site; ?> <?php echo $this_mdg_name; ?></i>
					</div>
					<div class="container-fluid p-3 mh167 text-center">
						<img data-src="<?php echo $this_mdg_picture; ?>" src="/upload/loading-min.gif" 
						alt="<?php echo $marque_name_site; ?> <?php echo $this_mdg_name; ?>"
						class="w-100 lazy" />
					</div>
					<div class="container-fluid regularContentBordered text-center pb-3">
						<a href="<?php echo $domain.'/'.$blog.'/'.$constructeurs.'/'.$marque_alias.'/'.$this_mdg_alias; ?>" class="blog-read-more">Lire plus</a>
					</div>

			</div>
			<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
			<?php
			}
			}
			?>
			</div>

	<!-- / COL LEFT -->
	</div>
	<div class="col-md-4 col-lg-3">
	<!-- COL RIGHT -->
	<!-- / COL RIGHT -->
	</div>
	</div>

	<?php
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
		WHERE  CGC_MARQUE_ID = $marque_id 
		AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MDG_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 1 
		ORDER BY CGC_ID, MODELE_NAME, TYPE_NAME";
	$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
	if ($request_cross_gamme_car->num_rows > 0) 
	{
	?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>LES MODELES LES PLUS CONSULTÉES <?php echo $marque_name_site; ?></h2>
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
			$addon_content_seo_gamme_car="Catalogue pièces détachées pour ".$this_marque_name_meta_title." ".$this_modele_name_meta." ".$this_type_name_meta." ".$this_type_nbch." ch ".$this_type_date." neuves #CompSwitch#";
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
				<div class="container-fluid regularContentBordered p-3 mh167">
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
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->


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