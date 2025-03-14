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
		if($rsverif["valide"]=='1')
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
			<h1>Welcome to the <?php echo $domainname; ?></h1>
			<h2>Search, Select and Buy...</h2>
	</div>
	<div class="container-fluid Page-Welcome-Box">

<?php
if ($privilegeGranted == "SA")
{
?>
		<div class="row">
			<!-- BOX GRID -->
				<div class="col-6 GRID-BOX">
					
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE cyan text-center">
							Purchase Order
						</div>
						<div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
							
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_commande
JOIN 2027_xmassdoc_reseller_access_code ON id = com_clt_id 
WHERE com_type = 'PO' AND com_level = '1' ORDER BY com_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$po_1_id = $Commande["com_id"];
	?>
	<div class="row">
		<div class="col-5 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo utf8_encode($Commande["company"]); ?></b>
			<br><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?>
			
		</div>
		<div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo $Commande["com_matricule"]; ?></b>
			<br><?php echo $Commande["com_date"]; ?>
			
		</div>
		<div class="col-3 GRID-BOX-CONTENT-ACCOUNT-COL">
		
<button type="button" class="FICHE-ARTICLE" data-toggle="modal" data-target="#poModal" data-whatever="<?php echo $po_1_id; ?>">View details</button>



		</div>
	</div>
	<?php
}
?>
<div class="modal DARK-MODAL fade" id="poModal" tabindex="-1" role="dialog" aria-labelledby="poModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header cyan">
        <h5 class="modal-title" id="poModalLabel">&nbsp;</h5>
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
	$('#poModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  //modal.find('.modal-title').text('Modifier ' + recipient)
  modal.find('.modal-title').text('Purchase Order Verification')
  modal.find('.modal-body input').val(recipient)
  //var url = "<?php echo $domain; ?>/dept.magazin.stock.update.php?pieceid="+ recipient
  var url = "<?php echo $domain; ?>/purchaseorder/update/"+ recipient
  $(".modal-body").html('<iframe width="100%" height="397" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-6 GRID-BOX">
					
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE green text-center">
							ORder ticket
						</div>
						<div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
							
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_boncommande
JOIN 2027_xmassdoc_reseller_access_code ON id = boncom_clt_id WHERE boncom_etat_id = '1' ORDER BY boncom_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$boncom_id = $Commande["boncom_id"];
	?>
	<div class="row">
		<div class="col-5 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo utf8_encode($Commande["company"]); ?></b>
			<br><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?>
			
		</div>
		<div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo $Commande["boncom_matricule"]; ?></b>
			<br><?php echo $Commande["boncom_date"]; ?>
			
		</div>
		<div class="col-3 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<button type="button" class="FICHE-ARTICLE" data-toggle="modal" data-target="#bcModal" data-whatever="<?php echo $boncom_id; ?>">Get BL</button>
			
		</div>
	</div>
	<?php
}
?>
<div class="modal DARK-MODAL fade" id="bcModal" tabindex="-1" role="dialog" aria-labelledby="bcModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header cyan">
        <h5 class="modal-title" id="bcModalLabel">&nbsp;</h5>
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
	$('#bcModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  //modal.find('.modal-title').text('Modifier ' + recipient)
  modal.find('.modal-title').text('Generate BL')
  modal.find('.modal-body input').val(recipient)
  //var url = "<?php echo $domain; ?>/dept.magazin.stock.update.php?pieceid="+ recipient
  var url = "<?php echo $domain; ?>/generatebl/"+ recipient
  $(".modal-body").html('<iframe width="100%" height="397" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-6 GRID-BOX">
					
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE sand text-center">
							BL
						</div>
						<div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
							
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_bl
JOIN 2027_xmassdoc_reseller_access_code ON id = bl_clt_id WHERE bl_etat_id = '1' ORDER BY bl_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$bl_id = $Commande["bl_id"];
	?>
	<div class="row">
		<div class="col-5 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo utf8_encode($Commande["company"]); ?></b>
			<br><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?>
			
		</div>
		<div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<b><?php echo $Commande["bl_matricule"]; ?></b>
			<br><?php echo $Commande["bl_date"]; ?>
			
		</div>
		<div class="col-3 GRID-BOX-CONTENT-ACCOUNT-COL">
			
			<button type="button" class="FICHE-ARTICLE" data-toggle="modal" data-target="#fcModal" data-whatever="<?php echo $bl_id; ?>">Generate Invoice</button>
			
		</div>
	</div>
	<?php
}
?>
<div class="modal DARK-MODAL fade" id="fcModal" tabindex="-1" role="dialog" aria-labelledby="fcModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header cyan">
        <h5 class="modal-title" id="fcModalLabel">&nbsp;</h5>
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
	$('#fcModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var recipient = button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  //modal.find('.modal-title').text('Modifier ' + recipient)
  modal.find('.modal-title').text('Generate Invoice')
  modal.find('.modal-body input').val(recipient)
  //var url = "<?php echo $domain; ?>/dept.magazin.stock.update.php?pieceid="+ recipient
  var url = "<?php echo $domain; ?>/generateinvoice/"+ recipient
  $(".modal-body").html('<iframe width="100%" height="397" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->

		
		

		</div>
<?php
}
else
{
?>		
			
<div class="row">
<!-- BOX GRID -->
				<div class="col-12 col-sm-6 GRID-BOX">
					
					<div class="row">
						<div class="col-12 GRID-BOX-TITLE cyan text-center">
							select your car
							<span><br>( manufacturer / model / year / motorization )</span>
						</div>
						<div class="col-12 GRID-BOX-CONTENT">
							
<!--  form-->
<form method="post" action="" >
<div class="row">
<div class="col-12 col-md-3 p-1"> 
  <select name="form-marq" id="form-marq" class="REF-SEARCH-CAR-FORM-INPUT-SELECT">
  <option>- Manufacturer -</option>
  <?php
  $qScarPop=mysql_query("SELECT DISTINCT marque_id , marque_name_site, marque_logo FROM $sqltable_Car_marque 
  WHERE marque_affiche = 1  AND marque_vt_ap = 1
  ORDER BY marque_name_site");
  while($rScarPop=mysql_fetch_array($qScarPop))
  {
  ?>
  <option value="<?php echo $rScarPop['marque_id']; ?>"><?php echo utf8_encode($rScarPop['marque_name_site']); ?></option>
  <?php
  }
  ?>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-year" id="form-year" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled>
    <option>- Year -</option>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-model" id="form-model" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled>
    <option>- Model -</option>
  </select>
</div>
<div class="col-12 col-md-3 p-1">
  <select name="form-type" id="form-type" class="REF-SEARCH-CAR-FORM-INPUT-SELECT" disabled onchange="MM_jumpMenu('parent',this,0)">
    <option>- Motorization -</option>
  </select>
</div>
</div>
</form>
<!-- / form-->

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
	<div class="col-12 col-sm-6">




<div class="row">
			<!-- BOX GRID -->
				<div class="col-12 GRID-BOX mt-3 mt-xl-0">
					
					<div class="row">
						<div class="col-12 col-md-4 GRID-BOX-TITLE sand text-center">
							search by type mine
							<span><br>( car number )</span>
						</div>
						<div class="col-12 col-md-8 GRID-BOX-CONTENT">
							
							<form action="<?php  echo $domain; ?>/searchmine/" method="POST" role="form">
							<div class="row">
								<div class="col-8">
									
									<input type="text" name="ref_mine" class="REF-SEARCH-FORM-INPUT-TXT" placeholder="Type mine"/>
									
								</div>
								<div class="col-4">
									
									<input type="submit" class="REF-SEARCH-FORM-INPUT-SUBMIT" value="" />
									<input name="linkto" type="hidden" value="carType" />
									
								</div>
							</div>
							</form>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
			<!-- BOX GRID -->
				<div class="col-12  GRID-BOX mt-3 mt-xl-0">
					
					<div class="row">
						<div class="col-12 col-md-4 GRID-BOX-TITLE fuchia text-center">
							search by engine code
							<span><br>( car code )</span>
						</div>
						<div class="col-12 col-md-8 GRID-BOX-CONTENT">
							
							<form action="<?php  echo $domain; ?>/searchengine/" method="POST" role="form">
							<div class="row">
								<div class="col-8">
									
									<input type="text" name="engine_code" class="REF-SEARCH-FORM-INPUT-TXT" placeholder="Engine code"/>
									
								</div>
								<div class="col-4">
									
									<input type="submit" class="REF-SEARCH-FORM-INPUT-SUBMIT" value="" />
									
								</div>
							</div>
							</form>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
		</div>





	</div>
</div>
<div class="row">
	<!-- BOX GRID -->
				<div class="col-12 GRID-BOX mt-3">
					
					<div class="row">
						<div class="col-12 col-md-4 GRID-BOX-TITLE nuts text-center">
							search by reference
							<span><br>( item ref )</span>
						</div>
						<div class="col-12 col-md-8 GRID-BOX-CONTENT">
							
							<form  action="<?php  echo $domain; ?>/search/"  method="GET" role="form">
							<div class="row">
								<div class="col-8">
									
									<input type="text" name="quest" class="REF-SEARCH-FORM-INPUT-TXT" placeholder="R&eacute;f. d'origine ou commercial de votre pi&egrave;ce"/>
									
								</div>
								<div class="col-4">
									
									<input type="submit" class="REF-SEARCH-FORM-INPUT-SUBMIT" value="" />
									
								</div>
							</div>
							</form>

						</div>
					</div>

				</div>
			<!-- / BOX GRID -->
</div>

<div class="row">
<!-- BOX GRID -->
<div class="col-12 GRID-BOX mt-3">
					
<div class="row">
<?php
$queryEquip=mysql_query("SELECT pm_id, pm_name_site, pm_name_meta, pm_preview, pm_logo 
  FROM $sqltable_Piece_marque 
  WHERE pm_affiche = 1 
  ORDER BY pm_tri");
while($resultEquip=mysql_fetch_array($queryEquip))
{
  ?>
  <div class="col-6 col-sm-3 col-md-1 HOME-EQUIP-BLOC">
    

          <div class="row">
            <div class="col-12">
            
            <img 
            src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $resultEquip['pm_logo']; ?>" 
            alt="<?php echo utf8_encode($resultEquip['pm_name_meta']); ?>" class="w-100" style="border:0px;"/>

            </div>
          </div>



  </div>
  <?php
}
?>
</div>

</div>
<!-- / BOX GRID -->
</div>


<?php
}
?>	
	</div>
</div>

<?Php
// PIED DE PAGE
@require_once('footer.last.section.php');
?>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
    $(function() {
        
        $("#form-marq").change(function() {
            $("#form-year").load("v2.get.car.year.php?formCarMarqueid=" + $("#form-marq").val());
            document.getElementById("form-year").disabled = false;
            $("#form-model").load("v2.get.car.model.php?formCarMarqueid=0&formCarMarqueYear=0");
            $("#form-type").load("v2.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0");
            document.getElementById("form-model").disabled = true;
            document.getElementById("form-type").disabled = true;

        });

        $("#form-year").change(function() {
            $("#form-model").load("v2.get.car.model.php?formCarMarqueid=" + $("#form-marq").val() + "&formCarMarqueYear=" + $("#form-year").val());
            document.getElementById("form-model").disabled = false;
            $("#form-type").load("v2.get.car.type.php?formCarMarqueid=0&formCarMarqueYear=0&formCarModelid=0");
            document.getElementById("form-type").disabled = true;
        });

        $("#form-model").change(function() {
            $("#form-type").load("v2.get.car.type.php?formCarMarqueid=" + $("#form-marq").val() + "&formCarMarqueYear=" + $("#form-year").val() + "&formCarModelid=" + $("#form-model").val());
            document.getElementById("form-type").disabled = false;
        });



    });

function MM_jumpMenu(targ,selObj,restore){ //v3.0
eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}

</script>
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