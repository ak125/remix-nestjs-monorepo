<?php 
session_start();
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="newmember";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

$ask2signResponseCode = 0;
$ask2signResponse = "";
$ask2signResponseMsg = "";

if(isset($_POST['ASK2SIGN']))
{
    $ask2signResponseCode = 1;

    $mail=$_POST['mail']; 
    if(empty($mail))
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= 'Veuillez mentionner votre E-mail.<br>';
    }
    else
    {
        $query_exist = "SELECT * FROM ___XTR_CUSTOMER WHERE CST_MAIL = '$mail'";
        $request_exist = $conn->query($query_exist);
        if ($request_exist->num_rows > 0) 
		{
			$ask2signResponseCode = 2;
			$ask2signResponse = "Merci de vérifier vos données.";
			$ask2signResponseMsg .= "Vous avez déjà un compte, <a target='_parent' href='/connexion.html'>connectez vous</a> sur votre compte pour effectuer vos achats.<br>";
		}
    }

    $mp=$_POST['mp'];
    if (empty($mp))
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez mentionner votre mot de passe.<br>";
    }

    $mpconf=$_POST['mpconf'];
    if (empty($mpconf))
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez mentionner la confirmation de votre mot de passe.<br>";
    }

    if($mpconf!=$mp)
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez confirmer correctement votre mot de passe.<br>";
    }

    $civ=$_POST['civ'];

    $nom=$_POST['nom'];
    if (empty($nom))
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez mentionner votre nom.";
    }
  
    $prenom=$_POST['prenom'];

    $tel=$_POST['tel'];

    $gsm=$_POST['gsm'];
    if (!empty($gsm))
    {
		if(!(is_numeric($gsm)))
		{
			$ask2signResponseCode = 2;
			$ask2signResponse = "Merci de vérifier vos données.";
			$ask2signResponseMsg .= "Le num&eacute;ro de GSM ne doit contenir que des chiffres.<br>";
		}
    }
    else
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Le num&eacute;ro de GSM doit &ecirc;tre renseign&eacute;.<br>";
    }

	$adr=$_POST['adr'];
	if (empty($adr))
	{
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez mentionner votre adresse.<br>";
	}

	$zipcode=$_POST['zipcode'];
	if (!empty($zipcode))
	{
		if(!(is_numeric($zipcode)))
		{
			$ask2signResponseCode = 2;
			$ask2signResponse = "Merci de vérifier vos données.";
			$ask2signResponseMsg .= "Le code postal ne doit contenir que des chiffres.<br>";
		}
	}
	else
	{
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Le code postal doit &ecirc;tre renseign&eacute;.<br>";
	}

	$ville=$_POST['ville'];
    if (empty($ville))
    {
		$ask2signResponseCode = 2;
		$ask2signResponse = "Merci de vérifier vos données.";
		$ask2signResponseMsg .= "Veuillez mentionner votre ville.<br>";
    }

    $pays=$_POST['pays'];

    if($ask2signResponseCode == 1)
    {

// CREATION DE COMPTE

    // CREATION COMPTE CLIENT
	$mpcryptedmp=$mp;
	$cryptedmp = crypt(md5($mpcryptedmp),"im10tech7");

	$civ= mysqli_real_escape_string($conn, $civ);
	$nom= mysqli_real_escape_string($conn, $nom);
	$prenom= mysqli_real_escape_string( $conn, $prenom);
	$tel= mysqli_real_escape_string($conn, $tel);
	$gsm= mysqli_real_escape_string($conn, $gsm);
	$adr= mysqli_real_escape_string($conn, $adr);
	$zipcode= mysqli_real_escape_string($conn, $zipcode);
	$pays= mysqli_real_escape_string($conn, $pays);
	$mail= mysqli_real_escape_string($conn, $mail);

	$ask2signQuery = "INSERT INTO `___XTR_CUSTOMER` (`CST_ID`, `CST_MAIL`, `CST_PSWD`, `CST_KEYLOG`, `CST_CIVILITY`, `CST_NAME`, `CST_FNAME`, `CST_ADDRESS`, `CST_ZIP_CODE`, `CST_CITY`, `CST_COUNTRY`, `CST_TEL`, `CST_GSM`, `CST_IS_PRO`, `CST_RS`, `CST_SIRET`, `CST_ACTIV`) VALUES (NULL, '$mail', '$cryptedmp', 'CST_KEYLOG', '$civ', '$nom', '$prenom', '$adr', '$zipcode', '$ville', '$pays', '$tel', '$gsm', '0', '', '', '1')";
	if($conn->query($ask2signQuery) === TRUE)
	{
		$ask2signResponseCode = 1;
		$ask2signResponseLinkCode = $_POST['ASK2SIGNLINK'];
		if($ask2signResponseLinkCode==1)
		{
			$ask2signResponseLink="connexion.html";
		}
		if($ask2signResponseLinkCode==7)
		{
			$ask2signResponseLink="validation.html";
		}
		// Last ID
		$customer_id_injected = $conn->insert_id;
		if($customer_id_injected>0)
		{
			// CREATE FACTURATION
			$ask2billingQuery = "INSERT INTO `___XTR_CUSTOMER_BILLING_ADDRESS` (`CBA_ID`, `CBA_CST_ID`, `CBA_MAIL`, `CBA_CIVILITY`, `CBA_NAME`, `CBA_FNAME`, `CBA_ADDRESS`, `CBA_ZIP_CODE`, `CBA_CITY`, `CBA_COUNTRY`, `CBA_TEL`, `CBA_GSM`) VALUES (NULL, '$customer_id_injected', '$mail', '$civ', '$nom', '$prenom', '$adr', '$zipcode', '$ville', '$pays', '$tel', '$gsm')";
			$conn->query($ask2billingQuery);
			// CREATE LIVRAISON
			$ask2deliveryQuery = "INSERT INTO `___XTR_CUSTOMER_DELIVERY_ADDRESS` (`CDA_ID`, `CDA_CST_ID`, `CDA_MAIL`, `CDA_CIVILITY`, `CDA_NAME`, `CDA_FNAME`, `CDA_ADDRESS`, `CDA_ZIP_CODE`, `CDA_CITY`, `CDA_COUNTRY`, `CDA_TEL`, `CDA_GSM`) VALUES (NULL, '$customer_id_injected', '$mail', '$civ', '$nom', '$prenom', '$adr', '$zipcode', '$ville', '$pays', '$tel', '$gsm')";
			$conn->query($ask2deliveryQuery);
		}
	}
	else
	{
		$ask2signResponseCode = 2;
		$ask2signResponse = "Erreur critique.";
		$ask2signResponseMsg .= "Une erreur a surgie lors de la création de compte :<br>".$conn->error;
	}

	// ENVOIE DE MAIL


// FIN CREATION DE COMPTE
	}
}
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
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<h1><?php echo $pageh1; ?></h1>
		</span>
	</div>
	<div class="col-12 pt-3 pb-3">
			<?php echo $pagecontent; ?>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12">           
	<?php
	if($ask2signResponseCode==1)
	{
	?>  
	<!-- VALIDATION --> 
	<div class="row">
	<div class="col-lg-12 text-center">
		<div class="response-green">Inscription réussite</div>
	</div>
	</div>
	<div class="row">
	<div class="col-lg-12 text-center response-validation">
		Bienvenue : <?php echo $civ." ".$nom." ".$prenom; ?>
		<br>
		Vous allez être rediriger dans quelques secondes, merci de patientez.
		<br>
		Si cette opération dure trop longtemps, merci de <a target="_parent" href="<?php echo $ask2signResponseLink; ?>">Cliquez ici pour être rediriger</a>.
		<meta http-equiv="refresh" content="3; URL=<?php echo $ask2signResponseLink; ?>">
	</div>
	</div>
	<!-- / VALIDATION -->
	<?php
	}
	else
	{
	?>
	<!-- FORMULAIRE -->     
	<form action="" method="post" role="form">
	    
	    <div class="row">
	      <div class="col-lg-12 text-center">
	        <?php
            if($ask2signResponseCode==2)
            {
              ?>
              <div class="response-red"><b><?php echo $ask2signResponse ;?></b>
                <br><span><?php echo $ask2signResponseMsg; ?></span></div>
              <?php
            }
	        ?>
	      </div>
	    </div>

	    <div class="row">
		    <div class="col-12 pt-3 pb-3">
				<h2 class="TXT">Données de connexion</h2>
			</div>
	    </div>

	    <div class="row">
	        <div class="col-12 col-md-4 pb-3">
	            Email *
	            <input type="email" name="mail" class="subscribe" value="<?php echo $mail; ?>" required autocomplete="off"/> 
	        </div>
	        <div class="col-12 col-md-4 pb-3">
	            Mot de passe
	            <input type="password" name="mp" class="subscribe" value="" required autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-4 pb-3">
	            Confirmer votre mot de passe *
	            <input type="password" name="mpconf" class="subscribe" value="" required autocomplete="off"/>
	          
	        </div>
	    </div>
	    
	    <div class="row">
	    	<div class="col-12 pt-3 pb-3">
				<h2 class="TXT">Données personnelles</h2>
			</div>
	    </div>
	    
	    <div class="row">
	        <div class="col-3 col-md-2 pb-3">
	            Civilité
	            <select name="civ" class="subscribe">
	                <option <?php if ($civ=="Mr.") { ?> selected="selected" <?php } ?> >Mr.</option>
					<option <?php if ($civ=="Mlle.") { ?> selected="selected" <?php } ?> >Mlle.</option>
					<option <?php if ($civ=="Mme.") { ?> selected="selected" <?php } ?> >Mme.</option>
	            </select>
	        </div>
	        <div class="col-9 col-md-5 pb-3">
	            Nom *
	            <input type="text" name="nom" class="subscribe" value="<?php echo $nom; ?>" required autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-5 pb-3">
	            Prénom
	            <input type="text" name="prenom" class="subscribe" value="<?php echo $prenom; ?>" autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-6 pb-3">
	            Adresse *
	            <input type="text" name="adr" class="subscribe" value="<?php echo $adr; ?>" required autocomplete="off"/>
	          
	        </div>
	        <div class="col-12 col-md-2 pb-3">
	            Code postal *
	            <input type="text" name="zipcode" maxlength="5" class="subscribe" value="<?php echo $zipcode; ?>" required autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-2 pb-3">
	            Ville *
	            <input type="text" name="ville" class="subscribe" value="<?php echo $ville; ?>" required autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-2 pb-3">
	            Pays *
	            <select name="pays" class="subscribe">
                  <option <?php if ($pays=="France") { ?> selected="selected" <?php } ?> >France</option>
                </select>
	        </div>
	        <?php /*div class="col-12 col-md-6 pb-3">
	            Téléphone
	            <input type="number" name="tel" class="subscribe" value="<?php echo $tel; ?>" autocomplete="off"/>
	        </div */ ?>
	        <div class="col-12 col-md-6 pb-3">
	            Gsm *
	            <input type="text" name="gsm" minlength="6" class="subscribe" value="<?php echo $gsm; ?>" required autocomplete="off"/>
	        </div>
	        <div class="col-12 col-md-6 pb-3 text-center text-sm-right">
	            
	            <br><input type="submit" class="subscribeSubmit" value="Créer mon compte" />
	            <input type="hidden" name="ASK2SIGN" value="1">
	            <?php 
	            $amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']); 
	            if($amcnkCart_count>0) // PANIER REMPLI
	            	$ask2signResponseLinkCodeToGo = 7;
	            else
	            	$ask2signResponseLinkCodeToGo = 1;
	            ?>
	            <input type="hidden" name="ASK2SIGNLINK" value="<?php echo $ask2signResponseLinkCodeToGo; ?>">

	        </div>
	    </div>
	    
	    <?php /*div class="row">
	        <div class="col-12 text-center text-sm-right">
	            <input type="submit" class="subscribeSubmit" value="Créer mon compte" />
	            <input type="hidden" name="ASK2SIGN" value="1">
	            <?php 
	            $amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']); 
	            if($amcnkCart_count>0) // PANIER REMPLI
	            	$ask2signResponseLinkCodeToGo = 7;
	            else
	            	$ask2signResponseLinkCodeToGo = 1;
	            ?>
	            <input type="hidden" name="ASK2SIGNLINK" value="<?php echo $ask2signResponseLinkCodeToGo; ?>">   
	        </div>
	    </div */ ?>

	</form>
	<!-- FIN FORMULAIRE -->
	<?php
	}
	?>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

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