<?php 
session_start();
// parametres relatifs à la page
$typefile="my";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="adr.f";
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
// ON CHANGE LA CONNEXION
mysqli_close($sqlConn);
include('config/sql.extranet.conf.php');

$ask2updateResponseCode = 0;
$ask2updateResponse = "";
$ask2updateResponseMsg = "";

if(isset($_POST['ASK2UPDATE']))
{
    $ask2updateResponseCode = 1;

    $idtochange=$_POST['idtochange'];

    $civ=$_POST['civ'];

    $nom=$_POST['nom'];
    if (empty($nom))
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner votre nom.";
    }
  
    $prenom=$_POST['prenom'];

    $tel=$_POST['tel'];

    $gsm=$_POST['gsm'];
    if (!empty($gsm))
    {
		if(!(is_numeric($gsm)))
		{
			$ask2updateResponseCode = 2;
			$ask2updateResponse = "Merci de vérifier vos données.";
			$ask2updateResponseMsg .= "Le num&eacute;ro de GSM ne doit contenir que des chiffres.<br>";
		}
    }
    else
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Le num&eacute;ro de GSM doit &ecirc;tre renseign&eacute;.<br>";
    }

	$adr=$_POST['adr'];
	if (empty($adr))
	{
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner votre adresse.<br>";
	}

	$zipcode=$_POST['zipcode'];
	if (!empty($zipcode))
	{
		if(!(is_numeric($zipcode)))
		{
			$ask2updateResponseCode = 2;
			$ask2updateResponse = "Merci de vérifier vos données.";
			$ask2updateResponseMsg .= "Le code postal ne doit contenir que des chiffres.<br>";
		}
	}
	else
	{
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Le code postal doit &ecirc;tre renseign&eacute;.<br>";
	}

	$ville=$_POST['ville'];
    if (empty($ville))
    {
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Merci de vérifier vos données.";
		$ask2updateResponseMsg .= "Veuillez mentionner votre ville.<br>";
    }

    $pays=$_POST['pays'];

    //$raisonsociale = $_POST['raisonsociale'];
	//$siret = $_POST['siret'];

    if($ask2updateResponseCode == 1)
    {

// UPDATE DE COMPTE

    // UPDATE COMPTE CLIENT

	$idtochange= mysqli_real_escape_string($conn, $idtochange);
	$civ= mysqli_real_escape_string($conn, $civ);
	$nom= mysqli_real_escape_string($conn, $nom);
	$prenom= mysqli_real_escape_string($conn, $prenom);
	$tel= mysqli_real_escape_string($conn, $tel);
	$gsm= mysqli_real_escape_string($conn, $gsm);
	$adr= mysqli_real_escape_string($conn, $adr);
	$zipcode= mysqli_real_escape_string($conn, $zipcode);
	$ville= mysqli_real_escape_string($conn, $ville);
	$pays= mysqli_real_escape_string($conn, $pays);
	////$raisonsociale= mysqli_real_escape_string($conn, $raisonsociale);
	////$siret= mysqli_real_escape_string($conn, $siret);

	$ask2updateQuery = "UPDATE backofficeplateform_client SET factciv = '$civ', factnom = '$nom', factpre = '$prenom',  
		facttel = '$tel', factport = '$gsm', factadr = '$adr', factzipcode = '$zipcode', 
		factville = '$ville', factpays = '$pays'  
		WHERE numcli = $idtochange";
	if($conn->query($ask2updateQuery) === TRUE)
	{
		$ask2updateResponseCode = 1;
		$ask2updateResponse = "Mise à jours effectuée avec succès";
	}
	else
	{
		$ask2updateResponseCode = 2;
		$ask2updateResponse = "Erreur critique.";
		$ask2updateResponseMsg .= "Une erreur a surgie lors de la modification de votre adresse de facturation :<br>".$conn->error;
	}

	// ENVOIE DE MAIL


// FIN CREATION DE COMPTE
	}
}

