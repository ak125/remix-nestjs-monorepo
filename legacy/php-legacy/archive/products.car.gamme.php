<?php 
session_start();
// parametres relatifs à la page
$typefile="composit";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$pg_id=$_GET["pg_id"];
$marque_id=$_GET["marque_id"];
$modele_id=$_GET["modele_id"];
$type_id=$_GET["type_id"];
//Get filtre
if(isset($_GET['filtre_union']))
{
	$filtre_piece_fil_id = $_GET['filtre_union'];
}
else
{
	$filtre_piece_fil_id = 0;
}
if(isset($_GET['filtre_essieu']))
{
	$filtre_psf_id = $_GET['filtre_essieu'];
}
else
{
	$filtre_psf_id = 0;
}
if(isset($_GET['filtre_equip']))
{
	$filtre_pm_id = $_GET['filtre_equip'];
}
else
{
	$filtre_pm_id = 0;
}
?>
<?php
// QUERY SELECTOR CAR
$query_selector = "SELECT TYPE_DISPLAY 
	FROM AUTO_TYPE 
	JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
	JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
	WHERE TYPE_ID = $type_id
	AND TYPE_MODELE_ID = $modele_id 
	AND TYPE_MARQUE_ID = $marque_id 
	AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	$car_display = $result_selector['TYPE_DISPLAY'];
	$car_exist = 1;	
}
else
{
$car_display = 0;
$car_exist = 0;	
}
// QUERY SELECTOR GAMME
$query_selector = "SELECT PG_DISPLAY 
	FROM PIECES_GAMME 
	WHERE PG_ID = $pg_id AND PG_LEVEL IN (1,2)";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	$gamme_display = $result_selector['PG_DISPLAY'];
	$gamme_exist = 1;	
}
else
{
$gamme_display = 0;
$gamme_exist = 0;	
}
// DISPATCHING PAGES
if(($gamme_exist == 0)||($car_exist == 0))
{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           410                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php include("410.page.php"); ?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
else
{
	if(($gamme_display == 1)&&($car_display == 1))
	{	
		// HAS ARTICLE TO PRINT
		$GammeCarCountArticle = 0;
		$query_item_count = "SELECT DISTINCT PIECE_ID 
				FROM PIECES_RELATION_TYPE
				JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID
				WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID  = $pg_id AND PIECE_DISPLAY = 1";
		$request_item_count = $conn->query($query_item_count);
		$GammeCarCountArticle = $request_item_count->num_rows;

		// MIN PRICE
		$GammeCarMinPriceArticle = 0;
		$query_item_minprice = "SELECT DISTINCT MIN(PRI_VENTE_TTC*PIECE_QTY_SALE) AS MIN_PRICE
			FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID
			JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID
			WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID  = $pg_id 
			AND PIECE_DISPLAY = 1 AND PRI_DISPO = 1";
		$request_item_minprice = $conn->query($query_item_minprice);
		if($request_item_minprice->num_rows>0)
		{
			$result_item_minprice = $request_item_minprice->fetch_assoc();
			$GammeCarMinPriceArticle = intval($result_item_minprice["MIN_PRICE"]);
		}

		// END HAS ARTICLE TO PRINT
		if($GammeCarCountArticle>0)
		{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY GAMME
$query_pg = "SELECT PG_ALIAS, PG_NAME, PG_NAME_META, PG_RELFOLLOW, PG_IMG,
	MF_ID, MF_NAME, MF_NAME_META 
	FROM PIECES_GAMME 
	JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
	WHERE PG_ID = $pg_id 
	AND PG_DISPLAY = 1 AND PG_LEVEL IN (1,2) 
    AND MF_DISPLAY = 1";
$request_pg = $conn->query($query_pg);
$result_pg = $request_pg->fetch_assoc();
	// PG DATA
	$pg_name_site = $result_pg['PG_NAME'];
	$pg_name_meta = $result_pg['PG_NAME_META'];
    $pg_alias = $result_pg["PG_ALIAS"];
    $pg_relfollow = $result_pg["PG_RELFOLLOW"];
    // MF DATA
    $mf_id = $result_pg["MF_ID"];
    $mf_name_site = $result_pg["MF_NAME"];
    $mf_name_meta = $result_pg["MF_NAME_META"];
    // WALL
    if($isMacVersion == false)
	{
		$pg_pic = $result_pg['PG_IMG'];
	}
	else
	{
		$pg_pic = str_replace(".webp",".jpg",$result_pg['PG_IMG']);
	}
    $pg_wall = $result_pg["PG_WALL"];
// QUERY CAR
$query_motorisation = "SELECT TYPE_ALIAS, TYPE_NAME, TYPE_NAME_META, TYPE_POWER_PS, TYPE_BODY, TYPE_FUEL, 
	TYPE_MONTH_FROM, TYPE_YEAR_FROM, TYPE_MONTH_TO, TYPE_YEAR_TO, TYPE_RELFOLLOW,  
	MODELE_NAME, MODELE_NAME_META, MODELE_ALIAS, MODELE_RELFOLLOW, 
	MARQUE_ALIAS, MARQUE_NAME, MARQUE_NAME_META, MARQUE_NAME_META_TITLE, MARQUE_RELFOLLOW, MARQUE_LOGO 
	FROM AUTO_TYPE 
	JOIN AUTO_MODELE ON MODELE_ID = TYPE_MODELE_ID
	JOIN AUTO_MARQUE ON MARQUE_ID = TYPE_MARQUE_ID
	WHERE TYPE_ID = $type_id
	AND TYPE_MODELE_ID = $modele_id 
	AND TYPE_MARQUE_ID = $marque_id 
	AND TYPE_DISPLAY = 1 AND MODELE_DISPLAY = 1 AND MARQUE_DISPLAY = 1";
$request_motorisation = $conn->query($query_motorisation);
$result_motorisation = $request_motorisation->fetch_assoc();
	// MARQUE DATA
	$marque_name_site = $result_motorisation['MARQUE_NAME'];
	$marque_name_meta = $result_motorisation['MARQUE_NAME_META'];
	$marque_name_meta_title = $result_motorisation['MARQUE_NAME_META_TITLE'];
    $marque_alias = $result_motorisation["MARQUE_ALIAS"];
    $marque_relfollow = $result_motorisation["MARQUE_RELFOLLOW"];
    //$marque_logo = $result_motorisation["MARQUE_LOGO"];
    if($isMacVersion == false)
	{
		$marque_logo = $result_motorisation['MARQUE_LOGO'];
	}
	else
	{
		$marque_logo = str_replace(".webp",".png",$result_motorisation['MARQUE_LOGO']);
	}
    // MODELE DATA
	$modele_name_site = $result_motorisation['MODELE_NAME'];
	$modele_name_meta = $result_motorisation['MODELE_NAME_META'];
    $modele_alias = $result_motorisation["MODELE_ALIAS"];
    $modele_relfollow = $result_motorisation["MODELE_RELFOLLOW"];
    // TYPE DATA
    $type_name_site = $result_motorisation['TYPE_NAME'];
	$type_name_meta = $result_motorisation['TYPE_NAME_META'];
	$type_alias = $result_motorisation['TYPE_ALIAS'];
	$type_relfollow = $result_motorisation['TYPE_RELFOLLOW'];
	$type_date = "";
	if(empty($result_motorisation['TYPE_YEAR_TO']))
    {
    $type_date="du ".$result_motorisation['TYPE_MONTH_FROM']."/".$result_motorisation['TYPE_YEAR_FROM'];
    }
    else
    {
    $type_date="de ".$result_motorisation['TYPE_YEAR_FROM']." à ".$result_motorisation['TYPE_YEAR_TO'];
    }
	$type_nbch = $result_motorisation['TYPE_POWER_PS'];
	$type_carosserie = $result_motorisation['TYPE_BODY'];
	$type_fuel = $result_motorisation['TYPE_FUEL'];
	$type_code_moteur = "";
	$query_code_moteur = "SELECT TMC_CODE FROM AUTO_TYPE_MOTOR_CODE 
	WHERE TMC_TYPE_ID = $type_id";
	$request_code_moteur = $conn->query($query_code_moteur);
	if ($request_code_moteur->num_rows > 0) 
	{
		$k=0;
		while($result_code_moteur = $request_code_moteur->fetch_assoc())
		{
			if($k>0){ $type_code_moteur = $type_code_moteur.", "; }
			$type_code_moteur = $type_code_moteur.$result_code_moteur['TMC_CODE'];
			$k++;
		}
	}
	else
	{
		$type_code_moteur = " ";
	}
    // SEO & CONTENT
    $query_seo = "SELECT SGC_TITLE, SGC_DESCRIP, SGC_H1, SGC_PREVIEW, SGC_CONTENT 
    	FROM __SEO_GAMME_CAR 
		WHERE SGC_PG_ID = $pg_id ";
	$request_seo = $conn->query($query_seo);
	if ($request_seo->num_rows > 0) 
	{
		$result_seo = $request_seo->fetch_assoc();
		// META
		/////////////////////////////// title //////////////////////////////////////
		$pagetitle = strip_tags($result_seo['SGC_TITLE']);
			// Changement des variables standards
				$pagetitle=str_replace("#Gamme#",$pg_name_meta,$pagetitle);
				$pagetitle=str_replace("#VMarque#",$marque_name_meta_title,$pagetitle);
				$pagetitle=str_replace("#VModele#",$modele_name_meta,$pagetitle);
				$pagetitle=str_replace("#VType#",$type_name_meta,$pagetitle);
				$pagetitle=str_replace("#VAnnee#",$type_date,$pagetitle);
				$pagetitle=str_replace("#VNbCh#",$type_nbch,$pagetitle);
			// Changement min price
				$pagetitle=str_replace("#MinPrice#","à ".$GammeCarMinPriceArticle." €",$pagetitle);
			// Prix pas cher
			    $PrixPasCherTab=(($pg_id%100)+1+$type_id)%$PrixPasCherLength;
			    $pagetitle=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$pagetitle);
			// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
				$comp_switch_value="";
				if(strpos($pagetitle, $comp_switch_marker))
				{
					$query_seo_item_switch = "SELECT SIS_ID   
						FROM __SEO_ITEM_SWITCH 
						WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 1";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// GET SWITCH
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 1 
							ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
					}
					$pagetitle=str_replace($comp_switch_marker,$comp_switch_value,$pagetitle);
				}
		/////////////////////////////// description //////////////////////////////////////
        $pagedescription = strip_tags($result_seo['SGC_DESCRIP']);
			// Changement des variables standards
				$pagedescription=str_replace("#Gamme#",$pg_name_meta,$pagedescription);
				$pagedescription=str_replace("#VMarque#",$marque_name_meta_title,$pagedescription);
				$pagedescription=str_replace("#VModele#",$modele_name_meta,$pagedescription);
				$pagedescription=str_replace("#VType#",$type_name_meta,$pagedescription);
				$pagedescription=str_replace("#VAnnee#",$type_date,$pagedescription);
				$pagedescription=str_replace("#VNbCh#",$type_nbch,$pagedescription);
			// Changement min price
				$pagedescription=str_replace("#MinPrice#","à ".$GammeCarMinPriceArticle." €",$pagedescription);
			// Prix pas cher
			    $PrixPasCherTab=(($pg_id%100)+$type_id)%$PrixPasCherLength;
			    $pagedescription=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$pagedescription);
			// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
				$comp_switch_value="";
				if(strpos($pagedescription, $comp_switch_marker))
				{
					$query_seo_item_switch = "SELECT SIS_ID   
						FROM __SEO_ITEM_SWITCH 
						WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 2";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// GET SWITCH
						$comp_switch_debut = $type_id % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = $pg_id AND SIS_ALIAS = 2 
							ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
					}
					$pagedescription=str_replace($comp_switch_marker,$comp_switch_value,$pagedescription);
				}
			// Changement #CompSwitch_3_PG_ID#
		        $comp_switch_3_pg_id_marker="#CompSwitch_3_".$pg_id."#";
				$comp_switch_3_pg_id_value="";
				if(strpos($pagedescription, $comp_switch_3_pg_id_marker))
				{
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 3";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$comp_switch_3_pg_id_debut=($type_id+$pg_id+3) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 3 
							ORDER BY SGCS_ID LIMIT $comp_switch_3_pg_id_debut,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$comp_switch_3_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}
					$pagedescription=str_replace($comp_switch_3_pg_id_marker,$comp_switch_3_pg_id_value,$pagedescription);
				}
			// Changement #LinkGammeCar_PG_ID#
				$LinkGammeCar_pg_id_marker="#LinkGammeCar_".$pg_id."#";
				$LinkGammeCar_pg_id_value="";
				$LinkGammeCar_pg_id_value_2="";
				if(strstr($pagedescription, $LinkGammeCar_pg_id_marker))
				{
					// LinkGammeCar_PG_ID_1
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 1";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$LinkGammeCar_pg_id_debut=($type_id+$pg_id+2) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 1 
							ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$LinkGammeCar_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}
					// LinkGammeCar_PG_ID_2
					$query_seo_gamme_car_switch = "SELECT SGCS_ID   
						FROM __SEO_GAMME_CAR_SWITCH 
						WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 2";
					$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
					if ($request_seo_gamme_car_switch->num_rows > 0) 
					{
						$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
						// CONTENT
						$LinkGammeCar_pg_id_debut_2=($type_id+$pg_id+3) % $request_seo_gamme_car_switch_num_rows;
						$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $pg_id AND SGCS_ALIAS = 2 
							ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut_2,1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
						$LinkGammeCar_pg_id_value_2 = $result_seo_gamme_car_switch["SGCS_CONTENT"];
					}

					$LinkGammeCar_pg_id_link_full=$LinkGammeCar_pg_id_value." les ".$pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch et ".$LinkGammeCar_pg_id_value_2;

					$pagedescription=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_full,$pagedescription);
				}
		/////////////////////////////// keyword //////////////////////////////////////
        $pagekeywords = $pg_name_meta.", ".$marque_name_meta.", ".$modele_name_meta.", ".$type_name_meta.", ".$type_nbch." ch, ".$type_date.", ".$type_carosserie.", ".$type_fuel.", ".$type_code_moteur;
        // CONTENT
		/////////////////////////////// h1 //////////////////////////////////////
        $pageh1 = strip_tags($result_seo['SGC_H1']);
			// Changement des variables standards
				$pageh1=str_replace("#Gamme#",$pg_name_meta,$pageh1);
				$pageh1=str_replace("#VMarque#",$marque_name_meta_title,$pageh1);
				$pageh1=str_replace("#VModele#",$modele_name_meta,$pageh1);
				$pageh1=str_replace("#VType#",$type_name_meta,$pageh1);
				$pageh1=str_replace("#VAnnee#",$type_date,$pageh1);
				$pageh1=str_replace("#VNbCh#",$type_nbch,$pageh1);
			// Changement #CompSwitch#
		        $comp_switch_marker="#CompSwitch#";
				$comp_switch_value="";
				if(strpos($pageh1, $comp_switch_marker))
				{
					$query_seo_item_switch = "SELECT SIS_ID   
						FROM __SEO_ITEM_SWITCH 
						WHERE SIS_PG_ID = 0 AND SIS_ALIAS = 3";
					$request_seo_item_switch = $conn->query($query_seo_item_switch);
					if ($request_seo_item_switch->num_rows > 0) 
					{
						$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
						// GET SWITCH
						$comp_switch_debut = ($type_id+$pg_id) % $request_seo_item_switch_num_rows;
						$query_seo_item_switch = "SELECT SIS_CONTENT   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = 0 AND SIS_ALIAS = 3 
							ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
						$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
					}
					$pageh1=str_replace($comp_switch_marker,$comp_switch_value,$pageh1);
				}
		/////////////////////////////// preview & content //////////////////////////////////////
        $pagepreview = strip_tags($result_seo['SGC_PREVIEW'],'<a><br><b>');
        $pagecontent = strip_tags($result_seo['SGC_CONTENT'],'<a><br><b>');	
			// Changement des variables standards
				$pagecontent=str_replace("#Gamme#","<b>".$pg_name_site."</b>",$pagecontent);
				$pagecontent=str_replace("#VMarque#","<b>".$marque_name_site."</b>",$pagecontent);
				$pagecontent=str_replace("#VModele#","<b>".$modele_name_site."</b>",$pagecontent);
				$pagecontent=str_replace("#VType#","<b>".$type_name_site."</b>",$pagecontent);
				$pagecontent=str_replace("#VAnnee#","<b>".$type_date."</b>",$pagecontent);
				$pagecontent=str_replace("#VNbCh#","<b>".$type_nbch."</b>",$pagecontent);
				$pagecontent=str_replace("#VCarosserie#","<b>".$type_carosserie."</b>",$pagecontent);
				$pagecontent=str_replace("#VMotorisation#","<b>".$type_fuel."</b>",$pagecontent);
				$pagecontent=str_replace("#VCodeMoteur#","<b>".$type_code_moteur."</b>",$pagecontent);
			// Prix pas cher
			    $PrixPasCherTab=$type_id%$PrixPasCherLength;
			    $pagecontent=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab],$pagecontent);
			// Vous propose
			    $VousProposeTab=$type_id%$VousProposeLength;
			    $pagecontent=str_replace("#VousPropose#",$VousPropose[$VousProposeTab],$pagecontent);
			// Changement #CompSwitch_PG_ID_EXTERNE#
			    $query_seo_gamme_externe = "SELECT PG_ID   
				FROM PIECES_GAMME 
				WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)";
				$request_seo_gamme_externe = $conn->query($query_seo_gamme_externe);
				if ($request_seo_gamme_externe->num_rows > 0) 
				{
				while($result_seo_gamme_externe = $request_seo_gamme_externe->fetch_assoc())
				{
					$this_pg_id = $result_seo_gamme_externe['PG_ID'];
					$comp_switch_externe_marker="#CompSwitch_".$this_pg_id."#";
					$comp_switch_externe_value="";
					if(strstr($pagecontent, $comp_switch_externe_marker))
					{
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH
							$comp_switch_externe_debut = ($type_id+$this_pg_id) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id 
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value = $result_seo_item_switch["SGCS_CONTENT"];
						}
					$pagecontent=str_replace($comp_switch_externe_marker,$comp_switch_externe_value,$pagecontent);
					}
				}
				}
			// Changement #CompSwitch_1_PG_ID_EXTERNE#
			    $csnbre=1;
			    $query_seo_gamme_externe = "SELECT PG_ID   
				FROM PIECES_GAMME 
				WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)";
				$request_seo_gamme_externe = $conn->query($query_seo_gamme_externe);
				if ($request_seo_gamme_externe->num_rows > 0) 
				{
				while($result_seo_gamme_externe = $request_seo_gamme_externe->fetch_assoc())
				{
					$this_pg_id = $result_seo_gamme_externe['PG_ID'];
					// 1 
					$comp_switch_externe_marker="#CompSwitch_1_".$this_pg_id."#";
					$comp_switch_externe_value="";
					if(strstr($pagecontent, $comp_switch_externe_marker))
					{
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1 ";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH
							$comp_switch_externe_debut = ($type_id+$this_pg_id+$csnbre+1) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value = $result_seo_item_switch["SGCS_CONTENT"];
						}
					$pagecontent=str_replace($comp_switch_externe_marker,$comp_switch_externe_value,$pagecontent);
					}
					//2
					$comp_switch_externe_marker="#CompSwitch_2_".$this_pg_id."#";
					$comp_switch_externe_value="";
					if(strstr($pagecontent, $comp_switch_externe_marker))
					{
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2 ";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH
							$comp_switch_externe_debut = ($type_id+$this_pg_id+$csnbre+2) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value = $result_seo_item_switch["SGCS_CONTENT"];
						}
					$pagecontent=str_replace($comp_switch_externe_marker,$comp_switch_externe_value,$pagecontent);
					}
					//3
					$comp_switch_externe_marker="#CompSwitch_3_".$this_pg_id."#";
					$comp_switch_externe_value="";
					if(strstr($pagecontent, $comp_switch_externe_marker)||strstr($pagepreview, $comp_switch_externe_marker))
					{
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3 ";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH
							$comp_switch_externe_debut = ($type_id+$this_pg_id+$csnbre+3) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value = $result_seo_item_switch["SGCS_CONTENT"];
						}
					$pagecontent=str_replace($comp_switch_externe_marker,$comp_switch_externe_value,$pagecontent);
					$pagepreview=str_replace($comp_switch_externe_marker,$comp_switch_externe_value,$pagepreview);
					}
				$csnbre++;
				}
				}
			// Changement #CompSwitch_11_PG_ID# SWITCH FAMILLE FOR PAGE P 11/12/13/14/15/16
			    $CompSwitchID=11;
				while($CompSwitchID<17)
				{
					$comp_switch_marker="#CompSwitch_".$CompSwitchID."_".$pg_id."#";
					$comp_switch_value="";
					if(strstr($pagecontent, $comp_switch_marker))
					{
						$query_seo_family_switch = "SELECT SFGCS_ID   
							FROM __SEO_FAMILY_GAMME_CAR_SWITCH 
							WHERE SFGCS_MF_ID = '$mf_id' AND ( SFGCS_PG_ID = 0 OR SFGCS_PG_ID = $pg_id ) 
						    AND SFGCS_ALIAS = $CompSwitchID";
						$request_seo_family_switch = $conn->query($query_seo_family_switch);
						if ($request_seo_family_switch->num_rows > 0) 
						{
							$request_seo_family_switch_num_rows = $request_seo_family_switch->num_rows;
							// GET SWITCH
							$comp_switch_debut = ($type_id+$pg_id+$CompSwitchID) % $request_seo_family_switch_num_rows;
							$query_seo_family_switch = "SELECT SFGCS_CONTENT  
							FROM __SEO_FAMILY_GAMME_CAR_SWITCH 
							WHERE SFGCS_MF_ID = '$mf_id' AND ( SFGCS_PG_ID = 0 OR SFGCS_PG_ID = $pg_id ) 
						    AND SFGCS_ALIAS = $CompSwitchID
						    LIMIT $comp_switch_debut,1";
							$request_seo_family_switch = $conn->query($query_seo_family_switch);
							$result_seo_family_switch = $request_seo_family_switch->fetch_assoc();
							$comp_switch_value = $result_seo_family_switch["SFGCS_CONTENT"];
							if(strstr($comp_switch_value, '#VMarque#'))
						            {
						              $comp_switch_value="<a href='".$domain."/".$Auto."/".$marque_alias."-".$marque_id.".html'>".$comp_switch_value."</a>";
						            }
						}
					$pagecontent=str_replace($comp_switch_marker,$comp_switch_value,$pagecontent);
					}
				$CompSwitchID++;
				}
			// Changement #LinkCarAll#
				$comp_switch_marker="#LinkCarAll#";
				$comp_switch_value="";
				$comp_switch_value_link="";
				if(strstr($pagecontent, $comp_switch_marker))
				{
					// lien Car
					$comp_switch_value=$domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
					// generation lien et ancre du lien
					$comp_switch_value_link="<b>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_carosserie." ".$type_nbch."</b> ch <b>".$type_date."</b> pour code moteur : <b>".$type_code_moteur."</b>";
					// changement des  données gamme
					$pagecontent=str_replace($comp_switch_marker,$comp_switch_value_link,$pagecontent);
				}
			// Changement #LinkCar#
				$comp_switch_marker="#LinkCar#";
				$comp_switch_value="";
				$comp_switch_value_link="";
				if(strstr($pagecontent, $comp_switch_marker))
				{
					// lien Car
					$comp_switch_value=$domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
					// generation lien et ancre du lien
					$comp_switch_value_link="<b>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_fuel." ".$type_nbch."</b> ch";
					// changement des  données gamme
					$pagecontent=str_replace($comp_switch_marker,$comp_switch_value_link,$pagecontent);
				}
			// Changement #LinkGamme_PG_ID#
			    $query_seo_gamme_externe = "SELECT PG_ID, PG_ALIAS, PG_NAME   
				FROM PIECES_GAMME 
				WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)";
				$request_seo_gamme_externe = $conn->query($query_seo_gamme_externe);
				if ($request_seo_gamme_externe->num_rows > 0) 
				{
				while($result_seo_gamme_externe = $request_seo_gamme_externe->fetch_assoc())
				{
					$this_pg_id = $result_seo_gamme_externe['PG_ID'];
					$this_pg_alias = $result_seo_gamme_externe['PG_ALIAS'];
					$this_pg_name_site = $result_seo_gamme_externe['PG_NAME'];
					// Link To Gamme 
					$comp_switch_externe_marker="#LinkGamme_".$this_pg_id."#";
					$comp_switch_externe_value="";
					$comp_switch_externe_value_link="";
					if(strstr($pagecontent, $comp_switch_externe_marker))
					{
						// lien gamme
						$comp_switch_externe_value=$domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id.".html";
						// generation lien et ancre du lien
						$comp_switch_externe_value_link="<a href='".$comp_switch_externe_value."'><b>".$this_pg_name_site."</b></a>";
						$pagecontent=str_replace($comp_switch_externe_marker,$comp_switch_externe_value_link,$pagecontent);
					}
				}
				}
			// Changement #LinkGammeCar_PG_ID#
			    $csnbre=1;
			    $query_seo_gamme_externe = "SELECT PG_ID, PG_ALIAS, PG_NAME 
				FROM PIECES_GAMME 
				WHERE PG_DISPLAY = 1 AND PG_LEVEL IN (1,2)";
				$request_seo_gamme_externe = $conn->query($query_seo_gamme_externe);
				if ($request_seo_gamme_externe->num_rows > 0) 
				{
				while($result_seo_gamme_externe = $request_seo_gamme_externe->fetch_assoc())
				{
					$this_pg_id = $result_seo_gamme_externe['PG_ID'];
					$this_pg_alias = $result_seo_gamme_externe['PG_ALIAS'];
					$this_pg_name_site = $result_seo_gamme_externe['PG_NAME'];
					// Link To Gamme 
					$comp_switch_externe_marker="#LinkGammeCar_".$this_pg_id."#";
					$comp_switch_externe_value_1="";
					$comp_switch_externe_value_2="";
					if(strstr($pagecontent, $comp_switch_externe_marker)||strstr($pagepreview, $comp_switch_externe_marker))
					{
						// switch 1
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1 ";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH 1
							$comp_switch_externe_debut_1 = ($type_id+$this_pg_id+$csnbre+1) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut_1,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value_1 = $result_seo_item_switch["SGCS_CONTENT"];
						}
						// switch 2
						$query_seo_item_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2 ";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// GET SWITCH 1
							$comp_switch_externe_debut_2 = ($type_id+$this_pg_id+$csnbre+2) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2
								ORDER BY SGCS_ID LIMIT $comp_switch_externe_debut_2,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_externe_value_2 = $result_seo_item_switch["SGCS_CONTENT"];
						}
						// lien gamme car
						$linktoGammeCar=$domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
						// Article dans la page cible
						$query_item_count_target = "SELECT DISTINCT PIECE_ID 
								FROM PIECES_RELATION_TYPE
								JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_PG_ID = RTP_PG_ID
								WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID  = $this_pg_id AND PIECE_DISPLAY = 1";
						$request_item_count_target = $conn->query($query_item_count_target);
						$GammeCarCountArticleContent = $request_item_count_target->num_rows;
						if($GammeCarCountArticleContent>0)
						{
						//////////////////////////////////////////////////////////
						$FullLinktoGammeCar="<a href='".$linktoGammeCar."'><b>".$comp_switch_externe_value_1." les ".$this_pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch et ".$comp_switch_externe_value_2."</b></a>";
						$FullLinktoGammeCarPrev="<b>".$comp_switch_externe_value_1." les ".$this_pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch et ".$comp_switch_externe_value_2."</b>";
						//////////////////////////////////////////////////////////
						}
						else
						{
						//////////////////////////////////////////////////////////
						$FullLinktoGammeCar="<b>".$comp_switch_externe_value_1." les ".$this_pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch et ".$comp_switch_externe_value_2."</b>";
						$FullLinktoGammeCarPrev="<b>".$comp_switch_externe_value_1." les ".$this_pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch et ".$comp_switch_externe_value_2."</b>";
						//////////////////////////////////////////////////////////
						}
						// changement des  données gammeCar
						$pagecontent=str_replace($comp_switch_externe_marker,$FullLinktoGammeCar,$pagecontent);
						// SAME FOR THE PREVIW
						$pagepreview=str_replace($comp_switch_externe_marker,$FullLinktoGammeCarPrev,$pagepreview);
					}
				$csnbre++;
				}
				}
				// On change le marqueur existant dans le switch
				$pagecontent=str_replace("#VMarque#",$marque_name_site,$pagecontent);
	}
	else
	{
		$LinkToCar = $domain."/".$Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
		$LinkToGamme = $domain."/".$Piece."/".$pg_alias."-".$pg_id.".html";
      	//META
      	$pagetitle = $pg_name_meta." ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date;
	    $pagedescription = "Achetez ".$pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date.", d'origine à prix bas.";
	    $pagekeywords = $pg_name_meta.", ".$marque_name_meta.", ".$modele_name_meta.", ".$type_name_meta.", ".$type_nbch." ch, ".$type_date.", ".$type_carosserie.", ".$type_fuel.", ".$type_code_moteur;
	    // CONTENT
	    $pageh1 = $pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch ".$type_date;
	    $pagepreview = "";
		$pagecontent = "Le(s) <strong>".$pg_name_site."</strong> de la <a href='".$LinkToCar."'><strong>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch."</strong> ch</a> <strong>".$type_date."</strong> sont disponible sur Automecanik à un prix pas cher. <br> Identifiez le <a href='".$LinkToGamme."'><strong>".$pg_name_site."</strong></a> compatible avec votre <strong>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch." ch</strong> en suivant les plans d'entretien du constructeur <strong>".$marque_name_site."</strong> pour les périodes de contrôle et de remplacement du <strong>".$pg_name_site."</strong> de la <strong>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch."</strong> ch.<br> Lors du remplacement de la pièce nous vous conseillons de contrôler l'état d'usure des composants et des organes du <strong>".$family_name_site."</strong> de la <strong>".$marque_name_site." ".$modele_name_site." ".$type_name_site." ".$type_nbch."</strong> ch.";
	}

	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
	    $pagepreview = content_cleaner($pagepreview);
		$pagecontent = content_cleaner($pagecontent);

	// ROBOT
    $relfollow = 1;
    if(($pg_relfollow==1)&&($marque_relfollow==1)&&($modele_relfollow==1)&&($type_relfollow==1))
  	{
  		$pageRobots="index, follow";
    	$relfollow = 1;
    	// NB FAMILY
    	$query_count_family = "SELECT DISTINCT MC_MF_ID FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_DISPLAY = 1
			JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID AND PG_DISPLAY = 1 AND PIECES_GAMME.PG_LEVEL IN (1,2)
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			WHERE RTP_TYPE_ID = $type_id";
		$request_count_family = $conn->query($query_count_family);
		if ($request_count_family->num_rows < 3) 
		{
			$pageRobots="noindex, nofollow";
    		$relfollow = 0;
    	}
    	else
    	{
    	// NB GAMME
    		$query_count_gamme = "SELECT DISTINCT PG_ID FROM PIECES_RELATION_TYPE
				JOIN PIECES ON PIECE_ID = RTP_PIECE_ID AND PIECE_DISPLAY = 1
				JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID AND PG_DISPLAY = 1 AND PIECES_GAMME.PG_LEVEL IN (1,2)
				WHERE RTP_TYPE_ID = $type_id";
			$request_count_gamme = $conn->query($query_count_gamme);
			if ($request_count_gamme->num_rows < 5) 
			{
				$pageRobots="noindex, nofollow";
	    		$relfollow = 0;
	    	}
    	}
  	}
  	else
  	{
  		$pageRobots="noindex, nofollow";
    	$relfollow = 0;
  	}
  	// CANONICAL
  	$canonicalLink = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
		// NOT CANONICAL TO NOINDEX
		if ($relfollow == 1) 
		{
			$accessLink = $domain.$_SERVER['REQUEST_URI'];
			if (strcmp($canonicalLink, $accessLink) !== 0) 
			{
				$pageRobots="noindex, nofollow";
			}
		}
	// ARIANE
    $oldest_parent_arianelink = $Piece."/".$pg_alias."-".$pg_id.".html";
	$oldest_parent_arianetitle = $pg_name_site;
    $older_parent_arianelink = $Auto."/".$marque_alias."-".$marque_id.".html";
	$older_parent_arianetitle = $marque_name_site;
    $parent_arianelink = $Auto."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
	$parent_arianetitle = $modele_name_site." ".$type_name_site;
    $arianetitle = $pg_name_site." ".$marque_name_site." ".$modele_name_site." ".$type_name_site;
