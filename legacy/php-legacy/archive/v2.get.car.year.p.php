<?php
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$formCarMarqueid=$_GET['formCarMarqueid'];
$formGammeid=$_GET['formGammeid'];

if(($formCarMarqueid>0)&&($formGammeid>0))
{
        // Annee MIN
        $qmin=mysql_query("SELECT DISTINCT MIN(modele_annee_debut) AS anneedebut 
            FROM $sqltable_Car_modele 
            JOIN $sqltable_Car_type ON type_modele_id = modele_id
            JOIN prod_relation_auto ON relauto_type_id = type_id 
            JOIN prod_relation ON relation_id = relauto_relation_id 
            JOIN $sqltable_Piece ON piece_id = relation_piece_id 
            WHERE modele_marque_id = $formCarMarqueid 
            AND modele_affiche = 1 AND modele_vt_ap = 1
            AND type_affiche = 1 AND relauto_pg_id = $formGammeid AND piece_affiche = 1");

        
        $rmin=mysql_fetch_array($qmin);
        $minYear=$rmin["anneedebut"];

        // Annee MAX
        $qmax=mysql_query("SELECT DISTINCT MAX(COALESCE(modele_annee_fin,'$thisYear')) AS anneefin
            FROM $sqltable_Car_modele 
            JOIN $sqltable_Car_type ON type_modele_id = modele_id
            JOIN prod_relation_auto ON relauto_type_id = type_id 
            JOIN prod_relation ON relation_id = relauto_relation_id 
            JOIN $sqltable_Piece ON piece_id = relation_piece_id 
            WHERE modele_marque_id = $formCarMarqueid 
            AND modele_affiche = 1  AND modele_vt_ap = 1 
            AND type_affiche = 1 AND relauto_pg_id = $formGammeid AND piece_affiche = 1");
        $rmax=mysql_fetch_array($qmax);
        $maxYear=$rmax["anneefin"];

        ?>


        <option value="0">- Ann&eacute;e -</option>
        <?php 
        while($maxYear>=$minYear)
        {
        ?>
        <option value="<?php echo $maxYear; ?>"><?php echo $maxYear; ?></option>
        <?php
        $maxYear--;
        }

}
else
{
  ?>
  <option value="0">- Ann&eacute;e -</option>
  <?php
}
?>