<?php 
session_start();

// on vide le panier
unset($_SESSION['amcnkCart']);

// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="mycart";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

if(isset($_SESSION['myaklog'])) // le client est deja connecté
{

/*$vads_amount = $_POST['vads_amount'];
$vads_auth_mode = $_POST['vads_auth_mode'];
$vads_auth_number = $_POST['vads_auth_number'];
$vads_auth_result = $_POST['vads_auth_result'];
$vads_capture_delay = $_POST['vads_capture_delay'];
$vads_card_brand = $_POST['vads_card_brand'];
$vads_card_number = $_POST['vads_card_number'];
$vads_payment_certificate = $_POST['vads_payment_certificate'];
$vads_ctx_mode = $_POST['vads_ctx_mode'];
$vads_currency = $_POST['vads_currency'];
$vads_effective_amount = $_POST['vads_effective_amount'];
$vads_site_id = $_POST['vads_site_id'];
$vads_trans_date = $_POST['vads_trans_date'];
$vads_trans_id = $_POST['vads_trans_id'];
$vads_validation_mode = $_POST['vads_validation_mode'];
$vads_version = $_POST['vads_version'];
$vads_warranty_result = $_POST['vads_warranty_result'];
$vads_payment_src = $_POST['vads_payment_src'];
$vads_sequence_number = $_POST['vads_sequence_number'];
$vads_contract_used = $_POST['vads_contract_used'];
$vads_trans_status = $_POST['vads_trans_status'];
$vads_expiry_month = $_POST['vads_expiry_month'];
$vads_expiry_year = $_POST['vads_expiry_year'];
$vads_bank_code = $_POST['vads_bank_code'];
$vads_bank_product = $_POST['vads_bank_product'];
$vads_pays_ip = $_POST['vads_pays_ip'];
$vads_presentation_date = $_POST['vads_presentation_date'];
$vads_effective_creation_date = $_POST['vads_effective_creation_date'];
$vads_operation_type = $_POST['vads_operation_type'];
$vads_threeds_enrolled = $_POST['vads_threeds_enrolled'];
$vads_threeds_cavv = $_POST['vads_threeds_cavv'];
$vads_threeds_eci = $_POST['vads_threeds_eci'];
$vads_threeds_xid = $_POST['vads_threeds_xid'];
$vads_threeds_cavvAlgorithm = $_POST['vads_threeds_cavvAlgorithm'];
$vads_threeds_status = $_POST['vads_threeds_status'];
$vads_threeds_sign_valid = $_POST['vads_threeds_sign_valid'];
$vads_threeds_error_code = $_POST['vads_threeds_error_code'];
$vads_threeds_exit_status = $_POST['vads_threeds_exit_status'];
$vads_risk_control = $_POST['vads_risk_control'];
$vads_result = $_POST['vads_result'];
$vads_extra_result = $_POST['vads_extra_result'];
$vads_card_country = $_POST['vads_card_country'];
$vads_language = $_POST['vads_language'];
$vads_hash = $_POST['vads_hash'];
$vads_url_check_src = $_POST['vads_url_check_src'];
$vads_action_mode = $_POST['vads_action_mode'];
$vads_payment_config = $_POST['vads_payment_config'];
$vads_page_action = $_POST['vads_page_action'];
$signature =  $_POST['signature']; */

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

//mysqli_query($sqlConn, "update backofficeplateform_commande set code_transaction = '$Trans' where  id_com = '$commandeid_interne' ");
//mysqli_query($sqlConn, "update backofficeplateform_commande set code_autorisation = '$Auto' where  id_com = '$commandeid_interne' ");
//mysqli_query($sqlConn, "update backofficeplateform_commande set cart_paiement = '$Cb' where  id_com = '$commandeid_interne' ");

/*
// mise a jour de la table historique commande
mysqli_query($sqlConn, "SET NAMES 'utf8'");
mysqli_query($sqlConn, "INSERT INTO backofficeplateform_historique_commande (id_his,id_com, id_agent,id_etat,id_stat,date,desc_stat,comm_stat, nom_agent) VALUES ('','$commandeid_interne','','1','1','$datecommandepay','En cours de vérification de compatibilité et disponibilité','','SYSTEM')");

// generation de la facture correspondante a ce payment
$commandefactureeid = $commandeid_interne;
$qcf=mysqli_query($sqlConn, "select * from backofficeplateform_commande where id_com='$commandefactureeid' ");
$rcf=mysqli_fetch_array($qcf);
//id client
$factidclt=$rcf['id_clt'];
// automecanik
$machandisesite=1;
// fmcar
$machandiseste=1;
// tva
$currenttva=$rcf['tva'];
$machandisetva=number_format($currenttva, 2, '.', '');
$consignetotale=$rcf['tconsigne'];
$stHT=$rcf['prix_THT'];
$tvatotale=$rcf['montant_TVA'];
$livfrais=$rcf['frais_port'];
$stTTC=$rcf['montant_tt'];
$datecommande=$rcf['date_com'];
$connectedclt_liv_civ=$rcf['civilite_l'];
$connectedclt_liv_nom=$rcf['nom_l'];
$connectedclt_liv_prenom=$rcf['prenom_l'];
$connectedclt_liv_ste=$rcf['raison_social_l'];
$connectedclt_liv_zipcode=$rcf['code_post_l'];
$connectedclt_liv_ville=$rcf['commune_l'];
$connectedclt_liv_pays=$rcf['pays_l'];
$connectedclt_liv_mail=$rcf['mail_l'];
$connectedclt_liv_tel=$rcf['tel_l'];
$connectedclt_liv_adress=$rcf['adr1_l'];
$connectedclt_fact_civ=$rcf['civilite_f'];
$connectedclt_fact_nom=$rcf['nom_f'];
$connectedclt_fact_prenom=$rcf['prenom_f'];
$connectedclt_fact_ste=$rcf['raison_social_f'];
$connectedclt_fact_zipcode=$rcf['code_post_f'];
$connectedclt_fact_ville=$rcf['commune_f'];
$connectedclt_fact_pays=$rcf['pays_f'];
$connectedclt_fact_mail=$rcf['mail_f'];
$connectedclt_fact_tel=$rcf['tel_f'];
$connectedclt_fact_adress=$rcf['adr1_f'];

$id_liv=$rcf['id_liv'];
$type_liv=$rcf['type_liv'];
$num_PR=$rcf['num_PR'];
$adr_PR=$rcf['adr_PR'];
$cart_pay=$rcf['cart_paiement'];
$code_trans=$rcf['code_transaction'];
$code_auto=$rcf['code_autorisation'];
$date_pay=$rcf['date_pay'];
$parent=$rcf['parent'];
*/
/////////////////////////////////////////////////////// SUPPLEMENT //////////////////////////////////////////
/*if($parent>0)
{
// dans le cas que la commande est un supplement on recupere le numero de commande d'origine
$NumCommandeParent=$rcf['parent'];
$qcf2=mysqli_query($sqlConn, "select * from backofficeplateform_facture where id_com='$parent' ");
$rcf2=mysqli_fetch_array($qcf2);
$parent=$rcf2['id_fact'];
$parentcom=$rcf2['id_com'];

// mise a jour de la table historique commande du parent
mysqli_query($sqlConn, "SET NAMES 'utf8'");
mysqli_query($sqlConn, "INSERT INTO backofficeplateform_historique_commande (id_his,id_com, id_agent,id_etat,id_stat,date,desc_stat,comm_stat, nom_agent) VALUES ('','$NumCommandeParent','','1','14','$datecommandepay','Paiement sur supplément reçu','','SYSTEM')");

mysqli_query($sqlConn, "update backofficeplateform_commande set id_statut = '14' where  id_com = '$NumCommandeParent' ");

// mise a jours du supplement pour reception de paiement
mysqli_query($sqlConn, "update backofficeplateform_commande set id_statut = '14' where  id_com = '$commandeid_interne' ");
}*/

/////////////////////////////////////////////////////// FACTURE //////////////////////////////////////////
/*
mysqli_query($sqlConn, "INSERT INTO backofficeplateform_facture (id_fact, id_com, id_clt, id_site, id_societe, tva, tconsigne, prix_THT, montant_TVA, frais_port, montant_tt, date_com, cart_paiement, code_transaction, code_autorisation, date_pay, parent, civilite_l, nom_l, prenom_l, raison_social_l, code_post_l, commune_l, pays_l, mail_l, tel_l, adr1_l, adr2_l, adr3_l, civilite_f, nom_f, prenom_f, raison_social_f, code_post_f, commune_f, pays_f, mail_f, tel_f, adr1_f, adr2_f, adr3_f, id_liv, type_liv, num_PR, adr_PR) VALUES ('', '$commandefactureeid', '$factidclt', '$machandisesite', '$machandiseste', '$machandisetva', '$consignetotale', '$stHT', '$tvatotale', '$livfrais', '$stTTC', '$datecommande',
'$cart_pay', '$code_trans', '$code_auto', '$date_pay', '$parent', '$connectedclt_liv_civ', '$connectedclt_liv_nom', '$connectedclt_liv_prenom', '$connectedclt_liv_ste', '$connectedclt_liv_zipcode', '$connectedclt_liv_ville', '$connectedclt_liv_pays', '$connectedclt_liv_mail', '$connectedclt_liv_tel', '$connectedclt_liv_adress', '', '', '$connectedclt_fact_civ', '$connectedclt_fact_nom', '$connectedclt_fact_prenom', '$connectedclt_ste', '$connectedclt_fact_zipcode', '$connectedclt_fact_ville', '$connectedclt_fact_pays', '$connectedclt_fact_mail', '$connectedclt_fact_tel', '$connectedclt_fact_adr', '', '', '$id_liv', '$type_liv', '$num_PR', '$adr_PR')");

// insertion des lignes de factures a travers les ligne de commandes
// recuperer l'identifiant de la commande
$facture_id_injected = @mysql_insert_id();


$qlcf=mysqli_query($sqlConn, "select * from backofficeplateform_ligne_commande where id_com='$commandefactureeid' ");
while($rlcf=mysqli_fetch_array($qlcf))
{
$ideq=$rlcf["id_forn"];
$nomequip=$rlcf["nom_forn"];
$refart=$rlcf["ref"];
$idgam=$rlcf["pg_id"];
$nomgam=$rlcf["desc"];
$qartfact=$rlcf["quantite"];
$puht=$rlcf["prix_UHT"];
$puttc=$rlcf["prix_UTTC"];
$ptht=$rlcf["prix_THT"];
$ptttc=$rlcf["prix_TTTC"];
$consignefact=$rlcf["prix_consigne"];


mysqli_query($sqlConn, "INSERT INTO backofficeplateform_ligne_facture (id_lig_fact, id_fact, id_equip, nom_equip, ref, id_gamme, nom_gamme, quantite, prix_UHT, prix_UTTC, prix_THT, prix_TTTC, prix_consigne) VALUES ('', '$facture_id_injected', '$ideq', '$nomequip', '$refart', '$idgam', '$nomgam', '$qartfact', '$puht', '$puttc', '$ptht', '$ptttc', '$consignefact')");
}
*/


// creation des variables pour l'envoi du mail
// requete pour avoir le client en question
		$mailtogetinfoclt=$_SESSION['myaklog'];
	    $query_client_data = "SELECT * FROM ___XTR_CUSTOMER 
	        WHERE CST_MAIL = '$mailtogetinfoclt'";
	    $request_client_data = $conn->query($query_client_data);
	    $result_client_data = $request_client_data->fetch_assoc();
		
		$ClientMail = $result_client_data['CST_MAIL'] ;
		$ClientData = $result_client_data['CST_CIVITILY']." ".$result_client_data['CST_NAME']." ".$result_client_data['CST_FNAME'];
		$ClientSujet = "Confirmation de paiement" ;
		$ClientComAmount = $Mt/100 ;
		
// envoi du mail de confirmation au client
//SendMailToClientPaymentConfirmation( $domainname, $domainclientaccount, $domainwebsitecontact, $domainwebsitetel, $ClientMail, $ClientData, $ClientSujet, $Ref, $datecommandepay, $ClientComAmount  );

}
/////////////////////////////////// FIN SUCCESS ///////////////////////////////
/////////////////////////////////// FIN SUCCESS ///////////////////////////////
/////////////////////////////////// FIN SUCCESS ///////////////////////////////

	
	
	
}














?>

<form name="ConcludeCom" id="ConcludeCom" method="post" action="<?php echo $domain; ?>/mycart.payment.confirmation.done.php">  
<input type="hidden" name="commande_id" value="<?php echo $commandeid; ?>"> 
<input type="submit" value="Envoyer"> 

<script language="javascript">
document.ConcludeCom.submit();
</script>
<?php
}
?>