?>
<?php 
// parametres relatifs à la page
$arianefile="p";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php  echo $pagetitle; ?></title>
<meta name="title" content="<?php  echo $pagetitle; ?>" />
<meta name="description" content="<?php  echo $pagedescription; ?>" />
<meta name="keywords" content="<?php  echo $pagekeywords; ?>"/>
<meta name="robots" content="<?php echo $pageRobots; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- favicon -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">

<link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="manifest" href="/favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://ajax.googleapis.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/style.p.min.css" rel="stylesheet" media="all">
<!--
<link href="/assets/bootstrap-4.3.1/css/bootstrap.css" rel="stylesheet" media="all">
<link href="/assets/css/style.css" rel="stylesheet" media="all">-->
</head>
<body>

<?php
require_once('global.header.section.php');
?>

<div class="container-fluid globalthirdheader">
<div class="container-fluid mywidecontainer nocarform">
</div>
</div>

<div class="container-fluid containerPage">
<div class="container-fluid containerinPage">

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-sm-8 col-lg-9 d-none d-md-block">
	<!-- COL LEFT -->
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('config/ariane.conf.php');
		?>
	<!-- / COL LEFT -->
	</div>
	<div class="col-sm-4 col-lg-3 text-right d-none d-md-block">
	<!-- COL RIGHT -->
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('global.social.share.php');
		?>
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<img class="img-fluid" src="<?php echo $domain; ?>/upload/constructeurs-automobiles/icon/<?php echo $marque_logo; ?>" />
			<h1><?php echo $pageh1; ?></h1>
			<?php // echo $GammeCarCountArticle; articles ?>
		</span>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- FILTER PIECES -->
	<div class="container-fluid globalthirdheader pt-3">
	<div class="container-fluid mywidecontainer" style="color: #fff; padding-bottom: 17px;">

	<div class="row">
		<?php
		// Filltre GAMME ET UNION
		$query_get_pg_main_union_filter = "SELECT DISTINCT PIECE_FIL_ID, PIECE_FIL_NAME, 
			COUNT(PIECE_ID) AS NBP
			FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
			WHERE  RTP_TYPE_ID = $type_id AND RTP_PG_ID = $pg_id AND PIECE_DISPLAY = 1
			GROUP BY PIECE_FIL_ID";
		$request_get_pg_main_union_filter = $conn->query($query_get_pg_main_union_filter);
		if ($request_get_pg_main_union_filter->num_rows > 1) 
		{
			?>
			<div class="col-sm-3 filter-block filter-cross">

			<select name="filterbyGamme" id="filterbyGamme" class="filterP" onChange="if (this.value) window.location.href=this.value">
				<?php
				$this_piece_fil_id = 0;
				$LinkGammeCar_pg_id_link_union = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$this_piece_fil_id."/".$filtre_psf_id."/".$filtre_pm_id;
				?>
				<option value='<?php echo $LinkGammeCar_pg_id_link_union; ?>' <?php if($this_piece_fil_id==$filtre_piece_fil_id) echo 'selected="selected"' ?> >
					Tous les articles (<?php echo $GammeCarCountArticle; ?>)
				</option>
				<?php
				while($result_get_pg_main_union_filter = $request_get_pg_main_union_filter->fetch_assoc())
				{
					$this_piece_fil_id = $result_get_pg_main_union_filter['PIECE_FIL_ID'];
					// LINK TO P FILTRE
					$LinkGammeCar_pg_id_link_union = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$this_piece_fil_id."/".$filtre_psf_id."/".$filtre_pm_id;
					?>
					<option value='<?php echo $LinkGammeCar_pg_id_link_union; ?>' <?php if($this_piece_fil_id==$filtre_piece_fil_id) echo 'selected="selected"' ?> >
						<?php echo $result_get_pg_main_union_filter['PIECE_FIL_NAME']." (".$result_get_pg_main_union_filter['NBP'].")"; ?>
					</option>
					<?php
				}
				?>
			</select>
			
			</div>
			<?php
		}
		?>

		<?php
		// Filtre ESSIEU
		$query_get_technical_filter_essieu = "SELECT DISTINCT PSF_ID, PSF_SIDE
			FROM PIECES_RELATION_TYPE
			JOIN PIECES_SIDE_FILTRE ON PSF_ID = RTP_PSF_ID
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
			WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID = $pg_id AND PIECE_DISPLAY = 1 AND PSF_ID != 9999
			ORDER BY PSF_SORT";
		$request_get_technical_filter_essieu = $conn->query($query_get_technical_filter_essieu);
		if ($request_get_technical_filter_essieu->num_rows > 1) 
		{
			?>
			<div class="col-sm-2 filter-block filter-cross">

			<select name="filterbyEssieu" id="filterbyEssieu" class="filterP" onChange="if (this.value) window.location.href=this.value">
				<?php
				$this_psf_id = 0;
				$LinkGammeCar_pg_id_link_essieu = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$filtre_piece_fil_id."/".$this_psf_id."/".$filtre_pm_id;
				?>
				<option value='<?php echo $LinkGammeCar_pg_id_link_essieu; ?>' <?php if($this_psf_id==$filtre_psf_id) echo 'selected="selected"' ?> >
					Critères
				</option>
				<?php
				while($result_get_technical_filter_essieu = $request_get_technical_filter_essieu->fetch_assoc())
				{
					$this_psf_id = $result_get_technical_filter_essieu['PSF_ID'];
					// LINK TO P FILTRE
					$LinkGammeCar_pg_id_link_essieu = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$filtre_piece_fil_id."/".$this_psf_id."/".$filtre_pm_id;
					?>
					<option value='<?php echo $LinkGammeCar_pg_id_link_essieu; ?>' <?php if($this_psf_id==$filtre_psf_id) echo 'selected="selected"' ?> >
						<?php echo $result_get_technical_filter_essieu['PSF_SIDE']; ?>
					</option>
					<?php
				}
				?>
			</select>
			
			</div>
			<?php
		}
		?>

		<?php
		// Filltre EQUIP
		$query_get_eq_filter = "SELECT DISTINCT PM_ID, PM_NAME
			FROM PIECES_MARQUE
			JOIN PIECES_RELATION_TYPE ON RTP_PM_ID = PM_ID
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
			WHERE PM_DISPLAY = 1 AND RTP_TYPE_ID = $type_id AND RTP_PG_ID = $pg_id AND PIECE_DISPLAY = 1
			ORDER BY PM_SORT";
		$request_get_eq_filter = $conn->query($query_get_eq_filter);
		if ($request_get_eq_filter->num_rows > 1) 
		{
			?>
			<div class="col-sm-2 filter-block filter-cross">

			<select name="filterbyEquip" id="filterbyEquip" class="filterP" onChange="if (this.value) window.location.href=this.value">
				<?php
				$this_pm_id = 0;
				$LinkGammeCar_pg_id_link_pm = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$filtre_piece_fil_id."/".$filtre_psf_id."/".$this_pm_id;
				?>
				<option value='<?php echo $LinkGammeCar_pg_id_link_pm; ?>' <?php if($this_pm_id==$filtre_pm_id) echo 'selected="selected"' ?> >
					Marque
				</option>
				<?php
				while($result_get_eq_filter = $request_get_eq_filter->fetch_assoc())
				{
					$this_pm_id = $result_get_eq_filter['PM_ID'];
					// LINK TO P FILTRE
					$LinkGammeCar_pg_id_link_pm = $domain."/".$Piece."/".$pg_alias."-".$pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html/".$filtre_piece_fil_id."/".$filtre_psf_id."/".$this_pm_id;
					?>
					<option value='<?php echo $LinkGammeCar_pg_id_link_pm; ?>' <?php if($this_pm_id==$filtre_pm_id) echo 'selected="selected"' ?> >
						<?php echo $result_get_eq_filter['PM_NAME']; ?>
					</option>
					<?php
				}
				?>
			</select>
			
			</div>
			<?php
		}
		?>

		<?php
		// CROSS FAMILY
		$mc_mf_prime=$mf_id;	
		$query_get_cross_gamme = "SELECT DISTINCT PG_ID, PG_NAME, PG_NAME_URL, PG_NAME_META, PG_ALIAS
			FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
			JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
			JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
			WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID != $pg_id AND PIECE_DISPLAY = 1
			AND PG_LEVEL IN (1,2) AND PG_DISPLAY = 1 AND MC_MF_PRIME = $mc_mf_prime AND MC_PG_ID != $pg_id 
			ORDER BY MC_SORT";
		$request_get_cross_gamme = $conn->query($query_get_cross_gamme);
		if ($request_get_cross_gamme->num_rows > 0) 
		{
			?>
			<div class="col-sm-3 filter-block filter-cross">

			<select name="goGetP" id="goGetP" class="filterP" onChange="if (this.value) window.location.href=this.value">
				<?php
				?>
				<option value=''>
					Catalogue <?php echo $mf_name_site; ?>
				</option>
				<?php
				while($result_get_cross_gamme = $request_get_cross_gamme->fetch_assoc())
				{
					$this_pg_id = $result_get_cross_gamme['PG_ID'];
					$this_pg_alias = $result_get_cross_gamme['PG_ALIAS'];
					// LINK TO P CROSS
					$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";
					?>
					<option value='<?php echo $LinkGammeCar_pg_id_link; ?>'>
						<?php echo $result_get_cross_gamme['PG_NAME']; ?>
					</option>
					<?php
				}
				?>
			</select>

			</div>
			<?php
		}
		//}
		?>
			<div class="col-sm-2 filter-block-catalog-car">
				<a href="<?php echo $domain; ?>/<?php echo $parent_arianelink; ?>">catalogue<br>véhicule</a>
			</div>

	</div>
		
	</div>
	</div>
	<!-- / FILTER PIECES -->

	<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">				
	<div class="row p-0 m-0">
