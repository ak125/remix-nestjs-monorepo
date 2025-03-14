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
$orl_id = $_GET["orl_id"];
$orls_id = 94; // VALIDATION EQUIVALENCE 
$orls_id_PD = 5;
$orls_id_X = 2;
$pageH1 = "Commande n° ".$ord_id."/A";
$dept_id=1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  ACTION QUERY
$StatusUpdateResponseCode=0;
if(isset($_POST["update"]))
{
	$orl_id = $_POST['orl_id'];
	$orl_id_go_back = $_POST['orl_id_go_back'];
	//mysqli_set_charset($conn,"utf8");
	$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_ORLS_ID = $orls_id_PD  
		WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
	if($conn->query($StatusUpdateQuery) === TRUE)
	{
		$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_ORLS_ID = $orls_id_X  
			WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id_go_back";
		if($conn->query($StatusUpdateQuery) === TRUE)
		{
			// GENERATION D'UN TICKET DE CHANGEMENT
			$query_data_equiv = "SELECT ORL_ART_PRICE_SELL_TTC, ORL_ART_DEPOSIT_TTC 
				FROM ___XTR_ORDER_LINE 
			    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
			$request_data_equiv = $conn->query($query_data_equiv);
			if ($request_data_equiv->num_rows > 0)
			{ 
				$result_data_equiv = $request_data_equiv->fetch_assoc(); 
				$totalttc_new = $result_data_equiv['ORL_ART_PRICE_SELL_TTC']+$result_data_equiv['ORL_ART_DEPOSIT_TTC'];
			}
			$query_data = "SELECT ORL_ART_PRICE_SELL_TTC, ORL_ART_DEPOSIT_TTC 
				FROM ___XTR_ORDER_LINE 
			    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id_go_back";
			$request_data = $conn->query($query_data);
			if ($request_data->num_rows > 0)
			{ 
				$result_data = $request_data->fetch_assoc(); 
				$totalttc_old = $result_data['ORL_ART_PRICE_SELL_TTC']+$result_data['ORL_ART_DEPOSIT_TTC'];
			}
			$amount_ticket = $totalttc_new-$totalttc_old;
			$InsertQueryLine = "INSERT INTO `___XTR_ORDER_LINE_EQUIV_TICKET` (`ORLET_ID`, `ORLET_ORD_ID`, `ORLET_ORL_ID`, `ORLET_EQUIV_ID`, `ORLET_AMOUNT_TTC`) VALUES (NULL, '$ord_id', '$orl_id_go_back', '$orl_id', '$amount_ticket')";
			if($conn->query($InsertQueryLine)===TRUE)
			{
				//$StatusUpdateResponse = "Proposition d'équivalence acceptée et validée avec Succès.";
				$StatusUpdateResponse = "Ticket de paiement/remboursement généré avec Succès.";
				$StatusUpdateResponseCode=1;
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
	}
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
		$StatusUpdateResponseCode=2;
	}
}
// DATA
$query_data_equiv = "SELECT DISTINCT ORL_ID, ORL_PG_NAME, ORL_PM_NAME, ORL_ART_REF 
	FROM ___XTR_ORDER_LINE 
    JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id 
    ORDER BY ORL_ID";
$request_data_equiv = $conn->query($query_data_equiv);
if ($request_data_equiv->num_rows > 0)
	{ $result_data_equiv = $request_data_equiv->fetch_assoc(); }
//
$query_data = "SELECT DISTINCT ORL_ID, ORL_PG_NAME, ORL_PM_NAME, ORL_ART_REF 
	FROM ___XTR_ORDER_LINE 
    JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
    WHERE ORL_ORD_ID = $ord_id AND ORL_EQUIV_ID = $orl_id 
    ORDER BY ORL_ID";
$request_data = $conn->query($query_data);
if ($request_data->num_rows > 0)
	{ $result_data = $request_data->fetch_assoc(); }
// ACTION
$query_action = "SELECT * FROM ___XTR_ORDER_LINE_STATUS 
    WHERE ORLS_ID = $orls_id";
$request_action = $conn->query($query_action);
$result_action = $request_action->fetch_assoc();
$pageAction = $result_action["ORLS_ACTION"];
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

			<?php
			if($StatusUpdateResponseCode==1)
			{
			?>
			<u><?php echo $pageH1; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageAction; ?></u></h2>
			<?php
			}
			else
			{
			?>
			<u><?php echo $pageH1; ?> : <?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageAction; ?></u></h2>
			<?php
			}
			?>

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
			Etes vous sûr de vouloir procéder à la <b><?php echo $pageAction; ?></b> de cette pièce :<br>
			<b><u><?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u></b>
			<br>
			par
			<br>
			<b><u><?php echo $result_data_equiv['ORL_PG_NAME']; ?> <?php echo $result_data_equiv['ORL_PM_NAME']; ?> réf <?php echo $result_data_equiv['ORL_ART_REF']; ?></u></b>
			<br>
			Cette opération est définitive et irréversible et entrainera la génération d'un ticket de remboursement ou un lien de paiement de supplément, Merci de confirmer.
		</div>
		<div class="col-lg-12 text-center pt-3">
			<input type="submit" value="Valider la <?php echo $pageAction; ?>" class="TAKE-ACTION-SUBMIT-UPDATE-LINK" style="float: none;" />
			<input type="hidden" name="update" value="1">
			<input type="hidden" name="orl_id" value="<?php echo $orl_id; ?>">
			<input type="hidden" name="orl_id_go_back" value="<?php echo $result_data['ORL_ID']; ?>">
			<input type="hidden" name="update" value="1">
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