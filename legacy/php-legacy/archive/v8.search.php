<?php 
session_start();
// parametres relatifs à la page
$typefile="search";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// model required
require_once('v8model/items.php');
// Get Datas
if(isset($_GET['questCleaned']))
{
	$quest=$_GET["questCleaned"];
	$questCleaned=ClearSearchQuest($quest);
}
else
{
	$quest=$_POST["quest"];
	$questCleaned=ClearSearchQuest($quest);
}
// GLOBAL QUEST LISTING ITEMS 
	$result_items_list_array = get_search_items_list_array($questCleaned);
	// HAS ARTICLE TO PRINT
	$GammeCarCountArticle = 0;
	$GammeCarCountArticle = count(array_unique(array_column($result_items_list_array, 'PIECE_ID')));
	/*$query_item_count = "SELECT DISTINCT PIECE_ID
		FROM PIECES_REF_SEARCH
		INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID
		WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1";
	$request_item_count = $conn->query($query_item_count);
	$GammeCarCountArticle = $request_item_count->num_rows;*/
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// SEO & CONTENT
	//META
	$pagetitle = "Pièce détachée auto prix pas cher réf : ".$quest;
	$pagedescription = "Votre résultat de recherche pour : ".$quest;
	$pagekeywords = $quest;
	// CONTENT
	$pageh1 = "Résultat de recherche pour : ".$quest;
	// CLEAN SEO BEFORE PRINT
	$pagetitle = content_cleaner($pagetitle);
	$pagedescription = content_cleaner($pagedescription);
	$pagekeywords = content_cleaner($pagekeywords);
	$pageh1 = content_cleaner($pageh1);
// ROBOT
$pageRobots="noindex, nofollow";
$relfollow = 0;
// ARIANE
$arianetitle = $quest;
?>
<?php 
// parametres relatifs à la page
$arianefile="search";
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
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.search.css" rel="stylesheet" media="all">
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
		</div>

    </div>
</div>

<div class="container-fluid containerfiltersmobile">
    <button class="showfilters" onclick="openMyFilters()" >Afficher les filtres</button>
</div>

