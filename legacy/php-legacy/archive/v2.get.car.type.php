<?php
require_once('../config/sql.conf.php');
require_once('config/meta.conf.php');

$formCarMarqueid=$_GET['formCarMarqueid'];
$formCarMarqueYear=$_GET['formCarMarqueYear'];
$formCarModelid=$_GET['formCarModelid'];

if(($formCarMarqueid>0)&&($formCarMarqueYear>0)&&($formCarModelid>0))
{

        ?>

        <option value="0">- Motorization -</option>
        <?php 
        $queryGetType = mysql_query("SELECT DISTINCT type_id, type_alias, type_name_site ,type_carburant ,type_ch ,
            LEFT(type_date_debut,4) AS type_date_debut ,COALESCE(LEFT(type_date_fin,4),'$thisYear') AS type_date_fin,
            modele_alias, marque_alias
            FROM $sqltable_Car_type 
            JOIN $sqltable_Car_modele ON modele_id = type_modele_id
            JOIN $sqltable_Car_marque ON marque_id = modele_marque_id
            WHERE type_modele_id  = $formCarModelid AND type_affiche = 1
            ORDER BY type_moteur,type_litre,type_alias,type_ch,type_date_debut");
        while($resultGetType = mysql_fetch_array($queryGetType))
        {

$formCarLink = $domain."/searchcar/".$resultGetType['marque_alias']."-".$formCarMarqueid."/".$resultGetType['modele_alias']."-".$formCarModelid."/".$resultGetType["type_alias"]."-".$resultGetType["type_id"];
?>
<option value="<?php echo $formCarLink; ?>"><?php echo utf8_encode($resultGetType['type_name_site']." ".$resultGetType['type_carburant']." ".$resultGetType['type_ch']." Ch  &nbsp; ".$resultGetType['type_date_debut']."-".$resultGetType['type_date_fin']); ?></option>
<?php

        }

}
else
{
  ?>
  <option value="0">- Motorization -</option>
  <?php
}
?>