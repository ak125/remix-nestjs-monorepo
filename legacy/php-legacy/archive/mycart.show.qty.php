<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
// fichier du paramètrage du shopping
include("config/shopping_cart.function.php");
// piece id
$pieceidtakentoadd = $_GET['pieceidtakentoadd'];
//$pieceidtakentoadd = secureSuperGlobalPOST($pieceidtakentoadd);
// quantite demandé par le client
$qte = $_GET['qte'];




	$articletoupdate=$pieceidtakentoadd;	
	//supprim_article2($articletodelete);
	$action=$_GET['action'];
	if($action=='plus')
	{
		 $_SESSION['amcnkCart']['qte'][$articletoupdate]=$_SESSION['amcnkCart']['qte'][$articletoupdate]+1;
	}
	if($action=='minus')
	{
		 if($_SESSION['amcnkCart']['qte'][$articletoupdate]>1)
		 {
		 	$_SESSION['amcnkCart']['qte'][$articletoupdate]=$_SESSION['amcnkCart']['qte'][$articletoupdate]-1;
		}
	}
	if($action=='drop')
	{
		 supprim_article($articletoupdate);
	}
	
	
$urltakentoadd = $domain."/panier.html";

header("location:".$urltakentoadd);

?>