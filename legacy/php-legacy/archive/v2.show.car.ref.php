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
$pg_id=$_GET["pg_id"];
$marque_id=$_GET["marque_id"];
$modele_id=$_GET["modele_id"];
$type_id=$_GET["type_id"];

$CarExist = 1;
$CarAffiche= 1;
$GammeExist = 1;
$GammeAffiche= 1;

// CAR VERIFICATION
  $queryCarExist=mysql_query("SELECT type_id FROM $sqltable_Car_type 
    JOIN $sqltable_Car_modele ON modele_id = type_modele_id
    JOIN $sqltable_Car_marque ON marque_id =type_marque_id
    WHERE type_id = '$type_id' AND type_modele_id = '$modele_id' AND type_marque_id = '$marque_id' ORDER BY type_id LIMIT 0,1");
  if($resultCarExist=mysql_fetch_array($queryCarExist))
  {
    $queryCarAffiche=mysql_query("SELECT type_id FROM $sqltable_Car_type 
      JOIN $sqltable_Car_modele ON modele_id = type_modele_id
      JOIN $sqltable_Car_marque ON marque_id =type_marque_id
      WHERE type_id = '$type_id' AND type_modele_id = '$modele_id' AND type_marque_id = '$marque_id'
      AND type_affiche = 1 AND modele_affiche = 1 AND modele_vt_ap = 1 AND marque_affiche = 1 AND marque_vt_ap = 1");
    if($resultCarAffiche=mysql_fetch_array($queryCarAffiche))
    {
      $CarExist = 1;
      $CarAffiche= 1;
    }
    else
    {
      $CarExist = 1;
      $CarAffiche= 0;
    }
  }
  else
  {
    $CarExist = 0;
    $CarAffiche= 0;
  }


// GAMME VERIFICATION
  $queryGammeExist=mysql_query("SELECT pg_id FROM $sqltable_Piece_gamme WHERE pg_id = '$pg_id' ORDER BY pg_id LIMIT 0,1");
  if($resultGammeExist=mysql_fetch_array($queryGammeExist))
  {
    $queryGammeAffiche=mysql_query("SELECT pg_id FROM $sqltable_Piece_gamme WHERE pg_id = '$pg_id' 
        AND pg_affiche IN (1,2) ORDER BY pg_id LIMIT 0,1");
    if($resultGammeAffiche=mysql_fetch_array($queryGammeAffiche))
    {
      $GammeExist = 1;
      $GammeAffiche= 1;
    }
    else
    {
      $GammeExist = 1;
      $GammeAffiche= 0;
    }
  }
  else
  {
    $GammeExist = 0;
    $GammeAffiche = 0;
  }
if(($CarExist==0)||($GammeExist==0))
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
?>
<?php
}
else
{
  if(($CarAffiche==1)&&($GammeAffiche==1))
  {
    $qCountArticle=mysql_query("SELECT MIN(prix_vente_ttc*piece_quantite) AS MinPriceArticle  , COUNT(DISTINCT piece_id) AS CountArticle 
FROM prod_relation_auto 
JOIN prod_relation ON relauto_relation_id = relation_id 
JOIN prod_pieces ON piece_id = relation_piece_id AND piece_affiche = 1 
JOIN prod_pieces_prix ON prix_piece_id = piece_id AND prix_exist_dispo = 1
WHERE relauto_type_id = $type_id AND relauto_pg_id = $pg_id ");

    $rCountArticle=mysql_fetch_array($qCountArticle);
    $GammeCarCountArticle = $rCountArticle["CountArticle"];
    $GammeCarMinPriceArticle = intval($rCountArticle["MinPriceArticle"]);
    if($GammeCarCountArticle>0)
    {

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // CONFIGURATION
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
        $queryType=mysql_query("SELECT type_id, type_alias, type_name_meta, type_name_site, type_relfollow,
          type_ch, type_kw, type_design, type_carburant, type_boite, type_cylindre, type_volume_cube,
          LEFT(type_date_debut,4) AS type_date_debut,
          RIGHT(type_date_debut,2) AS type_date_debut_mois,
          LEFT(type_date_fin,4) AS type_date_fin
        FROM $sqltable_Car_type 
        WHERE type_id = '$type_id' 
        AND type_affiche = 1 ");
        $resultType=mysql_fetch_array($queryType);
          // motorisation datas
          $type_id = $resultType["type_id"];
          $type_alias = $resultType["type_alias"];
          $type_name_site = utf8_encode($resultType["type_name_site"]);
          $type_name_meta = utf8_encode($resultType["type_name_meta"]);
          // motorisation technical data
          $type_nbch=$resultType['type_ch'];
          $type_carosserie=utf8_encode($resultType['type_design']);
          $type_fuel=utf8_encode($resultType['type_carburant']);
          if(empty($resultType['type_date_fin']))
          {
          $type_date="du ".$resultType['type_date_debut_mois']."/".$resultType['type_date_debut'];
          }
          else
          {
          $type_date="de ".$resultType['type_date_debut']." à ".$resultType['type_date_fin'];
          }
          // autres infos
          $pagealimTyToPrint=$resultType['type_carburant'];
          $pagecylindTyToPrint=$resultType['type_cylindre'];
          $pagepuissTyToPrint=$resultType['type_ch']." ch (".$resultType['type_kw']." kw)";
          $pagedesignTyToPrint=$resultType['type_design'];
          $pagemoteurTyToPrint=$resultType['type_volume_cube']." cm3";
          $pageboiteTyToPrint=$resultType['type_boite'];
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

      // gamme query
        $queryGamme=mysql_query("SELECT  mf_id, mf_name_site, mf_name_meta, pg_id, pg_alias, pg_image, pg_name_site, pg_name_meta,
          pg_relfollow 
          FROM $sqltable_Piece_gamme
        JOIN $sqltable_Catalog_gamme ON mc_pg_id = pg_id
        JOIN $sqltable_Catalog_family ON mf_id = mc_mf_prime
        WHERE pg_id = '$pg_id' 
        AND pg_affiche IN (1,2) 
        AND mf_affiche = 1 ORDER BY pg_id LIMIT 0,1");
        $resultGamme=mysql_fetch_array($queryGamme);
        // family data
        $family_id = $resultGamme["mf_id"];
        $family_name_site = utf8_encode($resultGamme["mf_name_site"]);
        $family_name_meta = utf8_encode($resultGamme["mf_name_meta"]);
        // gamme datas
        $pg_id = $resultGamme["pg_id"];
        $pg_alias = $resultGamme["pg_alias"];
        $pg_name_site = utf8_encode($resultGamme["pg_name_site"]);
        $pg_name_meta = utf8_encode($resultGamme["pg_name_meta"]);
        $pg_photo = $resultGamme["pg_image"];

        $pageh1 = $pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;

        
      // CART DATA
        $linktobackaftercart=$_SERVER['REQUEST_URI'];
        $UrlTakenToAddItem=$linktobackaftercart;
      // FIN CART

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
</div>
<div class="row">
  <div class="col-2 GRID-BOX">



<?Php
if($isSmartPhoneVersion==0)
{
?>
<div class="row d-none d-md-block">
<div class="col-md-12 PAGE-Z-FAMILY-CONTAINER text-left">

<i><?php echo $family_name_site; ?></i>

</div>
</div>

<div class="row d-none d-md-block">
<div class="col-md-12 PAGE-P-FAMILY-CONTAINER-LIST text-left">

      
      <!-- GAMME DE LA MEME FAMILLE -->
      <div class="row">
      <?php
      $qgammePrimaire=mysql_query("SELECT DISTINCT pg_alias, pg_id, pg_name_site, pg_name_meta, pg_image FROM $sqltable_Catalog_gamme 
    JOIN $sqltable_Piece_gamme ON pg_id = mc_pg_id
    JOIN prod_pieces ON piece_pg_id = pg_id AND piece_affiche = 1
    JOIN prod_relation ON relation_piece_id = piece_id
    JOIN prod_relation_auto ON relauto_relation_id = relation_id AND relauto_pg_id = piece_pg_id
    WHERE mc_mf_id = $family_id AND pg_affiche IN (1,2) AND pg_id != $pg_id AND relauto_type_id = $type_id ORDER BY mc_tri");
      while($rgammePrimaire=mysql_fetch_array($qgammePrimaire))
      {
      $LinkToGammeCar = $domain."/searchcar/".$rgammePrimaire['pg_alias']."-".$rgammePrimaire['pg_id']."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id;
      ?>
      <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE">
        

          <a href="<?php echo $LinkToGammeCar; ?>">
          <div class="row nopadding nomargin">
            <!--div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-IMG text-center">

              < ?php
          // NEW ALGO APPEL PHOTO
            $gammePhoto = $domainparent."/upload/articles/gammes-produits/catalogue/".$rgammePrimaire['pg_image'];
            // FIN NEW ALGO APPEL PHOTO
            ?>
            <img src="< ?php echo $gammePhoto; ?>" alt="< ?php  echo $pg_name_meta; ?>" class="w-100" />

            </div -->
            <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-TITLE text-left">
              <?php echo utf8_encode($rgammePrimaire['pg_name_site']); ?>
            </div>
          </div>
          </a>


      </div>
      <?php
      }
      ?>
      <!-- / GAMME DE LA MEME FAMILLE -->
      <!-- GAMME D'AUTRE FAMILLE -->
      <?php
      $qgammePrimaire=mysql_query("SELECT DISTINCT pg_id, pg_alias, pg_name_site, pg_name_meta, pg_image 
      FROM  $sqltable_Cross_Piece_gamme 
      JOIN $sqltable_Piece_gamme ON pg_id = lcpg_pg_id_cross 
      JOIN $sqltable_Catalog_gamme ON mc_pg_id = pg_id
      WHERE lcpg_pg_id = $pg_id AND lcpg_pg_id_cross != $pg_id AND pg_affiche IN (1,2) 
      AND mc_mf_id != $family_id AND mc_mf_prime != $family_id
      ORDER BY lcpg_id");
      while($rgammePrimaire=mysql_fetch_array($qgammePrimaire))
      {
      $pg_id_this_this = $rgammePrimaire['pg_id'];
//////////////////////////////////////////////////////////
$qCountArticleCLeft=mysql_query("SELECT COUNT(DISTINCT piece_id) AS CountArticle FROM prod_relation_auto 
JOIN prod_relation ON relauto_relation_id = relation_id 
JOIN $sqltable_Piece ON piece_id = relation_piece_id AND piece_affiche = 1 
WHERE relauto_type_id = $type_id AND relauto_pg_id = $pg_id_this_this ");

$rCountArticleCLeft=mysql_fetch_array($qCountArticleCLeft);
$GammeCarCountArticleCLeft = $rCountArticleCLeft["CountArticle"];
if($GammeCarCountArticleCLeft>0)
{
//////////////////////////////////////////////////////////
      $LinkToGammeCar = $domain."/searchcar/".$rgammePrimaire['pg_alias']."-".$rgammePrimaire['pg_id']."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id;
      ?>
      <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE">
        

          <a href="<?php echo $LinkToGammeCar; ?>">
          <div class="row nopadding nomargin">
            <!--div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-IMG text-center">

              < ?php
          // NEW ALGO APPEL PHOTO
            $gammePhoto = $domainparent."/upload/articles/gammes-produits/catalogue/".$rgammePrimaire['pg_image'];
            // FIN NEW ALGO APPEL PHOTO
            ?>
            <img src="< ?php echo $gammePhoto; ?>" alt="< ?php  echo $pg_name_meta; ?>" class="w-100" />

            </div-->
            <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-TITLE text-left">
              <?php echo utf8_encode($rgammePrimaire['pg_name_site']); ?>
            </div>
          </div>
          </a>


      </div>
      <?php
//////////////////////////////////////////////////////////
}
//////////////////////////////////////////////////////////
      }
      ?>
      </div>
      <!-- / GAMME D'AUTRE FAMILLE -->


</div>
</div>

<div class="row d-none d-md-block">
<div class="col-md-12 PAGE-Z-FAMILY-CONTAINER white text-center p-0 pt-3 pb-3">

<i>MASSDOC<br>CATALOG</i>

</div>
</div>
<div class="row d-none d-md-block">
<div class="col-md-12 PAGE-P-FAMILY-CONTAINER-LIST text-left">


<?php
$qf=mysql_query("SELECT mf_id, mf_alias, mf_name_site, mf_description  FROM $sqltable_Catalog_family WHERE mf_affiche = 1 
  AND mf_id != $family_id ORDER BY mf_tri");
while($rf=mysql_fetch_array($qf))
{
$family_id_this=$rf['mf_id'];
$family_name_site_this=utf8_encode($rf['mf_name_site']);
$family_alias_this=$rf['mf_alias'];
?>     
      <!-- CATALOGUE -->
      <div class="row">
      <div class="col-md-12 PAGE-Z-FAMILY-CONTAINER text-left">

      <i><?php  echo $family_name_site_this; ?></i>

      </div>
      </div>
      <div class="row">
      <?php
      $qgammePrimaire=mysql_query("SELECT DISTINCT pg_alias, pg_id, pg_name_site, pg_name_meta, pg_image FROM $sqltable_Catalog_gamme 
    JOIN $sqltable_Piece_gamme ON pg_id = mc_pg_id
    JOIN prod_pieces ON piece_pg_id = pg_id AND piece_affiche = 1
    JOIN prod_relation ON relation_piece_id = piece_id
    JOIN prod_relation_auto ON relauto_relation_id = relation_id AND relauto_pg_id = piece_pg_id
    WHERE mc_mf_id = $family_id_this AND pg_affiche IN (1,2) AND pg_id != $pg_id AND relauto_type_id = $type_id ORDER BY mc_tri");
      while($rgammePrimaire=mysql_fetch_array($qgammePrimaire))
      {
      $LinkToGammeCar = $domain."/searchcar/".$rgammePrimaire['pg_alias']."-".$rgammePrimaire['pg_id']."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id;
      ?>
      <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE">
        

          <a href="<?php echo $LinkToGammeCar; ?>">
          <div class="row nopadding nomargin">
            <!--div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-IMG text-center">

              < ?php
          // NEW ALGO APPEL PHOTO
            $gammePhoto = $domainparent."/upload/articles/gammes-produits/catalogue/".$rgammePrimaire['pg_image'];
            // FIN NEW ALGO APPEL PHOTO
            ?>
            <img src="< ?php echo $gammePhoto; ?>" alt="< ?php  echo $pg_name_meta; ?>" class="w-100" />

            </div-->
            <div class="col-12 PAGE-Z-FAMILY-CONTAINER-LIST-ONE-TITLE text-left">
              <?php echo utf8_encode($rgammePrimaire['pg_name_site']); ?>
            </div>
          </div>
          </a>


      </div>
      <?php
      }
      ?>
      </div>
      <!-- / CATALOGUE -->
<?php
}
?>


</div>
</div>
<?Php
}
?>



  </div>
  <div class="col-10">

<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID --><!-- DATAGRID -->
<div class="big-demo" data-js="hero-demo">
  
<div class="row">
  <div class="col-12 GRID-BOX p-3">







<div class="filters">              
<?php
//if($isSmartPhoneVersion==0)
//{
?>
<?php
// EQUIP FILTER
    $FilterName = "Equipementiers";
    $FilterGroup = "filters-equips";
    ?>
    <div class="ui-group">
    <div class="button-group js-radio-button-group" data-filter-group="<?php echo $FilterGroup; ?>">
        
    
    <div class="row nopadding nomargin">
      <div class="col-12 text-center  nopadding nomargin" style="border: 1px solid  #fff; border-top:3px solid  #fff;">
            <button class="button is-checked w-100" style="border: 1px solid  #ebebeb; padding: 14px;" data-filter="*"><u>RESET FILTER</u></button>
      </div>
        <?php        
        // Critere selon la piece
        $qGcriVal =  mysql_query("SELECT DISTINCT pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo
        FROM prod_relation_auto
        JOIN prod_relation ON relation_id = relauto_relation_id
        JOIN $sqltable_Piece ON piece_id = relation_piece_id 
        JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id
        WHERE relauto_type_id = $type_id
        AND piece_affiche = 1
        AND pm_affiche = 1 
        AND relauto_pg_id = $pg_id
        ORDER BY pm_alias");
        while($rGcriVal = mysql_fetch_array($qGcriVal))
        {
          $FilterValue = utf8_encode($rGcriVal['pm_name_site']);
          $FilterClass = $rGcriVal['pm_alias'];
          ?>
      <div class="col-1 text-center nopadding nomargin" style="border: 1px solid  #fff;">
          <button class="button w-100" style="border: 1px solid  #ebebeb; padding: 2px;" data-filter=".<?php echo $FilterClass; ?>">
<img src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $rGcriVal['pm_logo']; ?>" 
                          alt="<?php echo $rGcriVal['pm_name_meta']; ?>" style="max-width:67px;"/>
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
  <div class="col-12 pt-2">




<?php // GRID DES ARTICLES ?> 
<div class="grid">
<?php
$ContentCountArticle = 0;
/*$qArticle=mysql_query("SELECT DISTINCT piece_id, piece_ref, piece_ref_propre, piece_name , piece_name_complement, 
    piece_quantite, piece_qte_cond, piece_name_tec, piece_name_com, piece_designation,
    piece_poids_g, piece_poids_kg, piece_longueur_metre, piece_poids_frais_port,
    pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo, pm_quality, pm_nature 
    FROM prod_relation_auto 
    JOIN prod_relation ON relauto_relation_id = relation_id 
    JOIN $sqltable_Piece ON piece_id = relation_piece_id AND piece_affiche = 1
    JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id AND pm_affiche = 1 
    JOIN prod_pieces_prix ON piece_id = prix_piece_id AND prix_exist_dispo = 1
    WHERE relauto_type_id = $type_id AND relauto_pg_id = $pg_id
    ORDER BY piece_avant DESC , prix_vente_ttc*piece_quantite");*/
$qArticle=mysql_query("SELECT DISTINCT piece_id, piece_ref, piece_ref_propre, piece_name , piece_name_complement, 
    piece_quantite, piece_qte_cond, piece_name_tec, piece_name_com, piece_designation,
    piece_poids_g, piece_poids_kg, piece_longueur_metre, piece_poids_frais_port,
    pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo, pm_quality, pm_nature 
    FROM prod_relation_auto 
    JOIN prod_relation ON relauto_relation_id = relation_id 
    JOIN $sqltable_Piece ON piece_id = relation_piece_id AND piece_affiche = 1
    JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id AND pm_affiche = 1 
    JOIN prod_pieces_prix ON piece_id = prix_piece_id AND prix_exist_dispo = 1
    WHERE relauto_type_id = $type_id AND relauto_pg_id = $pg_id
    ORDER BY piece_arriere+piece_avant , piece_avant DESC , prix_vente_ttc*piece_quantite");

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
// EQUIPEMENTIERS DATAS
$pm_id = $rArticle['pm_id'];
$pm_alias = $rArticle['pm_alias'];
$pm_name_site = utf8_encode($rArticle["pm_name_site"]);
$pm_name_meta = utf8_encode($rArticle["pm_name_meta"]);
$pm_logo = $rArticle['pm_logo'];
$pm_quality = utf8_encode($rArticle['pm_quality']);
$pm_nature = utf8_encode($rArticle['pm_nature']);
// PRICE QUERY
$qPRIX=mysql_query("SELECT DISTINCT prix_piece_id , prix_code_ean, prix_reference, prix_designations,prix_achat_ttc, 
    prix_ssremise_ttc, prix_consigne_ttc, prix_vente_ttc, prix_consigne_ttc,
    prix_remise, prix_achat_net_ht, prix_frs_remise, prix_marge_m2, prix_achat_ht
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

<div class="col-12 nopadding container-piece-element-item <?php echo $pm_alias; ?>" data-category="piece-class-filter" style="height: auto;">

<?php include("v2.show.car.ref.item.php"); ?>


</div>
<?php
}
$ContentCountArticle++;
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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    }
    else
    {
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
  // gamme query
  $query=mysql_query("SELECT pg_id, pg_alias, pg_name_site, pg_name_meta, pg_affiche 
      FROM $sqltable_Piece_gamme WHERE pg_id = '$pg_id' 
      ORDER BY pg_id LIMIT 0,1");
    $result=mysql_fetch_array($query);
    // car affiche
    $pg_affiche = $result["pg_affiche"];
    // gamme datas
    $pg_id = $result["pg_id"];
    $pg_alias = $result["pg_alias"];
    $pg_name_site = utf8_encode($result["pg_name_site"]);
    $pg_name_meta = utf8_encode($result["pg_name_meta"]);
    // car query
    $query=mysql_query("SELECT type_id, type_alias, type_name_meta, type_name_site, type_ch, LEFT(type_date_debut,4) AS type_date_debut, 
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
    $type_id = $result["type_id"];
    $type_alias = $result["type_alias"];
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
  $pageh1txt=$pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
  $pageh1txtComp="Cette combinaison ne contient aucun article pour le moment";
  $pagecontenttxt="Le(s) ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date." sont disponible sur Automecanik à un prix pas cher. <br> Identifiez le ".$pg_name_site." compatible avec votre ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch en suivant les plans d'entretien du constructeur ".$marque_name_site." pour les périodes de contrôle et de remplacement du ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.<br> Lors du remplacement de la pièce nous vous conseillons de contrôler l'état d'usure des composants et des organes liés de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.";
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
?>
<?php
    }

  }
  else
  {
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
  // gamme query
  $query=mysql_query("SELECT pg_id, pg_alias, pg_name_site, pg_name_meta, pg_affiche 
      FROM $sqltable_Piece_gamme WHERE pg_id = '$pg_id' 
      ORDER BY pg_id LIMIT 0,1");
    $result=mysql_fetch_array($query);
    // car affiche
    $pg_affiche = $result["pg_affiche"];
    // gamme datas
    $pg_id = $result["pg_id"];
    $pg_alias = $result["pg_alias"];
    $pg_name_site = utf8_encode($result["pg_name_site"]);
    $pg_name_meta = utf8_encode($result["pg_name_meta"]);
    // car query
    $query=mysql_query("SELECT type_id, type_alias, type_name_meta, type_name_site, type_ch, LEFT(type_date_debut,4) AS type_date_debut, 
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
    $type_id = $result["type_id"];
    $type_alias = $result["type_alias"];
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
  $pageh1txt=$pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
  $pageh1txtComp="Cette page est temporairement indisponible";
  $pagecontenttxt="Le(s) ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date." sont disponible sur Automecanik à un prix pas cher. <br> Identifiez le ".$pg_name_site." compatible avec votre ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch en suivant les plans d'entretien du constructeur ".$marque_name_site." pour les périodes de contrôle et de remplacement du ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.<br> Lors du remplacement de la pièce nous vous conseillons de contrôler l'état d'usure des composants et des organes liés de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.";
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
?>
<?php
  }
}
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
<script src="<?php echo $domainparent; ?>/assets/js/isotope.pkgd.min.js" async></script>
<script  async>
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