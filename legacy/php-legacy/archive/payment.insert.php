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
$pageH1 = "Injecter un paiement manuel";
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
$ord_id = $_GET["ord_id"];
//  INSERT QUERY
$InsertResponseCode=0;
if(isset($_POST["insert"]))
{
	$this_id_com = $ord_id."-A";
	$this_status = "OK";
	$this_PaymentID=$_POST['this_PaymentID'];
	$this_Amount=$_POST['this_Amount'];
	$this_PaymentMethod=$_POST['this_PaymentMethod'];
	$this_DatePayment=$_POST['this_DatePayment'];

	if(($ord_id>0)&&($this_PaymentID>0)&&($this_Amount>0)&&($this_PaymentMethod!="")&&($this_DatePayment!=""))
	{
		//mysqli_set_charset($conn,"utf8");
		$InsertQuery = "INSERT INTO `ic_postback` (`ID_IC_POSTBACK`, `id_com`, `Status`, `StatusCode`, `idsite`, `idste`, `OrderID`, `PaymentID`, `TransactionID`, `Amount`, `Currency`, `PaymentMethod`, `Ip`, `Ips`, `DatePayment`) VALUES (NULL, '$this_id_com', '$this_status', '', '1', '1', '$this_id_com', '$this_PaymentID', '$ord_id', '$this_Amount', 'EUR', '$this_PaymentMethod', 'FR', 'FR', '$this_DatePayment')";
		if($conn->query($InsertQuery) === TRUE)
		{
			$OrderUpdateQuery = "UPDATE ___XTR_ORDER SET ORD_IS_PAY = 1, ORD_DATE_PAY = '$this_DatePayment', 
				ORD_DEPT_ID = 1    
				WHERE ORD_ID = $ord_id";
			if($conn->query($OrderUpdateQuery) === TRUE)
			{
				$InsertResponse = "Paiement manuel injecté avec succès.";
				$InsertResponseCode=1;
				// Si c'est un rajout on change le statut de la commande
				$query_supplement_data = "SELECT ORD_PARENT 
				    FROM ___XTR_ORDER 
				    WHERE ORD_ID = '$ord_id'";
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
				$InsertResponse = "Une erreur est survenue : ".$conn->error;
				$InsertResponseCode=2;
			}
		}
		else
		{
			$InsertResponse = "Une erreur est survenue : ".$conn->error;
			$InsertResponseCode=2;
		}
	}
	else
	{
		$InsertResponse = "Une erreur est survenue.";
		$InsertResponseCode=2;
	}
	
}
// DATA
$query_data = "SELECT *
FROM ___XTR_ORDER
WHERE ORD_ID = $ord_id AND ORD_IS_PAY = 0";
$request_data = $conn->query($query_data);
$result_data = $request_data->fetch_assoc();
// FIN INSERT QUERY
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
			<u><?php echo "Commande n° ".$ord_id."/A";; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageH1; ?></u></h2>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center pt-3">
			<?php
				if($InsertResponseCode>0)
				{
					if($InsertResponseCode==1)
					{
						?>
						<div class="response-green"><?php echo $InsertResponse ;?></div>
						<?php
					}
					else
					{
						?>
						<div class="response-red"><?php echo $InsertResponse ;?></div>
						<?php
					}
				}
			?>
		</div>
	</div>
	<?php
	if($InsertResponseCode==0)
	{
	?>
	<div class="row">
		<div class="col-lg-12 text-center pb-3">
			Merci de remplir les champs suivants avec les données du module de paiement en ligne.
		</div>
		<div class="col-6 pt-3">
			Payment ID &nbsp; &nbsp; ( Numéro d'autorisation )
			<input name="this_PaymentID" value="" type="number" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-6 pt-3">
			Payment METHOD &nbsp; &nbsp; ( Moyen de paiement : <b>E-CARTEBLEUE</b> / <b>VISA</b> / <b>MASTER CARD</b> / <b>CB</b> )
			<input name="this_PaymentMethod" value="" type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-6 pt-1">
			Montant de la commande &nbsp; &nbsp; ( En centime d'euro, Sans virgule )
			<input name="this_Amount" value="" type="number" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-6 pt-1">
			Date du paiement &nbsp; &nbsp; ( Date d'autorisation : aaaa-mm-jj hh:mm:ss )
			<input name="this_DatePayment" value="" type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-12 text-center pt-3">
			<input type="submit" value="Valider le paiement manuel" class="TAKE-ACTION-SUBMIT-GALLERY w-100" />
			<input type="hidden" name="insert" value="1">
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