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

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-12">
				<p>
				<?php echo $pagecontent; ?>
				</p>
			</div>
		</div>

	</div>
</div>

<?php
$query_modele_group = "SELECT *
	FROM AUTO_MODELE
	WHERE MODELE_MARQUE_ID = $marque_id 
	AND MODELE_PARENT = 0
	AND MODELE_DISPLAY = 1
	ORDER BY MODELE_SORT , MODELE_NAME ASC";
$request_modele_group = $conn->query($query_modele_group);
if ($request_modele_group->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<?php
			while($result_modele_group = $request_modele_group->fetch_assoc())
			{
			$this_mdg_name = $result_modele_group["MODELE_NAME"];
			$this_mdg_alias = $result_modele_group["MODELE_ALIAS"];
			if($isMacVersion == false)
			{
			$this_mdg_pic = $result_modele_group['MODELE_PIC'];
			}
			else
			{
			$this_mdg_pic = str_replace(".webp",".png",$result_modele_group['MODELE_PIC']);
			}
			if(($this_mdg_pic=="no.webp")||($this_mdg_pic=="no.png")||($this_mdg_pic==""))
			{
				$this_mdg_picture = $domain."/upload/constructeurs-automobiles/marques-concepts/no.png";
			}
			else
			{
				$this_mdg_picture = $domain."/upload/constructeurs-automobiles/marques-modeles/".$marque_alias."/".$this_mdg_pic;
			}
			$this_mdg_date = "";
			if(empty($result_modele_group['MODELE_YEAR_TO']))
		    {
		    $this_mdg_date="du ".$result_modele_group['MODELE_MONTH_FROM']."/".$result_modele_group['MODELE_YEAR_FROM'];
		    }
		    else
		    {
		    $this_mdg_date="de ".$result_modele_group['MODELE_YEAR_FROM']." à ".$result_modele_group['MODELE_YEAR_TO'];
		    }
			?>
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3 pb-3">

					<div class="container-fluid multicarouselgrayforgrayBloc">
						<a href="<?php echo $domain.'/'.$blog.'/'.$constructeurs.'/'.$marque_alias.'/'.$this_mdg_alias; ?>">
						<img data-src="<?php echo $this_mdg_picture; ?>" src="/upload/loading-min.gif" 
						alt="<?php echo $marque_name_site; ?> <?php echo $this_mdg_name; ?>"
						class="w-100 img-fluid lazy anim" />
						<p>
							<b><?php echo $marque_name_site; ?> <?php echo $this_mdg_name; ?></b>
							<br><?php echo $this_mdg_date; ?>
						</p>
						</a>
					</div>

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
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-12">

				<h2>LES MODELES LES PLUS CONSULTÉES <?php echo $marque_name_site; ?></h2>
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
						?>
						<div class="item">
							<div class="pad15">

								<div class="container-fluid multicarouselgrayBloc">
									<img data-src="<?php echo $this_modele_group_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_marque_name_meta.' '.$result_cross_gamme_car['MODELE_NAME']; ?>" width="270" height="135" class="w-100 img-fluid lazy anim"/>
									<p>
										<b><?php echo content_cleaner($addon_title_seo_gamme_car); ?></b>
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