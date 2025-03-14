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
$pageH1 = "Archives";
$pageH2 = "Liste de toutes les commandes sur le site...";
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
        <div class="col-12 PANEL-LEFT align-self-center pt-3">
            <br><h1><?php echo $pageH1; ?></h1>
            <h2><?php echo $pageH2; ?></h2>
        </div>
    </div>

</div>
<div class="container-fluid Page-Welcome-Box">

<!-- CONTENU DE LA PAGE -->
<div class="row">
<div class="col-12 PANEL-LEFT">
<!-- TABLE CONTENT -->

        <div class="row">
            <div class="col-12 PANEL-BOX">
                <div class="container-fluid">
                <div class="row PANEL-BOX-IN">
                    <div class="col-12 PANEL-BOX-REGULAR-CONTENT">
                        

<!------------------------------------------- TABLE LIST ----------------------------------------------------->
<!------------------------------------------- TABLE LIST ----------------------------------------------------->
<!------------------------------------------- TABLE LIST ----------------------------------------------------->
<table id="dataTableList" class="table table-striped table-bordered table-txt w-100">
<thead>
    <tr>
        <th style="width: 170px;">Commande</th>
        <th style="width: 180px;">Date</th>
        <th>Client</th>
        <th style="width: 157px;">Statut commande</th>
        <th style="width: 220px;">Action</th>
    </tr>
</thead>
<tbody>
<?php
$query_commande_list = "SELECT ORD_ID, ORD_DATE, ORD_DATE_PAY, ORD_INFO,  
    ORD_AMOUNT_TTC, ORD_DEPOSIT_TTC, ORD_SHIPPING_FEE_TTC, ORD_TOTAL_TTC, 
    ORD_CST_ID, CST_CIVILITY, CST_NAME, CST_FNAME, CST_ADDRESS, CST_ZIP_CODE, CST_CITY, CST_COUNTRY, 
    CST_TEL, CST_GSM, CST_MAIL,  
    ORD_IS_PAY, ORD_DEPT_ID, ORD_PARENT
    FROM ___XTR_ORDER 
    JOIN ___XTR_CUSTOMER ON CST_ID = ORD_CST_ID AND CST_ACTIV = 1
    JOIN ___XTR_CUSTOMER_BILLING_ADDRESS ON CBA_ID = ORD_CBA_ID 
    JOIN ___XTR_CUSTOMER_DELIVERY_ADDRESS ON CDA_ID = ORD_CDA_ID 
    ORDER BY ORD_DATE DESC";
$request_commande_list = $conn->query($query_commande_list);
if ($request_commande_list->num_rows > 0) 
{
while($result_commande_list = $request_commande_list->fetch_assoc()) 
{
$ord_id_this = $result_commande_list['ORD_ID'];
$cst_id_this = $result_commande_list['ORD_CST_ID'];
$dept_id_this = $result_commande_list['ORD_DEPT_ID'];
$is_pay_this = $result_commande_list['ORD_IS_PAY'];
$is_supplement =  $result_commande_list['ORD_PARENT'];
?>
<tr>
    <td><b><?php echo $ord_id_this."/A"; ?></b><br>
        <?php if($is_supplement>0) echo "Supplément : ".$is_supplement; ?></td>
    <td>
                <b>DC</b> : <?php echo date('d/m/Y',strtotime($result_commande_list['ORD_DATE'])); ?>
                <br>
                <b>DP</b> : <?php echo date('d/m/Y',strtotime($result_commande_list['ORD_DATE_PAY'])); ?>
    </td>
    <td>
                <?php echo $result_commande_list['CST_CIVILITY']; ?> <?php echo $result_commande_list['CST_NAME']; ?> 
                <?php echo $result_commande_list['CST_FNAME']; ?>
                <br>
                <?php echo $result_commande_list['CST_TEL']; ?> / <?php echo $result_commande_list['CST_GSM']; ?>
    </td>
    <td>
                <?php if($dept_id_this==0) { ?><span class="archive-gray">En attente de paiement</span><?php } ?>
                <?php if($dept_id_this==1) { ?><span class="archive-blue">Département Commercial</span><?php } ?>
                <?php if($dept_id_this==2) { ?><span class="archive-green">Département Expédition</span><?php } ?>
                <?php if($dept_id_this==99) { ?><span class="archive-red">Commande Annulée</span><?php } ?>
    </td>
    <td text-center>

        <a href="<?php echo $domain; ?>/<?php echo $aliasDepartment; ?>/<?php echo $aliasSecondDepartment; ?>/<?php echo $ord_id_this; ?>"> Consulter</a>

    </td>
</tr>
<?php
}
}
?>
</tbody>
</table>
<script type="text/javascript">
    $(document).ready(function() {
    $('#dataTableList').DataTable( {
        "order": [[ 0, "desc" ]],
        "language": {               
            "search":"Recherche" , 
            "lengthMenu": "Afficher _MENU_ enregistrements par page",
            "zeroRecords": "Pas de résultat",
            "info": "Page _PAGE_ de _PAGES_",
            "infoEmpty": "Aucun enregistrement trouvé",
            "infoFiltered": "(Filtré de _MAX_ enregistrements)",                                    
            "paginate": {                                                                    
                "sFirst":   "Premier",                                                                    
                "sPrevious": "Précédant",                                                                    
                "sNext":     "Suivant",                                                                    
                "sLast":     "Dernier"                                                             
            } 
        }
    } );
} );
</script>
<!------------------------------------------- / TABLE LIST ----------------------------------------------------->
<!------------------------------------------- / TABLE LIST ----------------------------------------------------->
<!------------------------------------------- / TABLE LIST ----------------------------------------------------->




                    </div>
                </div>
                </div>
            </div>
        </div>

<!-- / TABLE CONTENT -->
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