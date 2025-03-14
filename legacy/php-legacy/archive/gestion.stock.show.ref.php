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

$quest=$_POST["quest"];
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

<?php
$qArticleChaine="SELECT DISTINCT piece_id, piece_ref, piece_ref_propre, piece_name, piece_name_complement,
  piece_quantite, piece_qte_cond, piece_name_tec, piece_name_com, piece_designation, 
  pm_id, pm_alias, pm_name_site, pm_name_meta, pm_logo, pm_quality, pm_nature,
  pg_id, pg_alias, pg_name_site, pg_name_meta
  FROM prod_pieces_reference
  JOIN $sqltable_Piece ON piece_id = reference_piece_id
  JOIN $sqltable_Piece_gamme ON pg_id = piece_pg_id
  JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id
  JOIN prod_pieces_prix ON piece_id = prix_piece_id AND prix_exist_dispo = 1 
  WHERE reference_research = '$questCleaned'
  AND piece_affiche = 1 AND pg_affiche IN (1,2) AND pm_affiche = 1 ORDER BY reference_reftype_id , prix_vente_ttc*piece_quantite";
$qArticle=mysql_query($qArticleChaine);
while($rArticle=mysql_fetch_array($qArticle))
{
$this_pg_id =  $rArticle["pg_id"];
$this_pm_id = $rArticle["pm_id"];
$this_refpropre = $rArticle["piece_ref_propre"];
// on teste que ca ne doit pas deja etre existant
$qStock=mysql_query("SELECT sp_id
  FROM 2027_xmassdoc_piece_stock 
  WHERE sp_refpropre = '$this_refpropre' AND sp_pg_id = '$this_pg_id' AND sp_pm_id = '$this_pm_id' ");    
?>
<form action="<?php echo $domain; ?>/stock/seek/ref/insert" method="POST" role="form">
<div class="row">
	<div class="col-2">
		<?php echo $rArticle["piece_ref"]; ?>
	</div>
	<div class="col-3">
		<?php echo utf8_encode($rArticle["piece_name"]." ".$rArticle["piece_name_complement"]); ?>
	</div>
	<div class="col-2">
		<?php echo utf8_encode($rArticle["pm_name_site"]); ?>
	</div>
	<?php
	if($Stock = mysql_fetch_array($qStock))
	{
	?>
	<div class="col-5 pb-3">
Use UPDATE Interface
    </div>
    <?php
	}
	else
	{
	?>
	<div class="col-3">

<input name="sp_qte" type="number" min="0" value="1" class="MODAL-INPUT" />

	</div>
	<div class="col-2 text-right">
        <input type="submit" name="submited" value="Mettre à jours" class="VALIDATE-MODAL">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="sp_pg_id" value="<?php echo $rArticle["pg_id"]; ?>">
        <input type="hidden" name="sp_pm_id" value="<?php echo $rArticle["pm_id"]; ?>">
        <input type="hidden" name="sp_ref" value="<?php echo $rArticle["piece_ref"]; ?>">
        <input type="hidden" name="sp_refpropre" value="<?php echo $rArticle["piece_ref_propre"]; ?>">
    </div>
    <?php
	}
	?>
</div>
</form>
<?php
}
?>

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