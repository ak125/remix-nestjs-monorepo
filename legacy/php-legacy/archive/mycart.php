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

  <div class="container-fluid Page-Welcome-Title">
      <h1>My Cart</h1>
      <h2>Search, Select and Buy...</h2>
  </div>
  <div class="container-fluid Page-Welcome-Box">
    



<div class="row">
<div class="col-12 GRID-BOX p-3">
<?php
// affichage du contenu du panier
$nbartonCart = count($_SESSION['panier']['id_article']);
if($nbartonCart==0) // mon panier est vide
{

?>
<div class="row">
  <div class="col-12 PAGE-STANDARD-RIGHT-CONTAINER-FORM">

        <div class="row">
          <div class="col-md-12 text-left"> 

        <div class="row">
            <div class="col-12 text-center">
                
                VOTRE PANIER EST VIDE 
                <br>
                Pour commencer vos achats, c'est par ici...

            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">

                
                    <a href="<?php  echo $domain; ?>/welcome" style="color:#243238;"><strong><span style="color:#e82042;">&lt;</span>&nbsp; commencer mes achats</strong></a>
                

            </div>
        </div>

          </div>
        </div>
  </div>
</div>

<?php

}
else // mon panier contient des articles
{

?>
<div class="row">
  <div class="col-12 PAGE-STANDARD-RIGHT-CONTAINER-FORM">

         
<!-- FORMULAIRE -->
<form action="<?php echo $domain; ?>/cart/validation" method="post" role="form">       

    <!-- MON PANIER -->
    <div class="row">
      <div class="col-12">
        <u><h2 class="PAGE-STANDARD-H2">My Cart
        <br /><strong>list</strong></h2></u>               
      </div>
    </div>
    <div class="row">
      <div class="col-12" style="padding: 27px;">

      
      <div class="row">
        <div class="col-12 form-validate-liv-holder">
          
          <div class="row">
            <div class="col-1 form-validate-liv-titre text-center">
                Brand
            </div>
            <div class="col form-validate-liv-titre">
                Description
            </div>
            <div class="col-1 form-validate-liv-titre">
                Ref.
            </div>
            <div class="col-1 form-validate-liv-titre">
                Shipping
            </div>
            <div class="col-1 form-validate-liv-titre">
                UP
            </div>
            <div class="col-2 form-validate-liv-titre text-center">
                Qty
            </div>
            <div class="col-1 form-validate-liv-titre">
                TP
            </div>
            <div class="col-1 form-validate-liv-titre">
                Del.
            </div>
          </div>

        </div>
      </div>
      <?php
      $stt=0;

      // CART CONTENT
      for($i = 0; $i < $nbartonCart; $i++) 
      {
      // recuperation des données de la session
      $piece_id = $_SESSION['panier']['id_article'][$i];
      $prix = $_SESSION['panier']['prix'][$i];
      $consigne = 0;
      $qte = $_SESSION['panier']['qte'][$i];
      $piece_ref_propre = $_SESSION['panier']['refpropre'][$i];
      $piece_ref = $_SESSION['panier']['ref'][$i];
      $pm_id = $_SESSION['panier']['equip'][$i];
      $urltakentoadd = $_SESSION['panier']['urltakentoadd'][$i];
      $disponibility = $_SESSION['panier']['disponibility'][$i];
      $reliq = $_SESSION['panier']['reliq'][$i];
      // recuperation des données de la piece
      $qCartLine=mysql_query("SELECT DISTINCT piece_id, piece_name, piece_name_complement,
          pm_name_site, pm_logo,
          piece_pm_id, piece_pg_id, piece_ref_propre
          FROM $sqltable_Piece
          JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id AND pm_affiche = 1
          WHERE piece_id = $piece_id AND piece_affiche = 1 ");
      if($rCartLine=mysql_fetch_array($qCartLine))
      {
      $piece_ref_propre=$rCartLine["piece_ref_propre"];
      $pg_id=$rCartLine["piece_pg_id"];
      $pm_id =$rCartLine["piece_pm_id"];
      ?>
      <div class="row mt-1">
          <div class="col-12 form-validate-liv-holder">
            
            <div class="row">
              <div class="col-12 form-validate-liv">
                
                  <div class="row">
                    <div class="col-1 text-center">

<img src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $rCartLine["pm_logo"]; ?>" style="max-height: 47px;" />
                        <!--?php
                        $existimg="";
                        $qTOFF=mysql_query("SELECT DISTINCT CONCAT(pic_direct_link,pic_name,'.jpg') AS piece_image, 
                          CONCAT(  '/products.art/', pic_dossier,  '/', pic_name,  '.', doc_extension ) AS piece_image_full 
                          FROM prod_pieces_picture
                          JOIN prod_doc_type ON doc_type_id = pic_doc_type_id 
                          WHERE pic_piece_id = $piece_id 
                          ORDER BY pic_tri ;");
                        if($rTOFF=mysql_fetch_array($qTOFF))
                        {
                        $existimg=$domainStaticFiles.$rTOFF['piece_image'];
                        $existimgfull=$domainStaticFiles.$rTOFF['piece_image_full'];
                        }
                        else
                        {
                        $existimg=$domainparent."/includes.img/visuelnondispo.png";
                        $existimgfull=$domainparent."/includes.img/visuelnondispo.png";
                        }
                        ?>
                        <img src="< ?php echo $existimgfull; ?>" class="w-100" style="max-height: 67px;"/ -->
                    </div>
                    <div class="col CART-CONTAINER-BOX">
                        <?php echo utf8_encode($rCartLine["piece_name"]." ".$rCartLine["piece_name_complement"]); ?>
                    </div>
                    <div class="col-1 CART-CONTAINER-BOX">
                        <b><?php echo utf8_encode($piece_ref); ?></b>
                    </div>
                    <div class="col-1 CART-CONTAINER-BOX text-center">


<?php
if($disponibility==1)
{
  ?>
  <span class="SHOW-LIST-REF-LINE-DISPO-YES">
  <?php
  echo "<b style='font-size:14px;'>In Stock</b>";
  ?>
  </span>
  <?php
}
else
{
  ?>
  <span class="SHOW-LIST-REF-LINE-DISPO-NO">
  <?php
  echo "<b style='font-size:14px;'>Reliquat</b>";
  ?>
  <?php
  if($reliq==2)
  {
  echo "(Express)";
  }
  else
  {
  echo "(Standard)";
  }
  ?>
  </span>
  <?php
}
?>

                    </div>
                    <div class="col-1 CART-CONTAINER-BOX">
                        <?php echo number_format($prix, 2, '.', ''); ?> <?php echo $Currency; ?>
                    </div>
                    <div class="col-2 CART-CONTAINER-BOX text-center">
                        

                        <a href="<?php  echo $domain; ?>/addtocartinCart.php?action=minus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="<?php  echo $domainparent; ?>/includes.img/qteminus.jpg"  style="border:1px solid #FFFFFF;" /></a>
                        &nbsp;&nbsp;
                        <?php echo $qte; ?>
                        &nbsp;&nbsp;
                        <a href="<?php  echo $domain; ?>/addtocartinCart.php?action=plus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="<?php  echo $domainparent; ?>/includes.img/qteplus.jpg"  style="border:1px solid #FFFFFF;" /></a>


                    </div>
                    <div class="col-1 CART-CONTAINER-BOX">
                        <?php  $st=$prix*$qte; $stt=$stt+$st; echo number_format($st, 2, '.', ''); ?> <?php echo $Currency; ?>
                    </div>
                    <div class="col-1" style="padding: 3px;">
                        
                        <a href="<?php  echo $domain; ?>/addtocartinCart.php?action=drop&pieceidtakentoadd=<?php echo $piece_id  ; ?>" style="text-decoration:underline; color:#e82042;">Del.</a>

                    </div>
                  </div>
                  
              </div>
            </div>

          </div>
      </div>

      <?php
      }
      }
      ?>
      <div class="row mt-1">
          <div class="col-6">
          </div>
          <div class="col-3 pt-3 pb-3 text-left" style="background:#f1f4f7; border-bottom: 2px solid #fff;">
              
              Total of your Order

          </div>
          <div class="col-3 pt-3 pb-3 text-right" style="background:#f1f4f7; border-bottom: 2px solid #fff;">
              
              <?php echo number_format($stt, 2, '.', ''); ?> <?php echo $Currency; ?>

          </div>
      </div>

      </div>
    </div>
    <!-- / MON PANIER -->
    <!-- MON PANIER -->
    <!--div class="row">
      <div class="col-12">
        <u><h2 class="PAGE-STANDARD-H2">Informations
        <br /><strong>Other datas</strong></h2></u>               
      </div>
    </div-->
    <div class="row">
      <div class="col-12" style="padding: 27px;">

      
        <!--div class="row">
            <div class="col-12">
                Immatriculation
                <input type="text" name="cartimmat" class="subscription-inscription-panel-area" placeholder="" autocomplete="off" style=" background: #f7f8f9;" />
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                VIN (Numéro de chassis)
                <input type="text" name="cartvin" class="subscription-inscription-panel-area" placeholder="" autocomplete="off" style=" background: #f7f8f9;" />
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                Réf d'origine ou commercial
                <input type="text" name="oemcom" class="subscription-inscription-panel-area" placeholder="" autocomplete="off" style=" background: #f7f8f9;" />
            </div>
        </div> 
        <div class="row">
            <div class="col-12">
                Info complémentaires
                <textarea name="infossup" rows="7" class="subscription-inscription-panel-area" placeholder="Rq. Merci de rajouter une description exacte des pièces." style=" background: #f7f8f9;" /></textarea>
            </div>
        </div> 
        <div class="row">
            <div class="col-12" style="background:#f1f4f7; padding:12px; border:14px solid #FFFFFF;">
                <input name="equiv" type="checkbox"  value="oui" />&nbsp;&nbsp;J'accepte une pièce equivalente en cas de rupture usine ou non compatibilité avec mon véhicule.
            </div>
        </div--> 
        <div class="row">
            <div class="col-12 text-right">
                <input type="submit" class="subscription-inscription-panel-submit-charte" value="Submit Order" style=" background: #25aa2b" />
                <input type="hidden" name="actionwithinvalidation" value="cartvalidator" /> 
            </div>
        </div>


      </div>
    </div>
    <!-- / MON PANIER -->


</form>
<!-- FIN FORMULAIRE -->


  </div>
</div>

<?php
}
// fin mon panier contient des articles
?>
</div>
</div>







  </div>

</div>


<?Php
// PIED DE PAGE
@require_once('footer.last.section.php');
?>
</body>
</html>
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