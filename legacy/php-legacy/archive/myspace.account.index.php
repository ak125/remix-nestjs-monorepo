<?php 
session_start();
// parametres relatifs à la page
$typefile="my";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="home";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="title" content="<?php  echo $pagetitle; ?>" />
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="keywords" content="<?php  echo $pagekeywords; ?>"/>
<meta name="robots" content="<?php echo $pageRobots; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<!-- CSS -->
<style>
@import url('https://fonts.googleapis.com/css?family=Oswald:300,400,700&display=swap');
</style>
<link href="/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="/assets/css/<?php echo $hr;?>.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('global.header.section.php');
?>

<div class="container-fluid globalthirdheader">
<div class="container-fluid mywidecontainer nocarform">
</div>
</div>

<div class="container-fluid containerPage">
<div class="container-fluid containerinPage txt18">

<?php
if(isset($_SESSION['myaklog'])) // le client est deja connecté
{
// GET CLIENT DATA
    $mailclt=$_SESSION['myaklog'];
    $query_client_data = "SELECT * FROM ___XTR_CUSTOMER 
        WHERE CST_MAIL = '$mailclt'";
    $request_client_data = $conn->query($query_client_data);
    $result_client_data = $request_client_data->fetch_assoc();
    	// personnel data
		$connectedclt_id = $result_client_data['CST_ID'];
		$connectedclt_mail = $result_client_data['CST_MAIL'];
		$connectedclt_civ = $result_client_data['CST_CIVITILY'];
		$connectedclt_nom = $result_client_data['CST_NAME'];
		$connectedclt_prenom = $result_client_data['CST_FNAME'];
		$connectedclt_adr = $result_client_data['CST_ADDRESS'];
		$connectedclt_tel = $result_client_data['CST_TEL'];
		$connectedclt_port = $result_client_data['CST_GSM'];
		$connectedclt_zipcode = $result_client_data['CST_ZIP_CODE'];
		$connectedclt_ville = $result_client_data['CST_CITY'];
		$connectedclt_pays = $result_client_data['CST_COUNTRY'];
		// company data
		$connectedclt_is_pro = $result_client_data['CST_IS_PRO'];
		$connectedclt_ste = $result_client_data['CST_RS'];
		$connectedclt_siret = $result_client_data['CST_SIRET'];
?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 d-none d-md-block">
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('config/ariane.conf.php');
		?>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
	
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-md-8 col-lg-9">
<!-- COL LEFT -->
<!-- COL LEFT -->
<!-- COL LEFT -->
<!-- COL LEFT -->
<!-- COL LEFT -->
<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<h1><?php echo $pageh1; ?></h1>
			<a class="myspaceout" href="<?php  echo $domain; ?>/<?php  echo $domainClient2022; ?>/out">Déconnexion</a>
		</span>
	</div>
</div>
<div class="row">
	<div class="col-12">

<!-- CONTENT BLOC -->
<div class="row">
<div class="col-12 pb-3">
	<h2 class="TXT">Mes coordonnées</h2>
</div>
</div>
<div class="row m-0 p-0">
	<div class="col-5 myaccount-title">
		Type de compte
	</div>
	<div class="col-7 myaccount-line">
		<?php if($connectedclt_is_pro==1) echo "Professionnel"; else echo "Particulier"; ?>
	</div>
	<?php
	if($connectedclt_is_pro==1)
	{
		?>
		<div class="col-5 myaccount-title">
			Société
		</div>
		<div class="col-7 myaccount-line">
			<?php echo $connectedclt_ste; ?>
		</div>
		<div class="col-5 myaccount-title">
			Siret
		</div>
		<div class="col-7 myaccount-line">
			<?php echo $connectedclt_siret; ?>
		</div>
		<?php
	}
	?>
	<div class="col-5 myaccount-title">
		Nom et prénom
	</div>
	<div class="col-7 myaccount-line">
		<?php echo $connectedclt_civ." ".$connectedclt_nom." ".$connectedclt_prenom; ?>
	</div>
	<div class="col-5 myaccount-title">
		Adresse
	</div>
	<div class="col-7 myaccount-line">
		<?php echo $connectedclt_adr.", ".$connectedclt_zipcode." - ".$connectedclt_ville.", ".$connectedclt_pays."."; ?>
	</div>
	<div class="col-5 myaccount-title">
		Email
	</div>
	<div class="col-7 myaccount-line">
		<?php echo $connectedclt_mail; ?>
	</div>
	<div class="col-5 myaccount-title">
		Téléphone
	</div>
	<div class="col-7 myaccount-line">
		<?php if($connectedclt_tel!="") echo $connectedclt_tel; else echo "-"; ?>
	</div>
	<div class="col-5 myaccount-title">
		Gsm
	</div>
	<div class="col-7 myaccount-line">
		<?php if($connectedclt_port!="") echo $connectedclt_port; else echo "-"; ?>
	</div>
</div>
<!-- / CONTENT BLOC -->

	</div>
	<div class="col-12 pt-3">

<!-- CONTENT BLOC -->
<div class="row">
<div class="col-12 pb-3">
	<h2 class="TXT">Mes messages</h2>
</div>
</div>
<div class="row m-0 p-0">
	<?php
	$query_message = "SELECT * FROM ___XTR_MSG 
		WHERE MSG_CST_ID = $connectedclt_id AND MSG_CNFA_ID != 0
        ORDER BY `MSG_DATE` DESC LIMIT 5";
    $request_message = $conn->query($query_message);
    if ($request_message->num_rows > 0) 
    {
    while($result_message = $request_message->fetch_assoc())
    {
	    $msg_id_this = $result_message['MSG_ID'];
	    ?>
		<div class="col-12 col-sm-6 col-md-2 myaccount-title <?php if($result_message['MSG_OPEN']==0) { echo 'newBg'; } ?>">
			<b><?php echo $result_message['MSG_ORD_ID']; ?>/A</b>
		</div>
		<div class="col-12 col-sm-6 col-md-3 myaccount-line <?php if($result_message['MSG_OPEN']==0) { echo 'newBg'; } ?>">
			<?php echo date('d/m/Y à H:i:s',strtotime($result_message['MSG_DATE']));?>
		</div>
		<div class="col-12 col-sm-6 col-md-5 col-lg-6 myaccount-line <?php if($result_message['MSG_OPEN']==0) { echo 'newBg'; } ?>">
			<?php echo strip_tags($result_message['MSG_SUBJECT']); ?>
		</div>
		<div class="col-12 col-sm-6 col-md-2 col-lg-1 myaccount-line text-center <?php if($result_message['MSG_OPEN']==0) { echo 'newBg'; } ?>">

				<button type="button" class="btn <?php if($result_message['MSG_OPEN']==1) { echo 'MYACCOUNTOPENMSG'; } else { echo 'MYACCOUNTOPENMSG2'; } ?>" data-toggle="modal" data-target="#openMsgFilModal" data-whatever="<?php echo $msg_id_this; ?>">&nbsp;</button>
				
		</div>
		<?php
	}
	}
	else
	{
		?>
		<div class="col-12 myaccount-title">
			Vous n'avez aucun message pour le moment.
		</div>
		<?php
	}
    ?>
</div>
<!-- / CONTENT BLOC -->

	</div>
	<div class="col-12 pt-3">

<!-- CONTENT BLOC -->
<div class="row">
<div class="col-12 pb-3">
	<h2 class="TXT">Mes commandes</h2>
</div>
</div>
<div class="row m-0 p-0">
	<?php
	$query_commande = "SELECT * FROM ___XTR_ORDER 
		WHERE ORD_CST_ID = $connectedclt_id 
		ORDER BY ORD_DATE DESC LIMIT 5";
    $request_commande = $conn->query($query_commande);
    if ($request_commande->num_rows > 0) 
    {
    while($result_commande = $request_commande->fetch_assoc())
    {
	    $commande_id_this = $result_commande['ORD_ID'];
	    $commande_is_payed = $result_commande['ORD_IS_PAY'];
	    ?>
		<div class="col-12 col-sm-6 col-md-2 myaccount-title <?php if($commande_is_payed==0) { echo 'newBg'; } ?>">
			<b><?php echo $result_commande['ORD_ID']; ?>/A</b>
		</div>
		<div class="col-8 col-sm-6 col-md-3 myaccount-line <?php if($commande_is_payed==0) { echo 'newBg'; } ?>">
			<?php echo date('d/m/Y',strtotime($result_commande['ORD_DATE'])); ?>
		</div>
		<div class="col-4 col-sm-3 col-md-2 myaccount-line <?php if($commande_is_payed==0) { echo 'newBg'; } ?>">
			<?php echo number_format($result_commande['ORD_TOTAL_TTC'],2,'.',''); ?> <?php echo $GlobalSiteCurrencyChar; ?>
		</div>
		<div class="col-8 col-sm-6 col-md-3 col-lg-4 myaccount-line <?php if($commande_is_payed==0) { echo 'newBg'; } ?>">
			<?php 
			if($commande_is_payed==0)
			{
				echo "En attente de paiement";
			}
			else
			{
				echo "Payé";
			}
			?>
		</div>
		<div class="col-4 col-sm-3 col-md-2 col-lg-1 myaccount-line text-center <?php if($commande_is_payed==0) { echo 'newBg'; } ?>">
			<?php 
			if($commande_is_payed==1)
			{
				/*?>
				<a target="_blank" href='<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/order/<?php echo $commande_id_this; ?>'><u>Facture</u></a>
				<?php*/
				echo "-";
			}
			else
			{
				echo "-";
			}
			?>
		</div>
		<?php
	}
	}
	else
	{
		?>
		<div class="col-12 myaccount-title">
			Vous n'avez aucune commande pour le moment.
		</div>
		<?php
	}
    ?>
</div>
<!-- / CONTENT BLOC -->

	</div>
</div>
<!-- / COL LEFT -->
<!-- / COL LEFT -->
<!-- / COL LEFT -->
<!-- / COL LEFT -->
<!-- / COL LEFT -->
	</div>
	<div class="col-md-4 col-lg-3">
<!-- COL RIGHT -->
<!-- COL RIGHT -->
<!-- COL RIGHT -->
<!-- COL RIGHT -->
<!-- COL RIGHT -->
<?php
include('myspace.account.menu.php');
?>
<!-- / COL RIGHT -->
<!-- / COL RIGHT -->
<!-- / COL RIGHT -->
<!-- / COL RIGHT -->
<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php
}
else
{
?>
	<?php
	// re connect
	$ask2connectResponseLinkCode = 1;
	require_once('myspace.connect.try.php');
	?>
<?php
}
?>

</div>
</div>

<?php
require_once('global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="/system/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.php');
?>
<div class="modal CART-MODAL fade" id="openMsgFilModal" tabindex="-1" role="dialog" aria-labelledby="openMsgFilModalLabel" aria-hidden="false">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<i><h5 class="modal-title" id="openMsgFilModalLabel">&nbsp;</h5></i>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body p-0 m-0">
&nbsp;
</div>
<div class="modal-footer">
<button type="button" class="btn rounded-0 CLOSE-CART-MODAL" data-dismiss="modal">Fermer</button>
</div>
</div>
</div>
</div>
<script type="text/javascript">
  $('#openMsgFilModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var recipient = button.data('whatever')
  var modal = $(this)
  modal.find('.modal-title').text('Message Commercial / Technique')
  modal.find('.modal-body input').val(recipient)
  var url = "<?php echo $domain; ?>/<?php echo $domainClient2022; ?>/msg/" + recipient
  $(".modal-body").html('<iframe width="100%"  height="427" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>