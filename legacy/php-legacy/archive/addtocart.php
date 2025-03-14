<?php
session_start();
include("../shopping_cart.function.php");
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

// url de retour
$urltakentoadd = $_POST['urltakentoadd'];
if (empty($urltakentoadd)){
	$urltakentoadd = $domainparent;
	}
$qte = $_POST['qte'];
if ($qte>0)
{



  function secureSuperGlobalPOST($key)
    {
        $key = htmlspecialchars(stripslashes($key));
        $key = str_ireplace("script", "blocked", $key);
        $key = mysql_real_escape_string($key);
		$key = str_ireplace("\'", "'", $key);
        return $key;
    }


// piece id
$pieceidtakentoadd = $_POST['pieceidtakentoadd'];
$pieceidtakentoadd = secureSuperGlobalPOST($pieceidtakentoadd);
// quantite demandé par le client
$qte = $_POST['qte'];
$qte = secureSuperGlobalPOST($qte);

// reference propre
$refpropretakentoadd = $_POST['refpropretakentoadd'];
$refpropretakentoadd = secureSuperGlobalPOST($refpropretakentoadd);
// prix
$prixtakentoadd = $_POST['prixtakentoadd'];
//$prixtakentoadd = secureSuperGlobalPOST($prixtakentoadd);
// consigne
$consignetakentoadd = $_POST['consignetakentoadd'];
//$consignetakentoadd = secureSuperGlobalPOST($consignetakentoadd);
// prix total
//$prixunitairettc=floatval($prixtakentoadd+$consignetakentoadd);
// equipementiers
$equiptakentoadd = $_POST['equiptakentoadd'];
$equiptakentoadd = secureSuperGlobalPOST($equiptakentoadd);
// action ajout
$addref = $_POST['addref'];
$addref = secureSuperGlobalPOST($addref);

// recuperation d'autres variable

// identifiant de la gamme de produit
$gammeidtakentoadd = $_POST['gammeidtakentoadd'];
//nom de la gamme de produits
$newq=mysql_query("SELECT pg_name
FROM prod_pieces_gamme 
WHERE pg_id = $gammeidtakentoadd
AND pg_hr = '$hr'");
$newr=mysql_fetch_array($newq);
$gammenametakentoadd = $newr['pg_name'];
// nom de l'equipementier
$newq=mysql_query("select * from prod_pieces_marque where pm_id='$equiptakentoadd' ");
$newr=mysql_fetch_array($newq);
$equipnametakentoadd = $newr['pm_name'];
// reference non propre
$reftakentoadd = $_POST['reftakentoadd'];


//////////////////////////////////
//////////////////////////////////
// calcul des frais de port, poids
$poidgrtakentoadd = $_POST['poidgrtakentoadd'];
$poidkgtakentoadd = $_POST['poidkgtakentoadd'];
$dimensiontakentoadd = $_POST['dimensiontakentoadd'];
$fraispoidstakentoadd = $_POST['fraispoidstakentoadd'];
// fin calcul des frais de port, poids
//////////////////////////////////////
//////////////////////////////////////
// reliq
$reliq = $_POST['reliq'];


// disponibilite
$disponibilityqte = $_POST['disponibilityqte'];
if($disponibilityqte>0)
      {
      $disponibility=1;
      }
      else
      {
      $disponibility=0;
      }

// disponibilite
$select['disponibility'] = $disponibility;

// reliq
$select['reliq'] = $reliq;

// piece id dans le panier
$select['id'] = $pieceidtakentoadd;
// prix dans le panier
$select['prix'] = $prixtakentoadd ;
// consigne dans le panier
$select['consigne'] = $consignetakentoadd ;
// prix dans le panier
$select['qte'] = $qte ;
// equipementiers dans le panier
$select['equip'] = $equiptakentoadd ;
// reference propre dans le panier
$select['refpropre'] = $refpropretakentoadd ;
// url pris pour l'insertion dans le panier
$select['urltakentoadd'] = $urltakentoadd ;

// identifiant de la gamme de produits
$select['gammeid'] = $gammeidtakentoadd ;
// nom de la gamme de produits
$select['gammename'] = $gammenametakentoadd ;
// nom de l'equipementier
$select['equipname'] = $equipnametakentoadd ;
// reference non propre
$select['ref'] = $reftakentoadd ;


//////////////////////////////////
//////////////////////////////////
// calcul des frais de port, poids
$select['poidgrtakentoadd'] = $poidgrtakentoadd;
$select['poidkgtakentoadd'] = $poidkgtakentoadd;
$select['dimensiontakentoadd'] = $dimensiontakentoadd;
$select['fraispoidstakentoadd'] = $fraispoidstakentoadd;
// fin calcul des frais de port, poids
//////////////////////////////////////
//////////////////////////////////////

/*$select['id']= "E-PPA002";
$select['qte'] = $_POST['quantity'];
//$select['qte'] ="1";
$select['prix'] = str_replace(" ", "", $Prix_vente_ttc);
//$select['prix'] ="8,33";
$select['taille'] = "";
$select['Montant_consigne_ttc'] = str_replace(" ", "", $Montant_consigne_ttc);
//$select['Montant_consigne_ttc'] ="8,33";
$select['Prix_euro_ht'] = str_replace(" ", "", $Prix_euro_ht);
//$select['Prix_euro_ht'] ="8,33";
$select['Prix_euro_ttc'] = str_replace(" ", "", $Prix_euro_ttc);
//$select['Prix_euro_ttc'] ="10,8";
$select['Prix_achat_ttc'] = str_replace(" ", "", $Prix_achat_ttc);
//$select['Prix_achat_ttc'] ="8,33";
$select['Remise'] = $Remise;
//$select['Remise'] = $remisep;
$select['marge'] = $marge;
//$select['marge'] ="10";
$select['marque'] = $marque;
$select['code_marque'] = $code_marque;
//$select['code_marque'] =$code_marque;
$select['categorie'] = $categorie;
$select['reference_art'] = $Ref_Fournisseur; */

//echo "*************************************************************<br><br>";
//echo "<br><br>";
//var_dump($select);
if(($prixtakentoadd>0)&&($qte>0))
{
ajout($select);
}

}

header("location:".$urltakentoadd);

?>