<div class="container-fluid containerPage">
    <div class="container-fluid mymaxwidth">
		
		<div class="row">
			<div id="myfilters" class="col-md-4 col-lg-3 containerfilters text-left 100vh">

				    <?php
				    $query_get_pg_filter = "SELECT DISTINCT PG_ID, PG_NAME, PG_ALIAS, COUNT(PIECE_ID) AS NBP 
						FROM PIECES_REF_SEARCH
						INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID  
						INNER JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
						WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 AND PG_DISPLAY = 1
						GROUP BY PIECE_PG_ID
						ORDER BY  PIECE_PG_ID , PIECE_SORT";
					$request_get_pg_filter = $conn->query($query_get_pg_filter);
					if ($request_get_pg_filter->num_rows > 1) 
					{
					?>
				    <form>
				    	<div></div>
				        <b>Gamme de produit</b>
				        <?php
			        	while ($result_get_pg_filter = $request_get_pg_filter->fetch_assoc()) 
						{
						$this_pg_name = $result_get_pg_filter['PG_NAME'];
						$this_pg_alias = $result_get_pg_filter['PG_ALIAS'];
				    	?>
						<label>
						<input type="checkbox" name="fl-pgselect" 
						value="<?php echo $this_pg_alias; ?>" 
						id="<?php echo $this_pg_alias; ?>" />
						&nbsp; &nbsp;<?php echo $result_get_pg_filter['PG_NAME']." (".$result_get_pg_filter['NBP'].")"; ?></label>
				        <?php
				    	}
				    	?>
				    </form>
				    <?php
					}
					?>

					<?php
					$query_get_eq_quality_filter = "SELECT DISTINCT PM_OES
						FROM PIECES_REF_SEARCH
						INNER JOIN PIECES_REF_BRAND ON PRB_ID = PRS_PRB_ID
						INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID 
				        INNER JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 		
						INNER JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID
						WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 
						AND PM_DISPLAY = 1 AND PRI_DISPO = 1 
						AND PM_OES IS NOT NULL
						ORDER BY PM_OES";
					$request_get_eq_quality_filter = $conn->query($query_get_eq_quality_filter);
					if ($request_get_eq_quality_filter->num_rows > 1) 
					{
					?>
				    <form>
				    	<div></div>
				        <b>Qualité</b>
				        <?php
						while ($result_get_eq_quality_filter = $request_get_eq_quality_filter->fetch_assoc()) 
						{
						$this_pm_quality = $result_get_eq_quality_filter['PM_OES'];
						if($this_pm_quality == 'A') $this_pm_quality = "AFTERMARKET";
				        else $this_pm_quality = "OES";
						$this_pm_quality_alias = url_title($this_pm_quality);
				    	?>
				    	<label>
						<input type="checkbox" name="fl-qualityselect" 
						value="<?php echo $this_pm_quality_alias; ?>" 
						id="<?php echo $this_pm_quality_alias; ?>" />
						&nbsp; &nbsp;<?php echo $this_pm_quality; ?>
						</label>
				        <?php
				    	}
				    	?>
				    	<?php
				    	$cs_pm_quality = "Echange Standard";
						$cs_pm_quality_alias = url_title($cs_pm_quality);
				    	?>
				    	<label>
						<input type="checkbox" name="fl-qualityselect" 
						value="<?php echo $cs_pm_quality_alias; ?>" 
						id="<?php echo $cs_pm_quality_alias; ?>" />
						&nbsp; &nbsp;<?php echo $cs_pm_quality; ?>
						</label>
				    </form>
				    <?php
					}
					?>

					<?php
					$query_get_stars_filter = "SELECT DISTINCT PM_NB_STARS 
						FROM PIECES_REF_SEARCH
						INNER JOIN PIECES_REF_BRAND ON PRB_ID = PRS_PRB_ID
						INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID 
				        INNER JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 		
						INNER JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID
						WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 
						AND PM_DISPLAY = 1 AND PRI_DISPO = 1 
						AND PM_NB_STARS > 0
						ORDER BY PM_NB_STARS DESC";
					$request_get_stars_filter = $conn->query($query_get_stars_filter);
					if ($request_get_stars_filter->num_rows > 1) 
					{
					?>
				    <form>
				    	<div></div>
				        <b>Etoiles</b>
				        <?php
						while ($result_get_stars_filter = $request_get_stars_filter->fetch_assoc()) 
						{
						$this_pm_stars = $result_get_stars_filter['PM_NB_STARS'];
						$this_pm_stars_alias = "st".$this_pm_stars."ars";
				    	?>
				    	<label class="stars">
						<input type="checkbox" name="fl-starsselect" 
						value="<?php echo $this_pm_stars_alias; ?>" 
						id="<?php echo $this_pm_stars_alias; ?>" />
						&nbsp; &nbsp;
						<?php
						$istars = 0;
						while($istars<$this_pm_stars) { echo '*'; $istars++; }
						if($istars<6)
						{
							echo "<u>";
							while($istars<6) { echo '*'; $istars++; }
							echo "</u>";
						}
						?>
						</label>
				        <?php
				    	}
				    	?>
				    </form>
				    <?php
					}
					?>
					
					<?php
					$query_get_eq_filter = "SELECT DISTINCT PM_ID, PM_NAME, PM_ALIAS  
						FROM PIECES_REF_SEARCH
						INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID
						INNER JOIN PIECES_MARQUE ON PM_ID = PIECE_PM_ID
						WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 AND PM_DISPLAY = 1
						ORDER BY PM_SORT";
					$request_get_eq_filter = $conn->query($query_get_eq_filter);
					if ($request_get_eq_filter->num_rows > 1) 
					{
					?>
				    <form>
				    	<div></div>
				        <b>Equipementiers</b>
				        <?php
						while ($result_get_eq_filter = $request_get_eq_filter->fetch_assoc()) 
						{
						$this_pm_name = $result_get_eq_filter['PM_NAME'];
						$this_pm_alias = $result_get_eq_filter['PM_ALIAS'];
				    	?>
				    	<label>
						<input type="checkbox" name="fl-pmselect" 
						value="<?php echo $this_pm_alias; ?>" 
						id="<?php echo $this_pm_alias; ?>" />
						&nbsp; &nbsp;<?php echo $this_pm_name; ?>
						</label>
				        <?php
				    	}
				    	?>
				    </form>
				    <?php
					}
					?>

					<div class="containerfiltersmobileapply">
						<button class="applyfilters" onclick="closeMyFilters()">appliquer</button>
						<button class="abortfilters" onclick="closeMyFilters()">annuler</button>
					</div>

			</div>
			<div class="col-md-8 col-lg-9 text-left">
		
				<div class="row">
				    <div class="col-12 headerlist">
				        <b><?php echo $GammeCarCountArticle; ?></b> produits disponibles
				    </div>
				    <div class="col-12 p-0">

						<div class="flowers">
				        <?php
						$result_bloc_listing = array_unique(array_column($result_items_list_array, 'PIECE_ID'));
				        foreach ($result_bloc_listing as $result_row => $result_value) 
				        {
				        $row_to_print = -1;
        				$row_to_print = $result_row;
				        $filter_pg_id = $result_items_list_array[$row_to_print]['PG_ID'];
						$filter_pg_name = $result_items_list_array[$row_to_print]['PG_NAME'];
						$filter_pg_alias = $result_items_list_array[$row_to_print]['PG_ALIAS'];
						$filter_flower_id = $filter_pg_alias;
				        /*
				        ?>
				        <div class="flower itemsTitle" data-id="<?php echo $filter_flower_id; ?>" data-category="<?php echo $filter_pg_alias; ?>">

				        	<h2><?php echo $filter_pg_name; ?></h2>
				        	<div></div>

				        </div>
				        <?php
				        */
            			$this_piece_name= $result_items_list_array[$row_to_print]['PIECE_NAME']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_SIDE']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_COMP'];
				        $piece_id_this = $result_items_list_array[$row_to_print]['PIECE_ID'];
				        $this_piece_ref = $result_items_list_array[$row_to_print]['PIECE_REF'];
				        $this_pm_id = $result_items_list_array[$row_to_print]['PM_ID'];
				        $this_pm_name = $result_items_list_array[$row_to_print]['PM_NAME'];
				        $this_pm_quality = $result_items_list_array[$row_to_print]['PM_OES'];
				        if($this_pm_quality == 'A') $this_pm_quality = "AFTERMARKET";
				        else $this_pm_quality = "OES";
				        $this_pm_stars = $result_items_list_array[$row_to_print]['PM_NB_STARS'];
						$piece_has_oem_this = $result_items_list_array[$row_to_print]['PIECE_HAS_OEM'];
				        $filter_pm_alias = url_title($result_items_list_array[$row_to_print]['PM_NAME']);
				        $filter_pm_quality_alias = url_title($this_pm_quality);
				        $filter_pm_stars = $result_items_list_array[$row_to_print]['PM_NB_STARS'];
						$filter_pm_stars_alias = "st".$filter_pm_stars."ars";
						$price_PV_TTC = $result_items_list_array[$row_to_print]['PRI_VENTE_TTC'] * $result_items_list_array[$row_to_print]['PIECE_QTY_SALE'];
						$price_CS_TTC = $result_items_list_array[$row_to_print]['PRI_CONSIGNE_TTC'] * $result_items_list_array[$row_to_print]['PIECE_QTY_SALE'];
						// ON VERIFIE SI C EST ECHANGE STANDARD
						if($price_CS_TTC>0)
						{
							$this_pm_quality = "Echange Standard";
							$filter_pm_quality_alias = url_title($this_pm_quality);
						}
						// PM LOGO
						if($isMacVersion == false)
						{
							$this_pm_img = $result_items_list_array[$row_to_print]['PM_LOGO'];
						}
						else
						{
							$this_pm_img = str_replace(".webp",".png",$result_items_list_array[$row_to_print]['PM_LOGO']);
						}
						// PHOTO
						if($result_items_list_array[$row_to_print]['PIECE_HAS_IMG']==1)
						{
							$photo_link = $result_items_list_array[$row_to_print]['PIECE_IMG'];
							$photo_alt = $result_items_list_array[$row_to_print]['PIECE_NAME']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_SIDE']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_COMP']." ".$result_items_list_array[$row_to_print]['PM_NAME']." ".$result_items_list_array[$row_to_print]['PIECE_REF'];
							$photo_title = $result_items_list_array[$row_to_print]['PIECE_NAME']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_SIDE']." ".$result_items_list_array[$row_to_print]['PIECE_NAME_COMP']." ".$marque_name_site." ".$modele_name_site. " ".$result_items_list_array[$row_to_print]['PM_NAME']." ".$result_items_list_array[$row_to_print]['PIECE_REF'];
						}
						else
						{
			                $photo_link = "upload/articles/no.png";
			                $photo_alt = "";
			                $photo_title = "";
						}
						?>
