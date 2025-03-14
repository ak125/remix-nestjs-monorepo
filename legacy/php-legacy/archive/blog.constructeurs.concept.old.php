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
$query_selector = "SELECT MDG_ID, MDG_DISPLAY, MARQUE_ID, MARQUE_DISPLAY  
	FROM AUTO_MODELE_GROUP 
	JOIN AUTO_MARQUE ON MARQUE_ID = MDG_MARQUE_ID
	WHERE MDG_ALIAS = '$mdg_alias' 
	AND MARQUE_ALIAS = '$marque_alias'";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	if($result_selector['MDG_DISPLAY']==1)
	{
	$marque_id = $result_selector['MARQUE_ID'];
	$mdg_id = $result_selector['MDG_ID'];
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_item = "SELECT * 
	FROM AUTO_MODELE_GROUP 
	JOIN AUTO_MARQUE ON MARQUE_ID = MDG_MARQUE_ID
	WHERE MDG_ID = '$mdg_id' 
	AND MARQUE_ID = '$marque_id'  
	AND MDG_DISPLAY = 1 
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
	$mdg_name = $result_item['MDG_NAME'];
    $mdg_alias = $result_item["MDG_ALIAS"];
    		if($isMacVersion == false)
			{
				$mdg_pic = $result_modele_group['MDG_PIC'];
			}
			else
			{
				$mdg_pic = str_replace(".webp",".png",$result_modele_group['MDG_PIC']);
			}
			if(($mdg_pic=="no.webp")||($mdg_pic=="no.png")||($mdg_pic==""))
			{
				$mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/no.png";
			}
			else
			{
				$mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/".$marque_alias."/".$mdg_pic;
			}

    // SEO & CONTENT
    	// META
		$pagetitle="Pièces auto ".$result_item['MARQUE_NAME_META']." ".$result_item['MDG_NAME']." à prix pas cher";
        $pagedescription = "Automecanik vous offre toutes les pièces et accessoires autos à prix pas cher pour ".$result_item['MARQUE_NAME_META']." ".$result_item['MDG_NAME'];
        $pagekeywords = $result_item['MARQUE_NAME'].", ".$result_item['MDG_NAME'];
        // CONTENT
        $pageh1 = " Choisissez votre ".$result_item['MARQUE_NAME']." ".$result_item['MDG_NAME'];
        $pagecontent = "Un vaste choix de pièces détachées <b>".$result_item['MARQUE_NAME']." ".$result_item['MDG_NAME']."</b> au meilleur tarif et de qualité irréprochable proposées par les grandes marques d'équipementiers automobile de première monte d'origine. Consultez notre catalogue de pièces auto <b>".$result_item['MARQUE_NAME']." ".$result_item['MDG_NAME']."</b> proposé par Automecanik.com et choisissez des pièces compatibles tel que kit distribution, kit embrayage, alternateur, démarreur, échappement et autres pièces.";	
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
					WHERE MODELE_MDG_ID = $mdg_id 
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
					<h2><?php echo $this_modele_name; ?></h2>
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