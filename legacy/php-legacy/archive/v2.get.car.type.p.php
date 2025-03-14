<?php
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$formCarMarqueid=$_GET['formCarMarqueid'];
$formCarMarqueYear=$_GET['formCarMarqueYear'];
$formCarModelid=$_GET['formCarModelid'];
$formGammeid=$_GET['formGammeid'];

if(($formCarMarqueid>0)&&($formCarMarqueYear>0)&&($formCarModelid>0)&&($formGammeid>0))
{

        ?>

        <option value="0">- Motorisation -</option>
        <?php 
        $queryGetType = mysql_query("SELECT DISTINCT type_id, type_alias, type_name_site, type_carburant, type_ch, 
            LEFT(type_date_debut,4) AS type_date_debut ,COALESCE(LEFT(type_date_fin,4),'$thisYear') AS type_date_fin, 
            modele_alias, marque_alias,
            pg_alias
            FROM $sqltable_Car_marque
            JOIN $sqltable_Car_modele ON modele_marque_id = marque_id
            JOIN $sqltable_Car_type  ON type_modele_id = modele_id
            JOIN prod_relation_auto ON relauto_type_id = type_id 
            JOIN prod_relation ON relation_id = relauto_relation_id 
            JOIN $sqltable_Piece ON piece_id = relation_piece_id 
            JOIN $sqltable_Piece_gamme ON pg_id = piece_pg_id
            WHERE type_modele_id  = $formCarModelid AND type_affiche = 1 AND relauto_pg_id = $formGammeid AND piece_affiche = 1
            ORDER BY type_moteur ,type_litre ,type_name_site ,type_ch ,type_date_debut");
        while($resultGetType = mysql_fetch_array($queryGetType))
        {

$formGammeCarLink = $domain."/".$RepPieceGamme."/".$resultGetType['pg_alias']."-".$formGammeid."/".$resultGetType['marque_alias']."-".$formCarMarqueid."/".$resultGetType['modele_alias']."-".$formCarModelid."/".$resultGetType["type_alias"]."-".$resultGetType["type_id"].".html";
?>
<option value="<?php echo $formGammeCarLink; ?>"><?php echo utf8_encode($resultGetType['type_name_site']." ".$resultGetType['type_carburant']." ".$resultGetType['type_ch']." Ch  &nbsp; ".$resultGetType['type_date_debut']."-".$resultGetType['type_date_fin']); ?></option>
<?php

        }

}
else
{
  ?>
  <option value="0">- Motorisation -</option>
  <?php
}
?>