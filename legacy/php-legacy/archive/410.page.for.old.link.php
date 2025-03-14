<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php'); 
// parametres relatifs à la page (niveau de la page)
$typefile="410";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

header("HTTP/1.1 410 Gone");
$pagetitle="Erreur 410 - Page supprimée";
$pagedescription="Page indisponible ou introuvable à cette adresse sur ".$domainwebsitename." votre catalogue de pièces détachées automobile neuves et d'origine pour toutes les marques & modèles de voitures.";
$pageh1txt="Erreur 410 - Page supprimée";
$pageh1txtComp="Cette page a pu être supprimée ou déplacée";
$pagecontenttxt="Page indisponible ou introuvable à cette adresse sur ".$domainwebsitename." votre catalogue de pièces détachées automobile neuves et d'origine pour toutes les marques & modèles de voitures, fabriquées par les grands équipementiers de pièces auto.";
$robots="noindex, nofollow";
?>
<!doctype html>
<html amp lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="robots" content="<?php  echo $robots; ?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<!-- CSS -->
<style>
@import url('https://fonts.googleapis.com/css?family=Oswald:300,400,700&display=swap');
</style>
<link href="/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="/assets/css/<?php echo $hr;?>.css" rel="stylesheet" media="all">
</head>
<body>

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
  <div class="row">
    <div class="col-md-8 text-right PAGE-410-SUGGEST-FLAG">
            /
    </div>
    <div class="col-md-4 PAGE-410-SUGGEST-CONTENT">
        
        <div class="row">
          <div class="col-md-12 PAGE-410-SUGGEST-CONTENT-TITLE">
            Oooopss, où voulez vous aller ?
          </div>
          <div class="col-md-12 PAGE-410-SUGGEST-CONTENT-TXT">
            <a href="<?php echo $domain; ?>/"><?php echo $domainwebsitename; ?></a>
            <br>
            <a href="<?php echo $domain; ?>/<?php echo $blog; ?>/"><?php echo $blog_arianetitle; ?></a>
          </div>
        </div>
    </div>
  </div>

</div>
</div>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="/system/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>