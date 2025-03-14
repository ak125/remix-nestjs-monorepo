<?php
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

$log=$_SESSION['im7mylog'];
$mykey=$_SESSION['im7mykey'];

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
	$rqverif= mysql_query("SELECT * FROM 2027_xmassdoc_reseller_access_code WHERE login='$log' AND keylog='$mykey'");
	if ($rsverif=mysql_fetch_array($rqverif))
	{
		if(($rsverif["valide"]=='1')&&($rsverif["type"] == "SA"))
		{
			$destinationLink = $accessPermittedLink;
			$ssid = $rsverif['id'];
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
<link href="<?php echo $domain; ?>/assets/css/intern.css" rel="stylesheet">
</head>
<body>
<div id="mainPageContentCover">&nbsp;</div>
<?Php
// ENTETE DE PAGE
@require_once('header.first.section.php');
// LEFT PANEL
@require_once('header.left.section.php');
?>

<div id="mainPageContent">

	<div class="container-fluid Page-Welcome-Title">

		<div class="row">
			<div class="col-9">

			<h1>Stock Management</h1>
			<h2>Search, Select and Buy...</h2>

			</div>
			<div class="col-3">

<button type="button" class="btn rounded-0 UPDATEADD w-100 mt-1" data-toggle="modal" data-target="#stockAddModal" data-whatever="">Add new Reference</button>
<div class="modal DARK-MODAL fade" id="stockAddModal" tabindex="-1" role="dialog" aria-labelledby="stockAddModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header cyan">
        <h5 class="modal-title" id="stockAddModalLabel">&nbsp;</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	&nbsp;
      </div>
      <div class="modal-footer">
        <button type="button" class="btn rounded-0 CLOSE-MODAL" data-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$('#stockAddModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  //modal.find('.modal-title').text('Modifier ' + recipient)
  modal.find('.modal-title').text('Stock Insert')
  modal.find('.modal-body input').val(recipient)
  //var url = "<?php echo $domain; ?>/dept.magazin.stock.update.php?pieceid="+ recipient
  var url = "<?php echo $domain; ?>/stock/seek/"
  $(".modal-body").html('<iframe width="100%" height="340" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>



			</div>
		</div>

	</div>
	<div class="container-fluid Page-Welcome-Box">


		<div class="row">
			<!-- BOX GRID -->
				<div class="col-3 GRID-BOX">
					
					<div class="row">
						<div class="col-6 GRID-BOX-TITLE-STOCK gray text-left">
							References in
							<br><span>Sale</span>
						</div>
						<div class="col-6 GRID-BOX-CONTENT-STOCK text-right">
							
							<b><?php
							$qCountArticle=mysql_query("SELECT COUNT(DISTINCT piece_id) AS CountArticle FROM $sqltable_Piece 
								WHERE piece_affiche = 1");
							$rCountArticle=mysql_fetch_array($qCountArticle);
    						echo $rCountArticle["CountArticle"];
							?></b>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-3 GRID-BOX">
					
					<div class="row">
						<div class="col-6 GRID-BOX-TITLE-STOCK sand text-left">
							References in
							<br><span>Stock</span>
						</div>
						<div class="col-6 GRID-BOX-CONTENT-STOCK text-right">
							
							<b><?php
							$qCountArticle=mysql_query("SELECT COUNT(DISTINCT sp_id) AS CountArticle FROM 2027_xmassdoc_piece_stock ");
							$rCountArticle=mysql_fetch_array($qCountArticle);
    						echo $rCountArticle["CountArticle"];
							?></b>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-3 GRID-BOX">
					
					<div class="row">
						<div class="col-6 GRID-BOX-TITLE-STOCK green text-left">
							References Stock
							<br><span>OK</span>
						</div>
						<div class="col-6 GRID-BOX-CONTENT-STOCK text-right">
							
							<b><?php
							$qCountArticle=mysql_query("SELECT COUNT(DISTINCT sp_id) AS CountArticle 
								FROM 2027_xmassdoc_piece_stock 
								WHERE sp_qte > 0");
							$rCountArticle=mysql_fetch_array($qCountArticle);
    						echo $rCountArticle["CountArticle"];
							?></b>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-3 GRID-BOX">
					
					<div class="row">
						<div class="col-6 GRID-BOX-TITLE-STOCK red text-left">
							References Stock
							<br><span>OUT</span>
						</div>
						<div class="col-6 GRID-BOX-CONTENT-STOCK text-right">
							
							<b><?php
							$qCountArticle=mysql_query("SELECT COUNT(DISTINCT sp_id) AS CountArticle 
								FROM 2027_xmassdoc_piece_stock 
								WHERE sp_qte = 0");
							$rCountArticle=mysql_fetch_array($qCountArticle);
    						echo $rCountArticle["CountArticle"];
							?></b>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-12 GRID-BOX mt-2">
					
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE-STOCK cyan text-center">
							update stock
						</div>
						<div class="col-12 GRID-BOX-CONTENT-STOCK text-center p-3">
							




		<table id="dataTableList" class="table table-striped table-bordered table-txt" style="width:100%;">
		<thead>
            <tr>
                <th>ID</th>
                <th>PRODUCT</th>
                <th>BRAND</th>
                <th>NAME</th>
                <th>REF</th>
                <th>QTY</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$qStock=mysql_query("SELECT sp_id, sp_ref, sp_pg_id, sp_pm_id, sp_qte,
			piece_id, piece_name, piece_name_complement
			FROM 2027_xmassdoc_piece_stock
			JOIN $sqltable_Piece ON  piece_ref_propre = sp_refpropre AND piece_pg_id = sp_pg_id AND piece_pm_id = sp_pm_id
			ORDER BY sp_pg_id, sp_pm_id");
		while($rStock=mysql_fetch_array($qStock))
		{
			?>
			<tr>
                <td><?php echo $rStock["sp_id"]; ?></td>
                <td><?php echo $rStock["sp_pg_id"]; ?></td>
                <td><?php echo $rStock["sp_pm_id"]; ?></td>
                <td><?php echo utf8_encode($rStock["piece_name"]." ".$rStock["piece_name_complement"]); ?></td>
                <td><?php echo $rStock["sp_ref"]; ?></td>
                <td><?php echo $rStock["sp_qte"]; ?></td>
                <td text-center>
                <button type="button" class="btn rounded-0 UPDATEADD" data-toggle="modal" data-target="#stockModal" data-whatever="<?php echo $rStock["sp_id"]; ?>">UPDATE</button>
            	</td>
            </tr>
			<?php
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
		            "lengthMenu": "Afficher _MENU_ articles par pages",
		            "zeroRecords": "Pas de résultat",
		            "info": "Page _PAGE_ de _PAGES_",
		            "infoEmpty": "Aucun enregistrement trouvé",
		            "infoFiltered": "(Filtré de _MAX_ total enregistrement)",                                    
					"paginate": {                                                                    
						"sFirst":   "Premier",                                                                    
						"sPrevious": "Précédent",                                                                    
						"sNext":     "Suivant",                                                                    
						"sLast":     "Dernier"                                                             
					} 
		        }
		    } );
		} );
		</script>
<div class="modal DARK-MODAL fade" id="stockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header cyan">
        <h5 class="modal-title" id="stockModalLabel">&nbsp;</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	&nbsp;
      </div>
      <div class="modal-footer">
        <button type="button" class="btn rounded-0 CLOSE-MODAL" data-dismiss="modal">Annuler</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	$('#stockModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  //modal.find('.modal-title').text('Modifier ' + recipient)
  modal.find('.modal-title').text('Stock Update')
  modal.find('.modal-body input').val(recipient)
  //var url = "<?php echo $domain; ?>/dept.magazin.stock.update.php?pieceid="+ recipient
  var url = "<?php echo $domain; ?>/stock/update/"+ recipient
  $(".modal-body").html('<iframe width="100%" height="340" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>





						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
		</div>
	
	</div>
</div>

<?Php
// PIED DE PAGE
@require_once('footer.last.section.php');
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
	//header("Location: ".$destinationLink);
	include("get.access.response.php");
}
?>