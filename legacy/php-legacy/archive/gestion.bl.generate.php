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
		if(($rsverif["valide"]=='1')&&($rsverif["type"] == "SA"))
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

$boncom_id = $_GET["boncom_id"];
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_boncommande
JOIN 2027_xmassdoc_reseller_access_code ON id = boncom_clt_id 
WHERE boncom_id = '$boncom_id'");    
$Commande = mysql_fetch_array($qCommande);
$colturee = 0;
if($Commande["boncom_etat_id"]=="2") { $colturee = 1; }
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
<body class="BODY-MODAL">


<div class="row">
<div class="col-12">

    <!-- MON PANIER -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 class="PAGE-STANDARD-H2"><?php echo utf8_encode($Commande["company"]); ?>
        <br /><strong><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?></strong></h2>               
      </div>
      <div class="col-6 text-right">
        <h2 class="PAGE-STANDARD-H2">Purchase order
        <br /><strong><?php echo utf8_encode($Commande["boncom_matricule"]); ?></strong></h2>               
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
            <div class="col-1 form-validate-liv-titre text-center">
                Qty
            </div>
            <div class="col-1 form-validate-liv-titre">
                TP
            </div>
          </div>

        </div>
      </div>
      <?php
      $stt=0;
      $consignet=0;
      $sendingforconfirmation = 1;

      // CART CONTENT
      $qCommandeLine = mysql_query("SELECT * FROM 2027_xmassdoc_boncommande_line WHERE lineboncom_boncom_id = $boncom_id ");
      while($CommandeLine = mysql_fetch_array($qCommandeLine)) 
      {
      // line commande
      $linecom_id = $CommandeLine['lineboncom_id'];
      // recuperation des données de la session
      $piece_id = $CommandeLine['lineboncom_piece_id'];
      $prix = $CommandeLine['lineboncom_price_unit'];
      $consigne = 0;
      $qte = $CommandeLine['lineboncom_qte'];
      $piece_ref_propre = $CommandeLine['lineboncom_refpropre'];  
      $piece_ref = $CommandeLine['lineboncom_ref'];
      $pm_id = $CommandeLine['lineboncom_pm_id'];
      $urltakentoadd = $CommandeLine['lineboncom_urltocart'];
      // recuperation des données de la piece
      $qCartLine=mysql_query("SELECT DISTINCT piece_id, piece_name, piece_name_complement,
          pm_name_site, pm_logo 
          FROM $sqltable_Piece
          JOIN $sqltable_Piece_marque ON pm_id = piece_pm_id AND pm_affiche = 1
          WHERE piece_id = $piece_id AND piece_affiche = 1 ");
      if($rCartLine=mysql_fetch_array($qCartLine))
      {
      ?>
      <div class="row mt-1">
          <div class="col-12 form-validate-liv-holder">
            
            <div class="row">
              <div class="col-12 form-validate-liv">
                
                  <div class="row">
                    <div class="col-1">
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
                        <img src="< ?php echo $existimgfull; ?>" class="w-100" style="max-height: 67px;"/-->
                    </div>
                    <div class="col CART-CONTAINER-BOX">
                        <?php echo utf8_encode($rCartLine["piece_name"]." ".$rCartLine["piece_name_complement"]); ?>
                    </div>
                    <div class="col-1 CART-CONTAINER-BOX">
                        <b><?php echo $piece_ref; ?></b>
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
                    <div class="col-1 CART-CONTAINER-BOX">
                        <?php echo $qte; ?>
                    </div>
                    <div class="col-1 CART-CONTAINER-BOX">
                        <?php  $st=$prix*$qte; $stt=$stt+$st; echo number_format($st, 2, '.', ''); ?> <?php echo $Currency; ?>
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

      <?php 
      if ($colturee == 0)
      {
      ?>
      <div class="row mt-2">
          <div class="col-7">
          </div>
          <div class="col-5 pt-3 pb-3 text-center">
              
              <a href="<?php echo $domain; ?>/generatebl/get/<?php echo $boncom_id; ?>/0" class="modal-action-upgrade" style=" background: #df1b19">Payment NO<br>Generate BL</a>

              <a href="<?php echo $domain; ?>/generatebl/get/<?php echo $boncom_id; ?>/1" class="modal-action-upgrade" style=" background: #25aa2b">Payment OK<br>Generate BL</a>

          </div>
      </div>
      <?php 
	  }
      ?>


      </div>
    </div>
    <!-- / MON PANIER -->

</div>
</div>

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