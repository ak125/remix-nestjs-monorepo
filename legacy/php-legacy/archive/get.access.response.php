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


<div class="row m-0">
    <div class="col-12 col-lg-6 MASSDOC-LEFT-PANEL">




    <div class="row">
        <div class="col-12 MASSDOC-WELCOME-PANEL">
            welcome to the massdoc
            <br><span>reseller plateform</span>
            <br>
            <p>This is a secure system and you will need to provide your login information to access the site.</p>

        </div>
        <div class="col-12 CONNECTION-PANEL">

            <div class="row">
                    <div class="col-12 GET-ACCESS-TITLE text-center">

                          <?php echo $domainname; ?>
                          <br>Server response : Access <u><?php echo $destinationLinkMsg; ?></u>

                    </div>
                    <div class="col-12 GET-ACCESS-CONTENT">



<?php
if($destinationLinkGranted==1)
{
?>
Welcome : <?php echo utf8_encode($reslog["prenom"]); ?><br><br>
You will be redirected to your administration area in a few seconds, please wait. 
<?php
}
else
{
?>
Blocking error :<br><br>
You will be redirected to your login interface in a few seconds. 
<?php
}
?>
If this operation lasts too long, please <a target="_parent" href="<?php echo $destinationLink; ?>" class="MASSDOC-REDIRECT"> Click here to be redirected</a>.
<!--meta http-equiv="refresh" content="3; URL=< ?php echo $destinationLink; ?>"-->

                    </div>
                    <div class="col-12">

                          

                    </div>
                  </div>

        </div>
    </div>




    </div>
    <div class="col-6 MASSDOC-RIGHT-PANEL p-0 d-none d-lg-block">



<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/next100.jpg" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/bmw.jpg" alt="Second slide">
    </div>
  </div>
</div>

<div class="MASSDOC-LOGO-PANEL">
<img src="<?php echo $domain; ?>/assets/img/mini-logo-icon-gray-connect.png" />
</div>


    </div>
</div> 

<?Php
// ENTETE DE PAGE
//@require_once('footer.last.section.php');
// FIN ENTETE DE PAGE
?>
</body>
</html>