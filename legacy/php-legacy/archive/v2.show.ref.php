<?php
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

$log=$_SESSION['im7mylog'];
$mykey=$_SESSION['im7mykey'];

if(($mykey==md5("default"))||($mykey=="")||($mykey=="NULL"))
{
	$destinationLink = $accessExpiredLink;
	$ssid=0;
	$accessRequest = false;
    $destinationLinkMsg = "Expired";
    $destinationLinkGranted = 0;
}
else
{
	$rqverif= mysql_query("SELECT * FROM 2027_xmassdoc_reseller_access_code WHERE login='$log' AND keylog='$mykey'");
	if ($rsverif=mysql_fetch_array($rqverif))
	{
		if($rsverif["valide"]=='1')
		{
			$destinationLink = $accessPermittedLink;
			$ssid = $rsverif['id'];
			$accessRequest = true;
		    $destinationLinkMsg = "Granted";
		    $destinationLinkGranted = 1;
		}
		else
		{
			$destinationLink = $accessSuspendedLink;
			$ssid =0;
			$accessRequest = false;
		    $destinationLinkMsg = "Suspended";
		    $destinationLinkGranted = 0;
		}
	}
	else
	{
		$destinationLink = $accessRefusedLink;
		$ssid=0;
		$accessRequest = false;
	    $destinationLinkMsg = "Denied";
	    $destinationLinkGranted = 0;
	}
}
?>
<?php
if($accessRequest==true) 
{
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $domaincorename; ?></title>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<!-- Données de la page d'accueil -->
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet">
<!-- CSS Bootstrap -->
<link rel="stylesheet" href="<?php  echo $domainparent; ?>/zoomimg/dist/css/lightbox.min.css">
<link href="<?php echo $domainparent; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<?php  echo $domainparent; ?>/zoomimg/dist/js/lightbox-plus-jquery.min.js" async></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="<?php echo $domainparent; ?>/system/bootstrap/js/bootstrap.min.js"></script>
<link href="<?php echo $domain; ?>/assets/css/intern.css" rel="stylesheet">
</head>
<body>
<div id="mainPageContentCover">&nbsp;</div>
<?Php
// ENTETE DE PAGE
@require_once('header.first.section.php');
// LEFT PANEL
@require_once('header.left.section.php');
?>

<div id="mainPageContent">

<?php
$quest=$_GET["quest"];
$pg_id_filter=0;
if($_GET["pr"])
  {
    $pg_id_filter=$_GET["pr"];
  }
function ClearSearchQuest($quest)
{
$quest=str_replace("(","",$quest);
$quest=str_replace(")","",$quest);
$quest=str_replace("[","",$quest);
$quest=str_replace("]","",$quest);
$quest=str_replace(",","",$quest);
$quest=str_replace("’","",$quest);
$quest=str_replace("'","",$quest);
$quest=str_replace(" ","",$quest);
$quest=str_replace(".","",$quest);
$quest=str_replace("/","",$quest);
$quest=str_replace("_","",$quest);
$quest=str_replace("*","",$quest);
$quest=str_replace("-","",$quest);
return ($quest);
}
// CONFIGURATION
// Clean quest (reference propre)
$questCleaned=ClearSearchQuest($quest);
// Nombre d'article
$qCountArticle=mysql_query("SELECT COUNT(DISTINCT piece_id) AS CountArticle
FROM prod_pieces_reference
JOIN $sqltable_Piece ON piece_id = reference_piece_id
WHERE reference_research = '$questCleaned'
AND piece_affiche = 1");

$rCountArticle=mysql_fetch_array($qCountArticle);
$SearchContArticle = $rCountArticle["CountArticle"];
$pageh1 = "Reference Search result : ".$quest;

      // CART DATA
        $linktobackaftercart=$_SERVER['REQUEST_URI'];
        $UrlTakenToAddItem=$linktobackaftercart;
      // FIN CART

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
  <div class="container-fluid Page-Welcome-Title">



<div class="row">
  <div class="col">
      <h1><?php echo $pageh1; ?></h1>
      <h2>Select your product...</h2>
  </div>
  <div class="col-2 text-center p-0 pt-1">


<button type="button" class="MASS-SELECT-CAR"  data-toggle="modal" data-target="#exampleModalLong">
<img src="<?php  echo $domain; ?>/assets/img/car-icon.svg" style="max-height: 47px; ">
<br>
Select Your Car
</button>
<!-- Modal FICHE -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
    <div class="modal-content nopadding">
      <div class="modal-header nopadding">
        <h4 class="modal-title MODAL-ACCOUNT-TITLE" id="exampleModalLongTitle">
          <i>select your car
              <span><br>( manufacturer / model / year / motorization )</span></i></h4>
      </div>
      <div class="modal-body">

<div class="container-fluid">
<?php
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
?>
<div class="row">
  <div class="col-12">

<!-- FORMULAIRE -->
    <div class="row">
      <div class="col-12" style="padding: 27px;">

<!--  form-->
<form method="post" action="" >
<div class="row">
<div class="col-12 col-md-3 p-1"> 
  <select name="form-marq" id="form-marq" class="REF-SEARCH-CAR-FORM-INPUT-SELECT">
  <option>- Manufacturer -</option>
  <?php
  $qScarPop=mysql_query("SELECT DISTINCT marque_id , marque_name_site, marque_logo FROM $sqltable_Car_marque 
  WHERE marque_affiche = 1  AND marque_vt_ap = 1
  ORDER BY marque_name_site");
  while($rScarPop=mysql_fetch_array($qScarPop))
  {
  ?>
  <option value="<?php echo $rScarPop['marque_id']; ?>"><?php echo utf8_encode($rScarPop['marque_name_site']); ?></option>
  <?php
  }
  ?>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-year" id="form-year" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled>
    <option>- Year -</option>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-model" id="form-model" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled>
    <option>- Model -</option>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-type" id="form-type" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled onchange="MM_jumpMenu('parent',this,0)">
    <option>- Motorization -</option>
  </select>
</div>
</div>
</form>
<!-- / form-->

      </div>
    </div>
<!-- FIN FORMULAIRE -->

  </div>
</div>
  
<?php
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
?>
</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<!-- / Modal FICHE -->


  </div>
</div>


  </div>
  <div class="container-fluid Page-Welcome-Box">
		


<div class="row">
  <div class="col-2 GRID-BOX pl-0 pr-0">


<div class="filters">  

<?php
// GAMME FILTER
    $FilterName = "PRODUCTS";
    $FilterGroup = "filters-gammes";
    $LinkToSearch = $domain."/search/?quest=".$quest;

    ?>

<div class="row nopadding nomargin">
  <div class="col-12 text-center" style="background: #243238; padding: 17px;">
        <b style="color:#fff; font-size: 18px;"><?php echo $FilterName; ?> : </b>
  </div>
  <!--div class="col-12 text-center  nopadding nomargin" style="border: 1px solid  #fff; border-top:3px solid  #fff;">
        <button class="button is-checked w-100" style="border: 1px solid  #ebebeb; padding: 14px;"><u>ALL PRODUCTS</u></button>
  </div-->
  <div class="col-12 text-center nopadding nomargin" style="border: 1px solid  #fff; font-size: 15px; font-weight: 300;">

          <a href="<?php echo $LinkToSearch; ?>">
              <button class="button is-checked w-100" style="border: 1px solid  #ebebeb; padding: 14px;" data-filter="*"><u>RESET FILTER</u></button>
          </a>

      </div>
    <?php        
    // Critere selon la piece
    $qGcriVal =  mysql_query("SELECT DISTINCT pg_id, pg_alias , pg_name_site, pg_name_meta, pg_image
      FROM prod_pieces_reference
      JOIN $sqltable_Piece ON piece_id = reference_piece_id
      JOIN $sqltable_Piece_gamme ON pg_id = piece_pg_id
      WHERE reference_research = '$questCleaned'
      AND piece_affiche = 1 AND pg_affiche IN (1,2)
      ORDER BY pg_alias");
    while($rGcriVal = mysql_fetch_array($qGcriVal))
    {
      $FilterValue = utf8_encode($rGcriVal['pg_name_site']);
      $FilterClass = $rGcriVal['pg_alias'];

      $LinkToSearch = $domain."/search/?quest=".$quest."&pr=".$rGcriVal['pg_id'];
      ?>

      <div class="col-6 text-center nopadding nomargin" style="border: 1px solid  #fff; font-size: 15px; font-weight: 300;">

          <a href="<?php echo $LinkToSearch; ?>">
          <div class="row nopadding nomargin" style="border: 1px solid  #ebebeb; padding: 14px;">
            <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-IMG text-center">

              <?php
          // NEW ALGO APPEL PHOTO
            $gammePhoto = $domainparent."/upload/articles/gammes-produits/catalogue/".$rGcriVal['pg_image'];
            // FIN NEW ALGO APPEL PHOTO
            ?>
            <img src="<?php echo $gammePhoto; ?>" alt="<?php  echo $pg_name_meta; ?>" class="w-100" />

            </div>
            <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-TITLE text-center">
              <?php echo utf8_encode($rGcriVal['pg_name_site']); ?>
            </div>
          </div>
          </a>

      </div>
      <?php
    }
    ?>
</div>

    <?php
// GAMME FILTER
?>

<br> 

<?php
//if($isSmartPhoneVersion==0)
//{
?>
<?php
// EQUIP FILTER
    $FilterName = "BRAND";
    $FilterGroup = "filters-equips";
    ?>
    <div class="ui-group">
    <div class="button-group js-radio-button-group" data-filter-group="<?php echo $FilterGroup; ?>">


<div class="row nopadding nomargin">
  <div class="col-12 text-center" style="background: #243238; padding: 17px;">
        <b style="color:#fff; font-size: 18px;"><?php echo $FilterName; ?></b>
  </div>
  <div class="col-12 text-center  nopadding nomargin" style="border: 1px solid  #fff; border-top:3px solid  #fff;">
        <button class="button is-checked w-100" style="border: 1px solid  #ebebeb; padding: 14px;" data-filter="*"><u>RESET FILTER</u></button>
  </div>
    <?php        
    // Critere selon la piece
    $qGcriValChaine =  "SELECT DISTINCT pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo
      FROM prod_pieces_reference
      JOIN $sqltable_Piece ON piece_id = reference_piece_id
      JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id
      WHERE reference_research ='$quest'
      AND piece_affiche = 1  AND pm_affiche = 1 ";
    if($pg_id_filter>0) { $qGcriValChaine.=" AND piece_pg_id = '$pg_id_filter' "; }
    $qGcriValChaine.=" ORDER BY pm_alias ";
    $qGcriVal =  mysql_query($qGcriValChaine);
    while($rGcriVal = mysql_fetch_array($qGcriVal))
    {
      $FilterValue = utf8_encode($rGcriVal['pm_name_site']);
      $FilterClass = $rGcriVal['pm_alias'];
      ?>
      <div class="col-6 text-center nopadding nomargin" style="border: 1px solid  #fff; font-size: 15px; font-weight: 300;">
          <button class="button w-100" style="border: 1px solid  #ebebeb; padding: 4px;" data-filter=".<?php echo $FilterClass; ?>">
                          <img 
                          src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $rGcriVal['pm_logo']; ?>"class="w-100"/>
          </button>
      </div>
      <?php
    }
    ?>
</div>

    </div>
    </div>
    <?php
// EQUIP FILTER
?>
<?Php
//}
?>
</div>


  </div>
  <div class="col-10">

<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<div class="big-demo" data-js="hero-demo">
  
<div class="row">
  <div class="col-12">






<?php // GRID DES ARTICLES ?> 
<div class="grid">
<?php
$qArticleChaine="SELECT DISTINCT piece_id, piece_ref, piece_ref_propre, piece_name, piece_name_complement,
  piece_quantite, piece_qte_cond, piece_name_tec, piece_name_com, piece_designation, 
  piece_poids_g, piece_poids_kg, piece_longueur_metre, piece_poids_frais_port, 
  pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo, pm_quality, pm_nature,
  pg_id, pg_alias, pg_name_site, pg_name_meta
  FROM prod_pieces_reference
  JOIN $sqltable_Piece ON piece_id = reference_piece_id
  JOIN $sqltable_Piece_gamme ON pg_id = piece_pg_id
  JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id
  JOIN prod_pieces_prix ON piece_id = prix_piece_id AND prix_exist_dispo = 1
  WHERE reference_research = '$questCleaned'
  AND piece_affiche = 1 AND pg_affiche IN (1,2) AND pm_affiche = 1";
if($pg_id_filter>0) { $qArticleChaine.=" AND pg_id = '$pg_id_filter' "; }
$qArticleChaine.=" ORDER BY reference_reftype_id , prix_vente_ttc*piece_quantite";

$qArticle=mysql_query($qArticleChaine);
while($rArticle=mysql_fetch_array($qArticle))
{
// PIECE DATAS
$piece_id = $rArticle["piece_id"];
$piece_ref = $rArticle["piece_ref"];
$piece_ref_propre = $rArticle["piece_ref_propre"];
$piece_name = utf8_encode($rArticle["piece_name"]);
$piece_name_comp = utf8_encode($rArticle["piece_name_complement"]);
// PIECE SUPPLEMENTS DATAS
$piece_quantite = $rArticle["piece_quantite"];
$piece_nb_paquet = $rArticle["piece_qte_cond"];
$GetDT=utf8_encode($rArticle['piece_name_tec']);
$GetDC=utf8_encode($rArticle['piece_name_com']);
$GetDG=utf8_encode($rArticle['piece_designation']);
// FRAIS
$piece_poids_frais_port=number_format($rArticle['piece_poids_frais_port'] * $piece_quantite, 2, '.', '');
$piece_poids_g= $rArticle['piece_poids_g'];
$piece_poids_kg= $rArticle['piece_poids_kg'];
$piece_longueur_metre= $rArticle['piece_longueur_metre'];
// GAMME DATAS
$pg_id = $rArticle["pg_id"];
$pg_name_site = utf8_encode($rArticle["pg_name_site"]);
$pg_name_meta = utf8_encode($rArticle["pg_name_meta"]);
$pg_alias = utf8_encode($rArticle["pg_alias"]);
// EQUIPEMENTIERS DATAS
$pm_id = $rArticle['pm_id'];
$pm_alias = $rArticle['pm_alias'];
$pm_name_site = utf8_encode($rArticle["pm_name_site"]);
$pm_name_meta = utf8_encode($rArticle["pm_name_meta"]);
$pm_logo = $rArticle['pm_logo'];
$pm_quality = utf8_encode($rArticle['pm_quality']);
$pm_nature = utf8_encode($rArticle['pm_nature']);
// PRICE QUERY
$qPRIX=mysql_query("SELECT DISTINCT prix_piece_id , prix_code_ean, prix_reference, prix_designations, 
    prix_achat_ht, prix_achat_net_ht, prix_achat_ttc, 
    prix_frs_remise, prix_ssremise_ttc, prix_remise, 
    prix_marge_m2,
    prix_consigne_ttc, 
    prix_vente_ht, prix_vente_ttc, 
    prix_frais_port 
    FROM prod_pieces_prix 
    WHERE prix_piece_id = $piece_id AND prix_exist_dispo = 1 AND prix_unite = 1 
    ORDER BY prix_exist_net DESC");
if($rPRIX=mysql_fetch_array($qPRIX))
{
// PRICE DATAS
$piece_selling_price_ttc_unit = number_format($rPRIX['prix_vente_ttc'], 2, '.', '');
$piece_selling_price_ttc = $piece_selling_price_ttc_unit * $piece_quantite;
$piece_selling_price_ttc_integer = intval($piece_selling_price_ttc);
$piece_selling_price_ttc_float = number_format((($piece_selling_price_ttc - $piece_selling_price_ttc_integer) * 100), 0, '.', '');
$piece_old_price_ttc = number_format(($rPRIX['prix_ssremise_ttc']*$piece_quantite), 2, '.', '');
$piece_consigne_price_ttc = number_format(($rPRIX['prix_consigne_ttc']*$piece_quantite), 2, '.', '');
?>

                <?php
                $FilterClassList = $pg_alias;
                $CritereTechnique = "<br>";
                $qGcri =  mysql_query("SELECT DISTINCT lpcg_cri_id, lpcg_cri_name, pc_donnee AS critere_value
                  FROM $sqltable_Link_Piece_critere_gamme
                  JOIN prod_critere ON cri_id = lpcg_cri_id
                  JOIN prod_pieces_critere ON pc_cri_id = cri_id
                  WHERE lpcg_pg_id = $pg_id
                  AND pc_piece_id = $piece_id
                  ORDER BY lpcg_level, lpcg_tri");
                while($rGcri = mysql_fetch_array($qGcri))
                {
                  if(empty($rGcri['lpcg_cri_name']))
                  {
                    //$FilterClassList = $FilterClassList.url_title(utf8_encode($rGcri['critere_value']),'-')." ";
                    $CritereTechnique = $CritereTechnique.$pg_name_site." ".utf8_encode($rGcri['critere_value'])."<br>";
                  }
                  else
                  {
                    //$FilterClassList = $FilterClassList.url_title(utf8_encode($rGcri['critere_value']),'-')." ";
                    $CritereTechnique = $CritereTechnique.utf8_encode($rGcri['lpcg_cri_name'])." : ".utf8_encode($rGcri['critere_value'])."<br>";
                  }
                }
                ?>
<div class="col-12 nopadding container-piece-element-item <?php echo $pm_alias; ?>" data-category="piece-class-filter">

<?php include("v2.show.car.ref.item.php"); ?>

</div>
<?php
}
}
?>
</div>
<?php // FIN GRID DES ARTICLES ?>





    
  </div>
</div>


</div>
<!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID -->
<!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID -->
<!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID --><!-- / DATAGRID -->

  </div>
</div>




  </div>

<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

</div>

<?Php
// PIED DE PAGE
@require_once('footer.last.section.php');
?>
</body>
</html>
<script>
    $(function() {
        
        $("#form-marq").change(function() {
            $("#form-year").load("<?php echo $domain; ?>/v2.get.car.year.php?formCarMarqueid=" + $("#form-marq").val());
            document.getElementById("form-year").disabled = false;
            $("#form-model").load("<?php echo $domain; ?>/v2.get.car.model.php?formCarMarqueid=0&formCarMarqueYear=0");
            $("#form-type").load("<?php echo $domain; ?>/v2.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0");
            document.getElementById("form-model").disabled = true;
            document.getElementById("form-type").disabled = true;

        });

        $("#form-year").change(function() {
            $("#form-model").load("<?php echo $domain; ?>/v2.get.car.model.php?formCarMarqueid=" + $("#form-marq").val() + "&formCarMarqueYear=" + $("#form-year").val());
            document.getElementById("form-model").disabled = false;
            $("#form-type").load("<?php echo $domain; ?>/v2.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0");
            document.getElementById("form-type").disabled = true;
        });

        $("#form-model").change(function() {
            $("#form-type").load("<?php echo $domain; ?>/v2.get.car.type.php?formCarMarqueid=" + $("#form-marq").val() + "&formCarMarqueYear=" + $("#form-year").val() + "&formCarModelid=" + $("#form-model").val());
            document.getElementById("form-type").disabled = false;
        });



    });

function MM_jumpMenu(targ,selObj,restore){ //v3.0
eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}

</script>
<script src="<?php echo $domainparent; ?>/assets/js/isotope.pkgd.min.js"></script>
<script>
// external js: isotope.pkgd.js

//var $grid = $('.grid').isotope({
//    itemSelector: '.container-piece-element-item'
//  });

// store filter for each group
var filters = {};

$('.filters').on( 'click', '.button', function( event ) {
  // init Isotope
  var $grid = $('.grid').isotope({
    itemSelector: '.container-piece-element-item'
  });
  var $button = $( event.currentTarget );
  // get group key
  var $buttonGroup = $button.parents('.button-group');
  var filterGroup = $buttonGroup.attr('data-filter-group');
  // set filter for group
  filters[ filterGroup ] = $button.attr('data-filter');
  // combine filters
  var filterValue = concatValues( filters );
  // set filter for Isotope
  $grid.isotope({ filter: filterValue });
});

// change is-checked class on buttons
$('.button-group').each( function( i, buttonGroup ) {
  var $buttonGroup = $( buttonGroup );
  $buttonGroup.on( 'click', 'button', function( event ) {
    $buttonGroup.find('.is-checked').removeClass('is-checked');
    var $button = $( event.currentTarget );
    $button.addClass('is-checked');
  });
});
  
// flatten object by concatting values
function concatValues( obj ) {
  var value = '';
  for ( var prop in obj ) {
    value += obj[ prop ];
  }
  return value;
}

</script>
<style type="text/css">
  .tabcontent {
    display: none;
}
/* ---- button ---- */

.button {
  display: inline-block;
  color: #243238;
  border: 0px;
  background: none;
  font-weight: 400px;
}

.button:hover {
  color: #e82042;
}

.button:active,
.button.is-checked {
  color: #e82042;
}


/* ---- button-group ---- */
</style>
<?php
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
}
else
{
	//header("Location: ".$destinationLink);
	include("get.access.response.php");
}
?>