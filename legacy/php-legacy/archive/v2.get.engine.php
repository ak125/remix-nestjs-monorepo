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


<?php
$engine_code=$_POST["engine_code"];

$pageh1 = "Engine Code Search result : ".$engine_code;
?>

    <div class="container-fluid Page-Welcome-Title">
            <h1><?php echo $pageh1; ?></h1>
            <h2>Search, Select and Buy...</h2>
    </div>
    <div class="container-fluid Page-Welcome-Box">
        
        <div class="row">
            <?php
            $query=mysql_query(" SELECT DISTINCT tcm_type_id, type_alias, type_name_site,
                type_ch, type_kw, type_design, type_carburant, type_boite, type_cylindre, type_volume_cube,
                LEFT(type_date_debut,4) AS type_date_debut,
                RIGHT(type_date_debut,2) AS type_date_debut_mois,
                LEFT(type_date_fin,4) AS type_date_fin,
                modele_id, modele_alias, modele_name_site,
                marque_id, marque_alias, marque_name_site, marque_name_meta, marque_logo
                FROM prod_auto_type_code 
                JOIN $sqltable_Car_type ON type_id = tcm_type_id
                JOIN $sqltable_Car_modele ON modele_id = type_modele_id
                JOIN $sqltable_Car_marque ON marque_id =type_marque_id
                WHERE tcm_code_moteur = '$engine_code' 
                AND type_affiche = 1 AND modele_affiche = 1 AND marque_affiche = 1
                ORDER BY marque_alias, modele_tri, type_id ");
            while($result=mysql_fetch_array($query))
            {
            // marque datas
            $marque_id = $result["marque_id"];
            $marque_alias = $result["marque_alias"];
            $marque_name_site = utf8_encode($result["marque_name_site"]);
            $marque_name_meta = utf8_encode($result["marque_name_meta"]);
            $marque_logo = $result["marque_logo"];
            // modele datas
            $modele_id = $result["modele_id"];
            $modele_alias = $result["modele_alias"];
            $modele_name_site = utf8_encode($result["modele_name_site"]);
            // motorisation datas
            $type_id = $result["tcm_type_id"];
            $type_alias = $result["type_alias"];
            $type_name_site = utf8_encode($result["type_name_site"]);
            // motorisation technical data
            $type_nbch=$result['type_ch'];
            $type_carosserie=utf8_encode($result['type_design']);
            $type_fuel=utf8_encode($result['type_carburant']);
            if(empty($result['type_date_fin']))
            {
            $type_date="du ".$result['type_date_debut_mois']."/".$result['type_date_debut'];
            }
            else
            {
            $type_date="de ".$result['type_date_debut']." à ".$result['type_date_fin'];
            }
            // link to car
            $linkToCar = $domain."/searchcar/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id;
            ?>
            <!-- BOX GRID -->
                <div class="col-6 GRID-BOX">
                    
                    <a href="<?php echo $linkToCar; ?>">
                    <div class="row">
                        <div class="col-2 col-md-1 PAGE-TYPE-CONTAINER-WALL text-center">
                            
                            <img src="<?php echo $domainparent; ?>/upload/constructeurs-automobiles/marques-logos/medium/<?php echo $marque_logo; ?>" alt="<?php echo $marque_name_meta; ?>" class="w-100" />

                        </div>
                        <div class="col-10 col-md-11 PAGE-TYPE-CONTAINER-WALL-DATAS p-3 pl-4 text-left">
                            
<?php echo $marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_fuel." ".$type_nbch." ch ".$type_date;
; ?>

                        </div>
                    </div>
                    </a>

                </div>
            <!-- / BOX GRID -->
            <?php
            }
            ?>
        </div>  

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