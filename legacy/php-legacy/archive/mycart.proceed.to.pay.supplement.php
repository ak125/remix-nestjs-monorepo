<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

if(isset($_SESSION['myaklog']))
{
////////////////////////////////////////////// CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// CONNECTE /////////////////////////////////////////////////

	// PREPARE PAYMENT
	$commande_id_injected = $_POST['ORD_ID'];
	$commande_id_injected_TOPAY = $commande_id_injected."/"."A";
	$commande_id_injected_TOPAY_CYBER = $commande_id_injected."-"."A";
	$idcltTOPAY = $_SESSION['myakid'];
	$mailcltTOPAY = $_SESSION['myaklog'];

	$query_payment_data = "SELECT ORD_ID, ORD_DATE, ORD_DATE_PAY, ORD_INFO, ORD_PARENT,  
        ORD_AMOUNT_TTC, ORD_DEPOSIT_TTC, ORD_SHIPPING_FEE_TTC, ORD_TOTAL_TTC, 
        ORD_CST_ID, CST_CIVILITY, CST_NAME, CST_FNAME, CST_ADDRESS, CST_ZIP_CODE, CST_CITY, CST_COUNTRY, 
        CST_TEL, CST_GSM, CST_MAIL
        FROM ___XTR_ORDER 
        JOIN ___XTR_CUSTOMER ON CST_ID = ORD_CST_ID AND CST_ACTIV = 1 AND CST_ID = '$idcltTOPAY' AND CST_MAIL = '$mailcltTOPAY'
        WHERE ORD_ID = '$commande_id_injected' AND ORD_IS_PAY = 0";
    $request_payment_data = $conn->query($query_payment_data);
    if ($request_payment_data->num_rows > 0) 
    {
        $result_payment_data = $request_payment_data->fetch_assoc();
		// MAIL
		$mailcltTOPAY =  str_replace(";",".",$result_payment_data['CST_MAIL']);
		//TOTAL TO PAY
		$totalTTCtoPAY = $result_payment_data['ORD_TOTAL_TTC'];
		$amountTOPAY =  $totalTTCtoPAY;
		$amountTOPAY =  str_replace(",",".",$amountTOPAY)*100;
		$amountTOPAY =  str_replace(" ","",$amountTOPAY);
		// PAYMENT METHOD
		$paymethod = $_POST['paymethod'];	
		// parametre de l'envoi au paiement
		$ID_PAYMENT_OPTIONS = $paymethod;

		// GO TO PAY
		if (($ID_PAYMENT_OPTIONS == "CB")||($ID_PAYMENT_OPTIONS == "PAYPAL")) 
		{ 
			$domain = "https://www.automecanik.com";
			$dateTimeCyber = date("YmdHis");
			$dateTimeCyberUTC=gmdate('YmdHis', strtotime($dateTimeCyber));
			$CancelLink=utf8_encode($domain."/cyberplus.my.cart.payment.cancel.php");
			$ErrorLink=utf8_encode($domain."/cyberplus.my.cart.payment.refused.php");
			$RefusedLink=utf8_encode($domain."/cyberplus.my.cart.payment.refused.php");
			$SuccessLink=utf8_encode($domain."/cyberplus.my.cart.payment.success.php");
			$CertificatTest="9300172162563656";
			$CertificatProd="9816635272016068";
			$CertificatToUse=$CertificatProd;
			$sitemerchantid="43962882";
			$signatureCyberPlusCHAINE="INTERACTIVE+".$amountTOPAY."+0+PRODUCTION+978+FR+".$mailcltTOPAY."+".$commande_id_injected_TOPAY_CYBER."+PAYMENT+SINGLE+POST+".$sitemerchantid."+".$dateTimeCyberUTC."+".$commande_id_injected."+".$CancelLink."+".$ErrorLink."+".$RefusedLink."+".$SuccessLink."+0+V2+".$CertificatToUse;
			$signatureCyberPlus = sha1($signatureCyberPlusCHAINE);
			?>
			<form name="ProceedToPay" id="ProceedToPay" method="post" action="https://paiement.systempay.fr/vads-payment/"> 
			<input type="hidden" name="vads_action_mode" value="INTERACTIVE"> 
			<input type="hidden" name="vads_amount" value="<?php echo $amountTOPAY; ?>">
			<input type="hidden" name="vads_capture_delay" value="0">
			<input type="hidden" name="vads_ctx_mode" value="PRODUCTION"> 
			<input type="hidden" name="vads_currency" value="978">  
			<input type="hidden" name="vads_cust_country" value="FR" />
			<input type="hidden" name="vads_cust_email" value="<?php echo $mailcltTOPAY; ?>" />
			<input type="hidden" name="vads_order_id" value="<?php echo $commande_id_injected_TOPAY_CYBER; ?>">
			<input type="hidden" name="vads_page_action" value="PAYMENT">
			<input type="hidden" name="vads_payment_config" value="SINGLE"> 
			<input type="hidden" name="vads_return_mode" value="POST"> 
			<input type="hidden" name="vads_site_id" value="<?php echo $sitemerchantid; ?>"> 
			<input type="hidden" name="vads_trans_date" value="<?php echo $dateTimeCyberUTC; ?>"> 
			<input type="hidden" name="vads_trans_id" value="<?php echo $commande_id_injected; ?>"> 
			<input type="hidden" name="vads_url_cancel" value="<?php echo $CancelLink; ?>" />
			<input type="hidden" name="vads_url_error" value="<?php echo $ErrorLink; ?>" />
			<input type="hidden" name="vads_url_refused" value="<?php echo $RefusedLink; ?>" />
			<input type="hidden" name="vads_url_success" value="<?php echo $SuccessLink; ?>" />
			<input type="hidden" name="vads_validation_mode" value="0">
			<input type="hidden" name="vads_version" value="V2"> 
			<input type="hidden" name="signature" value="<?php echo $signatureCyberPlus; ?>">  
			<input type="submit" value="Envoyer" disabled="disabled"> 
			</form> 
			<script language="javascript">
			document.ProceedToPay.submit();
			</script>
			<?php
		}
		else
		{
			// GO TO PAYBOX
			if ($ID_PAYMENT_OPTIONS == "PAYBOX")
			{ 
				$commande_id_injected_Paybox = $commande_id_injected;
				$domain = "https://www.automecanik.com";
				$dateTimePaybox = date("c");
				$CertificatTest="7731B4225651B0C434189E2A13B963F91D8BBE78AEC97838E40925569E25357373C792E2FBE5A6B8C0CBC12ED27524CC2EE0C4653C93A14A39414AA42F85AEE5";
				$CertificatProd="prod";
				$CertificatToUse=$CertificatTest;
				$sitemerchantsite="5259250";
				$sitemerchantrang="001";
				$sitemerchantid="822188223";
				$signaturePayboxCHAINE="PBX_SITE=".$sitemerchantsite.
					"&PBX_RANG=".$sitemerchantrang.
					"&PBX_IDENTIFIANT=".$sitemerchantid.
					"&PBX_TOTAL=".$amountTOPAY.
					"&PBX_DEVISE=978".
					"&PBX_CMD=".$commande_id_injected_Paybox.
					"&PBX_PORTEUR=".$mailcltTOPAY.
					"&PBX_RETOUR=Mt:M;Ref:R;Auto:A;Erreur:E".
					"&PBX_HASH=SHA512".
					"&PBX_TIME=".$dateTimePaybox;
				$binKey = pack("H*", $CertificatToUse);
				$signaturePaybox = strtoupper(hash_hmac('sha512', $signaturePayboxCHAINE, $binKey));
				?>
				<form name="ProceedToPay" id="ProceedToPay" method="POST" action="https://tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi">
				<input type="hidden" name="PBX_SITE" value="<?php echo $sitemerchantsite; ?>">
				<input type="hidden" name="PBX_RANG" value="<?php echo $sitemerchantrang; ?>">
				<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $sitemerchantid; ?>">
				<input type="hidden" name="PBX_TOTAL" value="<?php echo $amountTOPAY; ?>">
				<input type="hidden" name="PBX_DEVISE" value="978">
				<input type="hidden" name="PBX_CMD" value="<?php echo $commande_id_injected_Paybox; ?>">
				<input type="hidden" name="PBX_PORTEUR" value="<?php echo $mailcltTOPAY; ?>">
				<input type="hidden" name="PBX_RETOUR" value="Mt:M;Ref:R;Auto:A;Erreur:E">
				<input type="hidden" name="PBX_HASH" value="SHA512">
				<input type="hidden" name="PBX_TIME" value="<?php echo $dateTimePaybox; ?>">
				<input type="hidden" name="PBX_HMAC" value="<?php echo $signaturePaybox; ?>">
				<input type="submit" value="Envoyer">
				</form>
				<script language="javascript">
				document.ProceedToPay.submit();
				</script>
				<?php
			}
			else
			{
			echo "Payment function disabled for CB and PAYPAL.";
			}
			?>
			<?php	
		}
    }
	else
	{
		echo "Order id do not correspond to any record.";
		?>
		<?php	
	}

////////////////////////////////////////////// FIN CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN CONNECTE /////////////////////////////////////////////////
}
else
{
////////////////////////////////////////////// NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// NON CONNECTE /////////////////////////////////////////////////
echo "Not connected, An error occured";
?>
<?php
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
}
?>