<?php
session_start();
session_destroy();
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

$log=$_POST['userlog'];
$mp=$_POST['userpswd'];
$mpcrypt = crypt(md5($mp),"im10tech7");
$destinationLinkGranted = 0;
$reqlog= mysql_query("SELECT * FROM 2027_xmassdoc_reseller_access_code WHERE login='$log' AND password='$mpcrypt'");
if ($reslog=mysql_fetch_array($reqlog))
{
	if($reslog["valide"]=='1')
	{
		//enregistrement sur session
		$_SESSION['im7mylog']=$log;
		$_SESSION['im7mykey']=md5($reslog["mail"]);
	    $destinationLink = $accessPermittedLink;
	    $destinationLinkMsg = "Granted";
	    $destinationLinkGranted = 1;
	}
	else
	{
		// enregistrement d'une fausse session
		$_SESSION['im7mylog']="default";
		$_SESSION['im7mykey']=md5("default");
		$destinationLink = $accessSuspendedLink;
	    $destinationLinkMsg = "Suspended";
	    $destinationLinkGranted = 0;
	}
}
else
{
	// enregistrement d'une fausse session
	$_SESSION['im7mylog']="default";
	$_SESSION['im7mykey']=md5("default");
	$destinationLink = $accessRefusedLink;
	$destinationLinkMsg = "Denied";
	$destinationLinkGranted = 0;
}

include("get.access.response.php");
?>