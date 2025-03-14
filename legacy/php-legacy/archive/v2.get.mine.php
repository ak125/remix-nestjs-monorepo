<?php
require_once('../config/sql.conf.php');
require_once('config/meta.conf.php');

$ref_mine=$_POST['ref_mine'];
$linkto=$_POST['linkto'];


$q=mysql_query(" SELECT mine_type_id FROM prod_auto_type_mine  WHERE mine_search LIKE '$ref_mine' ");
$r=mysql_fetch_array($q);
$type_id=$r['mine_type_id'];

$q2=mysql_query("SELECT marque_id, marque_alias, modele_id, modele_alias, type_id, type_alias FROM $sqltable_Car_type 
    JOIN $sqltable_Car_modele ON modele_id = type_modele_id
    JOIN $sqltable_Car_marque ON marque_id =type_marque_id
    WHERE type_id = '$type_id' AND marque_affiche = 1 AND modele_affiche = 1 AND type_affiche = 1 ORDER BY type_id LIMIT 0,1");
if($r2=mysql_fetch_array($q2))
{
?>
    <?php
    if($linkto == 'carType')
    {           

        $linktoCARTYPE = $domain."/searchcar/".$r2['marque_alias']."-".$r2['marque_id']."/".$r2['modele_alias']."-".$r2["modele_id"]."/".$r2["type_alias"]."-".$r2["type_id"];
        header('Location:'.$linktoCARTYPE);
        exit();

    }
    ?>
<?Php
}
else
{
$linktonoResult = $domain."/welcome";
header('Location:'.$linktonoResult);
exit();
}
?>