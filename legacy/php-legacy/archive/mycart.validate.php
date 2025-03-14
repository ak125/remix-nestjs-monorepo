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

// details infos supplementaires
//$cartimmat=mysql_real_escape_string($_POST['cartimmat']);
//$cartvin=mysql_real_escape_string($_POST['cartvin']);
//$cartoemcom=mysql_real_escape_string($_POST['oemcom']);
//$cartinfosup=mysql_real_escape_string($_POST['infossup']);
//$cartequiv=$_POST['equiv'];
//$infoscomplementaires="Immatriculation : ".$cartimmat."<br>"."VIN (Numéro de chassis) : ".$cartvin."<br>"."Réf d origine ou commercial : ".$cartoemcom."<br>"."Infos complémentaires : ".$cartinfosup."<br>"."Equivalence : ".$cartequiv;
$infoscomplementaires="";
// date commanda
$datecommande=date("Y-m-j H:i:s");  
// total
$nbartonCart = count($_SESSION['panier']['id_article']);
$stt=0;
$consignet=0;
for($i = 0; $i < $nbartonCart; $i++) 
{ 
// recuperation des données de la session
$prix = $_SESSION['panier']['prix'][$i];
$consigne = 0;
$qte = $_SESSION['panier']['qte'][$i];
$stt=$stt+($prix*$qte); 
}

