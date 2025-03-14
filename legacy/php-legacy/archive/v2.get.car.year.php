<?php
require_once('../config/sql.conf.php');
require_once('config/meta.conf.php');

$formCarMarqueid=$_GET['formCarMarqueid'];

if($formCarMarqueid>0)
{
        // Annee MIN
        $qmin=mysql_query("SELECT DISTINCT MIN(modele_annee_debut) AS anneedebut 
        FROM 2022_auto_modele
        WHERE modele_marque_id = $formCarMarqueid AND modele_affiche = 1");
        $rmin=mysql_fetch_array($qmin);
        $minYear=$rmin["anneedebut"];

        // Annee MAX
        $qmax=mysql_query("SELECT DISTINCT MAX(COALESCE(modele_annee_fin,'$thisYear')) AS anneefin
        FROM 2022_auto_modele 
        WHERE modele_marque_id = $formCarMarqueid AND modele_affiche = 1");
        $rmax=mysql_fetch_array($qmax);
        $maxYear=$rmax["anneefin"];

        ?>


        <option value="0">- Year -</option>
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
  <option value="0">- Year -</option>
  <?php
}
?>