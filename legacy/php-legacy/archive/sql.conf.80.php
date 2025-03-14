<?php 
$servername = "127.0.0.1";
$username = "MASSDOCUSER";
$password = "MASSDOC@mg.2022";
$dbname = "MASSDOC";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn,"utf8");
?>
<?php
// MONO LANGUE
// parametrage de la langue dans le site
$hr='fr';
$HR='FR';
$hrHR='fr-FR';
$hrQuery=1;
// recuperation des données de configuration du site
$query_cnf = "SELECT CNF_NAME, CNF_DOMAIN, CNF_ADDRESS, CNF_MAIL, CNF_PHONE, CNF_PHONE_CALL, CNF_HR, CNF_GROUP_NAME, CNF_GROUP_DOMAIN, CNF_OWNER_NAME, CNF_OWNER_DOMAIN FROM ___CONFIG 
WHERE CNF_HR = $hrQuery ";
$request_cnf = $conn->query($query_cnf);
if ($request_cnf->num_rows > 0) 
{
    $result_cnf = $request_cnf->fetch_assoc();
        //parametrage des liens clefs dansle site web
        //$domain = $result_cnf["CNF_DOMAIN"];
		$domain = "http://35.233.73.7";
        $domainname = $result_cnf["CNF_NAME"];
        //parametrage des données relatifs au site
        $domainwebsitename = $result_cnf["CNF_NAME"];
        $domainwebsiteaddress = $result_cnf["CNF_ADDRESS"];
        $domainwebsitemail = $result_cnf["CNF_MAIL"];
        $domainwebsitetel = $result_cnf["CNF_PHONE"];
        $domainwebsiteteltocall = $result_cnf["CNF_PHONE_CALL"];
        $ownername = $result_cnf["CNF_OWNER_NAME"];
}
// repertory // Liste des repertoire predefinis
$domainClient2022 = "account";
$blog = "blog-pieces-auto";
$blog_arianetitle="Blog automobile";
    $entretien = "conseils";
    $entretien_title = "Montage et entretien";
$mySpace = "mon-compte";
$Piece = "pieces";
$Auto = "constructeurs";
$GlobalSiteCurrencyChar = "€";
$GlobalSiteTva = 20;
$GlobalSiteTvaFormatted = number_format($GlobalSiteTva, 2, '.', '');
$GlobalSiteTvaCoeff = 1.2;
// repertory // Liste des repertoire predefinis
?>
<?php
// PARAMETRAGE IP
$query_ip = "SELECT CNFIP_NAME, CNFIP_ALIAS, CNFIP_VALUE FROM ___CONFIG_IP 
WHERE CNFIP_ALIAS = 'tn' ";
$request_ip = $conn->query($query_ip);
if ($request_ip->num_rows > 0) 
{
    $result_ip = $request_ip->fetch_assoc();
    $IpBureauTn=$result_ip["CNFIP_VALUE"];
}
$query_ip = "SELECT CNFIP_NAME, CNFIP_ALIAS, CNFIP_VALUE FROM ___CONFIG_IP 
WHERE CNFIP_ALIAS = 'fr' ";
$request_ip = $conn->query($query_ip);
if ($request_ip->num_rows > 0) 
{
    $result_ip = $request_ip->fetch_assoc();
    $IpBureauFr=$result_ip["CNFIP_VALUE"];
}
$query_ip = "SELECT CNFIP_NAME, CNFIP_ALIAS, CNFIP_VALUE FROM ___CONFIG_IP 
WHERE CNFIP_ALIAS = 'amin' ";
$request_ip = $conn->query($query_ip);
if ($request_ip->num_rows > 0) 
{
    $result_ip = $request_ip->fetch_assoc();
    $IpBureauAmin=$result_ip["CNFIP_VALUE"];
}
?>
<?php
/**
MARQUEURS STATIQUES
**/
// MARQUEUR PRIX PAS CHER
$PrixPasCher=array();
$PrixPasCher[0]="pas cher";
$PrixPasCher[1]="prix bas";
$PrixPasCher[2]="moins cher";
$PrixPasCher[3]="prix réduit";
$PrixPasCher[4]="meilleur prix";
$PrixPasCher[5]="prix mini";
$PrixPasCher[6]="bas prix";
$PrixPasCher[7]="mini prix";
$PrixPasCher[8]="qualité prix";
$PrixPasCher[9]="meilleur tarif";
$PrixPasCher[10]="bas tarif";
$PrixPasCher[11]="tarif mini";
$PrixPasCher[12]="mini tarif";
$PrixPasCher[13]="super prix";
$PrixPasCher[14]="prix super";
$PrixPasCher[15]="coût réduit";
$PrixPasCher[16]="tarif réduit";
$PrixPasCher[17]="bas coût";
$PrixPasCher[18]="mini coût";
$PrixPasCher[19]="coût mini";
$PrixPasCher[20]="bon prix";
$PrixPasCher[21]="bon tarif";
$PrixPasCher[22]="juste prix";
$PrixPasCher[23]="tarif juste";
$PrixPasCher[24]="petit prix";
$PrixPasCher[25]="petit tarif";
$PrixPasCherLength=26;

