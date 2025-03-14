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
<link rel="canonical" href="<?php echo $domain; ?>">
<!-- DNS PREFETCHING -->
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://ajax.googleapis.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/v2.style.home.min.css" rel="stylesheet" media="all"> 
</head>
<body>

<?php
require_once('v2.global.header.section.php');
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
			<img src="/assets/img/icon-catalog-car.png" class="img-fluid" alt="choisir véhicule"> Sélect. véhicule
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
		<div class="col-sm-3 col-md-2 headerSearchCar">

			<select name="form-year" id="form-year" disabled="disabled">
				<option>Année</option>
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
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<h1><?php echo $pageh1; ?></h1>
		</span>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 homemarqtxt">
	<!-- COL LEFT -->
		<p>Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.</p>
	<!-- / COL LEFT -->
	</div>
	<div class="col-12">
	<!-- COL RIGHT -->
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
			<div class="col-12 pl-md-3">

<div class="container-fluid p-2 pt-0 catalogFamilyContent pr-md-0">
	
	<div class="row">
	<?php
	while($result_marque = $request_marque->fetch_assoc())
	{
		if($isMacVersion == false)
		{
			$this_marque_img = $result_marque['MARQUE_LOGO'];
		}
		else
		{
			$this_marque_img = str_replace(".webp",".png",$result_marque['MARQUE_LOGO']);
		}
	$this_marque_pic = $domain."/upload/constructeurs-automobiles/marques-logos/".$this_marque_img;
	$thislinktoMQ = $domain."/".$Auto."/".$result_marque['MARQUE_ALIAS']."-".$result_marque['MARQUE_ID'].".html";
	?>
		<div class="col-6 col-sm-4 col-md-2 col-lg-1 p-1">
			

<div class="row p-0 m-0">
<a href="<?php echo $thislinktoMQ; ?>">
	<div class="col-12 text-center align-middle catalogFamilyContentPic p-1">
				
        <img data-src="<?php echo $this_marque_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_marque['MARQUE_NAME_META']; ?>" title="<?php echo $result_marque['MARQUE_NAME_META']; ?>" width="94" height="111" class="w-100 img-fluid lazy"/>

	</div>
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
	<!-- / COL RIGHT -->
	</div>
	<div class="col-12 homemarqtxt">
	<!-- COL LEFT -->
		<p>
		Après avoir sélectionné le constructeur automobile correspondant à votre voiture vous pourrez sélectionner le modèle de votre véhicule.
		<br>
		Sinon vous pouvez vous rendre sur la page équipementier pour choisir votre marque préféré d'équipementier automobile, vous rendre sur la page d'accueil d'Automecanik et choisir directement une gamme de pièce détachée et ensuite choisir le modèle de votre véhicule ou la marque disponible pour cette pièce, ou vous rendre sur le blog d'Automecanik et choisir des articles de blog, des conseils, des méthodes de réparation correspondant au modèle de votre véhicule.
		<br>
		Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.</p>
	<!-- / COL LEFT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>Les pièces les plus vendues</h2>
		</span>
	</div>
	<div class="col-12">
	<!-- COL RIGHT -->
		<?php
		$query_catalog_gamme = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME ,PG_NAME_URL ,PG_NAME_META , PG_IMG, 
			SG_TITLE, SG_DESCRIP, BA_PREVIEW 
			FROM PIECES_GAMME 
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID 
			JOIN __SEO_GAMME ON SG_PG_ID = PG_ID 
			JOIN __BLOG_ADVICE ON BA_PG_ID = PG_ID AND BA_STATUS = 1
			WHERE PG_DISPLAY = 1 AND PG_LEVEL = 1 AND PG_TOP = 1
			ORDER BY MC_MF_ID, MC_SORT";
			$request_catalog_gamme = $conn->query($query_catalog_gamme);
			if ($request_catalog_gamme->num_rows > 0) 
			{
			?>

	<div class="row p-0 m-0">
	<?php
	while($result_catalog_gamme = $request_catalog_gamme->fetch_assoc())
	{
		if($isMacVersion == false)
		{
			$this_pg_img = $result_catalog_gamme['PG_IMG'];
		}
		else
		{
			$this_pg_img = str_replace(".webp",".jpg",$result_catalog_gamme['PG_IMG']);
		}
	$this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
	$this_pg_id = $result_catalog_gamme['PG_ID'];
	$this_pg_name_site = $result_catalog_gamme['PG_NAME'];
	$this_pg_name_meta = $result_catalog_gamme['PG_NAME_META'];
	$thislinktoZ = $domain."/".$Piece."/".$result_catalog_gamme['PG_ALIAS']."-".$result_catalog_gamme['PG_ID'].".html";

	$this_pagetitle = strip_tags($result_catalog_gamme['SG_TITLE']);
    $this_pagedescription = strip_tags($result_catalog_gamme['SG_DESCRIP'].".");
	?>
		<div class="col-12 col-md-6 blocToPCar">

				<div class="container-fluid p-3 pb-0 mh57">
					<span><?php echo $this_pagetitle; ?></span>
				</div>
				<div class="container-fluid regularContentBordered2 mh257 p-3">
					<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_catalog_gamme['PG_NAME_META']; ?>" title="<?php echo $result_catalog_gamme['PG_NAME_META']; ?>" width="225" height="165" class="w-100 img-fluid lazy"/> 
					<a href="<?php echo $thislinktoZ; ?>"><?php echo content_cleaner($this_pagedescription); ?></a>
					<br><?php echo content_cleaner($result_catalog_gamme['BA_PREVIEW']); ?>
				</div>

		</div>
	<?php
	}
	?>
	</div>
			<?php
			}
		?>
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>équipementiers automobile</h2>
		</span>
	</div>
	<div class="col-12 homemarqtxt pt-3">
	<!-- COL LEFT -->
		<span><i>Marque d'équipementiers d'origine et première monte des contructeurs automobiles</i></span>
		<br>
		<img class="img-fluid" src="<?php echo $domain; ?>/assets/img/separator.png" alt="" />
		<p>Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique et selon le logo de la marque constructeur de votre véhicule.
		<br>
		Après avoir sélectionné le constructeur automobile correspondant à votre voiture vous pourrez sélectionner le modèle de votre véhicule.</p>
	<!-- / COL LEFT -->
	</div>
	<div class="col-12">
	<!-- COL RIGHT -->
		<?php
			$query_equip = "SELECT PM_ID , PM_NAME_META, PM_PREVIEW, PM_LOGO
			FROM PIECES_MARQUE
			WHERE PM_DISPLAY = 1 
			AND PM_TOP = 1
			ORDER BY PM_SORT";
			$request_equip = $conn->query($query_equip);
			if ($request_equip->num_rows > 0) 
			{
			?>
			<div class="col-12 pt-3">

<div class="container-fluid catalogFamilyContent pl-md-0 pr-md-0">
	
	<div class="row">
	<?php
	while($result_equip = $request_equip->fetch_assoc())
	{
		if($isMacVersion == false)
		{
			$this_pm_img = $result_equip['PM_LOGO'];
		}
		else
		{
			$this_pm_img = str_replace(".webp",".png",$result_equip['PM_LOGO']);
		}
	$this_pm_pic = $domain."/upload/equipementiers-automobiles/".$this_pm_img;
	?>
		<div class="col-12 col-md-6 p-1">
			

<div class="row p-0 m-0">
	<div class="col-12 text-center align-middle homeequiptxt">
				
        <img data-src="<?php echo $this_pm_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_equip['PM_NAME_META']; ?>" title="<?php echo $result_equip['PM_NAME_META']; ?>" width="100" height="80" class="w-100 img-fluid lazy"/>

        <br>
        <p class="align-justify">
        	<?php echo $result_equip['PM_PREVIEW']; ?>
        </p>

	</div>
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
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>Automecanik, votre magasin en ligne des pièces détachées</h2>
		</span>
	</div>
	<div class="col-12 homemarqtxt">
		<p class="text-justify">
		Automecanik vous propose plusieurs références de pièces auto neuves et d'origine avec le meilleur rapport qualité/prix. Toutes vos pièces auto se trouvent dans un catalogue en ligne, groupées dans divers catégories : freinage (plaquettes, disques, étrier de frein...), filtration (filtre à huile, filtre à carburant...), moteur, direction/suspension, transmission, refroidissement (radiateur, pompe à eau...), éclairage, climatisation/ventilation, pièces électriques (alternateur, démarreur, vanne EGR, débitmétre...) et accessoires.
		<br> 
		Quelques soit le type de motorisation essence ou diesel, Automecanik vend pièces auto compatibles avec tous les constructeurs automobiles du marché tels que : Renault, Peugeot, Citroën, Audi, Fiat, Volkswagen, Ford, BMW, Mercedes, Alfa Romeo, Opel, Seat...etc. 
		<br>
		Toutes les pièces disponibles dans notre catalogue sont garanties par les plus grands équipementiers de pièces détachées automobile et conformes aux normes européenne comme Bosch, Valeo, Luk, Sachs, Delphi, Febi, SKF, TRW, SNR, Gates, Dayco, Continental, Magneti Marelli, Walker, Bosal, Bendix, ATE, Hella, Beru, Goodyear, Lizarte...etc.
		</p>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

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
<!-- JS Bootstrap -->
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</script>-->

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