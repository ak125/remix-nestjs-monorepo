<?php 
session_start();
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="connexion-inscription";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

$ask2connectResponseCode = 0;
$ask2connectResponse = "";
$ask2connectResponseMsg = "";

if(isset($_POST['ASK2CONNECTLINK'])) 
	$ask2connectResponseLinkCode = $_POST['ASK2CONNECTLINK'];
else
	$ask2connectResponseLinkCode = 1;

if(isset($_POST['ASK2CONNECT']))
{

    $requestedlog=$_POST['requestedlog'];
    $requestedmp=$_POST['requestedmp'];
    $requestedmpcrypt = crypt(md5($requestedmp),"im10tech7");

    $query_connect = "SELECT * FROM ___XTR_CUSTOMER 
        WHERE CST_MAIL = '$requestedlog' AND CST_PSWD = '$requestedmpcrypt'";
    $request_connect = $conn->query($query_connect);
    if ($request_connect->num_rows > 0) 
	{
		$result_connect = $request_connect->fetch_assoc();
		if($result_connect["CST_ACTIV"]==1)
		{
			$ask2connectResponseCode = 1;
			$ask2connectResponse = "Bienvenue";
			// test de redirection
			if($ask2connectResponseLinkCode==1) // de connexion.html
			{
			$ask2connectResponseLink=$domainClient2022."/";
			}
			if($ask2connectResponseLinkCode==7) // de validation.html
			{
			$ask2connectResponseLink="validation.html";
			}
			// CREATE SESSION
				// mail
				$_SESSION['myaklog']=$requestedlog;
				//identifiant
				$_SESSION['myakid']=$result_connect['CST_ID'];
				// numero session
				//$_SESSION['myakssid']=$spkey;
				// civilite
				$_SESSION['myakciv']=$result_connect['CST_CIVILITY'];
				// nom
				$_SESSION['myaknom']=$result_connect['CST_NAME'];
				// prenom
				$_SESSION['myakprenom']=$result_connect['CST_FNAME'];
			// FIN CREATE SESSION
		}
		else
		{
		$ask2connectResponseCode = 2;
		$ask2connectResponse = "Compte suspendu";
		$ask2connectResponseMsg .= "Votre compte n'est pas encore activé !!";
		}
	}
	else
    {
		$ask2connectResponseCode = 2;
		$ask2connectResponse = "Merci de vérifier vos données.";
		$ask2connectResponseMsg .= "Merci de vérifier votre Login et/ou Mot de passe !!";
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
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12">           
	<?php
	if($ask2connectResponseCode==1)
	{
	?>  
	<!-- VALIDATION --> 
	<div class="row">
	<div class="col-lg-12 text-center">
		<div class="response-green">Connexion réussite</div>
	</div>
	</div>
	<div class="row">
	<div class="col-lg-12 text-center response-validation">
		Bienvenue : <?php echo $_SESSION['myakciv']." ".$_SESSION['myaknom']." ".$_SESSION['myakprenom']; ?>
		<br>
		Vous allez être rediriger dans quelques secondes, merci de patientez.
		<br>
		Si cette opération dure trop longtemps, merci de <a target="_parent" href="<?php echo $ask2connectResponseLink; ?>">Cliquez ici pour être rediriger</a>.
		<meta http-equiv="refresh" content="3; URL=<?php echo $ask2connectResponseLink; ?>">
	</div>
	</div>
	<!-- / VALIDATION -->
	<?php
	}
	else
	{
	?>
	<div class="row">
	<div class="col-12 col-md-6">
	<!-- COL LEFT -->
	
			<!-- FORMULAIRE -->     
			<form action="" method="post" role="form">
			    
			    <div class="row">
			      <div class="col-lg-12 text-center">
			        <?php
		            if($ask2connectResponseCode==2)
		            {
		              ?>
		              <div class="response-red"><b><?php echo $ask2connectResponse ;?></b>
		                <br><span><?php echo $ask2connectResponseMsg; ?></span></div>
		              <?php
		            }
			        ?>
			      </div>
			    </div>

			    <div class="row">
				    <div class="col-12 pt-3 pb-3">
						<h2 class="TXT">Déjà Client sur Automecanik.com</h2>
					</div>
				    <div class="col-12 pt-3 pb-3">
						Connectez-vous sur votre espace client et payez en toute sécurité votre panier d'achat. Livraison express en 24/48H.
					</div>
			    </div>

			    <div class="row">
			        <div class="col-12 pt-3 pb-3">
			            Email *
			            <input type="email" name="requestedlog" class="subscribe" required autocomplete="off"/> 
			        </div>
			        <div class="col-12 pb-3">
			            Mot de passe
			            <input type="password" name="requestedmp" class="subscribe" required autocomplete="off"/>
			        </div>
			        <div class="col-12 text-center pb-3">
			            <input type="submit" class="subscribeSubmit" value="Connectez-vous" />
			            <input type="hidden" name="ASK2CONNECT" value="1">
			            <input type="hidden" name="ASK2CONNECTLINK" value="<?php echo $ask2connectResponseLinkCode; ?>">   
			        </div>
			    </div>

			</form>
			<!-- FIN FORMULAIRE -->
			<button type="button" class="btn btn-primary" style="padding: 0px; border:0px; background: none; color: #2196f3; font-size: 17px; font-weight: 300; text-decoration: underline; " data-toggle="modal" data-target="#resetPswd">
            Mot de passe oublié ?
          </button>
          <!-- Modal -->
          <div class="modal fade bd-example-modal-lg text-left" id="resetPswd" tabindex="-1" role="dialog" aria-labelledby="resetPswdTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content nopadding">
                <div class="modal-header nopadding">
                  <h5 class="modal-title ONE-MODEL-CONTENT-LIST-MOTORISATION-TITLE" id="resetPswdTitle">Réinitialiser mon mot de passe</h5>
                </div>
                <div class="modal-body">
<iframe width="100%" height="387" frameborder="0" allowtransparency="true" src="<?php echo $domain; ?>/pswd.html"></iframe>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
              </div>
            </div>
          </div>
          <!-- / Modal -->

	<!-- / COL LEFT -->
	</div>
	<div class="col-12 col-md-1 d-none d-md-block">
	</div>
	<div class="col-12 col-md-5">
	<!-- COL RIGHT -->
			
			<div class="row">
			    <div class="col-12 pt-3 pb-3">
					<h2 class="TXT">Nouveau Client sur Automecanik.com</h2>
				</div>
			    <div class="col-12 pt-3 pb-3">
					Créez un compte client et bénéficiez d'un espace personnel afin d'ajouter toutes vos pièces auto commandées en toute sécurité et gérer toutes vos commandes.
					<br>
					L'inscription est gratuite. Vous pourrez modifier vos données quand vous le souhaitez.
				</div>
			    <div class="col-12 pt-3 pb-3 text-center text-md-right">
					<a href="/inscription.html" class="ask-subscribe">Créer mon profil</a>
				</div>
		    </div>
	<!-- / COL RIGHT -->
	</div>
	</div>
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