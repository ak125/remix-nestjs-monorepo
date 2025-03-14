<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
//$kv = array();
ksort($_POST);
//var_dump($_POST);

$signatureCyberPlusCHAINE="";
$CertificatTest="9300172162563656";
$CertificatProd="9816635272016068";

$CertificatToUse=$CertificatProd;

foreach($_POST as $key => $val)
{
	if(strstr($key, "vads_"))
	{ $signatureCyberPlusCHAINE.=utf8_encode($val)."+"; }
}
$signatureCyberPlusCHAINE.=$CertificatToUse;

$signatureCyberPlus = sha1($signatureCyberPlusCHAINE);

$signaturePayment =  $_POST['signature'];
if(strcmp($signatureCyberPlus, $signaturePayment) !== 0)
{
	//echo "problem";
}
else
{
	// on va analyser dans ce cas les informations

		// recuperation des données de la commande
		$vads_order_id=$_POST['vads_order_id'];
		$commandeid = $vads_order_id;  // ex :   101010-A 
		$commandeid_interne_tab = explode("/",$commandeid);
		$commandeid_interne = $commandeid_interne_tab[0];  // ex : 101010
		
		//numéro d'Autorisation
		$vads_auth_number=$_POST['vads_auth_number'];
		$Auto=$vads_auth_number; 
		
		// montant sans virgule ni point du panier
		$vads_amount=$_POST['vads_amount'];
		$Mt=$vads_amount;
		
		// reference de commande
		$Ref=$vads_order_id;
		 
		// Type de Carte retenu
		$vads_card_brand=$_POST['vads_card_brand'];
		$Cb=$vads_card_brand;

		// Code pays de l'adresse IP de la carte au module de paiement
		$vads_card_country=$_POST['vads_card_country'];
		$Ips=$vads_card_country;
		
		//  Code pays de l'adresse IP de l'internaute au module de paiement
		$Ip=$vads_card_country;
		
		// numero de transaction
		$vads_trans_id=$_POST['vads_trans_id'];
		$Trans=$vads_trans_id; 

		// autre variable
		$vads_payment_config=$_POST['vads_payment_config'];
		$vads_trans_date=$_POST['vads_trans_date'];
		$vads_capture_delay=$_POST['vads_capture_delay'];
		$vads_auth_result=$_POST['vads_auth_result'];
		$vads_trans_status=$_POST['vads_trans_status'];

// code resultat
$vads_result=$_POST['vads_result'];

// on va tester le resultat de la commande en question

/////////////////////////////////// SUCCESS ///////////////////////////////////
/////////////////////////////////// SUCCESS ///////////////////////////////////
/////////////////////////////////// SUCCESS ///////////////////////////////////
if ($vads_result == "00") {
$CR = "OK";

// injection du paiement sur la table contenant les payements
$paymentInsertQuery = "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES (NULL, '$commandeid_interne', '$CR', '', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW())";
	$conn->query($paymentInsertQuery);

// mise à jours de la table commande
$orderUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_IS_PAY = 1 WHERE ORD_ID = '$commandeid_interne'";
	$conn->query($orderUpdateQuery);
	
$orderEtatUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_DEPT_ID = 1 WHERE ORD_ID = '$commandeid_interne'";
	$conn->query($orderEtatUpdateQuery);

$datecommandepay=date("Y-m-j H:i:s");
$orderDateUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_DATE_PAY = '$datecommandepay' WHERE  ORD_ID = '$commandeid_interne'";
	$conn->query($orderDateUpdateQuery);

				// Si c'est un rajout on change le statut de la commande
				$query_supplement_data = "SELECT ORD_PARENT 
				    FROM ___XTR_ORDER 
				    WHERE ORD_ID = '$commandeid_interne'";
				$request_supplement_data = $conn->query($query_supplement_data);
				if ($request_supplement_data->num_rows > 0) 
				{
				    $result_supplement_data = $request_supplement_data->fetch_assoc();
				    $is_supplement = $result_supplement_data['ORD_PARENT'];
					if($is_supplement>0)
					{
						$orderDateUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_ORDS_ID = '4' WHERE  ORD_ID = '$is_supplement'";
							$conn->query($orderDateUpdateQuery);
					}
				}

}
else
{


// injection du paiement sur la table contenant les payements
$paymentInsertQuery = "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES (NULL, '$commandeid_interne', 'ERR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW())";
	$conn->query($paymentInsertQuery);
}
/////////////////////////////////// FIN SUCCESS ///////////////////////////////
/////////////////////////////////// FIN SUCCESS ///////////////////////////////
/////////////////////////////////// FIN SUCCESS ///////////////////////////////

/////////////////////////////////// ERROR ///////////////////////////////////
/////////////////////////////////// ERROR ///////////////////////////////////
/////////////////////////////////// ERROR ///////////////////////////////////
/*
//Le marchand doit contacter la banque du porteur. Déprécié.
if ($vads_result == "02") {
$CR = "ERR";

// mise à jours de la table commande
mysqli_query($sqlConn, "update backofficeplateform_commande set paiement = 0 where id_com = '$commandeid_interne' ");
// injection du paiement sur la table contenant les payements
mysqli_query($sqlConn, "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES ('', '$commandeid_interne', '$CR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW());");
}
//Action refusée.
if ($vads_result == "05") {
$CR = "ERR";

// mise à jours de la table commande
mysqli_query($sqlConn, "update backofficeplateform_commande set paiement = 0 where id_com = '$commandeid_interne' ");
// injection du paiement sur la table contenant les payements
mysqli_query($sqlConn, "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES ('', '$commandeid_interne', '$CR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW());");
}
//Annulation de l'acheteur
if ($vads_result == "17") {
$CR = "ERR";

// mise à jours de la table commande
mysqli_query($sqlConn, "update backofficeplateform_commande set paiement = 0 where id_com = '$commandeid_interne' ");
// injection du paiement sur la table contenant les payements
mysqli_query($sqlConn, "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES ('', '$commandeid_interne', '$CR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW());");
}
//Erreur de format de la requête. A mettre en rapport avec la valorisation du champ vads_extra_result.
if ($vads_result == "30") {
$CR = "ERR";

// mise à jours de la table commande
mysqli_query($sqlConn, "update backofficeplateform_commande set paiement = 0 where id_com = '$commandeid_interne' ");
// injection du paiement sur la table contenant les payements
mysqli_query($sqlConn, "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES ('', '$commandeid_interne', '$CR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW());");
}
//Erreur technique.
if ($vads_result == "96") {
$CR = "ERR";

// mise à jours de la table commande
mysqli_query($sqlConn, "update backofficeplateform_commande set paiement = 0 where id_com = '$commandeid_interne' ");
// injection du paiement sur la table contenant les payements
mysqli_query($sqlConn, "INSERT INTO ic_postback (ID_IC_POSTBACK, id_com, Status, StatusCode, idsite, idste, OrderID, PaymentID, TransactionID, Amount, Currency, PaymentMethod, Ip, Ips, DatePayment) VALUES ('', '$commandeid_interne', '$CR', '$vads_result', '1', '1', '$Ref', '$Auto', '$Trans', '$Mt', 'EUR', '$Cb', '$Ip', '$Ips', NOW());");
}
*/
/////////////////////////////////// FIN ERROR ///////////////////////////////
/////////////////////////////////// FIN ERROR ///////////////////////////////
/////////////////////////////////// FIN ERROR ///////////////////////////////


	
	
	
}
?>