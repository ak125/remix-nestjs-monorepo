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
$aliasSecondDepartment = getSecondDepartmentAlias();

if ($rsverifPrivilege=1)
{
$pageH1 = "Page Z...";
$pageH2 = "Gestion du contenu de la page Z...";
$dept_id=7;
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
        <th style="width: 40px;">MF</th>
        <th style="width: 40px;">PG</th>
        <th>Gamme</th>
        <th style="width: 147px;">Action</th>
    </tr>
</thead>
<tbody>
<?php
$query_list = "SELECT DISTINCT MF_ID, MF_NAME 
			FROM PIECES_GAMME 
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			JOIN CATALOG_FAMILY ON MF_ID = MC_MF_ID
			WHERE PG_DISPLAY = 1  AND PG_LEVEL IN (1,2) AND MF_DISPLAY = 1
			ORDER BY MF_SORT";
$request_list = $conn->query($query_list);
if ($request_list->num_rows > 0) 
{
while($result_list = $request_list->fetch_assoc()) 
{
    $mf_id_this = $result_list["MF_ID"];

    $query_list_in = "SELECT DISTINCT PG_ID ,PG_ALIAS, PG_NAME 
			FROM PIECES_GAMME 
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			WHERE PG_DISPLAY = 1  AND PG_LEVEL IN (1,2) 
			AND MC_MF_ID = $mf_id_this
			ORDER BY MC_SORT";
	$request_list_in = $conn->query($query_list_in);
	if ($request_list_in->num_rows > 0) 
	{
	while($result_list_in = $request_list_in->fetch_assoc()) 
	{
    ?>
    <tr>
        <td><?php echo $mf_id_this; ?></td>
        <td><?php echo $result_list_in["PG_ID"]; ?></td>
        <td>
            <b><?php echo $result_list_in["PG_NAME"]; ?></b><br><?php echo $result_list["MF_NAME"]; ?>
        </td>
        <td text-center>

            <button type="button" class="btn rounded-0 UPDATE w-100" data-toggle="modal" data-target="#itemUpdateModal" data-whatever='<?php echo $result_list_in["PG_ID"]; ?>'>Balisage et contenu</button>

        </td>
    </tr>
    <?php
	}
	}
}
}
?>
</tbody>
</table>
<script type="text/javascript">
    $(document).ready(function() {
    $('#dataTableList').DataTable( {
        "order": [[ 0, "asc" ]],
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

        <div class="modal DARK-MODAL fade" id="itemUpdateModal" tabindex="-1" role="dialog" aria-labelledby="itemUpdateModalLabel" aria-hidden="false">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
        <div class="modal-header cyan">
        <h5 class="modal-title" id="itemUpdateModalLabel">&nbsp;</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
        &nbsp;
        </div>
        <div class="modal-footer">
        <button type="button" class="btn rounded-0 CLOSE-MODAL" data-dismiss="modal">Annuler et Fermer</button>
        </div>
        </div>
        </div>
        </div>
        <script type="text/javascript">
        $('#itemUpdateModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var recipient = button.data('whatever')
        var modal = $(this)
        modal.find('.modal-title').text('SEO Page Z @' + recipient)
        modal.find('.modal-body input').val(recipient)
        var url = "<?php echo $domain; ?>/<?php echo $aliasDepartment; ?>/<?php echo $aliasSecondDepartment; ?>/content/"+ recipient
        $(".modal-body").html('<iframe width="100%" height="427" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
        })
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