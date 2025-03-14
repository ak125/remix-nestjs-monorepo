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
$pageH1 = "Gestion des fournisseurs";
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
$pm_id = $_GET["pm_id"];
//  INSERT QUERY
$InsertResponseCode=0;
if(isset($_POST["insert"]))
{
	$spl_id = $_POST['spl_id'];

	if(($spl_id>0)&&($pm_id>0))
	{
		//mysqli_set_charset($conn,"utf8");
		$InsertQuery = "INSERT INTO `___XTR_SUPPLIER_LINK_PM` (`SLPM_ID`, `SLPM_PM_ID`, `SLPM_SPL_ID`, `SLPM_DISPLAY`) 
			VALUES (NULL, '$pm_id', '$spl_id', '1')";

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
// DATA
$query_data = "SELECT PM_ID, PM_NAME
FROM PIECES_MARQUE
WHERE PM_ID = $pm_id";
$request_data = $conn->query($query_data);
$result_data = $request_data->fetch_assoc();
$pm_name = $result_data['PM_NAME'];
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
			<h2 class="H2-MODAL"><u><?php echo $pm_name; ?></u></h2>
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
	
	<div class="row">
		<div class="col-6">
			Fournisseur
			<select name="spl_id" required class="TAKE-ACTION-INPUT">
				<option>-- Select --</option>
				<?php
				$query_supplier = "SELECT SPL_ID, SPL_NAME
				    FROM ___XTR_SUPPLIER
				    WHERE SPL_DISPLAY = 1 
				    ORDER BY SPL_NAME";
				$request_supplier = $conn->query($query_supplier);
				if ($request_supplier->num_rows > 0) 
				{
				while($result_supplier = $request_supplier->fetch_assoc()) 
				{
				    $spl_id_this = $result_supplier["SPL_ID"];
				    ?>
				<option value="<?php echo $spl_id_this; ?>"><?php echo $result_supplier["SPL_NAME"]; ?></option>
				<?php
				}
				}
				?>
			</select>
		</div>
		<div class="col-6 text-right">
			<br><input type="submit" value="ajouter" class="TAKE-ACTION-SUBMIT-GALLERY" />
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