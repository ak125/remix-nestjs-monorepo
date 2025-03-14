<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////             FICHE ARTICLE         /////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<div class="row">
  <div class="col-12 SHOW-LIST-REF-LINE">


<div class="row">
    <div class="col-2 SHOW-LIST-REF-LINE-TITLE text-center">

reference
        
    </div>

    <div class="col-4 SHOW-LIST-REF-LINE-TITLE">

photo

    </div>

    <div class="col-3 SHOW-LIST-REF-LINE-TITLE">

technical criteria

    </div>

    <div class="col-2 SHOW-LIST-REF-LINE-TITLE">

Purshase

    </div>

    <div class="col-1 SHOW-LIST-REF-LINE-TITLE">
          
Cart

    </div>
</div>




<div class="row">
    <div class="col-2 SHOW-LIST-REF-LINE-NAME-VAL">


        <div class="row">
          <div class="col-12 SHOW-LIST-REF-LINE-VAL-NAME">
            <?php echo $piece_name." <span>".$piece_name_comp."</span>"; ?>
          </div>
          <div class="col-12 SHOW-LIST-REF-LINE-VAL-REF">
            <?php echo $pm_name_site; ?><br>ref : <?php echo $piece_ref; ?>
          </div>
          <div class="col-12 SHOW-LIST-REF-LINE-VAL-ECHANGE">
            <?php
            if($piece_consigne_price_ttc>0)
            {
              echo "Echange standard";
            }
            ?>
            <?php if(!empty($GetDG)) { echo $GetDG; } ?>
          </div>
          <div class="col-12 SHOW-LIST-REF-LINE-LOGO text-center">
        
              <img src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $pm_logo; ?>" 
              style="max-width: 77px;" />
              
          </div>
        </div>
        
    </div>

    <div class="col-4 SHOW-LIST-REF-LINE-LOGO text-center">

        <?php
        $existimg="";
        $ni=1;
        $other = 0;
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
        $other = 1;
        }
        else
        {
        $existimg=$domainparent."/includes.img/visuelnondispo.png";
        $existimgfull=$domainparent."/includes.img/visuelnondispo.png";
        $other = 0;
        }
        ?>

