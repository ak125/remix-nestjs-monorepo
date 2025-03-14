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
//  INSERT QUERY
$InsertResponseCode=0;
if(isset($_POST["insert"]))
{
	$cnfa_name = mysqli_real_escape_string($conn, $_POST['cnfa_name']);
	$cnfa_fname = mysqli_real_escape_string($conn, $_POST['cnfa_fname']);
	$cnfa_mail = mysqli_real_escape_string($conn, $_POST['cnfa_mail']);
		if($cnfa_mail=="") $keylog = md5("ways2com@gmail.com"); else $keylog = md5($cnfa_mail);
	$cnfa_tel = mysqli_real_escape_string($conn, $_POST['cnfa_tel']);
	$cnfa_login = mysqli_real_escape_string($conn, $_POST['cnfa_login']);
	$cnfa_pswd = mysqli_real_escape_string($conn, $_POST['cnfa_pswd']);
		$mpcrypt = crypt(md5($cnfa_pswd),$pswdctyptogram);
	// Vendor level 1
	$cnfa_level = $_POST['cnfa_level'];

	if(($cnfa_name!="")&&($cnfa_fname!="")&&($cnfa_login!="")&&($cnfa_pswd!="")&&($cnfa_level!=""))
	{
		//mysqli_set_charset($conn,"utf8");
		$InsertQuery = "INSERT INTO `___CONFIG_ADMIN` (`CNFA_ID`, `CNFA_LOGIN`, `CNFA_PSWD`, `CNFA_MAIL`, `CNFA_KEYLOG`, `CNFA_LEVEL`, `CNFA_NAME`, `CNFA_FNAME`, `CNFA_TEL`, `CNFA_ACTIV`) 
			VALUES ('', '$cnfa_login', '$mpcrypt', '$cnfa_mail', '$keylog', '$cnfa_level', '$cnfa_name', '$cnfa_fname', '$cnfa_tel', '1')";

		if($conn->query($InsertQuery) === TRUE)
		{
			$InsertResponse = "Record saved successfully.";
			$InsertResponseCode=1;
		}
		else
		{
			$InsertResponse = "An error occured : ".$conn->error;
			$InsertResponseCode=2;
		}
	}
	else
	{
		$InsertResponse = "An error occured : Verify datas.";
		$InsertResponseCode=2;
	}
	
}
// FIN INSERT QUERY
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
	<!--div class="row">
		<div class="col-12 text-center pb-3">
			<h2 class="H2-MODAL"><u>< ?php echo $pageH1; ?>, Ajouter admin</u></h2>
		</div>
	</div-->
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
	
	<div class="row">
		<div class="col-lg-4">
			Nom
			<input name="cnfa_name" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Prénom
			<input name="cnfa_fname" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Email
			<input name="cnfa_mail" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-6">
			Tel
			<input name="cnfa_tel" type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-6">
			Département
			<select name="cnfa_level" required class="TAKE-ACTION-INPUT">
				<option>-- Select --</option>
				<option value="7">Commercial</option>
				<option value="8">Expédition</option>
			</select>
		</div>
		<div class="col-lg-6">
			Login
			<input name="cnfa_login" required type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-6">
			Password
			<input name="cnfa_pswd" required type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-12 text-right">
			<input type="submit" value="ajouter" class="TAKE-ACTION-SUBMIT" />
			<input type="hidden" name="insert" value="1">
		</div>
	</div>

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