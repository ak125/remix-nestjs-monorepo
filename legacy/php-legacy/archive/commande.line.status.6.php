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
$orls_id = 6; // PD
$orls_id_PD = 5;
$pageH1 = "Commande n° ".$ord_id."/A";
$dept_id = 1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
//  ACTION QUERY
$StatusUpdateResponseCode=0;
if(isset($_POST["update"]))
{
	$orl_id = $_POST['orl_id'];
	$spl_id = $_POST['spl_id'];
	$pa_u_ht = $_POST['pa_u_ht'];
	if(($orl_id>0)&&($spl_id>0)&&($pa_u_ht>0))
	{
		//mysqli_set_charset($conn,"utf8");
		$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_ORLS_ID = $orls_id_PD   
			WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
		if($conn->query($StatusUpdateQuery) === TRUE)
		{
				$qty_this = $_POST['qty_this'];
				$pa_u_ttc = $pa_u_ht * $GlobalSiteTvaCoeff;
				$pa_t_ht = $pa_u_ht * $qty_this;
				$pa_t_ttc = $pa_u_ttc * $qty_this;
					$query_supplier = "SELECT DISTINCT SPL_ID, SPL_NAME
						FROM ___XTR_SUPPLIER
					    WHERE SPL_ID = $spl_id ";
					$request_supplier = $conn->query($query_supplier);
					$result_supplier = $request_supplier->fetch_assoc();
					$spl_name = mysqli_real_escape_string($conn, $result_supplier['SPL_NAME']);
					$spl_date = date("Y-m-j H:i:s");
				// COMMANDER CHER FOURNISSEUR
				$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_SPL_ID = $spl_id, 
					ORL_SPL_NAME = '$spl_name', ORL_SPL_DATE = '$spl_date', 
					ORL_SPL_PRICE_BUY_UNIT_HT = '$pa_u_ht', ORL_SPL_PRICE_BUY_UNIT_TTC = '$pa_u_ttc', 
					ORL_SPL_PRICE_BUY_HT = '$pa_t_ht', ORL_SPL_PRICE_BUY_TTC = '$pa_t_ttc'
					WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
				if($conn->query($StatusUpdateQuery) === TRUE)
				{
						// COMMANDER CHER FOURNISSEUR
						$StatusUpdateQuery = "UPDATE ___XTR_ORDER_LINE SET ORL_ORLS_ID = $orls_id    
							WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id";
						if($conn->query($StatusUpdateQuery) === TRUE)
						{
							$StatusUpdateResponse = "Pièce commandée chez le fournisseur avec Succès.";
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
	else
	{
		$StatusUpdateResponse = "Une erreur est survenue : Vérifiez données.";
		$StatusUpdateResponseCode=2;
	}
}
// DATA
$query_data = "SELECT DISTINCT ORL_ID, ORL_PG_ID, ORL_PG_NAME, ORL_PM_ID, ORL_PM_NAME, ORL_ART_REF, ORL_ART_REF_CLEAN, 
	ORL_ART_QUANTITY, ORL_ART_PRICE_BUY_UNIT_HT, ORL_ART_PRICE_BUY_UNIT_TTC, ORL_ART_PRICE_BUY_HT, ORL_ART_PRICE_BUY_TTC, 
	ORL_SPL_ID, ORL_SPL_NAME, ORL_SPL_PRICE_BUY_UNIT_HT
	FROM ___XTR_ORDER_LINE 
    JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
    WHERE ORL_ORD_ID = $ord_id AND ORL_ID = $orl_id 
    ORDER BY ORL_ID";
$request_data = $conn->query($query_data);
$result_data = $request_data->fetch_assoc();
$pm_id_this = $result_data['ORL_PM_ID'];
$pg_id_this = $result_data['ORL_PG_ID'];
$ref_propre_this = $result_data['ORL_ART_REF_CLEAN'];
$qty_this = $result_data['ORL_ART_QUANTITY'];

if($result_data['ORL_SPL_ID']>0)
{
	$pa_u_ht = $result_data['ORL_SPL_PRICE_BUY_UNIT_HT'];
	// Fournisseur Default
	$FournisseurInitial = $result_data['ORL_SPL_NAME'];
}
else
{
	$pa_u_ht = $result_data['ORL_ART_PRICE_BUY_UNIT_HT'];
	// Fournisseur Default
	$query_fournisseur = "SELECT DISTINCT PIECE_ID , PRI_FRS FROM PIECES
	JOIN PIECES_PRICE on PRI_PIECE_ID = PIECE_ID
	WHERE PIECE_REF_CLEAN = '$ref_propre_this' AND PIECE_PG_ID = '$pg_id_this' AND PIECE_PM_ID = '$pm_id_this'
	ORDER BY PRI_TYPE DESC";
	$request_fournisseur = $conn->query($query_fournisseur);
	if ($request_fournisseur->num_rows > 0)
		{ $result_fournisseur = $request_fournisseur->fetch_assoc(); $FournisseurInitial = $result_fournisseur['PRI_FRS']; }
	else
		{ $FournisseurInitial = "-"; }
}

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
	//if($StatusUpdateResponseCode==0)
	//{
	?>
	<div class="row">
		<div class="col-lg-12 text-center pb-3">
			Choix du fournisseur pour la pièce :<br>
			<b><u><?php echo $result_data['ORL_PG_NAME']; ?> <?php echo $result_data['ORL_PM_NAME']; ?> réf <?php echo $result_data['ORL_ART_REF']; ?></u></b>
			<br>
			Merci de sélectionnez votre Fournisseur.
		</div>
		<div class="col-6 pt-3">
			<?php
			if($result_data['ORL_SPL_ID']>0)
			{
			?>
			Fournisseur &nbsp; &nbsp; ( Vous avez déjà choisie : <b><?php echo $FournisseurInitial; ?></b> )
			<?php
			}
			else
			{
			?>
			Fournisseur &nbsp; &nbsp; ( Tarification d'achat sur le site de : <b><?php echo $FournisseurInitial; ?></b> )
			<?php
			}
			?>
			<select name="spl_id" required class="TAKE-ACTION-INPUT">
				<option>-- Select --</option>
				<?php
				$query_supplier = "SELECT DISTINCT SPL_ID, SPL_NAME
					FROM ___XTR_SUPPLIER_LINK_PM
				    JOIN ___XTR_SUPPLIER ON SLPM_SPL_ID = SPL_ID
				    WHERE SPL_DISPLAY = 1 AND SLPM_PM_ID = $pm_id_this
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
		<div class="col-2 pt-3">
			Quantité
			<input name="qty_print" value="<?php echo $qty_this; ?>" type="text" disabled="disabled" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-4 pt-3">
			PA U HT
			<input name="pa_u_ht" value="<?php echo $pa_u_ht; ?>" type="text" class="TAKE-ACTION-INPUT" />
		</div>
		<div class="col-lg-12 text-center pt-3">
			<input type="submit" value="<?php echo $pageAction; ?>" class="TAKE-ACTION-SUBMIT-GALLERY w-100" />
			<input type="hidden" name="update" value="1">
			<input type="hidden" name="orl_id" value="<?php echo $orl_id; ?>">
			<input type="hidden" name="qty_this" value="<?php echo $qty_this; ?>">
		</div>
	</div>
	<?php
	//}
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