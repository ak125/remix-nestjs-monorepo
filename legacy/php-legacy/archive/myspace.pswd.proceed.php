<?php 
session_start();
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="connexion-inscription-reset";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

if($_GET['md5mail'])
{
    // RECUPERATION CODE MAIL
    $md5mail = $_GET['md5mail'];
    // GET MAIL FROM DATABASE
    $query_get_password = "SELECT * from ___XTR_CUSTOMER where md5(CST_MAIL) = '$md5mail'";
    $request_get_password = $conn->query($query_get_password);
    if ($request_get_password->num_rows > 0) 
    {
        $rResetPswd = $request_get_password->fetch_assoc();
        $resetClt = $rResetPswd['CST_ID'];
        if(isset($_POST['actionmyakChange']))
        {
            $mptochangeto = $_POST['mptochangeto'];
            $mptochangetoconf = $_POST['mptochangetoconf'];
            if(($mptochangeto!="")&&($mptochangetoconf!="")&&($mptochangeto==$mptochangetoconf))
            {
                $CryptPass = crypt(md5($mptochangeto),"im10tech7");
                $UpdateQuery = "UPDATE ___XTR_CUSTOMER SET CST_PSWD = '$CryptPass' WHERE CST_ID = $resetClt ";
                if($conn->query($UpdateQuery) === TRUE)
                {
                    $UpdateResponse = "Votre mot de passe a été mis à jours avec succès, veuillez vous reconnecter sur 
                    votre espace client.";
                    $UpdateResponseCode = 1;
                }
                else
                {
                    $UpdateResponse = "Une erreur est survenue, merci de réessayer plus tard.";
                    $UpdateResponseCode = 2;
                }    
            }
            else
            {
                $UpdateResponse = "Vérifier le nouveau mot de passe et sa confirmation.";
                $UpdateResponseCode = 2;   
            }
        }
    }
    else
    {
        $UpdateResponse = "Cet email n'existe pas.";
        $UpdateResponseCode = 2;
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
<link rel="canonical" href="<?php echo $domain; ?>/pswd.proceed.html">
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
    <div class="col-12">
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
	if($UpdateResponseCode==1)
	{
	?>  
	<!-- VALIDATION --> 
	<div class="row">
	<div class="col-lg-12 text-center">
		<div class="response-green">Changement mot de passe réussit</div>
	</div>
	</div>
	<div class="row">
	<div class="col-lg-12 text-center response-validation">
		Bienvenue :
		<br>
		Vous allez être rediriger pour vous connectez dans quelques secondes, merci de patientez.
		<br>
		Si cette opération dure trop longtemps, merci de <a target="_parent" href="<?php echo $domain; ?>/connexion.html">Cliquez ici pour être rediriger</a>.
		<meta http-equiv="refresh" content="3; URL=<?php echo $domain; ?>/connexion.html">
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

        <div class="row">
        <div class="col-lg-12 text-center">
        <?php
        if($UpdateResponseCode==2)
        {
        ?>
        <div class="response-red"><?php echo $UpdateResponse ;?></div>
        <?php
        }
        ?>
        </div>
        </div>

        <!-- FORMULAIRE -->     
        <form action="" method="post" role="form">

            <div class="row">
                <div class="col-12 pt-3 pb-3">
                    Nouveau mot de passe *
                    <input type="password" name="mptochangeto" class="subscribe" required autocomplete="off"/> 
                </div>
                <div class="col-12 pb-3">
                    Confirmer votre nouveau mot de passe *
                    <input type="password" name="mptochangetoconf" class="subscribe" required autocomplete="off"/>
                </div>
                <div class="col-12 text-center pb-3">
                    <input type="submit" class="subscribeSubmit" value="Modifier mon mot de passe" />
                    <input type="hidden" name="actionmyakChange" value="doit" /> 
                </div>
            </div>

        </form>
        <!-- FIN FORMULAIRE -->

    <!-- / COL LEFT -->
    </div>
    <div class="col-12 col-md-1 d-none d-md-block">
    </div>
    <div class="col-12 col-md-5">
    <!-- COL RIGHT -->
        <div class="row">
            <div class="col-12 pt-3 pb-3">
                Saisissez un nouveau mot de passe. Nous vous recommandons vivement de créer un mot de passe unique, que vous n'utilisez pas pour d'autres sites Web.
                <br>
                Après le changement de votre mot de passe vous devez vous reconnectez de nouveau sur votre espace client.
                <br>
                Remarque : Une fois modifié, vous ne pouvez pas réutiliser votre ancien mot de passe !
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