<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->
<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->				
	<?php
	$query_item_list = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_DES, PIECE_NAME, 
		COALESCE(PSF_SIDE,PIECE_NAME_SIDE) AS PIECE_NAME_SIDE, PIECE_NAME_COMP,
		PIECE_HAS_IMG, PIECE_HAS_OEM, PIECE_QTY_SALE, PIECE_QTY_PACK, 
		PM_ID, PM_NAME, PM_LOGO, COALESCE(PIECE_DES,PM_QUALITY) AS PM_QUALITY
		FROM PIECES_RELATION_TYPE
		JOIN PIECES_SIDE_FILTRE ON PSF_ID = RTP_PSF_ID
		JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
		JOIN PIECES_MARQUE ON PM_ID = PIECE_PM_ID
		JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID
		WHERE RTP_TYPE_ID = $type_id AND RTP_PG_ID = $pg_id 
		AND PIECE_DISPLAY = 1 AND PM_DISPLAY = 1 ";
		// INJECTION FILTRE EQUIPEMENTIER
		if($filtre_pm_id>0)
		{
			$query_item_list .= " 
			AND RTP_PM_ID = $filtre_pm_id";
		}
		// FIN INJECTION FILTRE EQUIPEMENTIER
		// INJECTION FILTRE GAMME UNION
		if($filtre_piece_fil_id>0)
		{
			$query_item_list .= " 
			AND PIECE_FIL_ID = $filtre_piece_fil_id";
		}
		// FIN INJECTION FILTRE GAMME UNION
		// INJECTION FILTRE ESSIEU
		if($filtre_psf_id>0)
		{
			$query_item_list .= " 
			AND RTP_PSF_ID = $filtre_psf_id";
		}
		// FIN INJECTION FILTRE ESSIEU
		$query_item_list .= " 
		ORDER BY PIECE_FIL_ID , PSF_SORT, PIECE_SORT, (PRI_VENTE_TTC*PIECE_QTY_SALE)";
	$request_item_list = $conn->query($query_item_list);
	if($request_item_list->num_rows)
	{
	?>
		<?php
		while($result_item_list = $request_item_list->fetch_assoc())
		{
			$piece_id_this = $result_item_list['PIECE_ID'];
			$piece_has_oem_this = $result_item_list['PIECE_HAS_OEM'];
			// PRICE
			$query_item_list_price = "SELECT PRI_CONSIGNE_TTC, PRI_VENTE_HT, PRI_VENTE_TTC 
				FROM PIECES_PRICE WHERE PRI_PIECE_ID = $piece_id_this ORDER BY PRI_TYPE DESC";
			$request_item_list_price = $conn->query($query_item_list_price);
			if($request_item_list_price->num_rows)
			{
				$result_item_list_price = $request_item_list_price->fetch_assoc();
				$price_PV_TTC = $result_item_list_price['PRI_VENTE_TTC'] * $result_item_list['PIECE_QTY_SALE'];
				$price_CS_TTC = $result_item_list_price['PRI_CONSIGNE_TTC'] * $result_item_list['PIECE_QTY_SALE'];
			}
			// PHOTO
			if($result_item_list['PIECE_HAS_IMG']==1)
			{
				$query_item_list_img = "SELECT CONCAT('rack/',PMI_FOLDER,'/',PMI_NAME,'.webp') AS PIECE_IMG 
					FROM PIECES_MEDIA_IMG WHERE PMI_PIECE_ID = $piece_id_this AND PMI_DISPLAY = 1";
				$request_item_list_img = $conn->query($query_item_list_img);
				if($request_item_list_img->num_rows)
				{
					$result_item_list_img = $request_item_list_img->fetch_assoc();
					$photo_link = $result_item_list_img['PIECE_IMG'];
					$photo_alt = $result_item_list['PIECE_NAME']." ".$result_item_list['PIECE_NAME_SIDE']." ".$result_item_list['PIECE_NAME_COMP']." ".$result_item_list['PM_NAME']." ".$result_item_list['PIECE_REF'];
					$photo_title = $result_item_list['PIECE_NAME']." ".$result_item_list['PIECE_NAME_SIDE']." ".$result_item_list['PIECE_NAME_COMP']." ".$marque_name_site." ".$modele_name_site. " ".$result_item_list['PM_NAME']." ".$result_item_list['PIECE_REF'];
				}
				else
				{
                    $photo_link = "upload/articles/no.png";
                    $photo_alt = "";
                    $photo_title = "";
				}
			}
			else
			{
                $photo_link = "upload/articles/no.png";
                $photo_alt = "";
                $photo_title = "";
			}
			?>
			<div class="col-12 col-sm-6 col-lg-4 col-xl-3 pieceContainer">

<div class="container-fluid pieceContainerin h-100">

	<div class="row p-0 m-0">
		<div class="col-12 pieceTitle">
			<?php echo $result_item_list['PIECE_NAME']; ?> <span><?php echo $result_item_list['PIECE_NAME_SIDE']; ?></span> <?php echo $result_item_list['PIECE_NAME_COMP']; ?>
		</div>
		<div class="col-5 align-self-center">
			<?php
			if($isMacVersion == false)
			{
				$this_pm_img = $result_item_list['PM_LOGO'];
			}
			else
			{
				$this_pm_img = str_replace(".webp",".png",$result_item_list['PM_LOGO']);
			}
			?>
			<img data-src="<?php echo $domain; ?>/upload/equipementiers-automobiles/<?php echo $this_pm_img; ?>" src="/upload/loading-min.gif" alt="<?php echo $result_item_list['PM_NAME']; ?>" title="<?php echo $result_item_list['PM_NAME']; ?>" width="100" height="80" class="w-100 img-fluid lazy"/>
		</div>
		<div class="col-7 pieceQuality align-self-center">
			<b>Qualité<br><?php echo $result_item_list['PM_QUALITY']; ?></b>
		</div>
		<div class="col-12 pieceEquip pt-2">
			<?php echo $result_item_list['PM_NAME']; ?> <span><?php echo $result_item_list['PIECE_REF']; ?></span>
		</div>
		<?php
		if($isMacVersion == false)
		{
		?>
		<div class="col-12 pieceWall">
			<img data-src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" src="/upload/loading-min.gif" alt="<?php echo $photo_alt; ?>" title="<?php echo $photo_title; ?>" width="360" height="360" class="w-100 wall img-fluid lazy"/>
		</div>
		<?php
		}
		?>
		<div class="col-12 pieceCriteria">
			<?php
			$query_item_list_technical = "SELECT DISTINCT PCL_CRI_ID, PCL_CRI_CRITERIA, PC_CRI_VALUE AS PCL_VALUE, 
				PCL_CRI_UNIT, PCL_SORT, PCL_LEVEL
				FROM PIECES_CRITERIA
				JOIN PIECES_CRITERIA_LINK ON PCL_CRI_ID = PC_CRI_ID AND PCL_PG_PID = PC_PG_PID
				WHERE PC_PIECE_ID = $piece_id_this AND PCL_DISPLAY = 1 AND PCL_LEVEL IN (1,2)
				UNION ALL
				SELECT DISTINCT PCL_CRI_ID, PCL_CRI_CRITERIA, RCP_CRI_VALUE AS PCL_VALUE, PCL_CRI_UNIT, PCL_SORT, PCL_LEVEL
				FROM PIECES_RELATION_CRITERIA
				JOIN PIECES_CRITERIA_LINK ON PCL_CRI_ID = RCP_CRI_ID AND PCL_PG_PID = RCP_PG_PID
				WHERE RCP_TYPE_ID = $type_id AND RCP_PIECE_ID = $piece_id_this AND PCL_DISPLAY = 1 AND PCL_LEVEL IN (1,2)
				ORDER BY PCL_LEVEL, PCL_SORT,PCL_CRI_CRITERIA LIMIT 0,4";
			$request_item_list_technical = $conn->query($query_item_list_technical);
			if($request_item_list_technical->num_rows)
			{
				while($result_item_list_technical= $request_item_list_technical->fetch_assoc())
				{
					echo "<span>".$result_item_list_technical["PCL_CRI_CRITERIA"]."</span> : ".$result_item_list_technical["PCL_VALUE"]." ".$result_item_list_technical["PCL_CRI_UNIT"]."<br>";
				}
			}
			?>
		</div>
		<div class="col-12">
			<button type="button" class="btn rounded-0 mt-3 PIECEDETAILS" data-toggle="modal" 
			data-target="#pieceTechnicalModal" 
    		data-whatever="<?php echo $piece_id_this; ?>/<?php echo $type_id; ?>">Fiche Détaillée</button>
		</div>
		<div class="col-6 piecePrice pr-0">

<?php //echo number_format($price_PV_TTC, 2, '.', ''); ?> <?php //echo $GlobalSiteCurrencyChar; ?>
<?php
$price_PV_TTC_integer = intval($price_PV_TTC);
$price_PV_TTC_float = number_format((($price_PV_TTC - $price_PV_TTC_integer) * 100), 0, '.', '');
?>
<?php echo $price_PV_TTC_integer; ?><span>.<?php printf("%02d",$price_PV_TTC_float); ?> <?php echo $GlobalSiteCurrencyChar; ?></span>

		</div>
		<div class="col-6">
			<button type="button" class="btn rounded-0 mt-3 ADDTOCART" data-toggle="modal" data-target="#addtomyCart" aria-label="Ajouter Au Panier"
    		data-whatever="<?php echo $piece_id_this; ?>">&nbsp;</button>
		</div>
		<div class="col-12 pieceConsigne">
			<?php 
			if($price_CS_TTC>0)
			{
				?>
				Consigne de <b><?php echo number_format($price_CS_TTC, 2, '.', ''); ?> <?php echo $GlobalSiteCurrencyChar; ?></b>
				(Total <span><?php echo number_format(($price_PV_TTC + $price_CS_TTC), 2, '.', ''); ?></span> <?php echo $GlobalSiteCurrencyChar; ?> TTC)
				<?php
			}
			?>
		</div>
	</div>

</div>

			</div>
		<?php
		}
		?>
	<?php
	}
	else
	{
		?>
		<div class="col-12">
		<div class="container-fluid noitemFound">
			
			Aucun articles en vente pour cette combinaison
			<br>
			<a href="<?php echo $canonicalLink; ?>">Réinitialiser les filtres</a>
			
		</div>
		</div>
		<?php
	}
	?>