<div class="flower" data-id="ref<?php echo $piece_id_this; ?>" data-category="<?php echo $filter_pg_alias; ?> <?php echo $filter_pm_quality_alias; ?> <?php echo $filter_pm_stars_alias; ?> <?php echo $filter_pm_alias; ?>">

	<div class="itemsBloc">

		<img data-src="<?php echo $domain; ?>/upload/equipementiers-automobiles/<?php echo $this_pm_img; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pm_name; ?>" title="<?php echo $this_pm_name; ?>" width="75" height="60" class="mw-100 pm img-fluid lazy"/>

		<div class="pm">
			<?php echo $this_pm_quality; ?><br>
			<b>
			<?php
			$istars = 0;
			while($istars<$this_pm_stars) { echo '*'; $istars++; }
			if($istars<6)
			{
			echo "<u>";
			while($istars<6) { echo '*'; $istars++; }
			echo "</u>";
			}
			?>
			</b>
		</div>

		<?php
		if($isMacVersion == false)
		{
		?>
			<img data-src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" src="/upload/loading-min.gif" alt="<?php echo $photo_alt; ?>" title="<?php echo $photo_title; ?>" width="360" height="360" class="w-100 tech img-fluid lazy"/>
		<?php
		}
		?>
		<b><?php echo $this_piece_name; ?><br>Ref : <?php echo $this_piece_ref; ?></b>

		<p>
		<?php
		$count_criteria = 0;
		foreach ($result_items_list_array as $result_row_attribute) 
        {
            if(($result_row_attribute['PIECE_ID'] == $piece_id_this)&&($count_criteria<3))
            {
				echo $result_row_attribute["PCL_CRI_CRITERIA"]." : <span>".$result_row_attribute["PCL_VALUE"]." ".$result_row_attribute["PCL_CRI_UNIT"]."</span><br>";
            	$count_criteria++;
            }
		}
		?>
		</p>

		<span class="price">
			<?php
			$price_PV_TTC_integer = intval($price_PV_TTC);
			$price_PV_TTC_float = number_format((($price_PV_TTC - $price_PV_TTC_integer) * 100), 0, '.', '');
			?>
			<?php echo $price_PV_TTC_integer; ?><strong>.<?php printf("%02d",$price_PV_TTC_float); ?> <?php echo $GlobalSiteCurrencyChar; ?></strong>
			<br>
			<u><?php 
			if($price_CS_TTC>0)
			{
				?>
				Consigne <?php echo number_format($price_CS_TTC, 2, '.', ''); ?> <?php echo $GlobalSiteCurrencyChar; ?>
				(Total <?php echo number_format(($price_PV_TTC + $price_CS_TTC), 2, '.', ''); ?> <?php echo $GlobalSiteCurrencyChar; ?> TTC)
				<?php
			}
			else
			{
				echo "Prix TTC";
			}
			?></u>
		</span>
		<button type="button" class="btn rounded-0 view" 
		data-toggle="modal" 
		data-target="#pieceTechnicalModal" 
		aria-label="Fiche" 
		data-whatever="<?php echo $piece_id_this; ?>/0"><i class="pe-7s-look"></i></button>
		<button type="button" class="btn rounded-0 cart" 
		data-toggle="modal" 
		data-target="#addtomyCart" 
		aria-label="Cart" 
		data-whatever="<?php echo $piece_id_this; ?>"><i class="pe-7s-cart"></i></button>

	</div>

