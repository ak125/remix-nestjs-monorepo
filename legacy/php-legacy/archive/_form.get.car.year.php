<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$thisYear = date("Y");
$formCarMarqueid=$_GET['formCarMarqueid'];

if($formCarMarqueid>0)
{
        ?>
        <?php
        // Annee MIN
        $query_get_car_min_year = "SELECT DISTINCT MIN(MODELE_YEAR_FROM) AS minYear   
        FROM AUTO_MODELE 
        WHERE MODELE_DISPLAY = 1 AND MODELE_MARQUE_ID = $formCarMarqueid ";
        $request_get_car_min_year = $conn->query($query_get_car_min_year);
        if ($request_get_car_min_year->num_rows > 0) 
        {
        $result_get_car_min_year = $request_get_car_min_year->fetch_assoc();
        $minYear=$result_get_car_min_year["minYear"];
        }
        // Annee MAX
        $query_get_car_max_year = "SELECT DISTINCT MAX(COALESCE(MODELE_YEAR_TO,'$thisYear')) AS maxYear   
        FROM AUTO_MODELE 
        WHERE MODELE_DISPLAY = 1 AND MODELE_MARQUE_ID = $formCarMarqueid ";
        $request_get_car_max_year = $conn->query($query_get_car_max_year);
        if ($request_get_car_max_year->num_rows > 0) 
        {
        $result_get_car_max_year = $request_get_car_max_year->fetch_assoc();
        $maxYear=$result_get_car_max_year["maxYear"];
        }
        ?>
        <option value="0">Année</option>
        <?php 
        while($maxYear>=$minYear)
        {
            if(($maxYear==2020)||($maxYear==2010)||($maxYear==2000))
            {
                ?>
                <option value="<?php echo $maxYear; ?>"  class="favorite"><?php echo $maxYear; ?></option>
                <?php
            }
            else
            {
                ?>
                <option value="<?php echo $maxYear; ?>"><?php echo $maxYear; ?></option>
                <?php
            }
        $maxYear--;
        }

}
else
{
  ?>
  <option value="0">Année</option>
  <?php
}
?>