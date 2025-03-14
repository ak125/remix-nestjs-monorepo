<?php 
session_start();
// parametres relatifs à la page
$typefile="search";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
if(isset($_POST["query"]))  
{  
      $query = $_POST["query"];
      $output = ''; 
      $query = "SELECT * FROM PIECES_GAMME WHERE PG_NAME LIKE '$query%' AND PG_LEVEL IN (1,2) AND PG_DISPLAY = 1 ORDER BY PG_LEVEL";  
      $result = mysqli_query($conn, $query);  
      $output = '<ul class="quicksearch">';  
      if(mysqli_num_rows($result) > 0)  
      {  
           while($row = mysqli_fetch_array($result))  
           {  
           		$linktoGamme = $domain."/".$Piece."/".$row["PG_ALIAS"]."-".$row["PG_ID"].".html";
                $output .= '<li><a href="'.$linktoGamme.'">'.$row["PG_NAME"].'</a></li>';  
           }  
      }  
      /*else  
      {  
           $output .= '<li>Country Not Found</li>';  
      }*/  
      $output .= '</ul>';  
      echo $output;  
} 
?>