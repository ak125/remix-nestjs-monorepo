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
			<h1>My Account</h1>
			<h2>Search, Select and Buy...</h2>
	</div>
	<div class="container-fluid Page-Welcome-Box">
		
		<div class="row">
			<!-- BOX GRID -->
        <div class="col-6 GRID-BOX">
          
          <div class="row">
            <div class="col-12 GRID-BOX-TITLE cyan text-center">
              Purchase Order
            </div>
            <div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
              
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_commande
JOIN 2027_xmassdoc_reseller_access_code ON id = com_clt_id 
WHERE com_clt_id = $ssid AND com_level = '1' ORDER BY com_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$po_1_id = $Commande["com_id"];
  ?>
  <div class="row">
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <b><?php echo $Commande["com_matricule"]; ?></b>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <?php echo $Commande["com_date"]; ?>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
<button type="button" class="FICHE-ARTICLE"  data-toggle="modal" data-target="#exampleModalLong<?php echo $po_1_id; ?>">
<u>View details</u>
</button>
<!-- Modal FICHE -->
<div class="modal fade" id="exampleModalLong<?php echo $po_1_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
    <div class="modal-content nopadding">
      <div class="modal-header nopadding">
        <h4 class="modal-title MODAL-ACCOUNT-TITLE" id="exampleModalLongTitle">
          <i><?php echo $Commande["com_matricule"]; ?></i></h4>
      </div>
      <div class="modal-body">

<div class="container-fluid">
<?php
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
?>
<div class="row">
  <div class="col-12">

<!-- FORMULAIRE -->

    <!-- MON PANIER -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 class="PAGE-STANDARD-H2"><?php echo utf8_encode($Commande["company"]); ?>
        <br /><strong><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?></strong></h2>               
      </div>
      <div class="col-6 text-right">
        <h2 class="PAGE-STANDARD-H2">Purchase order
        <br /><strong><?php echo utf8_encode($Commande["com_matricule"]); ?></strong> 
        <br><?php echo $Commande["com_date"]; ?></h2>              
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
            <div class="col-2 form-validate-liv-titre">
                STATUT
            </div>
          </div>

        </div>
      </div>
      <?php
      $stt=0;
      $consignet=0;

      // CART CONTENT
      $qCommandeLine = mysql_query("SELECT * FROM 2027_xmassdoc_commande_line WHERE linecom_com_id = $po_1_id ");
      while($CommandeLine = mysql_fetch_array($qCommandeLine)) 
      {
      // recuperation des données de la session
      $piece_id = $CommandeLine['linecom_piece_id'];
      $prix = $CommandeLine['linecom_price_unit'];
      $consigne = 0;
      $qte = $CommandeLine['linecom_qte'];
      $piece_ref_propre = $CommandeLine['linecom_refpropre'];  
      $piece_ref = $CommandeLine['linecom_ref'];
      $pm_id = $CommandeLine['linecom_pm_id'];
      $urltakentoadd = $CommandeLine['linecom_urltocart'];
      $disponibility = $CommandeLine['linecom_instock'];
      $reliq = $CommandeLine['linecom_shipping'];
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
                        <img src="< ?php echo $existimgfull; ?>" class="w-100" style="max-height: 67px;"/ -->
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
                    <div class="col-2 CART-CONTAINER-BOX text-center">
                        <span class="PO-1-LINE-ETAT-STANDBY"><?php if ($CommandeLine["linecom_statut"]==1) echo "Stand BY"; ?></span>
                        <span class="PO-1-LINE-ETAT-RECEIVED"><?php if ($CommandeLine["linecom_statut"]==11) echo "Received"; ?></span>
                        <span class="PO-1-LINE-ETAT-DELETED"><?php if ($CommandeLine["linecom_statut"]==12) echo "Deleted"; ?></span>
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
<!-- FIN FORMULAIRE -->

  </div>
</div>
  
<?php
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
?>
</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<!-- / Modal FICHE -->
      
    </div>
  </div>
  <?php
}
?>

            </div>
          </div>

        </div>
      <!-- / BOX GRID -->
      <!-- BOX GRID -->
        <div class="col-6 GRID-BOX">
          
          <div class="row">
            <div class="col-12 GRID-BOX-TITLE green text-center">
              Order Ticket
            </div>
            <div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
              
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_boncommande
JOIN 2027_xmassdoc_reseller_access_code ON id = boncom_clt_id 
WHERE boncom_clt_id = $ssid AND boncom_etat_id = '1' ORDER BY boncom_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$boncom_id = $Commande["boncom_id"];
  ?>
  <div class="row">
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <b><?php echo $Commande["boncom_matricule"]; ?></b>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <?php echo $Commande["boncom_date"]; ?>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
<button type="button" class="FICHE-ARTICLE"  data-toggle="modal" data-target="#exampleBCModalLong<?php echo $boncom_id; ?>">
<u>View details</u>
</button>
<!-- Modal FICHE -->
<div class="modal fade" id="exampleBCModalLong<?php echo $boncom_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleBCModalLongTitle" aria-hidden="true">
  <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
    <div class="modal-content nopadding">
      <div class="modal-header nopadding">
        <h4 class="modal-title MODAL-ACCOUNT-TITLE" id="exampleBCModalLongTitle">
          <i><?php echo $Commande["boncom_matricule"]; ?></i></h4>
      </div>
      <div class="modal-body">

