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

$commande_id_injected = $_GET["com_id"];
$level = 2;
$com_matricule = "PO/".$level."-".$commande_id_injected;

if($commande_id_injected>0)
{

    // mettre a jour la PO
    mysql_query("UPDATE 2027_xmassdoc_commande SET com_level = '$level' WHERE com_id = $commande_id_injected");
    mysql_query("UPDATE 2027_xmassdoc_commande SET com_matricule = '$com_matricule' WHERE com_id = $commande_id_injected");

    // ON CREE UN BON COMMANDE

$qClt=mysql_query("SELECT * FROM 2027_xmassdoc_commande WHERE com_id  = '$commande_id_injected'");    
$rClt = mysql_fetch_array($qClt);
$com_clt_id = $rClt['com_clt_id'];

// BC
$stt_bc=0;
$infoscomplementaires="";
// date commanda
$datecommande=date("Y-m-j H:i:s"); 

    mysql_query("INSERT INTO 2027_xmassdoc_boncommande (boncom_id, boncom_com_id, boncom_clt_id, boncom_type, boncom_etat_id, boncom_statut_id, boncom_amount, boncom_date, boncom_infosup) 
    VALUES ('', '$commande_id_injected', $com_clt_id, 'BC', '1', '1', '$stt_bc', '$datecommande', '$infoscomplementaires')");
    
    // recuperer l'identifiant de la commande
    $boncommande_id_injected = @mysql_insert_id();
    if($boncommande_id_injected>0)
    {
      $boncom_matricule = "BC/".$boncommande_id_injected;
      mysql_query("UPDATE 2027_xmassdoc_boncommande SET boncom_matricule = '$boncom_matricule' WHERE boncom_id = '$boncommande_id_injected'");
      
      // CART CONTENT
      $qCommandeDispoLine = mysql_query("SELECT * FROM 2027_xmassdoc_commande_line 
        WHERE linecom_com_id = $commande_id_injected ");
      while($CommandeDispoLine = mysql_fetch_array($qCommandeDispoLine)) 
      {
      
      // recuperation des données de la session
      $piece_id = $CommandeDispoLine['linecom_piece_id'];
      $prix = $CommandeDispoLine['linecom_price_unit'];
      $consigne = 0;
      $qte = $CommandeDispoLine['linecom_qte'];
      $piece_ref_propre = $CommandeDispoLine['linecom_refpropre'];
      $pm_id = $CommandeDispoLine['linecom_pm_id'];
      $urltakentoadd = $CommandeDispoLine['linecom_urltocart'];
      
      $pg_id =  $CommandeDispoLine['linecom_pg_id'];
      
      $piece_ref =  $CommandeDispoLine['linecom_ref'];
      $disponibility =  $CommandeDispoLine['linecom_instock'];
      $reliq =  $CommandeDispoLine['linecom_shipping'];
      
      // prix unitaire TTC
      $prix_UTTC=$prix;
      $prix_UTTC=number_format($prix_UTTC, 2, '.', ''); 

      // prix total TTC
      $prix_TTTC=$prix*$qte;
      $prix_TTTC=number_format($prix_TTTC, 2, '.', '');      
      

      $stt_bc=$stt_bc+($prix*$qte);

      mysql_query("INSERT INTO  2027_xmassdoc_boncommande_line ( lineboncom_id ,lineboncom_boncom_id , lineboncom_statut, lineboncom_piece_id, lineboncom_ref, lineboncom_refpropre, lineboncom_pg_id, lineboncom_pm_id, lineboncom_price_unit, lineboncom_qte, lineboncom_price_total, lineboncom_urltocart, lineboncom_instock, lineboncom_shipping) VALUES ('' ,'$boncommande_id_injected','1','$piece_id','$piece_ref', '$piece_ref_propre','$pg_id','$pm_id','$prix_UTTC','$qte','$prix_TTTC','$urltakentoadd', '$disponibility', '$reliq')");

      // on annule la piece du stock

      // fin mise a jour du stock

      }
      mysql_query("UPDATE 2027_xmassdoc_boncommande SET boncom_amount = '$stt_bc' WHERE boncom_id = '$boncommande_id_injected'");
      
    }


// fin creer un nouveau bon de commande


}
?>
<META http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/purchaseorder/update/<?php echo $commande_id_injected; ?>">
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