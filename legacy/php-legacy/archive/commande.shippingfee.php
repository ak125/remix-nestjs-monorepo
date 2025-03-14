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
$ords_id = 3; // AJOUT FRAIS DE PORT SUR COMMANDE
$pageH1 = "Commande n° ".$ord_id."/A";
$dept_id = 1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
// ACTION
$query_action = "SELECT * FROM ___XTR_ORDER_STATUS 
    WHERE ORDS_ID = $ords_id";
$request_action = $conn->query($query_action);
$result_action = $request_action->fetch_assoc();
$pageAction = $result_action["ORDS_ACTION"];
//  ACTION QUERY
$StatusUpdateResponseCode=0;
if(isset($_POST["update"]))
{
	// DETAILS COMMANDE PARENT
	$query_data_ord = "SELECT * 
		FROM ___XTR_ORDER
	    WHERE ORD_ID = $ord_id ";
	$request_data_ord = $conn->query($query_data_ord);
	$result_data_ord = $request_data_ord->fetch_assoc();
	$idtogetinfoclt = $result_data_ord['ORD_CST_ID'];
	$connectedclt_cba_id = $result_data_ord['ORD_CBA_ID'];
	$connectedclt_cda_id = $result_data_ord['ORD_CDA_ID'];
	$datecommande = date("Y-m-j H:i:s");
	$stTTC = $_POST['shippingfee'];
	$consignetotale = 0;
	$livfrais = 0;
	$totalTTCtoPAY = $stTTC;
	$id_liv = $result_data_ord['ORD_DA_ID'];
	$infoscomplementaires = "Rajout Frais de port sur la commande ".$ord_id;
	// CREATION D'UNE COMMANDE
	$InsertQuery = "INSERT INTO `___XTR_ORDER` (`ORD_ID`, `ORD_CST_ID`, `ORD_CBA_ID`, `ORD_CDA_ID`, `ORD_DATE`, `ORD_AMOUNT_HT`, `ORD_DEPOSIT_HT`, `ORD_SHIPPING_FEE_HT`, `ORD_TOTAL_HT`, `ORD_TVA`, `ORD_AMOUNT_TTC`, `ORD_DEPOSIT_TTC`, `ORD_SHIPPING_FEE_TTC`, `ORD_TOTAL_TTC`, `ORD_DA_ID`, `ORD_INFO`, `ORD_PARENT`) VALUES (NULL, '$idtogetinfoclt', '$connectedclt_cba_id', '$connectedclt_cda_id', '$datecommande', '0', '0', '0', '0', '$GlobalSiteTva', '$stTTC', '$consignetotale', '$livfrais', '$totalTTCtoPAY', '$id_liv', '$infoscomplementaires', '$ord_id')";
	if($conn->query($InsertQuery) === TRUE)
	{
		// INSERTION DE LA LIGNE DE COMMANDE
		$commande_id_injected = $conn->insert_id;
		if($commande_id_injected>0)
		{
			$StatusUpdateResponse = "Rajout frais de port envoyé avec Succès.";
			$StatusUpdateResponseCode=1;

			$orl_ord_id = $commande_id_injected;
			$orl_art_price_sell_unit_ht = ($_POST['shippingfee']/(($GlobalSiteTva/100)+1));
			$orl_art_price_sell_unit_ttc = $_POST['shippingfee'];
			$orl_art_quantity = 1;
			$orl_art_price_sell_ht = $orl_art_price_sell_unit_ht;
			$orl_art_price_sell_ttc = $orl_art_price_sell_unit_ttc;
			$InsertQueryLine = "INSERT INTO `___XTR_ORDER_LINE` (`ORL_ID`, `ORL_ORD_ID`, `ORL_PG_NAME`, `ORL_ART_PRICE_SELL_UNIT_HT`, `ORL_ART_PRICE_SELL_UNIT_TTC`, `ORL_ART_QUANTITY`, `ORL_ART_PRICE_SELL_HT`, `ORL_ART_PRICE_SELL_TTC`) VALUES (NULL, '$orl_ord_id', '$infoscomplementaires', '$orl_art_price_sell_unit_ht', '$orl_art_price_sell_unit_ttc', '$orl_art_quantity', '$orl_art_price_sell_ht', '$orl_art_price_sell_ttc')";
			$conn->query($InsertQueryLine);

				// GENERATION DU LIEN DE PAIEMENT
				$CryptedComId = md5($commande_id_injected);
				$paymentLink= "<a href='".$domainparent."/supplement/".$CryptedComId."' target='_blank'>".$domainparent."/supplement/".$CryptedComId."</a>";
				// CHANGEMENT DU STATUT DE LA COMMANDE
				$StatusUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_ORDS_ID = $ords_id 
					WHERE ORD_ID = $ord_id";
				if($conn->query($StatusUpdateQuery) === TRUE)
				{
					// Sauvegarder le lien de paiement
					$StatusUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_LINK = '$CryptedComId', ORD_LINK_TYPE = 2 
					WHERE ORD_ID = $commande_id_injected";
					if($conn->query($StatusUpdateQuery) === TRUE)
					{
					}
					else
					{
						$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
						$StatusUpdateResponseCode=2;
					}
				}
				else
				{
					$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
					$StatusUpdateResponseCode=2;
				}
				// SAUVEGARDER LE MESSAGE 
				$msg_date = date("Y-m-j H:i:s");
						$msg_subject = "Support Commercial : ".$pageAction;
						$msg_subject = mysqli_real_escape_string($conn, $msg_subject);
						$msg_msg = "Lien de paiement : ".$paymentLink;
						$msg_msg = mysqli_real_escape_string($conn, $msg_msg);
				$SendMsgQuery = "INSERT INTO `___XTR_MSG` (`MSG_ID`, `MSG_CST_ID`, `MSG_ORD_ID`, `MSG_CNFA_ID`, `MSG_DATE`, `MSG_SUBJECT`, `MSG_CONTENT`, `MSG_OPEN`) VALUES (NULL, '$idtogetinfoclt', '$ord_id', '$ssid', '$msg_date', '$msg_subject', '$msg_msg', '0')";
						if($conn->query($SendMsgQuery) === TRUE)
						{
						}
						else
						{
							$StatusUpdateResponse = "Envoie de message, Une erreur est survenue : ".$conn->error;
							$StatusUpdateResponseCode=2;
						}

				// ENVOIE D'UN MAIL DE VALIDATION POUR LE CLIENT

			
		}
		else
		{
			$StatusUpdateResponse = "Une erreur est survenue : Numéro de commande retourné est incorrect.";
			$StatusUpdateResponseCode=2;
		}
	}
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
		$StatusUpdateResponseCode=2;
	}
}
// FIN ACTION QUERY
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

			<u><?php echo $pageH1; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageAction; ?></u></h2>

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
		<div class="col-lg-12 text-center pb-3">
			Etes vous sûr de vouloir procéder au <b><?php echo $pageAction; ?></b> de cette commande :<br>
			<b><u><?php echo $pageH1; ?></u></b>
			<br>
			Cette opération entrainera la génération d'un lien de paiement sur la commande et l'envoie d'un mail au client, Merci de confirmer.
		</div>
		<div class="col-lg-6 text-center pt-3">
			<input type="text" required="required" name="shippingfee" placeholder="Montant du rajout" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-6 text-center pt-3">
			<input type="submit" value="Valider le <?php echo $pageAction; ?>" class="TAKE-ACTION-SUBMIT-GALLERY" style="float: none;" />
			<input type="hidden" name="update" value="1">
			<input type="hidden" name="ord_id" value="<?php echo $ord_id; ?>">
		</div>
	</div>
	<?php
	}
	?>
	<?php
	if($StatusUpdateResponseCode==1)
	{
	?>
	<div class="row">
		<div class="col-lg-12 text-center pb-3">
			Voici ci-joint le lien de paiement qui a été envoyé au client : <br><br>
			<?php echo $paymentLink; ?>
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