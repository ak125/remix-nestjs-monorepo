<?php 
session_start();
// on vide le panier
unset($_SESSION['amcnkCart']);
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="mycartconfirmpay";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

// recuperation des données de la commande
$commandeid = $_POST["commande_id"];
$commandeid = str_replace("-", "/", $commandeid);
// variable de retour du systeme de paiement
$erreur = $_POST['error']; // erreur généré en cas d'erreur
?>
<!doctype html>
<html amp lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="robots" content="noindex, nofollow" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
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
        .. /
    </div>
    <div class="col-md-7 PAGE-410-CONTENT">
        
        <div class="row">
          <div class="col-md-12 PAGE-410-CONTENT-TITLE">
            <h1 class="PAGE-410">Confirmation commande n&deg;  <?php echo $commandeid; ?></h1>
            <br>Votre commande a été bien confirmée.
          </div>
          <div class="col-md-12 PAGE-410-CONTENT-TXT">
Nous vous remercions pour votre confiance et nous tenons à vous informer que votre paiement a été bien reçu.
<br>
Votre commande numéro <b><?php echo $commandeid; ?></b> est désormé enregistrée sur votre espace client et sera traitée dans les plus brefs délais.
<br>
Merci de nous contacter, en cas de besoin, en sauvegardant votre numéro de commande <b><?php echo $commandeid; ?></b> et ce pour une assistance plus précise.
          </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 PAGE-410-SUGGEST-CONTENT">
        
        <div class="row">
          <div class="col-md-12 PAGE-410-SUGGEST-CONTENT-TITLE text-center">
            <a href="<?php echo $domain; ?>">Retournez sur <?php echo $domainwebsitename; ?></a>
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
?><?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.php');
?>