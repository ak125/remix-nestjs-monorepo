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
<!-- favicon -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.space.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('v7.global.header.section.php');
?>
<div class="container-fluid containerBanner">
    <div class="container-fluid mymaxwidth">

    	<div class="row d-flex flex-row-reverse">
			<div class="col-12 col-sm align-self-center">

				<h1><?php echo $pageh1; ?></h1>
				<div class="containerariane">
					<?php
					// fichier de recuperation et d'affichage des parametres de la base de données
					require_once('config/ariane.conf.php');
					?>
				</div>

			</div>
		</div>

    </div>
</div>

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

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
                      <div class="col-12">
                      <h2>Déjà Client sur Automecanik.com</h2>
                      <div class="divh2"></div>
                    </div>
                      <div class="col-12">
                      Connectez-vous sur votre espace client et payez en toute sécurité votre panier d'achat. Livraison express en 24/48H.
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-12 pt-3 pb-3">
                            Email *
                            <input type="email" name="requestedlog" class="myconnect" required autocomplete="off"/> 
                        </div>
                        <div class="col-12 pb-3">
                            Mot de passe
                            <input type="password" name="requestedmp" class="myconnect" required autocomplete="off"/>
                        </div>
                        <div class="col-12 text-center pb-3">
                            <input type="submit" class="myvalidate" value="Connectez-vous" />
                            <input type="hidden" name="ASK2CONNECT" value="1">
                            <input type="hidden" name="ASK2CONNECTLINK" value="<?php echo $ask2connectResponseLinkCode; ?>">   
                        </div>
                    </div>

                </form>
                <!-- FIN FORMULAIRE -->

                    <div class="row">
                        <div class="col-12 pb-3 text-center text-md-left">
                            <button type="button" class="btn btn-primary myforget" data-toggle="modal" data-target="#resetPswd">
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
                        </div>
                    </div>

            <!-- / COL LEFT -->
            </div>
            <div class="col-12 col-md-1 d-none d-md-block">
            </div>
            <div class="col-12 col-md-5">
            <!-- COL RIGHT -->
                
                <div class="row">
                    <div class="col-12">
                    <h2>Nouveau Client sur Automecanik.com</h2>
                    <div class="divh2"></div>
                  </div>
                    <div class="col-12">
                    Créez un compte client et bénéficiez d'un espace personnel afin d'ajouter toutes vos pièces auto commandées en toute sécurité et gérer toutes vos commandes.
                    <br>
                    L'inscription est gratuite. Vous pourrez modifier vos données quand vous le souhaitez.
                  </div>
                    <div class="col-12 pt-3 pb-3 text-center text-md-right">
                    <button class="myconnect" onclick="window.location.href='<?php echo $domain; ?>/inscription.html'">Créer mon profil</button> 
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

    </div>
</div>

<?php
require_once('v7.global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="<?php echo $domain; ?>/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/v7.analytics.track.php');
?>