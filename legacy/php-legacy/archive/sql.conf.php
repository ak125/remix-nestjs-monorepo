<?php 
$servername = "127.0.0.1";
$username = "MASSDOCSUPERUSER";
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
// parametrage de la langue dans le site

    $hr='fr';
    $HR='FR';
    $hrHR='fr-FR';
    $hrQuery=1;

// parametrage session
    $sessionlog = 'mass7mylog';
    $sessionkey = 'mass7mykey';
    $pswdctyptogram = 'MSz2Cc0re';

?>
<?php
// recuperation des données de configuration du site
$query_cnf = "SELECT * FROM ___CONFIG WHERE CNF_HR = $hrQuery";
$request_cnf = $conn->query($query_cnf);
if ($request_cnf->num_rows > 0) 
{
    $result_cnf = $request_cnf->fetch_assoc();
        // COMPANY       
        $domainname = $result_cnf["CNF_NAME"];
        $domain = $result_cnf["CNF_DOMAIN"]."/core";
		//$domain = "http://35.233.73.7/core";
        $domainparent = $result_cnf["CNF_DOMAIN"];
		//$domainparent = "http://35.233.73.7";
        $siteaddress = $result_cnf["CNF_ADDRESS"];
        $sitemail = $result_cnf["CNF_MAIL"];
        $sitephone = $result_cnf["CNF_PHONE"];
        $sitephonetocall = $result_cnf["CNF_PHONE_CALL"];
        // GROUP NAME // SAME AS COMPANY IN MONO ENTITY
        $groupname = $result_cnf["CNF_GROUP_NAME"];
        $groupdomain = $result_cnf["CNF_GROUP_DOMAIN"];
        // OWNER // FA GROUP
        $domaincorename = $result_cnf["CNF_OWNER_NAME"];
        $ownerdomain = $result_cnf["CNF_OWNER_DOMAIN"];

        
        // gestion d'access
        $accessPermittedLink = $domain."/welcome";
        $accessRefusedLink = $domain."/denied";
        $accessExpiredLink = $domain."/expired";
        $accessSuspendedLink = $domain."/suspended";
        $destinationLinkWelcome = $domain."/welcome";
}
$GlobalSiteCurrencyChar = "€";
$GlobalSiteTva = 20;
$GlobalSiteTvaFormatted = number_format($GlobalSiteTva, 2, '.', '');
$GlobalSiteTvaCoeff = 1.2;
?>
<?php
//Note
//$ORD_DEPT_ID = 0 --> NON PAYE
//$ORD_DEPT_ID = 1 --> PAYE --> COMMERCIAL
//$ORD_DEPT_ID = 2 --> PAYE --> EXPEDITION
//$ORD_DEPT_ID = 99 --> ARCHIVE
?>