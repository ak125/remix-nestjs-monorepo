<?php
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$formCarMarqueid=$_GET['formCarMarqueid'];
$formCarMarqueYear=$_GET['formCarMarqueYear'];
$formGammeid=$_GET['formGammeid'];

if(($formCarMarqueid>0)&&($formCarMarqueYear>0))
{

        ?>

        <option value="0">- Mod&egrave;le -</option>
        <?php 
        $queryGetModel = mysql_query("SELECT DISTINCT modele_id, modele_name_site, modele_annee_debut, 
            COALESCE(modele_annee_fin,'$thisYear') AS modele_annee_fin 
            FROM $sqltable_Car_modele 
            JOIN $sqltable_Car_type ON type_modele_id = modele_id
            JOIN prod_relation_auto ON relauto_type_id = type_id 
            JOIN prod_relation ON relation_id = relauto_relation_id 
            JOIN $sqltable_Piece ON piece_id = relation_piece_id 
            WHERE modele_marque_id = $formCarMarqueid AND modele_affiche = 1 AND modele_vt_ap = 1 
            AND type_affiche = 1 AND relauto_pg_id = $formGammeid AND piece_affiche = 1
            AND modele_annee_debut <= $formCarMarqueYear AND COALESCE(modele_annee_fin,'$thisYear') >= $formCarMarqueYear
            ORDER BY modele_sm_id, modele_tri");
        while($resultGetModel = mysql_fetch_array($queryGetModel))
        {
        ?>
    <option value="<?php echo $resultGetModel['modele_id']; ?>"><?php echo utf8_encode($resultGetModel['modele_name_site']." &nbsp; ".$resultGetModel['modele_annee_debut']."-".$resultGetModel['modele_annee_fin']); ?></option>
        <?php
        }

}
else
{
  ?>
  <option value="0">- Mod&egrave;le -</option>
  <?php
}
?>