<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->
<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->
	</div>
	</div>
	</div>
	<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>informations sur les <?php echo $pg_name_site; ?> de la <?php echo $marque_name_site; ?> <?php echo $modele_name_site; ?> <?php echo $type_name_site; ?></h2>
		</span>
	</div>
	<div class="col-12 pt-3 pb-3">
		<p class="text-justify contenth2"><?php echo $pagepreview; ?>...<br><?php echo $pagecontent; ?></p>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2>Pièces à contrôler lors du remplacement des <?php echo $pg_name_site; ?></h2>
		</span>
	</div>
	<div class="col-12 pt-3 pb-3">
		
		<?php
		// CROSS FAMILY
		$query_get_cross_gamme = "SELECT DISTINCT PG_ID, PG_NAME, PG_NAME_URL, PG_NAME_META, PG_ALIAS, PG_IMG 
			FROM PIECES_RELATION_TYPE
			JOIN PIECES ON PIECE_ID = RTP_PIECE_ID
			JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
			JOIN PIECES_GAMME_CROSS ON PGC_PG_CROSS = PG_ID
			JOIN CATALOG_GAMME ON MC_PG_ID = PGC_PG_CROSS
			WHERE RTP_TYPE_ID = $type_id AND PIECE_DISPLAY = 1
			AND PG_LEVEL IN (1,2) AND PG_DISPLAY = 1 AND PGC_PG_ID = $pg_id AND PGC_PG_CROSS != $pg_id 
			ORDER BY PGC_LEVEL, MC_SORT, PG_NAME";
		$request_get_cross_gamme = $conn->query($query_get_cross_gamme);
		if ($request_get_cross_gamme->num_rows > 1) 
		{
			?>
			<div class="row p-0 m-0">
			<?php
			while($result_get_cross_gamme = $request_get_cross_gamme->fetch_assoc())
			{
				$this_pg_id = $result_get_cross_gamme['PG_ID'];
				$this_pg_name_site =  $result_get_cross_gamme['PG_NAME'];
				$this_pg_name_meta = $result_get_cross_gamme['PG_NAME_META'];
				$this_pg_alias = $result_get_cross_gamme['PG_ALIAS'];
				if($isMacVersion == false)
				{
					$this_pg_img = $result_get_cross_gamme['PG_IMG'];
				}
				else
				{
					$this_pg_img = str_replace(".webp",".jpg",$result_get_cross_gamme['PG_IMG']);
				}
				$this_pg_pic = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
				// LINK TO P CROSS
				$LinkGammeCar_pg_id_link = $domain."/".$Piece."/".$this_pg_alias."-".$this_pg_id."/".$marque_alias."-".$marque_id."/".$modele_alias."-".$modele_id."/".$type_alias."-".$type_id.".html";

				// SEO TITLE ET DESCRIPTION DE LA PAGE P
				$query_seo_gamme_car = "SELECT SGC_TITLE, SGC_DESCRIP   
					FROM __SEO_GAMME_CAR 
					WHERE SGC_PG_ID = $this_pg_id 
					ORDER BY SGC_ID DESC";
				$request_seo_gamme_car = $conn->query($query_seo_gamme_car);
				if ($request_seo_gamme_car->num_rows > 0) 
				{
				$result_seo_gamme_car = $request_seo_gamme_car->fetch_assoc();
				$addon_title_seo_gamme_car=strip_tags($result_seo_gamme_car['SGC_TITLE']);
					$addon_title_seo_gamme_car=str_replace("#CompSwitch#","#CompSwitchTitle#",$addon_title_seo_gamme_car);
				$addon_content_seo_gamme_car=strip_tags($result_seo_gamme_car['SGC_DESCRIP']);
				$addon_content_seo_gamme_car = $addon_content_seo_gamme_car."<br><a>".$addon_title_seo_gamme_car."</a>";
				/////////////////////////////// addon_content_seo_gamme_car //////////////////////////////////////
				// Changement des variables standards
					$addon_content_seo_gamme_car=str_replace("#Gamme#",$this_pg_name_site,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#VMarque#",$marque_name_site,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#VModele#",$modele_name_site,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#VType#",$type_name_site,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#VAnnee#",$type_date,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#VNbCh#",$type_nbch,$addon_content_seo_gamme_car);
			        $addon_content_seo_gamme_car=str_replace("#MinPrice#","",$addon_content_seo_gamme_car);
			    // Changement #CompSwitch_3_PG_ID#
			        $comp_switch_3_pg_id_marker="#CompSwitch_3_".$this_pg_id."#";
					$comp_switch_3_pg_id_value="";
					if(strpos($addon_content_seo_gamme_car, $comp_switch_3_pg_id_marker))
					{
						$query_seo_gamme_car_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						if ($request_seo_gamme_car_switch->num_rows > 0) 
						{
							$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
							// CONTENT
							$comp_switch_3_pg_id_debut=($type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
							$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 3 
								ORDER BY SGCS_ID LIMIT $comp_switch_3_pg_id_debut,1";
							$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
							$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
							$comp_switch_3_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
						}
						$addon_content_seo_gamme_car=str_replace($comp_switch_3_pg_id_marker,$comp_switch_3_pg_id_value,$addon_content_seo_gamme_car);
					}
				// Changement #PrixPasCher#	
					$PrixPasCherTab=(($this_pg_id%100)+$type_id)%$PrixPasCherLength;
					$PrixPasCherTab2=(($this_pg_id%100)+$type_id+1)%$PrixPasCherLength;
			        $addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab2],$addon_content_seo_gamme_car);
			        //$addon_content_seo_gamme_car=str_replace("#PrixPasCher#",$PrixPasCher[$PrixPasCherTab2],$addon_content_seo_gamme_car);
			    // Changement #CompSwitch#
			        $comp_switch_marker="#CompSwitch#";
			        $comp_switch_marker_2="#CompSwitchTitle#";
					$comp_switch_value="";
					$comp_switch_value_2="";
					//if(strpos($addon_content_seo_gamme_car, $comp_switch_marker))
					//{
						$query_seo_item_switch = "SELECT SIS_ID   
							FROM __SEO_ITEM_SWITCH 
							WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1";
						$request_seo_item_switch = $conn->query($query_seo_item_switch);
						if ($request_seo_item_switch->num_rows > 0) 
						{
							$request_seo_item_switch_num_rows = $request_seo_item_switch->num_rows;
							// CONTENT
							$comp_switch_debut = ($type_id+1) % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SIS_CONTENT   
								FROM __SEO_ITEM_SWITCH 
								WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1 
								ORDER BY SIS_ID LIMIT $comp_switch_debut,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_value = $result_seo_item_switch["SIS_CONTENT"];
							// TITLE
							$comp_switch_debut_2 = $type_id % $request_seo_item_switch_num_rows;
							$query_seo_item_switch = "SELECT SIS_CONTENT   
								FROM __SEO_ITEM_SWITCH 
								WHERE SIS_PG_ID = $this_pg_id AND SIS_ALIAS = 1 
								ORDER BY SIS_ID LIMIT $comp_switch_debut_2,1";
							$request_seo_item_switch = $conn->query($query_seo_item_switch);
							$result_seo_item_switch = $request_seo_item_switch->fetch_assoc();
							$comp_switch_value_2 = $result_seo_item_switch["SIS_CONTENT"];
						}
						$addon_content_seo_gamme_car=str_replace($comp_switch_marker,$comp_switch_value,$addon_content_seo_gamme_car);
						$addon_content_seo_gamme_car=str_replace($comp_switch_marker_2,$comp_switch_value_2,$addon_content_seo_gamme_car);
					//}
				// Changement #LinkGammeCar_PG_ID#
					$LinkGammeCar_pg_id_marker="#LinkGammeCar_".$this_pg_id."#";
					$LinkGammeCar_pg_id_value="";
					$LinkGammeCar_pg_id_value_2="";
					if(strstr($addon_content_seo_gamme_car, $LinkGammeCar_pg_id_marker))
					{
						// LinkGammeCar_PG_ID_1
						$query_seo_gamme_car_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						if ($request_seo_gamme_car_switch->num_rows > 0) 
						{
							$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
							// CONTENT
							$LinkGammeCar_pg_id_debut=($type_id+$this_pg_id+2) % $request_seo_gamme_car_switch_num_rows;
							$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 1 
								ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut,1";
							$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
							$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
							$LinkGammeCar_pg_id_value = $result_seo_gamme_car_switch["SGCS_CONTENT"];
						}
						// LinkGammeCar_PG_ID_2
						$query_seo_gamme_car_switch = "SELECT SGCS_ID   
							FROM __SEO_GAMME_CAR_SWITCH 
							WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2";
						$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
						if ($request_seo_gamme_car_switch->num_rows > 0) 
						{
							$request_seo_gamme_car_switch_num_rows = $request_seo_gamme_car_switch->num_rows;
							// CONTENT
							$LinkGammeCar_pg_id_debut_2=($type_id+$this_pg_id+3) % $request_seo_gamme_car_switch_num_rows;
							$query_seo_gamme_car_switch = "SELECT SGCS_CONTENT   
								FROM __SEO_GAMME_CAR_SWITCH 
								WHERE SGCS_PG_ID = $this_pg_id AND SGCS_ALIAS = 2 
								ORDER BY SGCS_ID LIMIT $LinkGammeCar_pg_id_debut_2,1";
							$request_seo_gamme_car_switch = $conn->query($query_seo_gamme_car_switch);
							$result_seo_gamme_car_switch = $request_seo_gamme_car_switch->fetch_assoc();
							$LinkGammeCar_pg_id_value_2 = $result_seo_gamme_car_switch["SGCS_CONTENT"];
						}

						$LinkGammeCar_pg_id_link_no=$LinkGammeCar_pg_id_value." les ".$this_pg_name_site." et ".$LinkGammeCar_pg_id_value_2;

						$addon_content_seo_gamme_car=str_replace($LinkGammeCar_pg_id_marker,$LinkGammeCar_pg_id_link_no,$addon_content_seo_gamme_car);

						$LinkGammeCar_pg_id_link_full="<a href='".$LinkGammeCar_pg_id_link."'>";

						$addon_content_seo_gamme_car=str_replace("<a>",$LinkGammeCar_pg_id_link_full,$addon_content_seo_gamme_car);
					}
				/////////////////////////////// fin addon_content_seo_gamme_car //////////////////////////////////
				}
				else
				{
					$addon_content_seo_gamme_car = "Achetez ".$this_pg_name_meta." ".$marque_name_meta." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date.", d'origine à prix bas."."<br><a href='".$LinkGammeCar_pg_id_link."'>".$this_pg_name_meta." ".$marque_name_meta_title." ".$modele_name_meta." ".$type_name_meta." ".$type_nbch." ch ".$type_date."</a>";
				}
			?>
			<div class="col-12 col-md-6 blocToPCar">

				<div class="container-fluid p-3 pb-0 mh57">
					<span><?php echo $this_pg_name_site." pour ".$marque_name_site." ".$modele_name_site." ".$type_name_site; ?></span>
				</div>
				<div class="container-fluid regularContentBordered2 mh237 p-3">
					<img data-src="<?php echo $this_pg_pic; ?>" src="/upload/loading-min.gif" alt="<?php echo $this_pg_name_meta; ?>" width="225" height="165" class="img-fluid lazy"/> <?php echo content_cleaner($addon_content_seo_gamme_car); ?>
				</div>

			</div>
			<?php
			}
			?>
			</div>
			<?php
		}
		//}
		?>

	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<?php
	$query_blog = "SELECT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_UPDATE, 
	PG_NAME, PG_ALIAS, PG_IMG
    FROM __BLOG_ADVICE 
	JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
    WHERE BA_PG_ID = $pg_id 
    ORDER BY BA_UPDATE DESC, BA_CREATE DESC";
	$request_blog = $conn->query($query_blog);
	if ($request_blog->num_rows > 0) 
	{
	$result_blog = $request_blog->fetch_assoc();
	$this_ba_id = $result_blog['BA_ID'];
	$this_ba_h1 = $result_blog['BA_H1'];
	$this_ba_alias = $result_blog['BA_ALIAS'];
	$this_ba_preview = $result_blog['BA_PREVIEW'];
	$this_pg_name_site = $result_blog['PG_NAME'];
	$this_pg_alias = $result_blog['PG_ALIAS'];
	?>
	<div class="row">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh2">
			<h2><?php echo $this_ba_h1; ?></h2>
		</span>
	</div>
	<div class="col-12 pt-3 pb-3">
		
		<div class="container-fluid blocToBloginP">
		<?php
		// photo article blog
		$this_ba_wall = $result_blog['BA_WALL'];
		if($this_ba_wall=="no.jpg")
		{
		// image standard de la gamme
		if($isMacVersion == false)
		{
			$this_pg_img = $result_blog['PG_IMG'];
		}
		else
		{
			$this_pg_img = str_replace(".webp",".jpg",$result_blog['PG_IMG']);
		}
		$this_ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
		}
		else
		{
		// image de l'article
		$this_ba_wall_link = $domain."/upload/blog/conseils/mini/".$this_ba_wall;
		}
		?>
		<img src="<?php echo $this_ba_wall_link; ?>" 
			alt="<?php echo $this_ba_h1; ?>"
			width="225" height="165" class="img-fluid" />
		<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>" class="blog-z-read-more" target="_blank"><?php echo $this_ba_preview; ?></a>
		<?php
		$query_h2 = "SELECT BA2_CONTENT
				FROM __BLOG_ADVICE_H2
				WHERE BA2_BA_ID = $this_ba_id
				ORDER BY BA2_ID ASC LIMIT 1";
		$request_h2 = $conn->query($query_h2);
		if ($request_h2->num_rows > 0) 
		{
		while($result_h2 = $request_h2->fetch_assoc())
		{
		?>
		<br><?php echo strip_tags($result_h2['BA2_CONTENT'],'<b><ul><li><br>'); ?>
		<?php
		}
		}
		?>
		</div>

	</div>
	</div>
	<?php
	}
	?>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

</div>
</div>

<?php
require_once('global.footer.section.php');
?>

<button class="btn goBacktoTop" onclick="topFunction()" id="myBtnTop" title="Vers le haut">
TOP
</button>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.min.php');
?>
<?php
// fichier de panier shopping add / print
include('global.mycart.call.inpage.php');
?>
<?php
// fichier de panier shopping add / print
include('products.car.gamme.fiche.call.php');
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 200                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
		}
		else
		{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* ////////////                      NO ITEM FOUND                  ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php $pageDisabled = "p0"; include("412.page.php"); ?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 412                     ////////////////////// */
/* ////////////                      NO ITEM FOUND                  ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
		}
	}
	else
	{
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           412                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php $pageDisabled = "p"; include("412.page.php"); ?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 412                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
	}
}
?>