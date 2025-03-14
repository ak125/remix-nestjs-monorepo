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
// DATAS
$cnfa_id = $_GET['cnfa_id'];
//  INSERT QUERY
$UpdateResponseCode=0;
if(isset($_POST["update"]))
{
	$cnfa_name = mysqli_real_escape_string($conn, $_POST['cnfa_name']);
	$cnfa_fname = mysqli_real_escape_string($conn, $_POST['cnfa_fname']);
	$cnfa_mail = mysqli_real_escape_string($conn, $_POST['cnfa_mail']);
		if($cnfa_mail=="") $keylog = md5("ways2com@gmail.com"); else $keylog = md5($cnfa_mail);
	$cnfa_tel = mysqli_real_escape_string($conn, $_POST['cnfa_tel']);
	$cnfa_level = $_POST['cnfa_level'];

	if(($cnfa_name!="")&&($cnfa_fname!="")&&($cnfa_level!=""))
	{
		//mysqli_set_charset($conn,"utf8");
		$UpdateQuery = "UPDATE `___CONFIG_ADMIN` SET `CNFA_NAME` = '$cnfa_name', `CNFA_FNAME` = '$cnfa_fname', 
		`CNFA_MAIL` = '$cnfa_mail', `CNFA_KEYLOG` = '$keylog', `CNFA_TEL` = '$cnfa_tel', 
		`CNFA_LEVEL` = '$cnfa_level' WHERE CNFA_ID = $cnfa_id";

		if($conn->query($UpdateQuery) === TRUE)
		{
			$UpdateResponse = "Record updated successfully.";
			$UpdateResponseCode=1;

			$mp=$_POST['cnfa_pass'];
			if(($mp!=NULL)&&($mp!=""))
			{
				$mpcrypt = crypt(md5($mp),$pswdctyptogram);
				$UpdateQueryPass = "UPDATE `___CONFIG_ADMIN` SET `CNFA_PSWD` = '$mpcrypt' WHERE CNFA_ID = $cnfa_id";
				$conn->query($UpdateQueryPass);
			}
		}
		else
		{
			$UpdateResponse = "An error occured : ".$conn->error;
			$UpdateResponseCode=2;
		}
	}
	else
	{
		$UpdateResponse = "An error occured : Verify datas.";
		$UpdateResponseCode=2;
	}
	
}
// FIN INSERT QUERY
$query_data = "SELECT * FROM ___CONFIG_ADMIN WHERE CNFA_ID = $cnfa_id";
$request_data = $conn->query($query_data);
$result_data = $request_data->fetch_assoc();
$cnfa_name = $result_data['CNFA_NAME'];
$cnfa_fname = $result_data['CNFA_FNAME'];
$cnfa_mail = $result_data['CNFA_MAIL'];
$cnfa_tel = $result_data['CNFA_TEL'];
$cnfa_level = $result_data['CNFA_LEVEL'];
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
				if($UpdateResponseCode>0)
				{
					if($UpdateResponseCode==1)
					{
						?>
						<div class="response-green"><?php echo $UpdateResponse ;?></div>
						<?php
					}
					else
					{
						?>
						<div class="response-red"><?php echo $UpdateResponse ;?></div>
						<?php
					}
				}
			?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-4">
			Nom
			<input name="cnfa_name" value="<?php echo $cnfa_name; ?>" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Prénom
			<input name="cnfa_fname" value="<?php echo $cnfa_fname; ?>" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Email
			<input name="cnfa_mail" value="<?php echo $cnfa_mail; ?>" type="text" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Tel
			<input name="cnfa_tel" value="<?php echo $cnfa_tel; ?>" type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Password
			<input name="cnfa_pass" value="" type="password" required class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-4">
			Département
			<select name="cnfa_level" required class="TAKE-ACTION-INPUT">
				<option>-- Select --</option>
				<option value="7" <?php if($cnfa_level==7) echo 'selected="selected"'; ?>>Commercial</option>
				<option value="8" <?php if($cnfa_level==8) echo 'selected="selected"'; ?>>Expédition</option>
			</select>
		</div>
		<div class="col-lg-12 text-right">
			<input type="submit" value="ajouter" class="TAKE-ACTION-SUBMIT" />
			<input type="hidden" name="update" value="1">
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