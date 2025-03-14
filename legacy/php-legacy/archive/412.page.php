<?php 
// parametres relatifs à la page (niveau de la page)
$typefile="412";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');



if($pageDisabled=="z")
{
header("HTTP/1.1 200 OK");
$codePage = 412;

    $query_412 = "SELECT PG_NAME, PG_NAME_META 
        FROM PIECES_GAMME 
        WHERE PG_ID = $pg_id";
    $request_412 = $conn->query($query_412);
    $result_412 = $request_412->fetch_assoc();
        // PG DATA
        $pg_name_site = $result_412['PG_NAME'];
        $pg_name_meta = $result_412['PG_NAME_META'];
        // SEO
        $pagetitle=$pg_name_meta." neuf & à prix bas";
        $pagedescription="Votre ".$pg_name_meta." au meilleur tarif, de qualité & à prix pas cher pour toutes marques et modèles de voitures.";
        $pageh1txt=$pg_name_site;
        $pageh1txtComp="Cette page est temporairement indisponible";
        $pagecontenttxt="Vous êtes à la recherche d'un <b>".$pg_name_site."</b> d'origine et de qualité irréprochable ? Avec  ".$domainwebsitename.", tous vos <b>".$pg_name_site."</b> au meilleur tarif sont disponibles dans toutes les références, proposés par les grands équipementiers de pièces détachées automobiles.";
        $robots="noindex, nofollow";
}
if($pageDisabled=="mq")
{
header("HTTP/1.1 200 OK");
$codePage = 412;

    $query_412 = "SELECT MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE 
        FROM AUTO_MARQUE 
        WHERE MARQUE_ID = $marque_id";
    $request_412 = $conn->query($query_412);
    $result_412 = $request_412->fetch_assoc();
        // MARQUE DATA
        $marque_name_site = $result_412['MARQUE_NAME'];
        $marque_name_meta = $result_412['MARQUE_NAME_META'];
        $marque_name_meta_title = $result_412['MARQUE_NAME_META_TITLE'];
        // SEO
        $pagetitle="Pièces ".$marque_name_meta_title." neuves & d'origine";
        $pagedescription="Retrouvez tous vos pièces détachées et accessoires pour la ".$marque_name_meta." avec un choix énorme à prix réduit. Commandez pièces auto ";
        $pageh1txt=$marque_name_site;
        $pageh1txtComp="Cette page est temporairement indisponible";
        $pagecontenttxt="Un vaste choix de pièces détachées <b>".$marque_name_site."</b> au meilleur tarif et de qualité irréprochable proposées par les grandes marques d'équipementiers automobile comme Bosch, Valeo, Hella, SKF, Magneti Marelli, Bosal, Hutchinson, Gates, Dayco et autres marques de première monte d'origine. Consultez notre catalogue de pièces auto <b>".$marque_name_site."</b> proposé par ".$domainwebsitename." et choisissez les pièces compatibles comme : kit distribution, kit embrayage, alternateur, démarreur, échappement et autres pièces nécessaires pour la marque <b>".$marque_name_site."</b>.";
        $robots="noindex, nofollow";
}
if($pageDisabled=="ty")
{
header("HTTP/1.1 410 Gone");
$codePage = 410;

    $query_412 = "SELECT TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, TYPE_BODY, TYPE_FUEL, 
        TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, TYPE_RELFOLLOW, 
        MODELE_NAME, MODELE_NAME_META, 
        MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, 
        MARQUE_ALIAS, MARQUE_RELFOLLOW
        FROM AUTO_TYPE 
        JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
        JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
        WHERE TYPE_ID = $type_id
        AND TYPE_MODELE_ID = $modele_id 
        AND TYPE_MARQUE_ID = $marque_id ";
    $request_412 = $conn->query($query_412);
    $result_412 = $request_412->fetch_assoc();
        // TYPE DATA
        $type_name_site = $result_412['TYPE_NAME'];
        $type_name_meta = $result_412['TYPE_NAME_META'];
        $type_date = "";
        if(empty($result_412['TYPE_YEAR_TO']))
        {
        $type_date = "du ".$result_412['TYPE_MONTH_FROM']."/".$result_412['TYPE_YEAR_FROM'];
        }
        else
        {
        $type_date = "de ".$result_412['TYPE_YEAR_FROM']." à ".$result_412['TYPE_YEAR_TO'];
        }
        $type_nbch = $result_412['TYPE_POWER_PS'];
        // MODELE DATA
        $modele_name_site = $result_412['MODELE_NAME'];
        $modele_name_meta = $result_412['MODELE_NAME_META'];
        // MARQUE DATA
        $marque_name_site = $result_412['MARQUE_NAME'];
        $marque_name_meta = $result_412['MARQUE_NAME_META'];
        $marque_name_meta_title = $result_412['MARQUE_NAME_META_TITLE'];
        $marque_alias = $result_412["MARQUE_ALIAS"];
        $marque_relfollow = $result_412["MARQUE_RELFOLLOW"];
        if($marque_relfollow==1)
            { $goGetMarque = "<a href='".$domain."/".$Auto."/".$marque_alias."-".$marque_id.".html'><b>".$marque_name_site."</b></a>"; }
            else
            { $goGetMarque = $marque_name_site; }
        // SEO
        $pagetitle="Pièces auto ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch (".$type_date.")";
        $pagedescription="Retrouvez notre catalogue de pièces auto pour la  ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch (".$type_date.") au meilleur tarif. Commandez vos pièces détachées ".$marque_name_meta." ".$modele_name_meta." en toute sécurité, livraison 24/48H. ";
        $pageh1txt=$marque_name_site." ".$modele_name_site." ".$type_name_site."  ".$type_nbch." ch (".$type_date.")";
        $pageh1txtComp="Cette page est temporairement indisponible";
        $pagecontenttxt="Vous disposez d'un catalogue varié de toutes les pièces détachées pour votre ".$goGetMarque." ".$modele_name_site." ".$type_name_site."  ".$type_nbch." ch (".$type_date."), disponibles dans les grandes marques d'équipementiers comme Bosch, Valeo, SKF, Continental, Gates, LUK, Dayco... etc.";
        $robots="noindex, nofollow";
}
if($pageDisabled=="p")
{

    //echo $gamme_display; echo $car_display; 
    //GAMME
    $query_pg_412 = "SELECT PG_NAME, PG_NAME_META, PG_ALIAS, PG_DISPLAY, PG_RELFOLLOW 
        FROM PIECES_GAMME 
        WHERE PG_ID = $pg_id";
    $request_pg_412 = $conn->query($query_pg_412);
    $result_pg_412 = $request_pg_412->fetch_assoc();
        // PG DATA
        $pg_name_site = $result_pg_412['PG_NAME'];
        $pg_name_meta = $result_pg_412['PG_NAME_META'];
        $pg_alias = $result_pg_412["PG_ALIAS"];
        $pg_display = $result_pg_412["PG_DISPLAY"];
        $pg_relfollow = $result_pg_412["PG_RELFOLLOW"];
        if($pg_display==1)
            { $goGetGamme = "<a href='".$domain."/".$Piece."/".$pg_alias."-".$pg_id.".html'><b>".$pg_name_site."</b></a>"; }
            else
            { $goGetGamme = $pg_name_site; }
    // CAR
    $query_car_412 = "SELECT TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, TYPE_BODY, TYPE_FUEL, 
        TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, TYPE_RELFOLLOW, 
        MODELE_NAME, MODELE_NAME_META, TYPE_ALIAS, TYPE_DISPLAY, 
        MODELE_ALIAS, 
        MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, 
        MARQUE_ALIAS, MARQUE_RELFOLLOW
        FROM AUTO_TYPE 
        JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
        JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
        WHERE TYPE_ID = $type_id
        AND TYPE_MODELE_ID = $modele_id 
        AND TYPE_MARQUE_ID = $marque_id ";
    $request_car_412 = $conn->query($query_car_412);
    $result_car_412 = $request_car_412->fetch_assoc();
        // TYPE DATA
        $type_name_site = $result_car_412['TYPE_NAME'];
        $type_name_meta = $result_car_412['TYPE_NAME_META'];
        $type_date = "";
        if(empty($result_car_412['TYPE_YEAR_TO']))
        {
        $type_date = "du ".$result_car_412['TYPE_MONTH_FROM']."/".$result_car_412['TYPE_YEAR_FROM'];
        }
        else
        {
        $type_date = "de ".$result_car_412['TYPE_YEAR_FROM']." à ".$result_car_412['TYPE_YEAR_TO'];
        }
        $type_nbch = $result_car_412['TYPE_POWER_PS'];
        $type_display = $result_car_412["TYPE_DISPLAY"];
        $type_alias = $result_car_412["TYPE_ALIAS"];
        // MODELE DATA
        $modele_name_site = $result_car_412['MODELE_NAME'];
        $modele_name_meta = $result_car_412['MODELE_NAME_META'];
        $modele_alias = $result_car_412["MODELE_ALIAS"];
        // MARQUE DATA
        $marque_name_site = $result_car_412['MARQUE_NAME'];
        $marque_name_meta = $result_car_412['MARQUE_NAME_META'];
        $marque_name_meta_title = $result_car_412['MARQUE_NAME_META_TITLE'];
        $marque_alias = $result_car_412["MARQUE_ALIAS"];
        $marque_relfollow = $result_car_412["MARQUE_RELFOLLOW"];
        if($type_display==1)
            { 
                $goGetTYPE = "<a href='".$domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html'><b>".$marque_name_site." ".$modele_name_site." ".$type_name_site."</b></a>"; 
                // ENTETE PAGE
                header("HTTP/1.1 200 OK");
                $codePage = 412;
            }
            else
            { 
                $goGetTYPE = $marque_name_site." ".$modele_name_site." ".$type_name_site; 
                // ENTETE PAGE
                header("HTTP/1.1 410 Gone");
                $codePage = 410;
            }
    // SEO
    $pagetitle=$pg_name_meta." ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date;
	$pagedescription="Achetez ".$pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date.", d'origine à prix bas.";
	$pageh1txt=$pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
	$pageh1txtComp="Cette page est temporairement indisponible";
	$pagecontenttxt="Le(s) ".$pg_name_site." de la ".$goGetTYPE." ".$type_nbch." ch ".$type_date." sont disponible sur Automecanik à un prix pas cher. <br> Identifiez le(s) ".$goGetGamme." compatible avec votre ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch en suivant les plans d'entretien du constructeur ".$marque_name_site." pour les périodes de contrôle et de remplacement du ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.<br> Lors du remplacement de la pièce nous vous conseillons de contrôler l'état d'usure des composants et des organes liés de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.";
    $robots="noindex, nofollow";
}
if($pageDisabled=="p0")
{
header("HTTP/1.1 200 OK");
$codePage = 412;

    //echo $gamme_display; echo $car_display; 
    //GAMME
    $query_pg_412 = "SELECT PG_NAME, PG_NAME_META, PG_ALIAS, PG_RELFOLLOW 
        FROM PIECES_GAMME 
        WHERE PG_ID = $pg_id";
    $request_pg_412 = $conn->query($query_pg_412);
    $result_pg_412 = $request_pg_412->fetch_assoc();
        // PG DATA
        $pg_name_site = $result_pg_412['PG_NAME'];
        $pg_name_meta = $result_pg_412['PG_NAME_META'];
        $pg_alias = $result_pg_412["PG_ALIAS"];
        $pg_relfollow = $result_pg_412["PG_RELFOLLOW"];
        if($pg_relfollow==1)
            { $goGetGamme = "<a href='".$domain."/".$Piece."/".$pg_alias."-".$pg_id.".html'><b>".$pg_name_site."</b></a>"; }
            else
            { $goGetGamme = $pg_name_site; }
    // CAR
    $query_car_412 = "SELECT TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, TYPE_BODY, TYPE_FUEL, 
        TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, TYPE_ALIAS, TYPE_RELFOLLOW, 
        MODELE_NAME, MODELE_NAME_META, 
        MODELE_ALIAS, MODELE_RELFOLLOW, 
        MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, 
        MARQUE_ALIAS, MARQUE_RELFOLLOW 
        FROM AUTO_TYPE 
        JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
        JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
        WHERE TYPE_ID = $type_id
        AND TYPE_MODELE_ID = $modele_id 
        AND TYPE_MARQUE_ID = $marque_id ";
    $request_car_412 = $conn->query($query_car_412);
    $result_car_412 = $request_car_412->fetch_assoc();
        // TYPE DATA
        $type_name_site = $result_car_412['TYPE_NAME'];
        $type_name_meta = $result_car_412['TYPE_NAME_META'];
        $type_date = "";
        if(empty($result_car_412['TYPE_YEAR_TO']))
        {
        $type_date = "du ".$result_car_412['TYPE_MONTH_FROM']."/".$result_car_412['TYPE_YEAR_FROM'];
        }
        else
        {
        $type_date = "de ".$result_car_412['TYPE_YEAR_FROM']." à ".$result_car_412['TYPE_YEAR_TO'];
        }
        $type_nbch = $result_car_412['TYPE_POWER_PS'];
        $type_alias = $result_car_412["TYPE_ALIAS"];
        $type_relfollow = $result_car_412["TYPE_RELFOLLOW"];
        // MODELE DATA
        $modele_name_site = $result_car_412['MODELE_NAME'];
        $modele_name_meta = $result_car_412['MODELE_NAME_META'];
        $modele_alias = $result_car_412["MODELE_ALIAS"];
        $modele_relfollow = $result_car_412["MODELE_RELFOLLOW"];
        // MARQUE DATA
        $marque_name_site = $result_car_412['MARQUE_NAME'];
        $marque_name_meta = $result_car_412['MARQUE_NAME_META'];
        $marque_name_meta_title = $result_car_412['MARQUE_NAME_META_TITLE'];
        $marque_alias = $result_car_412["MARQUE_ALIAS"];
        $marque_relfollow = $result_car_412["MARQUE_RELFOLLOW"];
        if(($marque_relfollow==1)&&($modele_relfollow==1)&&($type_relfollow==1))
            { $goGetMarque = "<a href='".$domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html'><b>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch</b></a>"; }
            else
            { $goGetMarque = $marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch"; }
    // SEO
    $pagetitle=$pg_name_meta." ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date;
	$pagedescription="Achetez ".$pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date.", d'origine à prix bas.";
	$pageh1txt=$pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
	$pageh1txtComp="Cette combinaison ne contient aucun article pour le moment";
	$pagecontenttxt="Le(s) ".$pg_name_site." de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date." sont disponible sur Automecanik à un prix pas cher. <br> Identifiez le(s) ".$goGetGamme." compatible avec votre ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch en suivant les plans d'entretien du constructeur ".$marque_name_site." pour les périodes de contrôle et de remplacement du ".$pg_name_site." de la ".$goGetMarque.".<br> Lors du remplacement de la pièce nous vous conseillons de contrôler l'état d'usure des composants et des organes liés de la ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch.";
    $robots="noindex, nofollow";
}
?>
<!doctype html>
<html amp lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="robots" content="<?php  echo $robots; ?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<!-- CSS -->
<style>
@import url('https://fonts.googleapis.com/css?family=Oswald:300,400,700&display=swap');
</style>
<link href="/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="/assets/css/<?php echo $hr;?>.css" rel="stylesheet" media="all">
</head>
<body>

<div class="container-fluid PAGE-410-CONTAINER">
<div class="container-fluid PAGE-410-CONTAINER-IN">

  <div class="row">
    <div class="col-md-5 text-right PAGE-410-FLAG">
    		<?php echo $codePage; ?> /
    </div>
    <div class="col-md-7 PAGE-410-CONTENT">
    		
    		<div class="row">
    			<div class="col-md-12 PAGE-410-CONTENT-TITLE">
    				<h1 class="PAGE-410"><?php echo $pageh1txt; ?></h1>
    				<br><?php echo $pageh1txtComp; ?>
    			</div>
    			<div class="col-md-12 PAGE-410-CONTENT-TXT">
    				<?php echo $pagecontenttxt; ?>
    			</div>
    		</div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8 text-right PAGE-410-SUGGEST-FLAG">
    		/
    </div>
    <div class="col-md-4 PAGE-410-SUGGEST-CONTENT">
    		
    		<div class="row">
    			<div class="col-md-12 PAGE-410-SUGGEST-CONTENT-TITLE">
    				Oooopss, où voulez vous aller ?
    			</div>
    			<div class="col-md-12 PAGE-410-SUGGEST-CONTENT-TXT">
    				<a href="<?php echo $domain; ?>/"><?php echo $domainwebsitename; ?></a>
    				<br>
    				<a href="<?php echo $domain; ?>/<?php echo $blog; ?>/"><?php echo $blog_arianetitle; ?></a>
    			</div>
    		</div>
            
    </div>
  </div>

</div>
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