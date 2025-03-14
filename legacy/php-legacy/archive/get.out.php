<?php
session_start();
session_destroy();
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');

//header("Location: ".$domain);
?>
<meta http-equiv="refresh" content="0; URL=<?php echo $domain; ?>/expired">