if($_SESSION['panier'])
{
mysql_query("SET NAMES 'utf8'");
mysql_query("INSERT INTO 2027_xmassdoc_commande (com_id, com_id_parent, com_clt_id, com_type, com_level, com_etat_id, com_statut_id, com_amount, com_date, com_infosup) 
  VALUES ('', '0', $ssid, 'PO', '0', '1', '1', '$stt', '$datecommande', '$infoscomplementaires')");
// recuperer l'identifiant de la commande
$commande_id_injected = @mysql_insert_id();
if($commande_id_injected>0)
{
      $com_matricule = "PO/0-".$commande_id_injected;
      mysql_query("UPDATE 2027_xmassdoc_commande SET com_matricule = '$com_matricule' WHERE com_id = '$commande_id_injected'");
      /* /////////////////////////////////// LIGNE DE COMMANDE ///////////////////// */
      // affichage du contenu du panier
      $nbartonCart = count($_SESSION['panier']['id_article']);
      $consignetotal=0;
      for($i = 0; $i < $nbartonCart; $i++) 
      { 
      // recuperation des données de la session
      $piece_id = $_SESSION['panier']['id_article'][$i];
      $prix = $_SESSION['panier']['prix'][$i];
      $consigne = 0;
      $qte = $_SESSION['panier']['qte'][$i];
      $piece_ref_propre = $_SESSION['panier']['refpropre'][$i];
      $pm_id = $_SESSION['panier']['equip'][$i];
      $urltakentoadd = $_SESSION['panier']['urltakentoadd'][$i];
      
      $pg_id = $_SESSION['panier']['gammeid'][$i];
      $pg_name = $_SESSION['panier']['gammename'][$i];
      $pg_name=str_replace("'"," ",$pg_name);
      $pm_name = $_SESSION['panier']['equipname'][$i];
      $pm_name=str_replace("'"," ",$pm_name);
      $piece_ref = $_SESSION['panier']['ref'][$i];
      $disponibility = $_SESSION['panier']['disponibility'][$i];
      $reliq = $_SESSION['panier']['reliq'][$i];
      
      // prix unitaire TTC
      $prix_UTTC=$prix;
      $prix_UTTC=number_format($prix_UTTC, 2, '.', ''); 

      // prix total TTC
      $prix_TTTC=$prix*$qte;
      $prix_TTTC=number_format($prix_TTTC, 2, '.', '');      
      

      mysql_query("INSERT INTO  2027_xmassdoc_commande_line ( linecom_id ,linecom_com_id , linecom_statut, linecom_piece_id, linecom_ref, linecom_refpropre, linecom_pg_id, linecom_pm_id, linecom_price_unit, linecom_qte, linecom_price_total, linecom_urltocart, linecom_instock, linecom_shipping) VALUES ('' ,'$commande_id_injected','1','$piece_id','$piece_ref', '$piece_ref_propre','$pg_id','$pm_id','$prix_UTTC','$qte','$prix_TTTC','$urltakentoadd', '$disponibility', '$reliq')");

      }
      
      /* /////////////////////////////////// LIGNE DE COMMANDE ///////////////////// */

      unset($_SESSION['panier']);


// PO/1
$stt_po1=0;
$qCommandeDispo=mysql_query("SELECT linecom_id FROM 2027_xmassdoc_commande_line 
WHERE linecom_com_id = $commande_id_injected AND linecom_instock = '0' AND linecom_shipping = '1' ");    
if($CommandeDispo = mysql_fetch_array($qCommandeDispo))
{
    mysql_query("INSERT INTO 2027_xmassdoc_commande (com_id, com_id_parent, com_clt_id, com_type, com_level, com_etat_id, com_statut_id, com_amount, com_date, com_infosup) 
    VALUES ('', '$commande_id_injected', $ssid, 'PO', '1', '1', '1', '$stt_po1', '$datecommande', '$infoscomplementaires')");
    
    // recuperer l'identifiant de la commande
    $commande_id_injected_po1 = @mysql_insert_id();
    if($commande_id_injected_po1>0)
    {
      $com_matricule_po1 = "PO/1-".$commande_id_injected_po1;
      mysql_query("UPDATE 2027_xmassdoc_commande SET com_matricule = '$com_matricule_po1' WHERE com_id = '$commande_id_injected_po1'");
      
      // CART CONTENT
      $qCommandeDispoLine = mysql_query("SELECT * FROM 2027_xmassdoc_commande_line 
        WHERE linecom_com_id = $commande_id_injected  AND linecom_instock = '0' AND linecom_shipping = '1' ");
      while($CommandeDispoLine = mysql_fetch_array($qCommandeDispoLine)) 
      {
      
      // recuperation des données de la session
      $piece_id = $CommandeDispoLine['linecom_piece_id'];
      $prix = $CommandeDispoLine['linecom_price_unit'];
      $consigne = 0;
      $qte = $CommandeDispoLine['linecom_qte'];
      $piece_ref_propre = $CommandeDispoLine['linecom_refpropre'];
      $pm_id = $CommandeDispoLine['linecom_pm_id'];
      $urltakentoadd = $CommandeDispoLine['linecom_urltocart'];
      
      $pg_id =  $CommandeDispoLine['linecom_pg_id'];
      
      $piece_ref =  $CommandeDispoLine['linecom_ref'];
      $disponibility =  $CommandeDispoLine['linecom_instock'];
      $reliq =  $CommandeDispoLine['linecom_shipping'];
      
      // prix unitaire TTC
      $prix_UTTC=$prix;
      $prix_UTTC=number_format($prix_UTTC, 2, '.', ''); 

      // prix total TTC
      $prix_TTTC=$prix*$qte;
      $prix_TTTC=number_format($prix_TTTC, 2, '.', '');      
      

      $stt_po1=$stt_po1+($prix*$qte);

      mysql_query("INSERT INTO  2027_xmassdoc_commande_line ( linecom_id ,linecom_com_id , linecom_statut, linecom_piece_id, linecom_ref, linecom_refpropre, linecom_pg_id, linecom_pm_id, linecom_price_unit, linecom_qte, linecom_price_total, linecom_urltocart, linecom_instock, linecom_shipping) VALUES ('' ,'$commande_id_injected_po1','1','$piece_id','$piece_ref', '$piece_ref_propre','$pg_id','$pm_id','$prix_UTTC','$qte','$prix_TTTC','$urltakentoadd', '$disponibility', '$reliq')");

      }
      mysql_query("UPDATE 2027_xmassdoc_commande SET com_amount = '$stt_po1' WHERE com_id = '$commande_id_injected_po1'");

    }
}


// BC
$stt_bc=0;
$qCommandeDispo=mysql_query("SELECT linecom_id FROM 2027_xmassdoc_commande_line 
WHERE linecom_com_id = $commande_id_injected AND  ( linecom_instock = '1' OR linecom_shipping = '2' )  ");    
if($CommandeDispo = mysql_fetch_array($qCommandeDispo))
{
    mysql_query("INSERT INTO 2027_xmassdoc_boncommande (boncom_id, boncom_com_id, boncom_clt_id, boncom_type, boncom_etat_id, boncom_statut_id, boncom_amount, boncom_date, boncom_infosup) 
    VALUES ('', '$commande_id_injected', $ssid, 'BC', '1', '1', '$stt_bc', '$datecommande', '$infoscomplementaires')");
    
    // recuperer l'identifiant de la commande
    $boncommande_id_injected = @mysql_insert_id();
    if($boncommande_id_injected>0)
    {
      $boncom_matricule = "BC/".$boncommande_id_injected;
      mysql_query("UPDATE 2027_xmassdoc_boncommande SET boncom_matricule = '$boncom_matricule' WHERE boncom_id = '$boncommande_id_injected'");
      
      // CART CONTENT
      $qCommandeDispoLine = mysql_query("SELECT * FROM 2027_xmassdoc_commande_line 
        WHERE linecom_com_id = $commande_id_injected  AND ( linecom_instock = '1' OR linecom_shipping = '2' )  ");
      while($CommandeDispoLine = mysql_fetch_array($qCommandeDispoLine)) 
      {
      
      // recuperation des données de la session
      $piece_id = $CommandeDispoLine['linecom_piece_id'];
      $prix = $CommandeDispoLine['linecom_price_unit'];
      $consigne = 0;
      $qte = $CommandeDispoLine['linecom_qte'];
      $piece_ref_propre = $CommandeDispoLine['linecom_refpropre'];
      $pm_id = $CommandeDispoLine['linecom_pm_id'];
      $urltakentoadd = $CommandeDispoLine['linecom_urltocart'];
      
      $pg_id =  $CommandeDispoLine['linecom_pg_id'];
      
      $piece_ref =  $CommandeDispoLine['linecom_ref'];
      $disponibility =  $CommandeDispoLine['linecom_instock'];
      $reliq =  $CommandeDispoLine['linecom_shipping'];
      
      // prix unitaire TTC
      $prix_UTTC=$prix;
      $prix_UTTC=number_format($prix_UTTC, 2, '.', ''); 

      // prix total TTC
      $prix_TTTC=$prix*$qte;
      $prix_TTTC=number_format($prix_TTTC, 2, '.', '');      
      

      $stt_bc=$stt_bc+($prix*$qte);

      mysql_query("INSERT INTO  2027_xmassdoc_boncommande_line ( lineboncom_id ,lineboncom_boncom_id , lineboncom_statut, lineboncom_piece_id, lineboncom_ref, lineboncom_refpropre, lineboncom_pg_id, lineboncom_pm_id, lineboncom_price_unit, lineboncom_qte, lineboncom_price_total, lineboncom_urltocart, lineboncom_instock, lineboncom_shipping) VALUES ('' ,'$boncommande_id_injected','1','$piece_id','$piece_ref', '$piece_ref_propre','$pg_id','$pm_id','$prix_UTTC','$qte','$prix_TTTC','$urltakentoadd', '$disponibility', '$reliq')");

      // on annule la piece du stock
      $qStockUpdate=mysql_query("SELECT sp_id, sp_qte
      FROM 2027_xmassdoc_piece_stock 
      WHERE sp_refpropre = '$piece_ref_propre' AND sp_pg_id = '$pg_id' AND sp_pm_id = '$pm_id'");    
      $StockUpdate = mysql_fetch_array($qStockUpdate);
      $sp_id_this = $StockUpdate["sp_id"];
      $sp_qte_this_new = $StockUpdate["sp_qte"]-$qte;
      mysql_query("UPDATE 2027_xmassdoc_piece_stock SET sp_qte = '$sp_qte_this_new' WHERE sp_id = '$sp_id_this'");
      // fin mise a jour du stock

      }
      mysql_query("UPDATE 2027_xmassdoc_boncommande SET boncom_amount = '$stt_bc' WHERE boncom_id = '$boncommande_id_injected'");
      
    }
}


// BC/0
/*$qCommandeDispo=mysql_query("SELECT linecom_id FROM 2027_xmassdoc_commande_line 
WHERE linecom_com_id = $commande_id_injected AND ( linecom_instock = '1' OR linecom_shipping = '2' ) ");    
if($CommandeDispo = mysql_fetch_array($qCommandeDispo))
{
mysql_query("INSERT INTO 2027_xmassdoc_commande (com_id, com_id_parent, com_clt_id, com_type, com_level, com_etat_id, com_statut_id, com_amount, com_date, com_infosup) 
  VALUES ('', '0', $ssid, 'PO', '0', '1', '1', '$stt', '$datecommande', '$infoscomplementaires')");   
}*/


}
else
{
    ?>
      <META http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/cart/">
    <?php
}
}
else
{
    ?>
      <META http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/cart/">
    <?php
}
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
			<h1>Purchase order received</h1>
			<h2>Search, Select and Buy...</h2>
	</div>
	<div class="container-fluid Page-Welcome-Box">
		



<div class="row">
<div class="col-12 GRID-BOX p-3">

<div class="row">
  <div class="col-12 PAGE-STANDARD-RIGHT-CONTAINER-FORM">

        
<!-- FORMULAIRE -->
<?php
$qCommande=mysql_query("SELECT * FROM 2027_xmassdoc_commande
JOIN 2027_xmassdoc_reseller_access_code ON id = com_clt_id 
WHERE com_id = $commande_id_injected");    
$Commande = mysql_fetch_array($qCommande);
?>
    <!-- MON PANIER -->
    <div class="row">
      <div class="col-6 text-left">
        <h2 class="PAGE-STANDARD-H2"><?php echo utf8_encode($Commande["company"]); ?>
        <br /><strong><?php echo utf8_encode($Commande["type"]."-".$Commande["id"]); ?></strong></h2>               
      </div>
      <div class="col-6 text-right">
        <h2 class="PAGE-STANDARD-H2">Purchase order
        <br /><strong><?php echo utf8_encode($Commande["com_matricule"]); ?></strong></h2>               
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
      $qCommandeLine = mysql_query("SELECT * FROM 2027_xmassdoc_commande_line WHERE linecom_com_id = $commande_id_injected ");
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
          pm_name_site , pm_logo
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

<form action="<?php echo $domain; ?>/welcome" method="post" role="form"> 
      <div class="row">
          <div class="col-12 text-right">
              <input type="submit" class="subscription-inscription-panel-submit-charte" value="ORDER AGAIN" />
          </div>
      </div>
</form>

      </div>
    </div>
    <!-- / MON PANIER -->


<!-- FIN FORMULAIRE -->


  </div>
</div>


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