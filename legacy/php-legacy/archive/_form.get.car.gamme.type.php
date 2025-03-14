<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once('config/sql.conf.php');
require_once('config/meta.conf.php');

$thisYear = date("Y");
$formGammeid=$_GET['formGammeid'];
    $query_pg_in = "SELECT PG_ALIAS 
        FROM PIECES_GAMME 
        WHERE PG_ID = $formGammeid 
        AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)";
    $request_pg_in = $conn->query($query_pg_in);
    $result_pg_in = $request_pg_in->fetch_assoc();
    $this_pg_alias = $result_pg_in["PG_ALIAS"];
$formCarMarqueid=$_GET['formCarMarqueid'];
$formCarMarqueYear=$_GET['formCarMarqueYear'];
$formCarModelid=$_GET['formCarModelid'];

if(($formCarMarqueid>0)&&($formCarMarqueYear>0)&&($formCarModelid>0)&&($formGammeid>0))
{

        ?>

        <option value="0">Motorisation</option>
        <?php 
        $query_get_car_in_type_fuel = "SELECT DISTINCT TMF_ID , TMF_MOTOR, TMF_SORT
            FROM AUTO_TYPE_MOTOR_FUEL
            JOIN AUTO_TYPE ON TYPE_TMF_ID = TMF_ID
            JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
            JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
            WHERE TYPE_MODELE_ID = $formCarModelid AND TMF_DISPLAY = 1 AND TYPE_DISPLAY = 1 
            AND RTP_PG_ID = $formGammeid AND PIECE_DISPLAY = 1
			AND TYPE_YEAR_FROM <= $formCarMarqueYear AND COALESCE(TYPE_YEAR_TO,'$thisYear') >= $formCarMarqueYear
            ORDER BY TMF_SORT";
        $request_get_car_in_type_fuel = $conn->query($query_get_car_in_type_fuel);
        if ($request_get_car_in_type_fuel->num_rows > 0) 
        {
        while($result_get_car_in_type_fuel = $request_get_car_in_type_fuel->fetch_assoc())
        {
        $formCarMotorid = $result_get_car_in_type_fuel["TMF_ID"];
        $formCarMotorlabel = $result_get_car_in_type_fuel["TMF_MOTOR"];


                $query_get_car_in_type = "SELECT DISTINCT TYPE_ID, TYPE_ALIAS, TYPE_NAME, TYPE_POWER_PS, TYPE_FUEL, TYPE_YEAR_FROM, COALESCE(TYPE_YEAR_TO,'$thisYear') AS TYPE_TO,
                    MODELE_ALIAS, MARQUE_ALIAS, TYPE_SORT
                    FROM AUTO_TYPE
                    JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
                    JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
                    JOIN PIECES_RELATION_TYPE ON RTP_TYPE_ID = TYPE_ID
                    JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID AND PIECE_PM_ID = RTP_PM_ID
                    JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
                    WHERE TYPE_TMF_ID = $formCarMotorid AND TYPE_MODELE_ID = $formCarModelid AND RTP_PG_ID = $formGammeid
                    AND TYPE_DISPLAY = 1 AND PIECE_DISPLAY = 1
					AND TYPE_YEAR_FROM <= $formCarMarqueYear AND COALESCE(TYPE_YEAR_TO,'$thisYear') >= $formCarMarqueYear
                    ORDER BY TYPE_SORT";
                $request_get_car_in_type = $conn->query($query_get_car_in_type);
                if ($request_get_car_in_type->num_rows > 0) 
                {
                ?>
                <optgroup label="<?php echo $formCarMotorlabel; ?>" class="favorite">
                <?php
                    while($result_get_car_in_type = $request_get_car_in_type->fetch_assoc())
                    {

        $formCarLink = $domain."/".$Piece."/".$this_pg_alias."-".$formGammeid."/".$result_get_car_in_type['MARQUE_ALIAS']."-".$formCarMarqueid."/".$result_get_car_in_type['MODELE_ALIAS']."-".$formCarModelid."/".$result_get_car_in_type["TYPE_ALIAS"]."-".$result_get_car_in_type["TYPE_ID"].".html";
        ?>
        <option  class="white" value="<?php echo $formCarLink; ?>"><?php echo $result_get_car_in_type['TYPE_NAME']." ".$result_get_car_in_type['TYPE_FUEL']." ".$result_get_car_in_type['TYPE_POWER_PS']." Ch  &nbsp; ".$result_get_car_in_type['TYPE_YEAR_FROM']."-".$result_get_car_in_type['TYPE_TO']; ?></option>
        <?php

                    }
                ?>
                </optgroup>
                <?php
                }


        }
        }
        else
        {
            ?>
            <option value="0">Motorisation</option>
            <?php
        }


}
else
{
  ?>
  <option value="0">Motorisation</option>
  <?php
}
?>