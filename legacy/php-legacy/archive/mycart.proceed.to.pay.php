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

// GET CLIENT DATA
	$mailtogetinfoclt=$_SESSION['myaklog'];
	$idtogetinfoclt=$_SESSION['myakid'];

// GET BILLIG DATA
	$connectedclt_cba_id = $_POST['cba_id'];

// GET DELIVERY DATA
	$connectedclt_cda_id = $_POST['cda_id'];
	$id_liv = $_POST['livmethod']; // c'est le id du livreur

// MORE DETAILS FROM CART
	$cartimmat=$_SESSION['cartimmat'];
	$cartvin=$_SESSION['cartvin'];
	$cartoemcom=$_SESSION['oemcom'];
	$cartinfosup=$_SESSION['infossup'];
	$cartequiv=$_SESSION['equiv'];
	$infoscomplementaires="Immatriculation : ".$cartimmat."<br>"."VIN (Numero de chassis) : ".$cartvin."<br>"."Ref d origine ou commercial : ".$cartoemcom."<br>"."Infos complementaires : ".$cartinfosup."<br>"."Equivalence : ".$cartequiv;
	$infoscomplementaires = mysqli_real_escape_string($conn, $infoscomplementaires);

// TOTALS VALUES EXISTING IN ORDER
	$amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']);
	// Sous Total et Consigne
	$amcnkCart_total_amount = 0;
	$amcnkCart_total_consigne = 0;
	/*for($i = 0; $i < $amcnkCart_count; $i++) 
	{ 
	$piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
	$piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
	$pvttc_qte_this = $_SESSION['amcnkCart']['prix'][$i];
	$pcttc_qte_this = $_SESSION['amcnkCart']['consigne'][$i];

	    $amcnkCart_total_amount = $amcnkCart_total_amount + ($pvttc_qte_this*$piece_qte_this);
	    $amcnkCart_total_consigne = $amcnkCart_total_consigne + ($pcttc_qte_this*$piece_qte_this);
	}*/
	for($i = 0; $i < $amcnkCart_count; $i++) 
	{ 
	$piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
	$piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
		$query_piece_global = "SELECT DISTINCT PIECE_ID, (PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, 
		(PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC 
		FROM PIECES 
		JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
		JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
		WHERE PIECE_ID = $piece_id_this AND PIECE_DISPLAY = 1 
		ORDER BY PIECE_SORT";
		$request_piece_global = $conn->query($query_piece_global);
		if ($request_piece_global->num_rows > 0) 
		{
			$result_piece_global = $request_piece_global->fetch_assoc();
			$amcnkCart_total_amount = $amcnkCart_total_amount + ($result_piece_global['PVTTC']*$piece_qte_this);
			$amcnkCart_total_consigne = $amcnkCart_total_consigne + ($result_piece_global['PCTTC']*$piece_qte_this);		
	    }
	}
	$consignet = $amcnkCart_total_consigne;
	$stt = $amcnkCart_total_amount;
	// sous total HT et TVA
	$stHT=($stt/(($GlobalSiteTva/100)+1));
	$tvatotale=$stt-$stHT;
	// consigne totale
	$consignetotale=number_format($consignet, 2, '.', '');
	// soustotal HT
	$stHT=number_format($stHT, 2, '.', '');
	// total TVA
	$tvatotale=number_format($tvatotale, 2, '.', '');
	// soustotal TTC
	$stTTC=number_format($stt, 2, '.', '');

// PAYMENT METHOD
	$paymethod = $_POST['paymethod'];	
	// parametre de l'envoi au paiement
	$ID_PAYMENT_OPTIONS = $paymethod;
// ORDER DATES
	// date commanda
	$datecommande=date("Y-m-j H:i:s");
// FRAIS DE PORT
	$livfrais=$_POST['FinalShippingFraisAtivated'];
	$livfrais=number_format($livfrais, 2, '.', '');
// TOTAL TTC to PAY
	$totalTTCtoPAY=$stTTC+$livfrais+$consignetotale;
		
// INJECT ORDER	
	// Query
	$InsertQuery = "INSERT INTO `___XTR_ORDER` (`ORD_ID`, `ORD_CST_ID`, `ORD_CBA_ID`, `ORD_CDA_ID`, `ORD_DATE`, `ORD_AMOUNT_HT`, `ORD_DEPOSIT_HT`, `ORD_SHIPPING_FEE_HT`, `ORD_TOTAL_HT`, `ORD_TVA`, `ORD_AMOUNT_TTC`, `ORD_DEPOSIT_TTC`, `ORD_SHIPPING_FEE_TTC`, `ORD_TOTAL_TTC`, `ORD_DA_ID`, `ORD_INFO`) VALUES (NULL, '$idtogetinfoclt', '$connectedclt_cba_id', '$connectedclt_cda_id', '$datecommande', '0', '0', '0', '0', '$GlobalSiteTva', '$stTTC', '$consignetotale', '$livfrais', '$totalTTCtoPAY', '$id_liv', '$infoscomplementaires')";
	if($conn->query($InsertQuery) === TRUE)
	{
		// Last ID
		$commande_id_injected = $conn->insert_id;
		if($commande_id_injected>0)
		{
				// ORDER LINE INJECT
				$orl_ord_id = $commande_id_injected;
				$amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']);
				for($i = 0; $i < $amcnkCart_count; $i++) 
				{ 
					// recuperation des données de la session
					$piece_url_this = $_SESSION['amcnkCart']['urltakentoadd'][$i];
					$piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
					$piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
					$piece_prix_this = $_SESSION['amcnkCart']['prix'][$i];
					$piece_consigne_this = $_SESSION['amcnkCart']['consigne'][$i];

					$query_piece = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_PG_ID, PIECE_NAME, 
						PIECE_PM_ID, PM_NAME,  
						(PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, (PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC,
						(PRI_GROS_HT * PIECE_QTY_SALE) AS PAPHT, 
						PRI_REMISE,
						(PRI_ACHAT_HT * PIECE_QTY_SALE) AS PAHT,
						PRI_MARGE
						FROM PIECES 
						JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
						JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
						WHERE PIECE_ID = $piece_id_this AND PIECE_DISPLAY = 1 
						ORDER BY PIECE_SORT";
					$request_piece = $conn->query($query_piece);
					if ($request_piece->num_rows > 0) 
					{
					$result_piece = $request_piece->fetch_assoc();
					// CREATION DES CHAMP POUR INJECTION
						// Gamme
						$orl_pg_id = $result_piece['PIECE_PG_ID'];
						$orl_pg_name = mysqli_real_escape_string($conn, $result_piece['PIECE_NAME']);
						// Equipementier
						$orl_pm_id = $result_piece['PIECE_PM_ID'];
						$orl_pm_name = mysqli_real_escape_string($conn, $result_piece['PM_NAME']);
						// Reference
						$orl_art_ref = $result_piece['PIECE_REF'];
						$orl_art_ref_clean = $result_piece['PIECE_REF_CLEAN'];
						// Prix d'achat unistaire fournisseur sans remise
						$orl_art_price_buy_unit_public_ht = $result_piece['PAPHT'];
						$orl_art_price_buy_unit_public_ttc = $result_piece['PAPHT']*$GlobalSiteTvaCoeff;
						// remise fournisseur
						$orl_art_price_buy_discount = $result_piece['PRI_REMISE'];
						// prix d'achat unistaire fournisseur avec remise
						$orl_art_price_buy_unit_ht = $result_piece['PAHT'];
						$orl_art_price_buy_unit_ttc = $result_piece['PAHT']*$GlobalSiteTvaCoeff;
						// marge sur vente
						$orl_art_price_sell_margin = $result_piece['PRI_MARGE'];
						// prix de vente unitaire
						$orl_art_price_sell_unit_ht = $result_piece['PVTTC']/$GlobalSiteTvaCoeff;
						$orl_art_price_sell_unit_ttc = $result_piece['PVTTC'];
						// prix de consigne unitaire
						$orl_art_deposit_unit_ht = $result_piece['PCTTC']/$GlobalSiteTvaCoeff;
						$orl_art_deposit_unit_ttc = $result_piece['PCTTC'];
						// quantity
						$orl_art_quantity = $piece_qte_this;
						// prix d'achat fournisseur avec remise
						$orl_art_price_buy_ht = $orl_art_quantity*$orl_art_price_buy_unit_ht;
						$orl_art_price_buy_ttc = $orl_art_quantity*$orl_art_price_buy_unit_ttc;
						// prix de vente
						$orl_art_price_sell_ht = $orl_art_quantity*$orl_art_price_sell_unit_ht;
						$orl_art_price_sell_ttc = $orl_art_quantity*$orl_art_price_sell_unit_ttc;
						// prix de consigne
						$orl_art_deposit_ht = $orl_art_quantity*$orl_art_deposit_unit_ht;
						$orl_art_deposit_ttc = $orl_art_quantity*$orl_art_deposit_unit_ttc;
					// INJECTION QUERY
					$InsertQueryLine = "INSERT INTO `___XTR_ORDER_LINE` (`ORL_ID`, `ORL_ORD_ID`, `ORL_PG_ID`, `ORL_PG_NAME`, `ORL_PM_ID`, `ORL_PM_NAME`, `ORL_ART_REF`, `ORL_ART_REF_CLEAN`, `ORL_ART_PRICE_BUY_UNIT_PUBLIC_HT`, `ORL_ART_PRICE_BUY_UNIT_PUBLIC_TTC`, `ORL_ART_PRICE_BUY_DISCOUNT`, `ORL_ART_PRICE_BUY_UNIT_HT`, `ORL_ART_PRICE_BUY_UNIT_TTC`, `ORL_ART_PRICE_SELL_MARGIN`, `ORL_ART_PRICE_SELL_UNIT_HT`, `ORL_ART_PRICE_SELL_UNIT_TTC`, `ORL_ART_DEPOSIT_UNIT_HT`, `ORL_ART_DEPOSIT_UNIT_TTC`, `ORL_ART_QUANTITY`, `ORL_ART_PRICE_BUY_HT`, `ORL_ART_PRICE_BUY_TTC`, `ORL_ART_PRICE_SELL_HT`, `ORL_ART_PRICE_SELL_TTC`, `ORL_ART_DEPOSIT_HT`, `ORL_ART_DEPOSIT_TTC`, `ORL_WEBSITE_URL`) VALUES (NULL, '$orl_ord_id', '$orl_pg_id', '$orl_pg_name', '$orl_pm_id', '$orl_pm_name', '$orl_art_ref', '$orl_art_ref_clean', '$orl_art_price_buy_unit_public_ht', '$orl_art_price_buy_unit_public_ttc', '$orl_art_price_buy_discount', '$orl_art_price_buy_unit_ht', '$orl_art_price_buy_unit_ttc', '$orl_art_price_sell_margin', '$orl_art_price_sell_unit_ht', '$orl_art_price_sell_unit_ttc', '$orl_art_deposit_unit_ht', '$orl_art_deposit_unit_ttc', '$orl_art_quantity', '$orl_art_price_buy_ht', '$orl_art_price_buy_ttc', '$orl_art_price_sell_ht', '$orl_art_price_sell_ttc', '$orl_art_deposit_ht', '$orl_art_deposit_ttc', '$piece_url_this')";
					$conn->query($InsertQueryLine);
				    }
				}
				// END ORDER LINE INJECT

				// SENDING MAIL

    			// PREPARE PAYMENT
    			$commande_id_injected_TOPAY = $commande_id_injected."/"."A";
				$commande_id_injected_TOPAY_CYBER = $commande_id_injected."-"."A";
				$mailcltTOPAY = $mailtogetinfoclt;
					// changement due a l'enlevement du test d'inscription demandé par mourad
					$mailcltTOPAY =  str_replace(";",".",$mailcltTOPAY);
				$idcltTOPAY = $_SESSION['myakid'];
				$amountTOPAY =  $totalTTCtoPAY;
					$amountTOPAY =  str_replace(",",".",$amountTOPAY)*100;
					$amountTOPAY =  str_replace(" ","",$amountTOPAY);
				
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
						?>
						<meta http-equiv="refresh" content="17; URL=<?php echo $domain; ?>/validation.html">
						<?php	
					}
				}
		}
		else
		{
			echo "Wrong last ID, An error occured.";
			?>
			<meta http-equiv="refresh" content="17; URL=<?php echo $domain; ?>/validation.html">
			<?php
		}
	}
	else
	{
		echo "Inject Order, An error occured : ".$conn->error;
		?>
		<meta http-equiv="refresh" content="17; URL=<?php echo $domain; ?>/validation.html">
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
<meta http-equiv="refresh" content="17; URL=<?php echo $domain; ?>/validation.html">
<?php
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
////////////////////////////////////////////// FIN NON CONNECTE /////////////////////////////////////////////////
}
?>