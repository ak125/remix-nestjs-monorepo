<?php
$qf=mysql_query("SELECT mf_id, mf_alias, mf_name_site, mf_description  FROM $sqltable_Catalog_family WHERE mf_affiche = 1 
  ORDER BY mf_tri");
while($rf=mysql_fetch_array($qf))
{
$family_id=$rf['mf_id'];
$family_name_site=utf8_encode($rf['mf_name_site']);
$family_alias=$rf['mf_alias'];
?>
<div class="row">
<div class="col-12 HOME-CATALOG-FAMILY-MOBILE text-left">

<div class="row">
  <div class="col-12 HOME-CATALOG-FAMILY-TITLE-MOBILE">
    <i><?php  echo $family_name_site; ?></i><br>
        <img src="<?php echo $domainparent; ?>/assets/img/separator.png" />
  </div>
  <div class="col-12 HOME-CATALOG-FAMILY-PRODUCTS-MOBILE">
    






<div class="row">

<?php
/*$qgammePrimaire=mysql_query("SELECT pg_alias, pg_id, pg_name_site, pg_name_meta, pg_image FROM $sqltable_Catalog_gamme 
  JOIN $sqltable_Piece_gamme ON pg_id = mc_pg_id
  WHERE mc_mf_id = $family_id AND pg_affiche IN(1,2) ORDER BY mc_tri");*/
$qgammePrimaire=mysql_query("SELECT DISTINCT pg_id, pg_alias,  pg_name_site, pg_name_meta, pg_image FROM $sqltable_Catalog_gamme 
  JOIN $sqltable_Piece_gamme ON pg_id = mc_pg_id
  JOIN prod_relation_auto ON  relauto_pg_id = pg_id
  JOIN prod_relation ON relauto_relation_id = relation_id 
  JOIN prod_pieces ON piece_id = relation_piece_id 
  WHERE relauto_type_id = $type_id AND piece_affiche = 1
  AND mc_mf_id = $family_id AND pg_affiche IN(1,2) ORDER BY mc_tri");
while($rgammePrimaire=mysql_fetch_array($qgammePrimaire))
{
$pg_id = $rgammePrimaire['pg_id'];
$pg_image = $rgammePrimaire['pg_image'];
$pg_name_site = utf8_encode($rgammePrimaire['pg_name_site']);
$pg_name_meta = utf8_encode($rgammePrimaire['pg_name_meta']);
$LinkToGammeCar = $domain."/searchcar/".$rgammePrimaire['pg_alias']."-".$rgammePrimaire['pg_id']."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id;
?>
<div class="col-6 col-sm-4 col-md-2 nopadding" style="background: #ECECED; border: 2px solid #fff; margin-bottom: 11px;">
  
  <div class="container nopadding" style="background: #ECECED;">
    <a href="<?php echo $LinkToGammeCar; ?>">
      <div class="col-12 text-center align-middle" style="padding: 11px; background: #fff;   border: 1px solid #eceded">
        
        <?php
        $gammePhoto = $domainparent."/upload/articles/gammes-produits/catalogue/".$pg_image;
        ?>
        <img src="<?php echo $gammePhoto; ?>" alt="<?php  echo $pg_name_meta; ?>" class="w-100" style="max-width: 100%;"/>

      </div>
      <div class="col-12 text-center" style="font-size: 15px; padding-top: 11px; padding-bottom: 13px; background: #404950; color: #ffffff">
        <?php echo $pg_name_site; ?>
      </div>
    </a>
  </div>


</div>
<?php
}
?>

</div>






  </div>
</div>

</div>
</div>
<?php
}
?>