// GET CLIENT DATA
    $mailclt=$_SESSION['myaklog'];
    $query_client_data = "SELECT * FROM backofficeplateform_client 
        WHERE mailcli = '$mailclt'";
    $request_client_data = $conn->query($query_client_data);
    $result_client_data = $request_client_data->fetch_assoc();
    	// personnel data
		$connectedclt_id = $result_client_data['numcli'];
		$connectedclt_mail = $result_client_data['mailcli'];
		$connectedclt_civ = $result_client_data['civcli'];
		$connectedclt_nom = $result_client_data['nomcli'];
		$connectedclt_prenom = $result_client_data['precli'];
		$connectedclt_adr = $result_client_data['adrcli'];
		$connectedclt_tel = $result_client_data['telcli'];
		$connectedclt_port = $result_client_data['portcli'];
		$connectedclt_zipcode = $result_client_data['zipcodecli'];
		$connectedclt_ville = $result_client_data['villecli'];
		$connectedclt_pays = $result_client_data['payscli'];
		// company data
		$connectedclt_ste = $result_client_data['raisonsociale'];
		$connectedclt_siret = $result_client_data['siret'];
		// Facture data
		$connectedclt_fact_mail = $result_client_data['factmail'];
		$connectedclt_fact_civ = $result_client_data['factciv'];
		$connectedclt_fact_nom = $result_client_data['factnom'];
		$connectedclt_fact_prenom = $result_client_data['factpre'];
		$connectedclt_fact_adr = $result_client_data['factadr'];
		$connectedclt_fact_tel = $result_client_data['facttel'];
		$connectedclt_fact_port = $result_client_data['factport'];
		$connectedclt_fact_zipcode = $result_client_data['factzipcode'];
		$connectedclt_fact_ville = $result_client_data['factville'];
		$connectedclt_fact_pays = $result_client_data['factpays'];
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
			Cette interface vous permettra de mettre à jours l'ensemble des données (nom, prénom, adresse relatives à la facturation des achats du compte <?php echo $mailclt; ?>. 
			<br>
			Nous vous recommandons vivement de prendre le temps de remplir tout les champs et ce afin que nous puissions vous fournir un meilleur service.
			<br>
			Remarque : Le changement de vos données n'affectera en aucun cas vos documents comptables déjà générés tel que les factures et les bons de retour.
		</div>
    </div>

    <div class="row">
	    <div class="col-12 pt-3 pb-3">
			<h2 class="TXT">Coordonnées de la société</h2>
		</div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 pb-3">
            Société
            <input type="text" name="raisonsociale" class="subscribe" value="<?php echo $connectedclt_ste; ?>" disabled="disabled"/> 
        </div>
        <div class="col-12 col-md-6 pb-3">
            Siret
            <input type="text" name="siret" class="subscribe" value="<?php echo $connectedclt_siret; ?>" disabled="disabled"/>
        </div>
    </div>
    
    <div class="row">
    	<div class="col-12 pt-3 pb-3">
			<h2 class="TXT">Coordonnées personnelles</h2>
		</div>
    </div>
    
    <div class="row">
        <div class="col-3 col-md-2 pb-3">
            Civilité
            <select name="civ" class="subscribe">
                <option <?php if ($connectedclt_fact_civ=="Mr.") { ?> selected="selected" <?php } ?> >Mr.</option>
				<option <?php if ($connectedclt_fact_civ=="Mlle.") { ?> selected="selected" <?php } ?> >Mlle.</option>
				<option <?php if ($connectedclt_fact_civ=="Mme.") { ?> selected="selected" <?php } ?> >Mme.</option>
            </select>
        </div>
        <div class="col-9 col-md-5 pb-3">
            Nom *
            <input type="text" name="nom" class="subscribe" value="<?php echo $connectedclt_fact_nom; ?>" required autocomplete="off"/>
        </div>
        <div class="col-12 col-md-5 pb-3">
            Prénom
            <input type="text" name="prenom" class="subscribe" value="<?php echo $connectedclt_fact_prenom; ?>" autocomplete="off"/>
        </div>
        <div class="col-12 pb-3">
            Email
            <input type="text" name="mail" class="subscribe" value="<?php echo $connectedclt_fact_mail; ?>" disabled="disabled"/>
          
        </div>
        <div class="col-12 col-md-6 pb-3">
            Adresse *
            <input type="text" name="adr" class="subscribe" value="<?php echo $connectedclt_fact_adr; ?>" required autocomplete="off"/>
          
        </div>
        <div class="col-12 col-md-2 pb-3">
            Code postal *
            <input type="number" name="zipcode" class="subscribe" value="<?php echo $connectedclt_fact_zipcode; ?>" required autocomplete="off"/>
        </div>
        <div class="col-12 col-md-2 pb-3">
            Ville *
            <input type="text" name="ville" class="subscribe" value="<?php echo $connectedclt_fact_ville; ?>" required autocomplete="off"/>
        </div>
        <div class="col-12 col-md-2 pb-3">
            Pays *
            <select name="pays" class="subscribe">
              <option <?php if ($connectedclt_fact_pays=="France") { ?> selected="selected" <?php } ?> >France</option>
            </select>
        </div>
        <div class="col-12 col-md-6 pb-3">
            Téléphone
            <input type="number" name="tel" class="subscribe" value="<?php echo $connectedclt_fact_tel; ?>" autocomplete="off"/>
        </div>
        <div class="col-12 col-md-6 pb-3">
            Gsm *
            <input type="number" name="gsm" class="subscribe" value="<?php echo $connectedclt_fact_port; ?>" required autocomplete="off"/>
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