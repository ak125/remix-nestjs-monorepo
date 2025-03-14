<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$thisYear = date("Y");
$formCarMarqueid=$_GET['formCarMarqueid'];
$formCarMarqueYear=$_GET['formCarMarqueYear'];

if(($formCarMarqueid>0)&&($formCarMarqueYear>0))
{

        ?>

        <option value="0">Modèle</option>
        <?php 
        $query_get_car_in_modele = "SELECT DISTINCT MODELE_ID, MODELE_NAME,  
            MODELE_YEAR_FROM, COALESCE(MODELE_YEAR_TO,'$thisYear') AS MODELE_TO, MODELE_SORT 
            FROM AUTO_MODELE
            WHERE MODELE_DISPLAY = 1 AND MODELE_MARQUE_ID = $formCarMarqueid
            AND MODELE_YEAR_FROM <= $formCarMarqueYear AND COALESCE(MODELE_YEAR_TO,'$thisYear') >= $formCarMarqueYear
            ORDER BY MODELE_SORT";
        $request_get_car_in_modele = $conn->query($query_get_car_in_modele);
        if ($request_get_car_in_modele->num_rows > 0) 
        {
            while($result_get_car_in_modele = $request_get_car_in_modele->fetch_assoc())
            {
    ?>
    <option value="<?php echo $result_get_car_in_modele['MODELE_ID']; ?>"><?php echo $result_get_car_in_modele['MODELE_NAME']." &nbsp; ".$result_get_car_in_modele['MODELE_YEAR_FROM']."-".$result_get_car_in_modele['MODELE_TO']; ?></option>
    <?php
            }
        }
        else
        {
          ?>
          <option value="0">Modèle</option>
          <?php
        }
}
else
{
  ?>
  <option value="0">Modèle</option>
  <?php
}
?>