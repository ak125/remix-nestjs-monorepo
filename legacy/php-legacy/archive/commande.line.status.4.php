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
$orls_id = 4; // PND
$pageH1 = "Commande n° ".$ord_id."/A";
$dept_id=1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  ACTION QUERY
$linkform = "";
$StatusUpdateResponseCode=0;
if(isset($_POST["update"]))
{
	$orl_id = $_POST['orl_id'];
	//mysqli_set_charset($conn,"utf8");
	$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_ORLS_ID = $orls_id 
		WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
	if($conn->query($StatusUpdateQuery) === TRUE)
	{
		$StatusUpdateResponse = "Traitement et Statut de Pièce mis à jours avec Succès.";
		$StatusUpdateResponseCode=1;
		$linkform = $domain."/".$aliasDepartment."/".$aliasSecondDepartment."/orderline/".$orl_id."/91";
	}
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
		$StatusUpdateResponseCode=2;
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


	<form method="post" action="<?php echo $linkform; ?>" class="TAKE-ACTION">
	<div class="row">
		<div class="col-12 text-center pb-3">

			<u><?php echo $pageH1; ?> : <?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u>
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
			Etes vous sûr de vouloir valider la <b><?php echo $pageAction; ?></b> de cette pièce :<br>
			<b><u><?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u></b>
			<br>
			Cette opération entrainera la proposition d'une équivalence, Merci de confirmer.
		</div>
		<div class="col-lg-12 text-center pt-3">
			<input type="submit" value="Confirmer la ''<?php echo $pageAction; ?>'' et continuer vers la proposition d'équivalence" class="TAKE-ACTION-SUBMIT-DELETE-LINK" style="float: none;" />
			<input type="hidden" name="update" value="1">
			<input type="hidden" name="orl_id" value="<?php echo $orl_id; ?>">
		</div>
	</div>
	<?php
	}
	?>
	<?php
	if($StatusUpdateResponseCode==1) // On va proposer une équivalence
	{
	?>
	<div class="row">
		<div class="col-lg-12 text-center pb-3">
			Proposition d'équivalence pour la pièce :<br>
			<b><u><?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u></b>
			<br>
			Merci d'entrer la pièce_id de l'équivalence.
		</div>
		<div class="col-6 pt-3">
			<input name="piece_id_proposed" type="text" placeholder="Piece ID (Page Search)" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-6 pt-3">
			<input type="submit" value="Proposer l'équivalence" class="TAKE-ACTION-SUBMIT-GALLERY" />
			<input type="hidden" name="orl_id" value="<?php echo $orl_id; ?>">
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