</div>
						<?php
				    	}
				        ?>
				    	</div>

				    </div>
				</div>

			</div>
		</div>


	</div>
</div>

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
var $filterCheckboxes = $('input[type="checkbox"]');

$filterCheckboxes.on('change', function() {

  var selectedFilters = {};

  $filterCheckboxes.filter(':checked').each(function() {

    if (!selectedFilters.hasOwnProperty(this.name)) {
      selectedFilters[this.name] = [];
    }

    selectedFilters[this.name].push(this.value);

  });

  // create a collection containing all of the filterable elements
  var $filteredResults = $('.flower');

  // loop over the selected filter name -> (array) values pairs
  $.each(selectedFilters, function(name, filterValues) {

    // filter each .flower element
    $filteredResults = $filteredResults.filter(function() {

      var matched = false,
        currentFilterValues = $(this).data('category').split(' ');

      // loop over each category value in the current .flower's data-category
      $.each(currentFilterValues, function(_, currentFilterValue) {

        // if the current category exists in the selected filters array
        // set matched to true, and stop looping. as we're ORing in each
        // set of filters, we only need to match once

        if ($.inArray(currentFilterValue, filterValues) != -1) {
          matched = true;
          return false;
        }
      });

      // if matched is true the current .flower element is returned
      return matched;

    });
  });

  $('.flower').hide().filter($filteredResults).show();

});

</script>

<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
</script>

<script type="text/javascript">
$(function() {
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 197) {
            $(".containerfiltersmobile").addClass('sticky');
        } else {
            $(".containerfiltersmobile").removeClass("sticky");
        }
    });
});
</script>

<?php
// fichier de panier shopping add / print
include('global.mycart.call.inpage.php');
?>

<?php
// fichier de panier shopping add / print
include('v7.products.fiche.call.php');
?>

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
?>