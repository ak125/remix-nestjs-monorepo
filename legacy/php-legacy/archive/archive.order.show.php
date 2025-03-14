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
            $sslevel = $result_log['CNFA_LEVEL'];
			$accessRequest = true;
		    $destinationLinkMsg = "Granted";
		    $destinationLinkGranted = 1;
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
$aliasSecondDepartment = getsecondDepartmentAlias();

if ($rsverifPrivilege=1)
{
$ord_id=$_GET['ord_id'];
$pageH1 = "Commande n° ".$ord_id."/A";
$pageH2 = "Liste des commandes en cours...";
$dept_id=1;
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// PAGE SESSION GRANTED /////////////////////////////////////////////////////////
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="<?php echo $domainparent; ?>/system/bootstrap/js/bootstrap.min.js"></script>
<!-- Data Table -->
<script src="<?php echo $domainparent; ?>/system/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $domainparent; ?>/system/datatables/dataTables.bootstrap4.min.js"></script>
<link href="<?php echo $domainparent; ?>/system/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/intern.css" rel="stylesheet">
</head>
<body>
<div id="mainPageContentCover">&nbsp;</div>
<?Php
// ENTETE DE PAGE
@require_once('../header.first.section.php');
// LEFT PANEL
@require_once('../header.left.section.php');
?>

<div id="mainPageContent">
<div class="container-fluid Page-Welcome-Title">

	<div class="row">
		<div class="col-10 PANEL-LEFT align-self-center pt-3">
			<br><h1><?php echo $pageH1; ?></h1>
            <h2><?php echo $pageH2; ?></h2>
		</div>
		<div class="col-2 PANEL-LEFT align-self-center">
			&nbsp;
		</div>
	</div>

</div>
<div class="container-fluid Page-Welcome-Box">

<!-- CONTENU DE LA PAGE -->
<div class="row">
<div class="col-12 PANEL-LEFT">

<!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES -->
<!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES -->
<!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES -->
<!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES -->
<!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES --><!-- LISTE COMMANDES -->
<?php
$query_commande_list = "SELECT ORD_ID, ORD_DATE, ORD_DATE_PAY, ORD_INFO,  
    ORD_AMOUNT_TTC, ORD_DEPOSIT_TTC, ORD_SHIPPING_FEE_TTC, ORD_TOTAL_TTC, 
    ORD_CST_ID, CST_CIVILITY, CST_NAME, CST_FNAME, CST_ADDRESS, CST_ZIP_CODE, CST_CITY, CST_COUNTRY, 
    CST_TEL, CST_GSM, CST_MAIL, 
    ORD_CBA_ID, CBA_CIVILITY, CBA_NAME, CBA_FNAME, CBA_ADDRESS, CBA_ZIP_CODE, CBA_CITY, CBA_COUNTRY, 
    ORD_CDA_ID, CDA_CIVILITY, CDA_NAME, CDA_FNAME, CDA_ADDRESS, CDA_ZIP_CODE, CDA_CITY, CDA_COUNTRY
    FROM ___XTR_ORDER 
    JOIN ___XTR_CUSTOMER ON CST_ID = ORD_CST_ID AND CST_ACTIV = 1
    JOIN ___XTR_CUSTOMER_BILLING_ADDRESS ON CBA_ID = ORD_CBA_ID 
    JOIN ___XTR_CUSTOMER_DELIVERY_ADDRESS ON CDA_ID = ORD_CDA_ID 
    WHERE ORD_ID = $ord_id
    ORDER BY ORD_DATE DESC";
$request_commande_list = $conn->query($query_commande_list);
if ($request_commande_list->num_rows > 0) 
{
$result_commande_list = $request_commande_list->fetch_assoc();
$ord_id_this = $result_commande_list['ORD_ID'];
$cst_id_this = $result_commande_list['ORD_CST_ID'];
?>
<div class="container-fluid order-list-content mb-3">
    
    <?php 
    if(($result_commande_list['CST_ZIP_CODE']==65535)||($result_commande_list['CBA_ZIP_CODE']==65535)||($result_commande_list['CDA_ZIP_CODE']==65535))
    {
    ?>
    <div class="row p-0 m-0 mb-1 red">  
        <div class="col-12 order-header-data-bloc text-center">
            Vérifier le <b>code postal</b> du client !!! &nbsp; En cas d'erreur contactez le webmaster avant de traiter la commande.
        </div>
    </div>
    <?php 
    }
    ?>
    <div class="row p-0 m-0 cyan">  
        <div class="col-9 order-header-bloc align-self-center">
            Commande n° <span><?php echo $ord_id_this; ?>/A</span>
        </div> 
        <div class="col-3 order-header-data-bloc align-self-center text-right">
            <b>DC</b> : <?php echo date('d/m/Y',strtotime($result_commande_list['ORD_DATE'])); ?>
            &nbsp;
            <b>DP</b> : <?php echo date('d/m/Y',strtotime($result_commande_list['ORD_DATE_PAY'])); ?>
        </div>
    </div>

    <div class="row p-0 m-0">
        <div class="col-2 order-first-bloc">
                <b><u>Commandée par :</u></b>
                <br>
                <?php echo $result_commande_list['CST_CIVILITY']; ?> <?php echo $result_commande_list['CST_NAME']; ?> 
                <?php echo $result_commande_list['CST_FNAME']; ?>
                <br>
                <?php echo $result_commande_list['CST_ADDRESS']; ?> - <?php echo $result_commande_list['CST_ZIP_CODE']; ?> 
                <?php echo $result_commande_list['CST_CITY']; ?>, <?php echo $result_commande_list['CST_COUNTRY']; ?>
        </div>
        <div class="col-2 order-first-bloc">
                <b><u>Coordonnées de contact :</u></b><br>
                <?php echo $result_commande_list['CST_TEL']; ?> / <?php echo $result_commande_list['CST_GSM']; ?>
                <br>
                <?php echo $result_commande_list['CST_MAIL']; ?>
        </div>
        <div class="col-2 order-first-bloc">
                <b><u>Facturée à :</u></b><br>
                <?php echo $result_commande_list['CBA_CIVILITY']; ?> <?php echo $result_commande_list['CBA_NAME']; ?> 
                <?php echo $result_commande_list['CBA_FNAME']; ?>
                <br>
                <?php echo $result_commande_list['CBA_ADDRESS']; ?> - <?php echo $result_commande_list['CBA_ZIP_CODE']; ?> 
                <?php echo $result_commande_list['CBA_CITY']; ?>, <?php echo $result_commande_list['CBA_COUNTRY']; ?>
        </div>
        <div class="col-2 order-first-bloc">
                <b><u>Livrée à :</u></b><br>
                <?php echo $result_commande_list['CDA_CIVILITY']; ?> <?php echo $result_commande_list['CDA_NAME']; ?> 
                <?php echo $result_commande_list['CDA_FNAME']; ?>
                <br>
                <?php echo $result_commande_list['CDA_ADDRESS']; ?> - <?php echo $result_commande_list['CDA_ZIP_CODE']; ?> 
                <?php echo $result_commande_list['CDA_CITY']; ?>, <?php echo $result_commande_list['CDA_COUNTRY']; ?>
        </div> 
        <div class="col-1 order-first-bloc">
                <b><u>Paiement :</u></b>
                <br>
                CB
                <br>
                Master Card
        </div>
        <div class="col-3 order-first-bloc">
                <b><u>Informations véhicules :</u></b>
                <br>
                <?php echo str_replace("<br>", " / ", $result_commande_list['ORD_INFO']); ?>
        </div> 
        <div class="col-12 order-details-bloc">
            

            <div class="container-fluid order-line-title">

                    <div class="row">
                        <div class="col-5 order-line-title-col">
                            Article
                        </div>
                        <div class="col-2 order-line-title-col text-center">
                            PA U HT
                        </div>
                        <div class="col-2 order-line-title-col text-center">
                            PV U TTC
                        </div>
                        <div class="col-1 order-line-title-col text-center">
                            QTY
                        </div>
                        <div class="col-2 order-line-title-col text-center">
                            PT TTC
                        </div>
                    </div>
                    
            </div>
            <?php
            $query_commande_line = "SELECT * FROM ___XTR_ORDER_LINE 
                JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
                WHERE ORL_ORD_ID = $ord_id_this 
                AND ORL_EQUIV_ID = 0
                ORDER BY ORL_ID";
            $request_commande_line = $conn->query($query_commande_line);
            if ($request_commande_line->num_rows > 0) 
            {
                while($result_commande_line = $request_commande_line->fetch_assoc())
                { 
                $ord_line_id_this = $result_commande_line['ORL_ID'];
                $ord_line_orls_id_this = $result_commande_line['ORL_ORLS_ID'];
                ?>
                <div class="container-fluid order-line-data" style="background: #<?php echo $result_commande_line['ORLS_COLOR']; ?>">

<div class="row">
<div class="col-5 order-line-data-col">
    <?php echo $result_commande_line['ORL_PG_NAME']; ?> <?php echo $result_commande_line['ORL_PM_NAME']; ?> réf <?php echo $result_commande_line['ORL_ART_REF']; ?><br>
    <?php
    if($result_commande_line['ORL_ART_DEPOSIT_UNIT_TTC']>0)
    {
    ?>
    + Consigne <?php echo $result_commande_line['ORL_ART_QUANTITY']*$result_commande_line['ORL_ART_DEPOSIT_UNIT_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?> TTC 
    <?php
    }
    else
    {
    ?>
    -
    <?php
    }
    ?>
</div>
<div class="col-2 order-line-data-col text-center"  style="color: #e30101;">
    <?php 
    if($result_commande_line['ORL_SPL_ID']>0)
    {
        ?>
        <?php echo $result_commande_line['ORL_SPL_PRICE_BUY_UNIT_HT']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
        <br><b><?php echo $result_commande_line['ORL_SPL_NAME']; ?></b>
        <?php
    }
    else
    {
        ?>
        <?php echo $result_commande_line['ORL_ART_PRICE_BUY_UNIT_HT']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
        <br>-
        <?php
    }
    ?>
</div>
<div class="col-2 order-line-data-col text-center">
    <?php echo $result_commande_line['ORL_ART_PRICE_SELL_UNIT_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
</div>
<div class="col-1 order-line-data-col text-center">
    <?php echo $result_commande_line['ORL_ART_QUANTITY']; ?>
</div>
<div class="col-2 order-line-data-col text-center">
    <?php echo $result_commande_line['ORL_ART_PRICE_SELL_TTC']; ?> <?php echo $GlobalSiteCurrencyChar; ?>
</div>
</div>
<div class="row">
<div class="col-12 order-line-data-col-action pl-3">
    <b>Statut</b> : &nbsp; <?php echo $result_commande_line['ORLS_NAME']; ?>
</div>
</div>

<?php
// 93 --> refus d'équivalence donc inutile d'afficher la proposition
$query_commande_line_equiv = "SELECT * FROM ___XTR_ORDER_LINE 
JOIN ___XTR_ORDER_LINE_STATUS ON ORLS_ID = ORL_ORLS_ID
WHERE ORL_EQUIV_ID = $ord_line_id_this AND ORL_ORLS_ID != 93
ORDER BY ORL_ID";
$request_commande_line_equiv = $conn->query($query_commande_line_equiv);
if ($request_commande_line_equiv->num_rows > 0) 
{
$result_commande_line_equiv = $request_commande_line_equiv->fetch_assoc();
?>
<div class="row">
<div class="col-12 order-line-data-col-action pl-3">
    <b>Au lieu de</b> : &nbsp; <?php echo $result_commande_line_equiv['ORL_PG_NAME']; ?> <?php echo $result_commande_line_equiv['ORL_PM_NAME']; ?> réf <?php echo $result_commande_line_equiv['ORL_ART_REF']; ?>
    &nbsp; // &nbsp; <?php echo $result_commande_line_equiv['ORLS_NAME']; ?>
    &nbsp; // &nbsp; Remboursement de 500.00 €
</div>
</div>
<?php
}
?>

                </div>
                <?php
                }
            }
            ?>
            <div class="container-fluid order-line-title pt-3">

                    <div class="row">
                        <div class="col-5">
                            &nbsp;
                        </div>
                        <div class="col-4 order-line-title-col">
                            Sous Total
                        </div>
                        <div class="col-3 order-line-title-col-data-total text-center">
                            <?php echo $result_commande_list['ORD_AMOUNT_TTC']; ?>
                        </div>
                        <div class="col-5">
                            &nbsp;
                        </div>
                        <div class="col-4 order-line-title-col">
                            Consigne
                        </div>
                        <div class="col-3 order-line-title-col-data-total text-center">
                            <?php echo $result_commande_list['ORD_DEPOSIT_TTC']; ?>
                        </div>
                        <div class="col-5">
                            &nbsp;
                        </div>
                        <div class="col-4 order-line-title-col">
                            Frais Port
                        </div>
                        <div class="col-3 order-line-title-col-data-total text-center">
                            <?php echo $result_commande_list['ORD_SHIPPING_FEE_TTC']; ?>
                        </div>
                        <div class="col-5">
                            &nbsp;
                        </div>
                        <div class="col-4 order-line-title-col">
                            Total
                        </div>
                        <div class="col-3 order-line-title-col-data-total text-center">
                            <?php echo $result_commande_list['ORD_TOTAL_TTC']; ?>
                        </div>
                    </div>
                    
            </div>

            



        </div>
    </div>

</div>

<?php
}
else
{
?>
<div class="container-fluid order-list-no-content">
    <div class="row">
        <div class="col-12 text-center p-3">
        Commande non existante ou nn traitable.
        <br>
        Contactez votre webmaster.
        </div>
    </div>
</div>
<?php
}
?>
<!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES -->
<!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES -->
<!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES -->
<!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES -->
<!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES --><!-- END LISTE COMMANDES -->

</div>
</div>
<!-- / CONTENU DE LA PAGE -->

</div>
</div>

<?Php
// PIED DE PAGE
@require_once('../footer.last.section.php');
?>
</body>
</html>
<?php
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////// FIN PAGE SESSION GRANTED /////////////////////////////////////////////////////////
}
else
{
	include("../get.access.response.no.privilege.php");
}

}
else
{
	//header("Location: ".$destinationLink);
	include("../get.access.response.php");
}
?>