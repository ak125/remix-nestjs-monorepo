<?php
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('../config/meta.conf.php');

$log=$_SESSION[$sessionlog];
$mykey=$_SESSION[$sessionkey];

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
	$query_log = "SELECT * FROM ___CONFIG_ADMIN WHERE CNFA_LOGIN='$log' AND CNFA_KEYLOG='$mykey' AND CNFA_LEVEL > 6";
	$request_log = $conn->query($query_log);
	if ($request_log->num_rows > 0) 
	{
	$result_log = $request_log->fetch_assoc();
		if($result_log["CNFA_ACTIV"]=='1')
		{
			$destinationLink = $accessPermittedLink;
			$ssid = $result_log['CNFA_ID'];
			$accessRequest = true;
		    $destinationLinkMsg = "Granted";
		    $destinationLinkGranted = 1;
		    // SECONDAIRE
			$ssname = $result_log['CNFA_NAME'];
			$ssfname = $result_log['CNFA_FNAME'];
			$ssjob = $result_log['CNFA_JOB'];
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
// Department DATAS
$aliasDepartment = getDepartmentAlias();
$aliasSecondDepartment = getSecondDepartmentAlias();
// Title
$ord_id = $_GET["ord_id"];
	$query_ord_data = "SELECT * 
		FROM ___XTR_ORDER 
	    WHERE ORD_ID = $ord_id";
	$request_ord_data = $conn->query($query_ord_data);
	$result_ord_data = $request_ord_data->fetch_assoc();
	$ord_cst_id = $result_ord_data['ORD_CST_ID'];
$orl_id = $_GET["orl_id"];
$orls_id = 91; // Envoyer proposition d'équivalence
	$query_action = "SELECT * FROM ___XTR_ORDER_LINE_STATUS 
	    WHERE ORLS_ID = $orls_id";
	$request_action = $conn->query($query_action);
	$result_action = $request_action->fetch_assoc();
	$pageAction = $result_action["ORLS_ACTION"];
$pageH1 = "Commande n° ".$ord_id."/A";
$dept_id=1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  ACTION QUERY
$piece_id_proposed = $_POST['piece_id_proposed'];
$StatusUpdateResponseCode=0;
if(isset($_POST["Ask4Equiv"]))
{
	$orl_id = $_POST['orl_id'];
	$piece_qty_proposed = $_POST['piece_qty_proposed'];
	$amount_proposed = $_POST['amount_proposed'];
	// Get new order line to insert
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
			WHERE PIECE_ID = $piece_id_proposed AND PIECE_DISPLAY = 1 
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
			$orl_art_quantity = $piece_qty_proposed;
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
			$InsertQueryLine = "INSERT INTO `___XTR_ORDER_LINE` (`ORL_ID`, `ORL_ORD_ID`, `ORL_PG_ID`, `ORL_PG_NAME`, `ORL_PM_ID`, `ORL_PM_NAME`, `ORL_ART_REF`, `ORL_ART_REF_CLEAN`, `ORL_ART_PRICE_BUY_UNIT_PUBLIC_HT`, `ORL_ART_PRICE_BUY_UNIT_PUBLIC_TTC`, `ORL_ART_PRICE_BUY_DISCOUNT`, `ORL_ART_PRICE_BUY_UNIT_HT`, `ORL_ART_PRICE_BUY_UNIT_TTC`, `ORL_ART_PRICE_SELL_MARGIN`, `ORL_ART_PRICE_SELL_UNIT_HT`, `ORL_ART_PRICE_SELL_UNIT_TTC`, `ORL_ART_DEPOSIT_UNIT_HT`, `ORL_ART_DEPOSIT_UNIT_TTC`, `ORL_ART_QUANTITY`, `ORL_ART_PRICE_BUY_HT`, `ORL_ART_PRICE_BUY_TTC`, `ORL_ART_PRICE_SELL_HT`, `ORL_ART_PRICE_SELL_TTC`, `ORL_ART_DEPOSIT_HT`, `ORL_ART_DEPOSIT_TTC`, `ORL_WEBSITE_URL`, `ORL_ORLS_ID`) VALUES (NULL, '$ord_id', '$orl_pg_id', '$orl_pg_name', '$orl_pm_id', '$orl_pm_name', '$orl_art_ref', '$orl_art_ref_clean', '$orl_art_price_buy_unit_public_ht', '$orl_art_price_buy_unit_public_ttc', '$orl_art_price_buy_discount', '$orl_art_price_buy_unit_ht', '$orl_art_price_buy_unit_ttc', '$orl_art_price_sell_margin', '$orl_art_price_sell_unit_ht', '$orl_art_price_sell_unit_ttc', '$orl_art_deposit_unit_ht', '$orl_art_deposit_unit_ttc', '$orl_art_quantity', '$orl_art_price_buy_ht', '$orl_art_price_buy_ttc', '$orl_art_price_sell_ht', '$orl_art_price_sell_ttc', '$orl_art_deposit_ht', '$orl_art_deposit_ttc', 'System', '$orls_id')";
			if($conn->query($InsertQueryLine)===TRUE)
			{
				$orl_id_injected = $conn->insert_id;
				if($orl_id_injected>0)
				{
					$ParentUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_EQUIV_ID = $orl_id_injected 
						WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
					if($conn->query($ParentUpdateQuery) === TRUE)
					{
						$msg_date = date("Y-m-j H:i:s");
						$msg_subject = "Support Technique : ".$pageAction;
						$msg_subject = mysqli_real_escape_string($conn, $msg_subject);
						$msg_msg = mysqli_real_escape_string($conn, $pageAction);
							
						$SendMsgQuery = "INSERT INTO `___XTR_MSG` (`MSG_ID`, `MSG_CST_ID`, `MSG_ORD_ID`, `MSG_CNFA_ID`, `MSG_DATE`, `MSG_SUBJECT`, `MSG_CONTENT`, `MSG_OPEN`) VALUES (NULL, '$ord_cst_id', '$ord_id', '$ssid', '$msg_date', '$msg_subject', '$msg_msg', '0')";
						if($conn->query($SendMsgQuery) === TRUE)
						{
							$StatusUpdateResponse = "Proposition d'équivalence effectuée avec Succès.";
							$StatusUpdateResponseCode=1;
						}
						else
						{
							$StatusUpdateResponse = "Envoie de message, Une erreur est survenue : ".$conn->error;
							$StatusUpdateResponseCode=2;
						}
					}
					else
					{
						$StatusUpdateResponse = "Update Old Equiv ID, Une erreur est survenue : ".$conn->error;
						$StatusUpdateResponseCode=2;
					}
				}
			}
			else
			{
				$StatusUpdateResponse = "Insert New Equiv, Une erreur est survenue : ".$conn->error;
				$StatusUpdateResponseCode=2;
			}
	    }

}
// DATA
$query_data = "SELECT DISTINCT ORL_ID, ORL_PG_NAME, ORL_PM_NAME, ORL_ART_REF 
	FROM ___XTR_ORDER_LINE 
    JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id 
    ORDER BY ORL_ID";
$request_data = $conn->query($query_data);
$result_data = $request_data->fetch_assoc();
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $domaincorename; ?></title>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<!-- Données de la page d'accueil -->
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domainparent; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<?php echo $domainparent; ?>/system/bootstrap/js/bootstrap.min.js"></script>
<!-- CKEDITOR -->
<script src="<?php echo $domainparent; ?>/system/ckeditor/ckeditor.js"></script>
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/intern.css" rel="stylesheet">
</head>
<body class="BODY-MODAL">


	<form method="post" action="" class="TAKE-ACTION">
	<div class="row">
		<div class="col-12 text-center pb-3">

			<u><?php echo $pageH1; ?> : <?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageAction; ?></u></h2>
			<br>
			Un mail pour la validation d'équivalence sera envoyé au client.

		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center pt-3">
			<?php
				if($StatusUpdateResponseCode>0)
				{
					if($StatusUpdateResponseCode==1)
					{
						?>
						<div class="response-green"><?php echo $StatusUpdateResponse ;?></div>
						<?php
					}
					else
					{
						?>
						<div class="response-red"><?php echo $StatusUpdateResponse ;?></div>
						<?php
					}
				}
			?>
		</div>
	</div>
	<?php
	if($StatusUpdateResponseCode==0)
	{
	?>
	<div class="row">
		<div class="col-6 PROP-EQUIV-BLOC p-0">
			
			<?php
			$query_piece_old = "SELECT * 
				FROM ___XTR_ORDER_LINE 
			    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id 
			    ORDER BY ORL_ID";
			$request_piece_old = $conn->query($query_piece_old);
			$result_piece_old = $request_piece_old->fetch_assoc();
			?>			
			<div class="row p-0 m-0">
				<div class="col-12 PROP-EQUIV-BLOC-COL text-center p-3">
					<u><b>Ancienne pièce</b></u>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Gamme</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_PG_NAME']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Référence</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_REF']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Equipementier</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_PM_NAME']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PA U HT</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_PRICE_BUY_UNIT_HT']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PV U TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_PRICE_SELL_UNIT_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>CU TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_DEPOSIT_UNIT_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Quantité</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_QUANTITY']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PT TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_PRICE_SELL_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>CT TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_QUANTITY']*$result_piece_old['ORL_ART_DEPOSIT_UNIT_TTC']; ?> 
					<?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>TOTAL TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $totalttc_old = ($result_piece_old['ORL_ART_QUANTITY']*$result_piece_old['ORL_ART_DEPOSIT_UNIT_TTC'])+$result_piece_old['ORL_ART_PRICE_SELL_TTC']; ?> 
					<?php echo $GlobalSiteCurrencyChar; ?>
				</div>
			</div>

		</div>
		<div class="col-6 PROP-EQUIV-BLOC p-0">
			
			<?php
			$query_piece_new = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_PG_ID, PIECE_NAME, 
						PIECE_PM_ID, PM_NAME,  
						(PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, (PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC,
						(PRI_GROS_HT * PIECE_QTY_SALE) AS PAPHT, 
						PRI_REMISE,
						(PRI_ACHAT_HT * PIECE_QTY_SALE) AS PAHT,
						PRI_MARGE
						FROM PIECES 
						JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
						JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
						WHERE PIECE_ID = $piece_id_proposed AND PIECE_DISPLAY = 1 
						ORDER BY PIECE_SORT";
			$request_piece_new = $conn->query($query_piece_new);
			$result_piece_new = $request_piece_new->fetch_assoc();
			?>			
			<div class="row p-0 m-0">
				<div class="col-12 PROP-EQUIV-BLOC-COL text-center p-3">
					<u><b>Nouvelle pièce</b></u>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Gamme</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PIECE_NAME']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Référence</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PIECE_REF']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Equipementier</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PM_NAME']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PA U HT</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PAHT']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PV U TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PVTTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>CU TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_new['PCTTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>Quantité</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_QUANTITY']; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>PT TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_QUANTITY']*$result_piece_new['PVTTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>CT TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $result_piece_old['ORL_ART_QUANTITY']*$result_piece_new['PCTTC']; ?> 
					<?php echo $GlobalSiteCurrencyChar; ?>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<b>TOTAL TTC</b>
				</div>
				<div class="col-6 PROP-EQUIV-BLOC-COL">
					<?php echo $totalttc_new = ($result_piece_old['ORL_ART_QUANTITY']*$result_piece_new['PCTTC'])+($result_piece_old['ORL_ART_QUANTITY']*$result_piece_new['PVTTC']); ?> 
					<?php echo $GlobalSiteCurrencyChar; ?>
				</div>
			</div>
			
		</div>
		<div class="col-12 PROP-EQUIV-BLOC-RECUP">
			<?php 
			$amountequiv = $totalttc_new-$totalttc_old; 
			if($amountequiv>0)
				echo "Supplément de ".$amountequiv." ".$GlobalSiteCurrencyChar." TTC";
			else
				echo "Remboursement de ".abs($amountequiv)." ".$GlobalSiteCurrencyChar." TTC";
			?>
		</div>
		<div class="col-12 text-center pt-3">
			<input type="submit" value="Envoyer la proposition d'équivalence" class="TAKE-ACTION-SUBMIT-GALLERY" />
			<input type="hidden" name="Ask4Equiv" value="1">
			<input type="hidden" name="orl_id" value="<?php echo $orl_id; ?>">
			<input type="hidden" name="piece_qty_proposed" value="<?php echo $result_piece_old['ORL_ART_QUANTITY']; ?>">
			<input type="hidden" name="piece_id_proposed" value="<?php echo $piece_id_proposed; ?>">
			<input type="hidden" name="amount_proposed" value="<?php echo $amountequiv; ?>">
		</div>
	</div>
	<?php
	}
	?>
	</form>



</body>
</html>
<?php
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
}
else
{
	//header("Location: ".$destinationLink);
	include("../get.access.response.php");
}
?>