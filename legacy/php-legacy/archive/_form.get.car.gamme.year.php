<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$thisYear = date("Y");
$formGammeid=$_GET['formGammeid'];
$formCarMarqueid=$_GET['formCarMarqueid'];

if(($formCarMarqueid>0)&&($formGammeid>0))
{
        ?>
        <?php
        // Annee min max
            $query_get_car_minmax_year = "SELECT MIN(MODELE_YEAR_FROM) AS minYear , 
                MAX(COALESCE(MODELE_YEAR_TO,'$thisYear')) AS maxYear
                FROM AUTO_MODELE
                JOIN AUTO_TYPE ON TYPE_MODELE_ID = MODELE_ID  
                JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
                JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
                WHERE MODELE_MARQUE_ID = $formCarMarqueid  AND RTP_PG_ID = $formGammeid
                AND MODELE_DISPLAY = 1 AND TYPE_DISPLAY = 1 AND PIECE_DISPLAY = 1";
            $request_get_car_minmax_year = $conn->query($query_get_car_minmax_year);
            if ($request_get_car_minmax_year->num_rows > 0) 
            {
            $result_get_car_minmax_year = $request_get_car_minmax_year->fetch_assoc();
            $minYear=$result_get_car_minmax_year["minYear"];
            $maxYear=$result_get_car_minmax_year["maxYear"];
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