<a class="example-image-link" href="<?php echo $existimgfull; ?>" data-lightbox="example-<?php echo $piece_ref_propre; ?>" data-title="<?php echo $piece_name." ".$piece_name_comp." ".$pm_name_site." réf ".$piece_ref; ?> (Fig-1)"><img src="<?php echo $existimgfull; ?>" alt="<?php echo $piece_name." ".$piece_name_comp." ".$pm_name_site." réf ".$piece_ref; ?>"/></a>
<?php
if($other==1)
{
$ni=2;
$qTOFF=mysql_query("SELECT DISTINCT CONCAT(  'products.art/', pic_dossier,  '/', pic_name,  '.', doc_extension ) AS piece_image_full 
FROM prod_pieces_picture
JOIN prod_doc_type ON doc_type_id = pic_doc_type_id 
WHERE pic_piece_id = $piece_id 
ORDER BY pic_tri LIMIT 1,10");
while($rTOFF=mysql_fetch_array($qTOFF))
{
$itemPhotoReal = $domainStaticFiles."/".$rTOFF['piece_image_full'];
?>
<a class="example-image-link" href="<?php echo $itemPhotoReal; ?>" data-lightbox="example-<?php echo $piece_ref_propre; ?>" data-title="<?php echo $piece_name." ".$piece_name_comp." ".$pm_name_site." réf ".$piece_ref; ?> (Fig-<?php echo $ni; ?>)"></a>
<?php
$ni++;
}
}
?>

    </div>

    <div class="col-3 SHOW-LIST-REF-LINE-NAME-VAL-CRITERIA">
<!-- CRITERE TECHNIQUE -->
<?php /*
//echo $CritereTechnique;
$qGcri =  mysql_query("SELECT DISTINCT cri_critere , relc_donnee , cri_unite 
FROM prod_relation_auto
JOIN prod_relation ON relation_id = relauto_relation_id 
JOIN prod_relation_critere ON relc_relation_id = relation_id 
JOIN prod_critere ON cri_id = relc_cri_id 
WHERE relation_piece_id = $piece_id  AND relauto_type_id = $type_id ORDER BY relc_value_ap , relc_tri");
while($rGcri = mysql_fetch_array($qGcri))
{
    echo utf8_encode($rGcri['cri_critere'])." : ".utf8_encode($rGcri['relc_donnee'])." ".utf8_encode($rGcri['cri_unite'])."<br>";
}
$qGcri =  mysql_query("SELECT DISTINCT cri_critere , pc_donnee , cri_unite 
FROM prod_pieces_critere 
JOIN prod_critere ON cri_id = pc_cri_id 
WHERE pc_piece_id = $piece_id ORDER BY pc_value_ap , pc_tri");
while($rGcri = mysql_fetch_array($qGcri))
{
    echo utf8_encode($rGcri['cri_critere'])." : ".utf8_encode($rGcri['pc_donnee'])." ".utf8_encode($rGcri['cri_unite'])."<br>";
} */
?>
<!-- ////////////////////////////////////////////////////////  -->
<?php 
//echo $CritereTechnique;
$qGcri =  mysql_query("SELECT DISTINCT cri_critere , relc_donnee , cri_unite 
FROM prod_relation_auto
JOIN prod_relation ON relation_id = relauto_relation_id 
JOIN prod_relation_critere ON relc_relation_id = relation_id 
JOIN prod_critere ON cri_id = relc_cri_id 
WHERE relation_piece_id = $piece_id  AND relauto_type_id = $type_id ORDER BY relc_value_ap , relc_tri ;");
while($rGcri = mysql_fetch_array($qGcri))
{
    echo utf8_encode($rGcri['cri_critere'])." : ".utf8_encode($rGcri['relc_donnee'])." ".utf8_encode($rGcri['cri_unite'])."<br>";
}
$qGcri =  mysql_query("SELECT DISTINCT cri_critere , pc_donnee , cri_unite 
FROM prod_pieces_critere 
JOIN prod_critere ON cri_id = pc_cri_id 
WHERE pc_piece_id = $piece_id ORDER BY pc_value_ap , pc_tri");
while($rGcri = mysql_fetch_array($qGcri))
{
    echo utf8_encode($rGcri['cri_critere'])." : ".utf8_encode($rGcri['pc_donnee'])." ".utf8_encode($rGcri['cri_unite'])."<br>";
}
?>
<!-- / CRITERE TECHNIQUE -->
<br>
<button type="button" class="FICHE-ARTICLE-COMP"  data-toggle="modal" data-target="#exampleModalLong<?php echo $piece_id; ?>">
<u>View more</u>
</button>
<!-- PLUS DE DETAILS -->
<div class="row">
  <div class="col-12">

<!-- Modal FICHE -->
<div class="modal fade" id="exampleModalLong<?php echo $piece_id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
    <div class="modal-content nopadding">
      <div class="modal-header nopadding">
        <h4 class="modal-title ONE-MODEL-CONTENT-LIST-MOTORISATION-TITLE" id="exampleModalLongTitle"><i><?php echo $piece_name." ".$piece_name_comp; ?> <?php echo $pm_name_site; ?> <?php echo $piece_ref; ?> <?php /* if($piece_consigne_price_ttc>0) { echo "en échange standard<br>"; } else { if(!empty($pm_quality)) { echo "de qualité ".$pm_quality; } } */ ?></i></h4>
      </div>
      <div class="modal-body">

<div class="container-fluid">
<?php
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FICHE DATAS /////////////////////////////////////
?>
  <div class="row">
      <div class="col-12 D2-ALL-DATA-TECH-DATA">
      CRITERES TECHNIQUES
      </div>
    </div>
<div class="row">
  <div class="col-md-12 text-left">

        <?php
        $countCritere = 1;
        $queryCritereCar =  mysql_query("SELECT DISTINCT cri_critere, relc_donnee, cri_unite
          FROM prod_relation_auto
          JOIN prod_relation ON relation_id = relauto_relation_id
          JOIN prod_relation_critere ON relc_relation_id = relation_id
          JOIN prod_critere ON cri_id = relc_cri_id 
          WHERE relation_piece_id = $piece_id AND relauto_type_id = $type_id
          ORDER BY relc_tri");
        while($resultCritereCar = mysql_fetch_array($queryCritereCar))
        {
          ?>
              <div class="row <?php if($countCritere==1) echo "LIGHT-GRAY-BLOC"; ?>" style=" border-left:4px solid #fff;">
                  <div class="col-6 FICHE-ARTICLE-CRITERES-LINE text-left">
                      <?php echo utf8_encode($resultCritereCar['cri_critere']); ?>
                  </div>
                  <div class="col-6 FICHE-ARTICLE-CRITERES-LINE text-left">
                      <?php echo utf8_encode($resultCritereCar['relc_donnee']." ".$resultCritereCar['cri_unite']); ?>
                  </div>
              </div>
          <?php
        if($countCritere==1) { $countCritere=2; } else  { $countCritere=1; }
        }
        $queryCritere =  mysql_query("SELECT DISTINCT cri_critere, pc_donnee, cri_unite
          FROM prod_pieces_critere
          JOIN prod_critere ON cri_id = pc_cri_id 
          WHERE pc_piece_id = $piece_id 
          ORDER BY pc_tri");
        while($resultCritere = mysql_fetch_array($queryCritere))
        {
          ?>
              <div class="row <?php if($countCritere==1) echo "LIGHT-GRAY-BLOC"; ?>" style=" border-left:3px solid #fff; border-right:3px solid #fff;">
                  <div class="col-6 FICHE-ARTICLE-CRITERES-LINE text-left">
                      <?php echo utf8_encode($resultCritere['cri_critere']); ?>
                  </div>
                  <div class="col-6 FICHE-ARTICLE-CRITERES-LINE text-left">
                      <?php echo utf8_encode($resultCritere['pc_donnee']." ".$resultCritere['cri_unite']); ?>
                  </div>
              </div>
          <?php
        if($countCritere==1) { $countCritere=2; } else  { $countCritere=1; }
        }
        ?>

  </div>
</div>

<div class="row">
  <div class="col-6 text-left" style="padding:17px; border-top: 8px solid #fff; background: #f4f5f6; border-right: 2px solid #fff;">


  <div class="row">
      <div class="col-12 D2-ALL-DATA-TECH-DATA">
      Références d'origine (OEM)
      </div>
    </div>
    <div class="row">
      <div class="col-12">
              <p>
                <?php
                $qRC=mysql_query("SELECT DISTINCT maker_name , reference_piece_ref FROM prod_pieces_reference 
                JOIN prod_maker ON reference_maker_id = maker_id 
                WHERE reference_piece_id = $piece_id AND reference_reftype_id = 3 ");
                while($rRC=mysql_fetch_array($qRC))
                {
                ?>
                <?php echo utf8_encode($rRC['maker_name']); ?> : <a href="<?php echo $domain.'/search/?quest='.$rRC['reference_piece_ref']; ?>"><u><?php echo $rRC['reference_piece_ref']; ?></u></a>
                <br />
                <?php
                }
                ?>
              </p>
      </div>
    </div>


  </div>
  <div class="col-6 text-left" style="padding:17px; border-top: 8px solid #fff; background: #f4f5f6; border-left: 2px solid #fff;">


  <div class="row">
      <div class="col-12 D2-ALL-DATA-TECH-DATA">
      Références équipementiers
      </div>
    </div>
    <div class="row">
      <div class="col-12">
              <p>
                <?php
                $qRE=mysql_query("SELECT DISTINCT maker_name , reference_piece_ref FROM prod_pieces_reference 
                JOIN prod_maker ON reference_maker_id = maker_id 
                WHERE reference_piece_id = $piece_id AND  reference_reftype_id IN (2,4) ORDER BY maker_name");
                while($rRE=mysql_fetch_array($qRE))
                {
                ?>
                <?php echo utf8_encode($rRE['maker_name']); ?> : <a href="<?php echo $domain.'/search/?quest='.$rRE['reference_piece_ref']; ?>"><u><?php echo $rRE['reference_piece_ref']; ?></u></a>
                <br />
                <?php
                }
                ?>
              </p>
      </div>
    </div>


  </div>
</div> 

<div class="row">
  <div class="col-6 text-left" style="padding:17px; border-top: 8px solid #fff; background: #f4f5f6; border-right: 2px solid #fff;">


    <div class="row">
      <div class="col-12 D2-ALL-DATA-TECH-DATA">
      MOTORISATION DIESEL
      </div>
    </div>
    <div class="row">
      <div class="col-12">
              <p>
                <?php
                $qRC=mysql_query("SELECT DISTINCT marque_id ,marque_alias , marque_name_site , modele_id , modele_alias, modele_name_site , type_id , type_alias, type_name_site , type_ch , type_kw , LEFT(type_date_debut,4) AS type_date_debut,IF(type_date_fin IS NULL ,'~',LEFT(type_date_fin,4)) AS type_date_fin , type_carburant FROM prod_relation
JOIN prod_relation_auto ON relauto_relation_id = relation_id
JOIN 2022_auto_type ON type_id = relauto_type_id AND type_affiche = 1
JOIN 2022_auto_modele ON modele_id = type_modele_id AND modele_affiche = 1
JOIN 2022_auto_marque ON marque_id = modele_marque_id AND marque_affiche = 1
WHERE relation_piece_id = $piece_id  AND type_carburant = 'Diesel'
ORDER BY marque_alias , modele_tri , type_alias , type_date_debut");
                while($rRC=mysql_fetch_array($qRC))
                {

$LinkToGammeCar = $domain."/searchcar/".$pg_alias."-".$pg_id."/".$rRC['marque_alias']."-".$rRC['marque_id']."/".$rRC['modele_alias']."-".$rRC['modele_id']."/".$rRC['type_alias']."-".$rRC['type_id'];
                ?>
                <a href="<?php echo $LinkToGammeCar; ?>"><u><?php echo utf8_encode($rRC['marque_name_site']." ".$rRC['modele_name_site']." ".$rRC['type_name_site']." ".$rRC['type_ch']." Ch (".$rRC['type_date_debut']."-".$rRC['type_date_fin'].")"); ?></u></a>
                <br />
                <?php
                }
                ?>
              </p>
      </div>
    </div>


  </div>
  <div class="col-6 text-left" style="padding:17px; border-top: 8px solid #fff; background: #f4f5f6; border-left: 2px solid #fff;">


  <div class="row">
      <div class="col-12 D2-ALL-DATA-TECH-DATA">
      MOTORISATION ESSENCE
      </div>
    </div>
    <div class="row">
      <div class="col-12">
              <p>
                <?php
                $qRC=mysql_query("SELECT DISTINCT marque_id ,marque_alias , marque_name_site , modele_id , modele_alias, modele_name_site , type_id , type_alias, type_name_site , type_ch , type_kw , LEFT(type_date_debut,4) AS type_date_debut,IF(type_date_fin IS NULL ,'~',LEFT(type_date_fin,4)) AS type_date_fin , type_carburant FROM prod_relation
JOIN prod_relation_auto ON relauto_relation_id = relation_id
JOIN 2022_auto_type ON type_id = relauto_type_id AND type_affiche = 1
JOIN 2022_auto_modele ON modele_id = type_modele_id AND modele_affiche = 1
JOIN 2022_auto_marque ON marque_id = modele_marque_id AND marque_affiche = 1
WHERE relation_piece_id = $piece_id  AND type_carburant = 'Essence'
ORDER BY marque_alias , modele_tri , type_alias , type_date_debut");
                while($rRC=mysql_fetch_array($qRC))
                {

$LinkToGammeCar = $domain."/searchcar/".$pg_alias."-".$pg_id."/".$rRC['marque_alias']."-".$rRC['marque_id']."/".$rRC['modele_alias']."-".$rRC['modele_id']."/".$rRC['type_alias']."-".$rRC['type_id'];
                ?>
                <a href="<?php echo $LinkToGammeCar; ?>"><u><?php echo utf8_encode($rRC['marque_name_site']." ".$rRC['modele_name_site']." ".$rRC['type_name_site']." ".$rRC['type_ch']." Ch (".$rRC['type_date_debut']."-".$rRC['type_date_fin'].")"); ?></u></a>
                <br />
                <?php
                }
                ?>
              </p>
      </div>
    </div>


  </div>
</div> 
<?php
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
//////////////////////////////////////// FIN FICHE DATAS /////////////////////////////////////
?>
</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<!-- / Modal FICHE -->

  </div>
</div>
<!-- / PLUS DE DETAILS -->


    </div>

    <div class="col-2 SHOW-LIST-REF-LINE-NAME-VAL-MULTI">

        <div class="row p-0">
          <div class="col-12 SHOW-LIST-REF-LINE-NAME-VAL-MULTI-IN">
              Supplier
              <br>
              <b><?php echo $rPRIX['prix_frs_remise']; ?></b>
          </div>
          <?php
          if($reslogDatas["id"]<251034)
          {
          ?>
          <div class="col-6 SHOW-LIST-REF-LINE-NAME-VAL-MULTI-IN">
              Public price
              <br>
              <b><?php echo $rPRIX['prix_achat_ht']*$piece_quantite; ?> HT</b>
          </div>
          <div class="col-6 SHOW-LIST-REF-LINE-NAME-VAL-MULTI-IN-WITH-BORDER">
              Discount
              <br>
              <b><?php if($rPRIX['prix_remise']==0) { echo "Net"; } else { echo $rPRIX['prix_remise']." %"; } ?></b>
          </div>
          <div class="col-6 SHOW-LIST-REF-LINE-NAME-VAL-MULTI-IN">

            Buying price
            <br>
            <b><?php echo $rPRIX['prix_achat_net_ht']*$piece_quantite; ?> HT
            </b>

          </div>
          <div class="col-6 SHOW-LIST-REF-LINE-NAME-VAL-MULTI-IN-WITH-BORDER">

            Margin
            <br>
            <b><?php echo $rPRIX['prix_marge_m2']; ?> %
            </b>

          </div>
          <?php
          }
          ?>
        </div>

    </div>

    <div class="col-1 SHOW-LIST-REF-LINE-NAME-CART">
          
<!-- FORM ADD TO CART -->
<?php
$qStock=mysql_query("SELECT sp_qte
      FROM 2027_xmassdoc_piece_stock
      JOIN $sqltable_Piece ON  piece_ref_propre = sp_refpropre AND piece_pg_id = sp_pg_id AND piece_pm_id = sp_pm_id
      WHERE piece_ref_propre = '$piece_ref_propre' AND piece_pg_id = '$pg_id' AND piece_pm_id = '$pm_id'
      ORDER BY sp_pg_id, sp_pm_id");
if($rStock=mysql_fetch_array($qStock))
{
  $qtedispo = $rStock['sp_qte'];
}
else
{
  $qtedispo = 0;
}
?>

<?php
if($qtedispo>0)
{
?>
      <form action="<?php echo $domain; ?>/addtocart.php" method="POST" role="form">
      <div class="row">
        <?php
        if($reslogDatas["id"]<251034)
        {
        ?>
        <div class="col-12 SHOW-LIST-REF-LINE-NAME-VAL-PRICE text-center">

                  Selling price
                  <br>
                  <span style="font-size: 21px; color: #52b006;"><?php echo $piece_selling_price_ttc; ?></span> <?php echo $Currency; ?><br>TTC
                  <br><br>
        </div>
        <?php
        }
        ?>
        <div class="col-12 pt-3 pb-1" style=" font-size: 12px; text-align: left;">
      Quantity
        </div>
        <div class="col-6 pr-0">

          
          <input name="reliq" type="hidden" value="0">
          <input name="qte" type="number" step="1" value="1" min="1" max="<?php echo $qtedispo; ?>" class="ADD-TO-CART-QTE">
          <input name="urltakentoadd" type="hidden" value="<?php echo $UrlTakenToAddItem; ?>" />
          <input name="refpropretakentoadd" type="hidden" value="<?php echo $piece_ref_propre; ?>" />
          <input name="reftakentoadd" type="hidden" value="<?php echo $piece_ref; ?>" />
          <input name="pieceidtakentoadd" type="hidden" value="<?php echo $piece_id; ?>" />
          <input name="prixtakentoadd" type="hidden" value="<?php echo $piece_selling_price_ttc; ?>" />
          <input name="consignetakentoadd" type="hidden" value="<?php echo $piece_consigne_price_ttc; ?>" />
          <input name="equiptakentoadd" type="hidden" value="<?php echo $pm_id; ?>" />
          <input name="gammeidtakentoadd" type="hidden" value="<?php echo $pg_id; ?>" />
          <input name="poidgrtakentoadd" type="hidden" value="<?php echo $piece_poids_g; ?>" />
          <input name="poidkgtakentoadd" type="hidden" value="<?php echo $piece_poids_kg; ?>" />
          <input name="dimensiontakentoadd" type="hidden" value="<?php echo $piece_longueur_metre; ?>" />
          <input name="fraispoidstakentoadd" type="hidden" value="<?php echo $piece_poids_frais_port; ?>" />
          <input name="disponibilityqte" type="hidden" value="<?php echo $qtedispo; ?>" />
          <input name="disponibilityreliq" type="hidden" value="0" />
          <input name="addref" type="hidden" value="AJOUT" />
        </div>
        <div class="col-6 pl-1">
         <input type="submit" value="&nbsp;" class="ADD-TO-CART-SUBMIT" />
        </div>
        <div class="col-12 SHOW-LIST-REF-LINE-NAME-VAL-PRICE text-center">


                  
                    <span class="SHOW-LIST-REF-LINE-DISPO-YES">
                    <?php
                    echo "<b>".$qtedispo."</b><br>in stock";
                    ?>
                    </span>
                  

        </div>
      </div>
      </form>
<?php
}
else
{
?>
      <form action="<?php echo $domain; ?>/addtocart.php" method="POST" role="form">
      <div class="row">
        <?php
        if($reslogDatas["id"]<251034)
        {
        ?>
        <div class="col-12 SHOW-LIST-REF-LINE-NAME-VAL-PRICE text-center">

                  Selling price
                  <br>
                  <span style="font-size: 21px; color: #52b006;"><?php echo $piece_selling_price_ttc; ?></span> <?php echo $Currency; ?><br>TTC
                  <br><br>
        </div>
        <?php
        }
        ?>
        <div class="col-12 pt-3 pr-0" style=" font-size: 12px; text-align: left;">
      
            <select name="reliq" class="ADD-TO-CART-QTE">
              <option value="1" selected="selected">
                Standard
              </option>
              <option value="2">
                Express
              </option>
            </select>

        </div>
        <div class="col-12 pb-1" style=" font-size: 12px; text-align: left;">
      Quantity
        </div>
        <div class="col-6 pr-0">

          
          <input name="qte" type="number" step="1" value="1" min="1" class="ADD-TO-CART-QTE">
          <input name="urltakentoadd" type="hidden" value="<?php echo $UrlTakenToAddItem; ?>" />
          <input name="refpropretakentoadd" type="hidden" value="<?php echo $piece_ref_propre; ?>" />
          <input name="reftakentoadd" type="hidden" value="<?php echo $piece_ref; ?>" />
          <input name="pieceidtakentoadd" type="hidden" value="<?php echo $piece_id; ?>" />
          <input name="prixtakentoadd" type="hidden" value="<?php echo $piece_selling_price_ttc; ?>" />
          <input name="consignetakentoadd" type="hidden" value="<?php echo $piece_consigne_price_ttc; ?>" />
          <input name="equiptakentoadd" type="hidden" value="<?php echo $pm_id; ?>" />
          <input name="gammeidtakentoadd" type="hidden" value="<?php echo $pg_id; ?>" />
          <input name="poidgrtakentoadd" type="hidden" value="<?php echo $piece_poids_g; ?>" />
          <input name="poidkgtakentoadd" type="hidden" value="<?php echo $piece_poids_kg; ?>" />
          <input name="dimensiontakentoadd" type="hidden" value="<?php echo $piece_longueur_metre; ?>" />
          <input name="fraispoidstakentoadd" type="hidden" value="<?php echo $piece_poids_frais_port; ?>" />
          <input name="disponibilityqte" type="hidden" value="0" />
          <input name="disponibilityreliq" type="hidden" value="1" />
          <input name="addref" type="hidden" value="AJOUT" />
        </div>
        <div class="col-6 pl-1">
         <input type="submit" value="&nbsp;" class="ADD-TO-CART-SUBMIT-ORANGE" />
        </div>
        <div class="col-12 SHOW-LIST-REF-LINE-NAME-VAL-PRICE text-center">


                  
              <span class="SHOW-LIST-REF-LINE-DISPO-NO" style="font-size: 12px;">
              <?php
              echo "Express :<br>+ 12.00 €";
              ?>
              </span>
                  

        </div>
      </div>
      </form>

<?php
}
?>
<!-- / FORM ADD TO CART -->

    </div>
</div>


  </div>
</div>
<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////             FIN FICHE ARTICLE         ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>