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
$pageH1 = "Désactiver une pièce du site web";
$dept_id=1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  ACTION QUERY
$StatusUpdateResponseCode=0;
if(isset($_POST["getpiece"]))
{
	$piece_id_disable = $_POST['piece_id_disable'];
	//mysqli_set_charset($conn,"utf8");
	$query_piece = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_NAME, PM_NAME
		FROM PIECES 
		JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
		JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
		WHERE PIECE_ID = $piece_id_disable AND PIECE_DISPLAY = 1 
		ORDER BY PIECE_SORT";
	$request_piece = $conn->query($query_piece);
	if ($request_piece->num_rows > 0) 
	{
		$result_piece = $request_piece->fetch_assoc();
		// Gamme
		$piece_pg_name = $result_piece['PIECE_NAME'];
		// Equipementier
		$piece_pm_name = $result_piece['PM_NAME'];
		// Reference
		$piece_art_ref = $result_piece['PIECE_REF'];

		$StatusUpdateResponseCode=1;
	}
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : Pièce ID introuvable / Déjà désactivée";
		$StatusUpdateResponseCode=2;
	}
}
if(isset($_POST["disable"]))
{
	$StatusUpdateResponseCode=1;
	$piece_id_disable = $_POST['piece_id_disable'];
	//mysqli_set_charset($conn,"utf8");
	$StatusUpdateQuery = "UPDATE PIECES SET PIECE_DISPLAY = 3 WHERE PIECE_ID = $piece_id_disable";
	if($conn->query($StatusUpdateQuery) === TRUE)
	{
		$StatusUpdateResponse = "La désactivation de la pièce a été effectuée avec Succès.";
		$StatusUpdateResponseCode=10;
	}
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : ".$conn->error;
		$StatusUpdateResponseCode=20;
	}
}
$pageAction = "Désactivation";
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

			<u><?php echo $pageH1; ?></u>
			<h2 class="H2-MODAL"><u><?php echo $pageAction; ?></u></h2>

		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 text-center pt-3">
			<?php
				if(($StatusUpdateResponseCode==2)||($StatusUpdateResponseCode==20))
				{
					?>
					<div class="response-red"><?php echo $StatusUpdateResponse ;?></div>
					<?php
				}
				if($StatusUpdateResponseCode==10)
				{
					?>
					<div class="response-green"><?php echo $StatusUpdateResponse ;?></div>
					<?php
				}
			?>
		</div>
	</div>
	<?php
	if(($StatusUpdateResponseCode==0)||($StatusUpdateResponseCode==2)||($StatusUpdateResponseCode==20))
	{
	?>
	<div class="row">
		<div class="col-lg-12 text-center pb-3">
			Merci d'entrer la pièce_id de la pièce à désactiver
		</div>
		<div class="col-6 pt-3">
			<input name="piece_id_disable" type="number" min="1" placeholder="Piece ID (Page Search)" required="required" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-6 pt-3">
			<input type="submit" value="Sélectionnez la pièce" class="TAKE-ACTION-SUBMIT-GALLERY" />
			<input type="hidden" name="getpiece" value="1">
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
			Etes vous sûr de vouloir valider la <b><?php echo $pageAction; ?></b> de cette pièce :<br>
			<b><u><?php echo $piece_pg_name; ?> <?php echo $piece_pm_name; ?> réf <?php echo $piece_art_ref; ?></u></b>
			<br>
			Cette opération entrainera la désactivation de la pièce du site web, Merci de confirmer.
		</div>
		<div class="col-lg-12 text-center pt-3">
			<input type="submit" value="Confirmer la désactivation de cette pièce" class="TAKE-ACTION-SUBMIT-DELETE-LINK" style="float: none;" />
			<input type="hidden" name="disable" value="1">
			<input type="hidden" name="piece_id_disable" value="<?php echo $piece_id_disable; ?>">
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