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
	$query_log = "SELECT * FROM ___CONFIG_ADMIN WHERE CNFA_LOGIN='$log' AND CNFA_KEYLOG='$mykey' AND CNFA_LEVEL = 9";
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
$pageH1 = "Gestion Staff";
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  SUSPENSION QUERY
$SuspensionResponseCode=0;
if(isset($_POST["suspension"]))
{
	$cnfa_id = $_POST['cnfa_id'];
	//mysqli_set_charset($conn,"utf8");
	$SuspensionQuery = "UPDATE ___CONFIG_ADMIN SET CNFA_ACTIV = 1 WHERE CNFA_ID = $cnfa_id";
	if($conn->query($SuspensionQuery) === TRUE)
	{
		$SuspensionResponse = "Agent enabled successfully.";
		$SuspensionResponseCode=1;
	}
	else
	{
		$SuspensionResponse = "An error occured : ".$conn->error;
		$SuspensionResponseCode=2;
	}
	$cnfa_name ="";
}
else
{
	$cnfa_id = $_GET["cnfa_id"];
	// DATA
	$query_data = "SELECT CNFA_NAME, CNFA_FNAME FROM ___CONFIG_ADMIN
	WHERE CNFA_ID = $cnfa_id";
	$request_data = $conn->query($query_data);
	$result_data = $request_data->fetch_assoc();
	$cnfa_name = $result_data['CNFA_NAME']." ".$result_data['CNFA_FNAME'];
}
// FIN SUSPENSION QUERY
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
			<u><?php echo $pageH1; ?>, Activer un vendeur</u>
			<h2 class="H2-MODAL"><u><?php echo $cnfa_name; ?></u></h2>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center pt-3">
			<?php
				if($SuspensionResponseCode>0)
				{
					if($SuspensionResponseCode==1)
					{
						?>
						<div class="response-green"><?php echo $SuspensionResponse ;?></div>
						<?php
					}
					else
					{
						?>
						<div class="response-red"><?php echo $SuspensionResponse ;?></div>
						<?php
					}
				}
			?>
		</div>
	</div>
	<?php
	if($SuspensionResponseCode==0)
	{
	?>
	<div class="row">
		<div class="col-lg-12 text-center">
			Etes vous sûr de vouloir activer ce compte : <b><?php echo $cnfa_name; ?></b>.<br>
			Merci de confirmer.
		</div>
		<div class="col-lg-12 text-center">
			<input type="submit" value="ACTIVER" class="TAKE-ACTION-SUBMIT-UPDATE-LINK" style="float: none;" />
			<input type="hidden" name="suspension" value="1">
			<input type="hidden" name="cnfa_id" value="<?php echo $cnfa_id; ?>">
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