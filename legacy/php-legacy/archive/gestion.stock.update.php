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
		if(($rsverif["valide"]=='1')&&($rsverif["type"] == "SA"))
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

$UpdateResponseCode=0;
if($_POST["update"])
{
$sp_id=$_POST['sp_id'];
$sp_qte=$_POST['sp_qte'];
$sp_qte_old=$_POST['sp_qte_old'];
$sp_ref=$_POST['sp_ref'];

    if($sp_qte>=0)
    {
      if(mysql_query("UPDATE 2027_xmassdoc_piece_stock SET sp_qte = $sp_qte WHERE sp_id = $sp_id"))
      {
        $UpdateResponse = "Quantité mise à jours avec succès.";
        $UpdateResponseCode=1;
        // HISTORIQUE ET TRACAGE
        /*$opdate=date("Y-m-d H:i:s");
        mysql_query("INSERT INTO 2022_historik_stock_update (id, op_name, op_piece_ref, op_piece_id, op_old_value, op_new_value, op_agent_id, op_date) VALUES ('', '$historyUpdateStockItem', '$op_piece_ref', '$pieceid', '$qte_stock_old', '$qte_stock', '$ssid', '$opdate');");*/
        // FIN HISTORIQUE ET TRACAGE
      }
      else
      {
        $UpdateResponse = "Une erreur est survenue.";
        $UpdateResponseCode=2;
      }
    }
    else
    {
      $UpdateResponse = "La quantité en stock doit être supérieure ou égale à Zéro.";
      $UpdateResponseCode=2;
    }

}
else
{
$sp_id = $_GET["sp_id"];
}
$qStock=mysql_query("SELECT sp_id, sp_ref, sp_pg_id, sp_pm_id, sp_qte,
      piece_id, piece_name, piece_name_complement 
  FROM 2027_xmassdoc_piece_stock 
  JOIN $sqltable_Piece ON  piece_ref_propre = sp_refpropre AND piece_pg_id = sp_pg_id AND piece_pm_id = sp_pm_id 
  WHERE sp_id = '$sp_id'");    
$Stock = mysql_fetch_array($qStock);
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
<body class="BODY-MODAL">


<div class="row">
<div class="col-12">

<form method="post" action="">
    <div class="row">
      <div class="col-6 centerVertical MODAL-LABEL-INPUT">Numéro</div>
      <div class="col-6">
        <input type="text" disabled="disabled" value="<?php echo $Stock['sp_id']; ?>" class="MODAL-INPUT" />
      </div>
    </div>
    <div class="row">
      <div class="col-6 centerVertical MODAL-LABEL-INPUT">Référence</div>
      <div class="col-6">
        <input type="text" disabled="disabled" value="<?php echo $Stock['sp_ref']; ?>" class="MODAL-INPUT" />
      </div>
    </div>
    <div class="row">
      <div class="col-6 centerVertical MODAL-LABEL-INPUT">Nom</div>
      <div class="col-6">
        <input type="text" disabled="disabled" value="<?php echo utf8_encode($Stock['piece_name'].' '.$Stock['piece_name_complement']); ?>" class="MODAL-INPUT" />
      </div>
    </div>
    <div class="row">
      <div class="col-6 centerVertical MODAL-LABEL-INPUT">Quantité en stock</div>
      <div class="col-6">
        <input name="sp_qte" type="number" min="0" value="<?php echo $Stock['sp_qte']; ?>" class="MODAL-INPUT" />
      </div>
    </div>
    <div class="row">
      <div class="col-8 centerVertical text-left">
      <?php
        if($UpdateResponseCode>0)
        {
          if($UpdateResponseCode==1)
          {
            ?>
            <span class="response-green"><?php echo $UpdateResponse ;?></span>
            <?php
          }
          else
          {
            ?>
            <span class="response-red"><?php echo $UpdateResponse ;?></span>
            <?php
          }
        }
      ?>
      </div>
      <div class="col-4 text-right">
        <input type="submit" name="submited" value="Mettre à jours" class="VALIDATE-MODAL">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="sp_id" value="<?php echo $Stock['sp_id']; ?>">
        <input type="hidden" name="sp_qte_old" value="<?php echo $Stock['sp_qte']; ?>">
      </div>
    </div>
</form>

</div>
</div>

</body>
</html>
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