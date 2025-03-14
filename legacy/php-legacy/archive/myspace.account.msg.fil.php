<?php 
session_start();
// parametres relatifs à la page
$typefile="my";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="msg";
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
<body class="BODY-MODAL">

<div class="container-fluid modalPage w-100">
<?php
if(isset($_SESSION['myaklog'])) // le client est deja connecté
{
// GET CLIENT DATA
    $msg_id = $_GET['msg_id'];
    $ssid = $_SESSION['myakid'];
    $query_msg_data = "SELECT * FROM ___XTR_MSG 
        WHERE MSG_ID = '$msg_id' AND MSG_CST_ID = '$ssid' ";
    $request_msg_data = $conn->query($query_msg_data);
    if ($request_msg_data->num_rows > 0) 
    {
        $result_msg_data = $request_msg_data->fetch_assoc();
        // MSG DATA
        $commande_id = $result_msg_data['MSG_ORD_ID'];
?>
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row">
        <div class="col-12">

                    <div class="row m-0 p-0">
                        <div class="col-12 msg-fil-title p-3">
                        <?php echo strip_tags($result_msg_data['MSG_SUBJECT']); ?>
                        </div>
                        <div class="col-12 msg-fil-content-1 p-3">
                        <?php echo strip_tags($result_msg_data['MSG_CONTENT'],"<br><a><b><p>"); ?>
                        </div>
                    </div>

        </div>
    </div>
    <!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php
    }
    else
    {
?>
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row">
        <div class="col-12 p-3">

                PAS LE DROIT

        </div>
    </div>
    <!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php       
    }
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