<div class="container-fluid">
<?php
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
?>
<div class="row">
  <div class="col-12">

<!-- FORMULAIRE -->

    <!-- MON PANIER -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 class="PAGE-STANDARD-H2"><?php echo utf8_encode($Commande["company"]); ?>
        <br /><strong><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?></strong></h2>               
      </div>
      <div class="col-6 text-right">
        <h2 class="PAGE-STANDARD-H2">Order Ticket
        <br /><strong><?php echo utf8_encode($Commande["boncom_matricule"]); ?></strong>
        <br><?php echo $Commande["boncom_date"]; ?></h2>  
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

      // CART CONTENT
      $qCommandeLine = mysql_query("SELECT * FROM 2027_xmassdoc_boncommande_line WHERE lineboncom_boncom_id = $boncom_id ");
      while($CommandeLine = mysql_fetch_array($qCommandeLine)) 
      {
      // recuperation des données de la session
      $piece_id = $CommandeLine['lineboncom_piece_id'];
      $prix = $CommandeLine['lineboncom_price_unit'];
      $consigne = 0;
      $qte = $CommandeLine['lineboncom_qte'];
      $piece_ref_propre = $CommandeLine['lineboncom_refpropre'];  
      $piece_ref = $CommandeLine['lineboncom_ref'];
      $pm_id = $CommandeLine['lineboncom_pm_id'];
      $urltakentoadd = $CommandeLine['lineboncom_urltocart'];
      $disponibility = $CommandeLine['lineboncom_instock'];
      $reliq = $CommandeLine['lineboncom_shipping'];
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
                        <img src="< ?php echo $existimgfull; ?>" class="w-100" style="max-height: 67px;"/ -->
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


      </div>
    </div>
    <!-- / MON PANIER -->
<!-- FIN FORMULAIRE -->

  </div>
</div>
  
<?php
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
?>
</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<!-- / Modal FICHE -->
      
    </div>
  </div>
  <?php
}
?>

            </div>
          </div>

        </div>
      <!-- / BOX GRID -->
      <!-- BOX GRID -->
        <div class="col-6 GRID-BOX">
          
          <div class="row">
            <div class="col-12 GRID-BOX-TITLE sand text-center">
              BL
            </div>
            <div class="col-12 GRID-BOX-CONTENT-ACCOUNT text-left">
              
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_bl
JOIN 2027_xmassdoc_reseller_access_code ON id = bl_clt_id 
WHERE bl_clt_id = $ssid ORDER BY bl_id DESC");    
while($Commande = mysql_fetch_array($qCommande))
{
$bl_id = $Commande["bl_id"];
  ?>
  <div class="row">
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <b><?php echo $Commande["bl_matricule"]; ?></b>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      <?php echo $Commande["bl_date"]; ?>
      
    </div>
    <div class="col-4 GRID-BOX-CONTENT-ACCOUNT-COL">
      
      &nbsp;
      
    </div>
  </div>
  <?php
}
?>

            </div>
          </div>

        </div>
      <!-- / BOX GRID -->
			
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