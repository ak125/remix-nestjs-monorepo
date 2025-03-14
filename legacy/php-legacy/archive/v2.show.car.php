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
<link href="<?php echo $domainparent; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
$marque_id=$_GET["marque_id"];
$modele_id=$_GET["modele_id"];
$type_id=$_GET["type_id"];

$queryPage=mysql_query("SELECT type_id FROM $sqltable_Car_type 
	JOIN $sqltable_Car_modele ON modele_id = type_modele_id
	JOIN $sqltable_Car_marque ON marque_id =type_marque_id
	WHERE type_id = '$type_id' AND type_modele_id = '$modele_id' AND type_marque_id = '$marque_id' ORDER BY type_id LIMIT 0,1");
if($resultPage=mysql_fetch_array($queryPage))
{
$queryPage=mysql_query("SELECT type_id FROM $sqltable_Car_type 
	JOIN $sqltable_Car_modele ON modele_id = type_modele_id
	JOIN $sqltable_Car_marque ON marque_id =type_marque_id
	WHERE type_id = '$type_id' AND type_modele_id = '$modele_id' AND type_marque_id = '$marque_id'
	AND type_affiche = 1 AND modele_affiche = 1 AND modele_vt_ap = 1 AND marque_affiche = 1 AND marque_vt_ap = 1");
if($resultPage=mysql_fetch_array($queryPage))
{// CONFIGURATION
    // marque query
    $queryMarque=mysql_query("SELECT marque_id, marque_alias, marque_name_meta, marque_name_site, marque_logo, marque_relfollow
    FROM $sqltable_Car_marque 
    WHERE marque_id = '$marque_id' 
    AND marque_affiche = 1 AND marque_vt_ap = 1 ");
    $resultMarque=mysql_fetch_array($queryMarque);
	    // marque datas
	    $marque_alias = $resultMarque["marque_alias"];
	    $marque_name_site = utf8_encode($resultMarque["marque_name_site"]);
	    $marque_name_meta = utf8_encode($resultMarque["marque_name_meta"]);
	    // marque logo
	    $marque_logo = $resultMarque["marque_logo"];
	    // marque robot (index / no index)
	    $marque_relfollow = $resultMarque["marque_relfollow"];

    // modele query
    $queryModele=mysql_query("SELECT modele_id, modele_alias, modele_name_meta, modele_name_site, modele_image, modele_relfollow
    FROM $sqltable_Car_modele
    WHERE modele_id = '$modele_id' 
    AND modele_affiche = 1 AND modele_vt_ap = 1 ");
    $resultModele=mysql_fetch_array($queryModele);
	    // modele datas
	    $modele_alias = $resultModele["modele_alias"];
	    $modele_name_site = utf8_encode($resultModele["modele_name_site"]);
	    $modele_name_meta = utf8_encode($resultModele["modele_name_meta"]);
	    // modele image
		if(empty($resultModele['modele_image']))
		{
		$modele_image = $domainparent."/upload/constructeurs-automobiles/marques-modeles/no.jpg";
		$modele_image_meta_alt = "";
		}
		else
		{
		$modele_image = $domainparent."/upload/constructeurs-automobiles/marques-modeles/".$marque_alias."/".$resultModele['modele_image'];
		$modele_image_meta_alt = $marque_name_meta.' '.$modele_name_meta;
		}

    // motorisation query
    $query=mysql_query("SELECT type_id, type_alias, type_name_meta, type_name_site, type_relfollow,
    	type_ch, type_kw, type_design, type_carburant, type_boite, type_cylindre, type_volume_cube,
    	LEFT(type_date_debut,4) AS type_date_debut,
    	RIGHT(type_date_debut,2) AS type_date_debut_mois,
    	LEFT(type_date_fin,4) AS type_date_fin
    FROM $sqltable_Car_type 
    WHERE type_id = '$type_id' 
    AND type_affiche = 1 ");
    $result=mysql_fetch_array($query);
	    // motorisation datas
	    $type_alias = $result["type_alias"];
	    $type_name_site = utf8_encode($result["type_name_site"]);
	    $type_name_meta = utf8_encode($result["type_name_meta"]);
	    // motorisation technical data
	    $type_nbch=$result['type_ch'];
	    $type_carosserie=utf8_encode($result['type_design']);
	    $type_fuel=utf8_encode($result['type_carburant']);
		if(empty($result['type_date_fin']))
		{
		$type_date="du ".$result['type_date_debut_mois']."/".$result['type_date_debut'];
		}
		else
		{
		$type_date="de ".$result['type_date_debut']." à ".$result['type_date_fin'];
		}
		// autres infos
		$pagealimTyToPrint=$result['type_carburant'];
		$pagecylindTyToPrint=$result['type_cylindre'];
		$pagepuissTyToPrint=$result['type_ch']." ch (".$result['type_kw']." kw)";
		$pagedesignTyToPrint=$result['type_design'];
		$pagemoteurTyToPrint=$result['type_volume_cube']." cm3";
		$pageboiteTyToPrint=$result['type_boite'];
		$typidcodemoteur=$type_id;
		$q10042015=mysql_query(" SELECT DISTINCT tcm_code_moteur FROM prod_auto_type_code WHERE tcm_type_id = '$typidcodemoteur'");
		$codemnb=0;
		$pagecodemoteurTyToPrint="";
		while($r10042015=mysql_fetch_array($q10042015))
		{
		if($codemnb>0) { $pagecodemoteurTyToPrint.=" &nbsp;-&nbsp; "; }
		$pagecodemoteurTyToPrint.=$r10042015["tcm_code_moteur"];
		$codemnb++;
		}
		if($codemnb==0)
		{
		$pagecodemoteurTyToPrint="_";
		}
	$pageh1 = $marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_fuel." ".$type_nbch." ch ".$type_date;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
  <div class="container-fluid Page-Welcome-Title">

      <h1><?php echo $pageh1; ?></h1>
      <h2>Select your product...</h2>

  </div>
	<div class="container-fluid Page-Welcome-Box">
		
<div class="row">
	<div class="col-4 GRID-BOX">

<!-- FORMULAIRE -->
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE cyan text-center">
							CHANGE your car
							<span><br>( manufacturer / model / year / motorization )</span>
						</div>
						<div class="col-12 GRID-BOX-CONTENT">
							
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
	<div class="col-8 GRID-BOX">
		
		<div class="row">
		    <div class="col-2 PAGE-TYPE-CONTAINER-WALL p-3" style="background: #fff">

		    <img src="<?php echo $domainparent; ?>/upload/constructeurs-automobiles/marques-logos/<?php echo $marque_logo; ?>" alt="<?php echo $marque_name_meta; ?>" class="w-100" />

			</div>
		    <div class="col-5 PAGE-TYPE-CONTAINER-WALL">

			<img src="<?php echo $modele_image; ?>" alt="<?php echo $modele_image_meta_alt; ?>" class="w-100">

			</div>
		    <div class="col-5 PAGE-TYPE-CONTAINER-WALL-DATAS">

				Fuel : <?php echo utf8_encode($pagealimTyToPrint); ?>
				<br />
				Number of Cylinders : <?php echo utf8_encode($pagecylindTyToPrint); ?>
				<br />
				Power : <?php echo utf8_encode($pagepuissTyToPrint); ?>
				<br />
				Design : <?php echo utf8_encode($pagedesignTyToPrint); ?>
				<br />
				Capacity : <?php echo utf8_encode($pagemoteurTyToPrint); ?>
				<br />
				Gear box : <?php echo utf8_encode($pageboiteTyToPrint); ?>
				<br />
				Engine code : <?php echo utf8_encode($pagecodemoteurTyToPrint); ?>

			</div>
		</div>

	</div>
	<div class="col-12" style="padding: 11px;">

		<?php include("v2.show.car.catalog.php"); ?>
		
	</div>
</div>

	</div>
<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else
{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
    $query=mysql_query("SELECT type_id, type_name_meta, type_name_site, type_ch, LEFT(type_date_debut,4) AS type_date_debut, 
    	RIGHT(type_date_debut,2) AS type_date_debut_mois, LEFT(type_date_fin,4) AS type_date_fin, type_affiche,
    	modele_id, modele_alias, modele_name_meta, modele_name_site, modele_affiche, modele_vt_ap,
    	marque_id, marque_alias, marque_name_meta, marque_name_site, marque_affiche, marque_vt_ap
    	FROM $sqltable_Car_type 
		JOIN $sqltable_Car_modele ON modele_id = type_modele_id
		JOIN $sqltable_Car_marque ON marque_id =type_marque_id
		WHERE type_id = '$type_id' AND type_modele_id = '$modele_id' AND type_marque_id = '$marque_id' ORDER BY type_id LIMIT 0,1");
    $result=mysql_fetch_array($query);
    // car affiche
    $marque_affiche = $result["marque_affiche"] * $result["marque_vt_ap"];
    $modele_affiche = $result["modele_affiche"] * $result["modele_vt_ap"];
    $type_affiche = $result["type_affiche"];
    // car datas
    $marque_id = $result["marque_id"];
    $marque_alias = $result["marque_alias"];
    $marque_name_site = utf8_encode($result["marque_name_site"]);
    $marque_name_meta = utf8_encode($result["marque_name_meta"]);
    $modele_id = $result["modele_id"];
    $modele_alias = $result["modele_alias"];
    $modele_name_site = utf8_encode($result["modele_name_site"]);
    $modele_name_meta = utf8_encode($result["modele_name_meta"]);
    $type_id = $result["marque_id"];
    $type_name_site = utf8_encode($result["type_name_site"]);
    $type_name_meta = utf8_encode($result["type_name_meta"]);
	$type_nbch=$result['type_ch'];
    if(empty($result['type_date_fin']))
		{
		$type_date="du ".$result['type_date_debut_mois']."/".$result['type_date_debut'];
		}
		else
		{
		$type_date="de ".$result['type_date_debut']." à ".$result['type_date_fin'];
		}
    // META & CONTENT
	$pageh1txt=$marque_name_site." ".$modele_name_site." ".$type_name_site."  ".$type_nbch." ch (".$type_date.")";
	$pageh1txtComp="Cette page est temporairement indisponible";
	$pagecontenttxt="Vous disposez d'un catalogue varié de toutes les pièces détachées pour votre ".$marque_name_site." ".$modele_name_site." ".$type_name_site."  ".$type_nbch." ch (".$type_date."), disponibles dans les grandes marques d'équipementiers comme Bosch, Valeo, SKF, Continental, Gates, LUK, Dayco... etc.";
?>
<div class="container-fluid PAGE-410-CONTAINER">
<div class="container-fluid PAGE-410-CONTAINER-IN">

  <div class="row">
    <div class="col-md-5 text-right PAGE-410-FLAG">
        412 /
    </div>
    <div class="col-md-7 PAGE-410-CONTENT">
        
        <div class="row">
          <div class="col-md-12 PAGE-410-CONTENT-TITLE">
            <h1 class="PAGE-410"><?php echo $pageh1txt; ?></h1>
            <br><?php echo $pageh1txtComp; ?>
          </div>
          <div class="col-md-12 PAGE-410-CONTENT-TXT">
            <?php echo $pagecontenttxt; ?>
          </div>
        </div>
    </div>
  </div>

</div>
</div>
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
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           410                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
$pageh1txt="Erreur 410 - Page supprimée";
$pageh1txtComp="Cette page a pu être supprimée ou déplacée";
$pagecontenttxt="Page indisponible ou introuvable à cette adresse sur ".$domainwebsitename." votre catalogue de pièces détachées automobile neuves et d'origine pour toutes les marques & modèles de voitures, fabriquées par les grands équipementiers de pièces auto.";
?>
<div class="container-fluid PAGE-410-CONTAINER">
<div class="container-fluid PAGE-410-CONTAINER-IN">

  <div class="row">
    <div class="col-md-5 text-right PAGE-410-FLAG">
        410 /
    </div>
    <div class="col-md-7 PAGE-410-CONTENT">
        
        <div class="row">
          <div class="col-md-12 PAGE-410-CONTENT-TITLE">
            <h1 class="PAGE-410"><?php echo $pageh1txt; ?></h1>
            <br><?php echo $pageh1txtComp; ?>
          </div>
          <div class="col-md-12 PAGE-410-CONTENT-TXT">
            <?php echo $pagecontenttxt; ?>
          </div>
        </div>
    </div>
  </div>

</div>
</div>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
?>

</div>

<?Php
// PIED DE PAGE
@require_once('footer.last.section.php');
?>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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