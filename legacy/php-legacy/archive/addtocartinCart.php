<?php
session_start();
include("../shopping_cart.function.php");
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

  function secureSuperGlobalPOST($key)
    {
        $key = htmlspecialchars(stripslashes($key));
        $key = str_ireplace("script", "blocked", $key);
        $key = mysql_real_escape_string($key);
		$key = str_ireplace("\'", "'", $key);
        return $key;
    }


// piece id
$pieceidtakentoadd = $_GET['pieceidtakentoadd'];
$pieceidtakentoadd = secureSuperGlobalPOST($pieceidtakentoadd);
// quantite demandé par le client
$qte = $_GET['qte'];




	$articletoupdate=$pieceidtakentoadd;	
	//supprim_article2($articletodelete);
	$action=$_GET['action'];
	if($action=='plus')
	{
		 $_SESSION['panier']['qte'][$articletoupdate]=$_SESSION['panier']['qte'][$articletoupdate]+1;
	}
	if($action=='minus')
	{
		 if($_SESSION['panier']['qte'][$articletoupdate]>1)
		 {
		 	$_SESSION['panier']['qte'][$articletoupdate]=$_SESSION['panier']['qte'][$articletoupdate]-1;
		}
	}
	if($action=='drop')
	{
		 supprim_article2($articletoupdate);
	}
	
	
$urltakentoadd = $domain."/cart/";

header("location:".$urltakentoadd);

?>