<?php 
session_start();
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



// injection du rejet de paiement sur la table contenant les payements

// mise à jours de la table commande

// envoi du mail de rejet au client
		$mailtogetinfoclt=$_SESSION['myaklog'];
	    $query_client_data = "SELECT * FROM ___XTR_CUSTOMER 
	        WHERE CST_MAIL = '$mailtogetinfoclt'";
	    $request_client_data = $conn->query($query_client_data);
	    $result_client_data = $request_client_data->fetch_assoc();
		
		$ClientMail = $result_client_data['CST_MAIL'] ;
		$ClientData = $result_client_data['CST_CIVITILY']." ".$result_client_data['CST_NAME']." ".$result_client_data['CST_FNAME'];
		$ClientSujet = "Rejet de paiement" ;
		$ClientComAmount = $Mt/100 ;
// envoi du mail de confirmation au client
//SendMailToClientPaymentRejected( $domainname, $domainclientaccount, $domainwebsitecontact, $domainwebsitetel, $ClientMail, $ClientData, $ClientSujet, $Ref );


}
?>

<form name="ConcludeCom" id="ConcludeCom" method="post" action="<?php echo $domain; ?>/mycart.payment.rejected.done.php">  
<input type="hidden" name="commande_id" value="<?php echo $commandeid; ?>">
<input type="hidden" name="error" value="<?php echo $vads_result; ?>"> 
<input type="submit" value="Envoyer"> 

<script language="javascript">
document.ConcludeCom.submit();
</script>
<?php
}
?>