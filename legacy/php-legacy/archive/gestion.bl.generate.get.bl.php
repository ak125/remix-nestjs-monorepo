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

$boncommande_id_injected = $_GET["boncom_id"];
$paybl = $_GET["paybl"];

if(($boncommande_id_injected>0)&&($paybl<10))
{

    // mettre a jour la PO
    mysql_query("UPDATE 2027_xmassdoc_boncommande SET boncom_etat_id = '2' WHERE boncom_id = $boncommande_id_injected");

    // ON CREE UN BON COMMANDE

$qClt=mysql_query("SELECT * FROM 2027_xmassdoc_boncommande WHERE boncom_id  = '$boncommande_id_injected'");    
$rClt = mysql_fetch_array($qClt);
$com_clt_id = $rClt['boncom_clt_id'];

// BC
$stt_bc=0;
$infoscomplementaires="";
// date commanda
$datecommande=date("Y-m-j H:i:s"); 

    mysql_query("INSERT INTO 2027_xmassdoc_bl (bl_id, bl_boncom_id, bl_clt_id, bl_type, bl_etat_id, bl_statut_id, bl_amount, bl_date, bl_infosup, bl_payment) 
    VALUES ('', '$boncommande_id_injected', $com_clt_id, 'BL', '1', '1', '$stt_bc', '$datecommande', '$infoscomplementaires',$paybl)");
    
    // recuperer l'identifiant de la commande
    $bl_id_injected = @mysql_insert_id();
    if($bl_id_injected>0)
    {
      $bl_matricule = "BL/".$bl_id_injected;
      mysql_query("UPDATE 2027_xmassdoc_bl SET bl_matricule = '$bl_matricule' WHERE bl_id = '$bl_id_injected'");
      
      // CART CONTENT
      $qCommandeDispoLine = mysql_query("SELECT * FROM 2027_xmassdoc_boncommande_line 
        WHERE lineboncom_boncom_id = $boncommande_id_injected ");
      while($CommandeDispoLine = mysql_fetch_array($qCommandeDispoLine)) 
      {
      
      // recuperation des données de la session
      $piece_id = $CommandeDispoLine['lineboncom_piece_id'];
      $prix = $CommandeDispoLine['lineboncom_price_unit'];
      $consigne = 0;
      $qte = $CommandeDispoLine['lineboncom_qte'];
      $piece_ref_propre = $CommandeDispoLine['lineboncom_refpropre'];
      $pm_id = $CommandeDispoLine['lineboncom_pm_id'];
      $urltakentoadd = $CommandeDispoLine['lineboncom_urltocart'];
      
      $pg_id =  $CommandeDispoLine['lineboncom_pg_id'];
      
      $piece_ref =  $CommandeDispoLine['lineboncom_ref'];
      $disponibility =  $CommandeDispoLine['lineboncom_instock'];
      $reliq =  $CommandeDispoLine['lineboncom_shipping'];
      
      // prix unitaire TTC
      $prix_UTTC=$prix;
      $prix_UTTC=number_format($prix_UTTC, 2, '.', ''); 

      // prix total TTC
      $prix_TTTC=$prix*$qte;
      $prix_TTTC=number_format($prix_TTTC, 2, '.', '');      
      

      $stt_bc=$stt_bc+($prix*$qte);

      mysql_query("INSERT INTO  2027_xmassdoc_bl_line ( linebl_id ,linebl_bl_id , linebl_statut, linebl_piece_id, linebl_ref, linebl_refpropre, linebl_pg_id, linebl_pm_id, linebl_price_unit, linebl_qte, linebl_price_total, linebl_urltocart, linebl_instock, linebl_shipping) VALUES ('' ,'$bl_id_injected','1','$piece_id','$piece_ref', '$piece_ref_propre','$pg_id','$pm_id','$prix_UTTC','$qte','$prix_TTTC','$urltakentoadd', '$disponibility', '$reliq')");

      // on annule la piece du stock

      // fin mise a jour du stock

      }
      mysql_query("UPDATE 2027_xmassdoc_bl SET bl_amount = '$stt_bc' WHERE bl_id = '$bl_id_injected'");
      
    }


// fin creer un nouveau bon de commande


}
?>
<META http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/generatebl/<?php echo $boncommande_id_injected; ?>">
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