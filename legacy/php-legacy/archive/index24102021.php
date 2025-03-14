<?php 
session_start();
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="home";
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
<link rel="canonical" href="<?php echo $domain; ?>">
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.home.css" rel="stylesheet" media="all">
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
				<input type="hidden" name="ASK2PAGE" value="1" />
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
$query_marque = "SELECT MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME_META, MARQUE_LOGO    
	FROM AUTO_MARQUE
	WHERE MARQUE_DISPLAY = 1
	AND MARQUE_ID NOT IN (339,441) 
	ORDER BY MARQUE_SORT";
$request_marque = $conn->query($query_marque);
if ($request_marque->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Marques automobile</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="3,6,8,10" data-slide="1" id="McMarque"  data-interval="1000">
				<div class="MultiCarousel-inner">

					<?php
					while($result_marque = $request_marque->fetch_assoc())
					{
						if($result_marque == false)
							{
								$this_marque_img = $result_marque['MARQUE_LOGO'];
							}
							else
							{
								$this_marque_img = str_replace(".webp",".png",$result_marque['MARQUE_LOGO']);
							}
						$this_marque_pic = $domain."/upload/constructeurs-automobiles/marques-logos/".$this_marque_img;
						$thislinktoPage = $domain."/".$Auto."/".$result_marque['MARQUE_ALIAS']."-".$result_marque['MARQUE_ID'].".html";
					?>
					<div class="item">
						<div class="pad15">

							<div class="container-fluid multicarouselwhiteBloc">
								<a href="<?php echo $thislinktoPage; ?>"><img data-src="<?php echo $this_marque_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_marque['MARQUE_NAME_META']; ?>" title="<?php echo $result_marque['MARQUE_NAME_META']; ?>" width="94" height="111" class="mw-100 img-fluid lazy"/></a>
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
			<div class="col-12 pt-3">

				<p>
				Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.
				<br>
				Après avoir sélectionné le constructeur automobile correspondant à votre voiture vous pourrez sélectionner le modèle de votre véhicule.
				<br>
				Sinon vous pouvez vous rendre sur la page équipementier pour choisir votre marque préféré d'équipementier automobile, vous rendre sur la page d'accueil d'Automecanik et choisir directement une gamme de pièce détachée et ensuite choisir le modèle de votre véhicule ou la marque disponible pour cette pièce, ou vous rendre sur le blog d'Automecanik et choisir des articles de blog, des conseils, des méthodes de réparation correspondant au modèle de votre véhicule.
				<br>
				Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.
				</p>
				
			</div>
		</div>

	</div>
</div>
<?php
}
?>

<?php
$query_catalog_family = "SELECT DISTINCT MF_ID, IF(MF_NAME_SYSTEM IS NULL, MF_NAME, MF_NAME_SYSTEM) AS MF_NAME, 
	MF_DESCRIPTION, MF_PIC 
	FROM PIECES_GAMME 
	JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	JOIN CATALOG_FAMILY ON MF_ID = MC_MF_ID
	WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 AND MF_DISPLAY = 1
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
					FROM PIECES_GAMME 
					JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
					WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 
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
						$thislinktoPage = $domain."/".$Piece."/".$result_catalog_gamme['PG_ALIAS']."-".$result_catalog_gamme['PG_ID'].".html";
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
$query_top_gamme = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_IMG, 
	SG_TITLE, SG_DESCRIP, BA_PREVIEW 
	FROM PIECES_GAMME 
	JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID 
	JOIN __SEO_GAMME ON SG_PG_ID = PG_ID 
	JOIN __BLOG_ADVICE ON BA_PG_ID = PG_ID
	WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 AND PG_TOP = 1
	ORDER BY MC_MF_ID, MC_SORT";
$request_top_gamme = $conn->query($query_top_gamme);
if ($request_top_gamme->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Les pièces les plus vendues</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<div class="MultiCarousel" data-items="1,2,3,4" data-slide="1" id="McTopGamme"  data-interval="1000">
				<div class="MultiCarousel-inner">

					<?php
					while($result_top_gamme = $request_top_gamme->fetch_assoc())
					{
						if($isMacVersion == false)
						{
							$this_pg_img = $result_top_gamme['PG_IMG'];
						}
						else
						{
							$this_pg_img = str_replace(".webp",".jpg",$result_top_gamme['PG_IMG']);
						}
					$this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
					$this_pg_id = $result_top_gamme['PG_ID'];
					$this_pg_name_site = $result_top_gamme['PG_NAME'];
					$this_pg_name_meta = $result_top_gamme['PG_NAME_META'];
					$thislinktoZ = $domain."/".$Piece."/".$result_top_gamme['PG_ALIAS']."-".$result_top_gamme['PG_ID'].".html";

					$this_pagetitle = strip_tags($result_top_gamme['SG_TITLE']);
				    $this_pagedescription = strip_tags($result_top_gamme['SG_DESCRIP'].".");
					?>
					<div class="item">
						<div class="pad15">

							<div class="container-fluid multicarouselwhiteBloc">
								<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="img-fluid lazy anim"/>
								<p>
									<b><?php echo $this_pagetitle; ?></b>
									<br>
									<a href="<?php echo $thislinktoZ; ?>"><?php echo content_cleaner($this_pagedescription); ?></a>
									<br><?php echo content_cleaner($result_top_gamme['BA_PREVIEW']); ?>
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

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Automecanik, votre magasin en ligne des pièces détachées</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<p>
					Automecanik vous propose plusieurs références de pièces auto neuves et d'origine avec le meilleur rapport qualité/prix. Toutes vos pièces auto se trouvent dans un catalogue en ligne, groupées dans divers catégories : freinage (plaquettes, disques, étrier de frein...), filtration (filtre à huile, filtre à carburant...), moteur, direction/suspension, transmission, refroidissement (radiateur, pompe à eau...), éclairage, climatisation/ventilation, pièces électriques (alternateur, démarreur, vanne EGR, débitmétre...) et accessoires.
					<br> 
					Quelques soit le type de motorisation essence ou diesel, Automecanik vend pièces auto compatibles avec tous les constructeurs automobiles du marché tels que : Renault, Peugeot, Citroën, Audi, Fiat, Volkswagen, Ford, BMW, Mercedes, Alfa Romeo, Opel, Seat...etc. 
					<br>
					Toutes les pièces disponibles dans notre catalogue sont garanties par les plus grands équipementiers de pièces détachées automobile et conformes aux normes européenne comme Bosch, Valeo, Luk, Sachs, Delphi, Febi, SKF, TRW, SNR, Gates, Dayco, Continental, Magneti Marelli, Walker, Bosal, Bendix, ATE, Hella, Beru, Goodyear, Lizarte...etc.
				</p>

			</div>
		</div>

	</div>
</div>

<?php
$query_equipementier = "SELECT PM_ID , PM_NAME_META, PM_PREVIEW, PM_LOGO
	FROM PIECES_MARQUE
	WHERE PM_DISPLAY = 1 
	AND PM_TOP = 1
	ORDER BY PM_SORT";
$request_equipementier = $conn->query($query_equipementier);
if ($request_equipementier->num_rows > 0) 
{
?>
<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div class="col-12">

				<h2>Marque d'équipementiers d'origine et première monte des contructeurs automobiles</h2>
				<div class="divh2"></div>

			</div>
			<div class="col-12">

				<p>
					Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.<br>Après avoir sélectionné le constructeur automobile correspondant à votre voiture vous pourrez sélectionner le modèle de votre véhicule.
				</p>

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
									<b><?php echo $result_equipementier['PM_NAME_META']; ?></b>
									<br>
									<?php echo $result_equipementier['PM_PREVIEW']; ?>.
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
require_once('v7.analytics.track.php');
?>