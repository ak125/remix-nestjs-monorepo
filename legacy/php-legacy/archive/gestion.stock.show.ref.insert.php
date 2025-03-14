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

$sp_pg_id=$_POST['sp_pg_id'];
$sp_pm_id=$_POST['sp_pm_id'];
$sp_qte=$_POST['sp_qte'];
$sp_ref=$_POST['sp_ref'];
$sp_refpropre=$_POST['sp_refpropre'];

    if(($sp_qte>=0)&&($sp_pg_id>=0)&&($sp_pm_id>=0)&&($sp_qte>=0)&&($sp_ref!=""))
    {
      
      mysql_query("INSERT INTO 2027_xmassdoc_piece_stock (`sp_id`, `sp_ref`, `sp_refpropre`, `sp_pg_id`, `sp_pm_id`, `sp_qte`) VALUES ('', '$sp_ref', '$sp_refpropre', '$sp_pg_id', '$sp_pm_id', '$sp_qte')");
      
    }



?>
<META http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/stock/seek/">
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