// MARQUEURS VOUS PROPOSE
$VousPropose=array();
$VousPropose[0]="vous propose";
$VousPropose[1]="vous offre";
$VousPropose[2]="met à votre disposition";
$VousPropose[3]="vend en ligne";
$VousPropose[4]="propose en ligne";
$VousPropose[5]="offre en ligne";
$VousPropose[6]="met en ligne";
$VousPropose[7]="propose à ces clients";
$VousPropose[8]="offre à ces clients";
$VousPropose[9]="propose aux acheteurs";
$VousPropose[10]="offre aux acheteurs";
$VousPropose[11]="vend à ces clients";
$VousPropose[12]="propose à ces clients";
$VousProposeLength=13;
 
// MARQUEURS FICHE COMPATIBILITE
$FicheCompatibilite=array();
$FicheCompatibilite[0]="soit identique avec votre voiture";
$FicheCompatibilite[1]="soit compatible avec ceux déposées";
$FicheCompatibilite[2]="soit identiques à ceux démontées";
$FicheCompatibilite[3]="soit compatible avec votre véhicule";
$FicheCompatibiliteLength=4; 
?>
<?php
function content_cleaner($string)
{
    $string = str_replace("\n"," ",$string); 
    $string = str_replace("\r"," ",$string); 
    $string = str_replace("\t"," ",$string); 
    $string = str_replace("&agrave;", "à", $string);
    $string = str_replace("&eacute;", "é", $string);
    $string = str_replace("&egrave;", "è", $string);
    $string = str_replace("&ecirc;", "ê", $string);
    $string = str_replace("&ocirc;", "ô", $string);
    $string = str_replace("&icirc;", "î", $string);
    $string = str_replace("&rsquo;", "'", $string);
    $string = str_replace("&#39;", "'", $string);
    $string = str_replace("&nbsp;", " ", $string);
    $string = str_replace("http://w", "https://w", $string);
    $string = str_replace("  ", " ", $string);
    $string = str_replace("..", ".", $string);
    return $string;
}
?>
<?php
function ClearSearchQuest($string)
{
    $string=str_replace("(","",$string);
    $string=str_replace(")","",$string);
    $string=str_replace("[","",$string);
    $string=str_replace("]","",$string);
    $string=str_replace(",","",$string);
    $string=str_replace("’","",$string);
    $string=str_replace("'","",$string);
    $string=str_replace(" ","",$string);
    $string=str_replace(".","",$string);
    $string=str_replace("/","",$string);
    $string=str_replace("_","",$string);
    $string=str_replace("*","",$string);
    $string=str_replace("-","",$string);
    return $string;
}
?>