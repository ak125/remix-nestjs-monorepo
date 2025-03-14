<?php 
session_start();
// parametres relatifs à la page
$typefile="my";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="pswrd.change";
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
$ask2updateResponseCode = 0;
$ask2updateResponse = "";
$ask2updateResponseMsg = "";

if(isset($_POST['ASK2UPDATE']))
{
    $ask2updateResponseCode = 1;

    $idtochange=$_POST['idtochange'];

    $mpold=$_POST['mptochange'];
    if (empty($mpold))
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner votre ancien mot de passe.<br>";
    }
    else
    {
    	// on verifie si l'ancien mot de passe est correct
	    $mailcltverif=$_SESSION['myaklog'];
		$mpcryptedmpverif=$mpold;
		$cryptedmpverif = crypt(md5($mpcryptedmpverif),"im10tech7");
	    $query_pswrd_verif = "SELECT * FROM ___XTR_CUSTOMER 
	        WHERE CST_MAIL = '$mailcltverif' AND CST_ID = '$idtochange' AND CST_PSWD = '$cryptedmpverif'";
	    $request_pswrd_verif = $conn->query($query_pswrd_verif);
	    if ($request_pswrd_verif->num_rows == 0) 
	    {
			$ask2updateResponseCode = 2;
			$ask2updateResponse = "Merci de vérifier vos données.";
			$ask2updateResponseMsg .= "Votre ancien mot de passe est incorrect.<br>";
	    }
    }

    $mp=$_POST['mptochangeto'];
    if (empty($mp))
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner votre nouveau mot de passe.<br>";
    }

    $mpconf=$_POST['mptochangetoconf'];
    if (empty($mpconf))
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner la confirmation de votre nouveau mot de passe.<br>";
    }

    if($mpconf!=$mp)
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez confirmer correctement votre mot de passe.<br>";
    }

    //$raisonsociale = $_POST['raisonsociale'];
	//$siret = $_POST['siret'];

    if($ask2updateResponseCode == 1)
    {

// UPDATE DE COMPTE

    // UPDATE COMPTE CLIENT
	$idtochange= mysqli_real_escape_string($conn, $idtochange);
	$mpcryptedmp=$mp;
	$newmpcrypt = crypt(md5($mpcryptedmp),"im10tech7");
	////$raisonsociale= mysqli_real_escape_string($conn, $raisonsociale);
	////$siret= mysqli_real_escape_string($conn, $siret);

	$ask2updateQuery = "UPDATE ___XTR_CUSTOMER SET CST_PSWD = '$newmpcrypt'  
		WHERE CST_ID = $idtochange";
	if($conn->query($ask2updateQuery) === TRUE)
	{
		$ask2updateResponseCode = 1;
		$ask2updateResponse = "Mise à jours effectuée avec succès";
	}
	else
	{
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Erreur critique.";
		$ask2updateResponseMsg .= "Une erreur a surgie lors de la modification de votre mot de passe :<br>".$conn->error;
	}

	// ENVOIE DE MAIL


// FIN CREATION DE COMPTE
	}
}

// GET CLIENT DATA
    $mailclt=$_SESSION['myaklog'];
    $query_client_data = "SELECT * FROM ___XTR_CUSTOMER 
        WHERE CST_MAIL = '$mailclt'";
    $request_client_data = $conn->query($query_client_data);
    $result_client_data = $request_client_data->fetch_assoc();
    	// personnel data
		$connectedclt_id = $result_client_data['CST_ID'];
// FIN CHANG. CONNEXION
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
	<div class="col-12 pt-3">

<!-- CONTENT BLOC -->
<!-- FORMULAIRE -->     
<form action="" method="post" role="form">
	    
	    <div class="row">
	      <div class="col-lg-12 text-center">
	        <?php
				if($ask2updateResponseCode>0)
				{
					if($ask2updateResponseCode==1)
					{
						?>
						<div class="response-green"><?php echo $ask2updateResponse ;?></div>
						<?php
					}
					else
					{
						?>
						<div class="response-red"><?php echo $ask2updateResponse ;?></b>
                		<br><span><?php echo $ask2updateResponseMsg; ?></span></div>
						<?php
					}
				}
			?>
	      </div>
	    </div>

    <div class="row">
	    <div class="col-12 pt-3 pb-3 text-justify">
			Saisissez un nouveau mot de passe pour <?php echo $mailclt; ?>. 
			<br>
			Nous vous recommandons vivement de créer un mot de passe unique, que vous n'utilisez pas pour d'autres sites Web.
			<br>
			Après le changement de votre mot de passe vous devez vous reconnectez de nouveau sur votre espace client.
			<br>
			Remarque : Une fois modifié, vous ne pouvez plus réutiliser votre ancien mot de passe !
		</div>
    </div>
    
    <div class="row pt-3">
        <div class="col-12 col-md-4 pb-3">
            Ancien mot de passe *
            <input type="password" name="mptochange" class="subscribe" value="" required autocomplete="off"/>
        </div>
        <div class="col-12 col-md-4 pb-3">
            Nouveau mot de passe *
            <input type="password" name="mptochangeto" class="subscribe" value="" required autocomplete="off"/>
        </div>
        <div class="col-12 col-md-4 pb-3">
            Confirmer votre nouveau mot de passe *
            <input type="password" name="mptochangetoconf" class="subscribe" value="" required autocomplete="off"/>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 text-center text-sm-right">
            <input type="submit" class="subscribeSubmit" value="mettre à jours" />
            <input type="hidden" name="idtochange" value="<?php echo $connectedclt_id; ?>">
            <input type="hidden" name="ASK2UPDATE" value="1">   
        </div>
    </div>

</form>
<!-- FIN FORMULAIRE -->
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