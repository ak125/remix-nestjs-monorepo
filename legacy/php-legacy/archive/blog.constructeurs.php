<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="constructeurs";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
// ARIANE
$arianetitle = $constructeurs_title;
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
<link rel="canonical" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $entretien; ?>">
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/style.blog.advice.min.css" rel="stylesheet" media="all">
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

	<div class="row">
		<div class="col-12 pt-3 pb-3">
			<span class="containerh2">
				<h2>Marques des constructeurs automobile</h2>
			</span>
		</div>
	</div>

	<div class="row p-0 m-0">
		<?php
		$query_marque = "SELECT MARQUE_ID, MARQUE_ALIAS, MARQUE_NAME_META, MARQUE_LOGO    
		FROM AUTO_MARQUE
		WHERE MARQUE_DISPLAY = 1
		AND MARQUE_ID NOT IN (339,441) 
		ORDER BY MARQUE_SORT";
		$request_marque = $conn->query($query_marque);
		if ($request_marque->num_rows > 0) 
		{
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
			$thislinktoMQ = $domain."/".$blog."/".$constructeurs."/".$result_marque['MARQUE_ALIAS'];
			?>
			<div class="col-6 col-sm-4 col-md-2 col-lg-1 p-1 text-center" style="background: #f1f2f3; border: 2px solid #fff;">
			
				<a href="<?php echo $thislinktoMQ; ?>">

				<img data-src="<?php echo $this_marque_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_marque['MARQUE_NAME_META']; ?>" title="<?php echo $result_marque['MARQUE_NAME_META']; ?>" width="94" height="111" class="mw-100 img-fluid lazy"/>

				</a>

			</div>
			<?php
		}
		}
		?>
	</div>

	<div class="row">
		<div class="col-12 pt-3 pb-3">
			<span class="containerh2">
				<h2>LES PIÈCES AUTOS D'ORIGINE OEM</h2>
			</span>
		</div>
		<div class="col-12 pb-3">
			<p class="text-justify contenth2">
			OEM abréviation de : Original Equipment Manufacturer.<br>
			Un producteur tiers sous contrat fournit des pièces OEM qui portent la marque du constructeur automobile. Le fabricant OEM procède au poinçonnage de l'article avec le logo de la marque du manufacturier, puis emballé et étiqueté dans le packaging de ce dernier.<br>
			Les pièces OEM en toutpoint identiques aux pièces notifiez première monte, c'est-à-dire celle qui équipait votre véhicule à sa sortie d'usine après la chaîne d'assemblage se vend exclusivement à travers le réseau des manufacturiers automobiles. Elles sont les seules à être conditionnées dans un emballage de la marque avec le logo du constructeur en question, dans la mesure où ces pièces de première monte suivent le dernier indice d'évolution de modification de la pièce. Mais aussi parce que, le concessionnaire engage sa réputation et offrent une garantie sur les pièces d'origine OEM.<br>
			Donc le fabricant équipementier qui a créé le produit original pour le constructeur automobile développe les pièces détachées OEM.Bien sûr,ces étapes supplémentaires augmentent le coût des équipements OEM et le prix pour le consommateur.<br>
			Dans le cadre du contrat entre le constructeur et l'équipementier, le producteur est autorisé par son partenaire à composer sa propre gamme de pièces auto. Cependant, les articles n'endossent pas le label du constructeur automobile. Au lieu de cela, ils portent la marque de l'équipementier auto qui les a élaborés.<br>
			Le composant a l'identique sans le logo constructeur, fabriqué par la même usine d'équipement, est disponible dans le commerce.
			Ces accessoires sont connus sous le nom de pièces OESLes pièces détachées OES sont des pièces d'origine sans la marque du constructeur automobile et sont fabriquées avec les machines et la même précision que les pièces d'origine, ce qui garantit un fonctionnement parfait.<br>
			Il est à noter que les concessionnaires se réservent une gamme nommée : pièces captives, affectées uniquement aux concessionnaire automobile.
			</p>
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
		WHERE TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MDG_DISPLAY = 1 AND MARQUE_DISPLAY = 1 
		AND CGC_LEVEL = 1
		GROUP BY TYPE_MARQUE_ID 
		ORDER BY CGC_ID, MARQUE_NAME, MODELE_NAME, TYPE_NAME";
	$request_cross_gamme_car = $conn->query($query_cross_gamme_car);
	if ($request_cross_gamme_car->num_rows > 0) 
	{
	?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>LES MODELES LES PLUS CONSULTÉES</h2>
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

</div>
</div>

<?php
require_once('global.footer.section.php');
?>

</body>
</html>
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