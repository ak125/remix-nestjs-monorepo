<?php 
session_start();
// parametres relatifs à la page
$typefile="search";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
if(isset($_GET['questCleaned']))
{
	$quest=$_GET["questCleaned"];
	$questCleaned=ClearSearchQuest($quest);
}
else
{
	$quest=$_POST["quest"];
	$questCleaned=ClearSearchQuest($quest);
}
//Get filtre
if(isset($_GET['filtre_union']))
{
	$filtre_piece_fil_id = $_GET['filtre_union'];
}
else
{
	$filtre_piece_fil_id = 0;
}
/*if(isset($_GET['filtre_essieu']))
{
	$filtre_psf_id = $_GET['filtre_essieu'];
}
else
{
	$filtre_psf_id = 0;
}*/
if(isset($_GET['filtre_equip']))
{
	$filtre_pm_id = $_GET['filtre_equip'];
}
else
{
	$filtre_pm_id = 0;
}

		// HAS ARTICLE TO PRINT
		$GammeCarCountArticle = 0;
		$query_item_count = "SELECT DISTINCT PIECE_ID
			FROM PIECES_REF_SEARCH
			INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID
			WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1";
		$request_item_count = $conn->query($query_item_count);
		$GammeCarCountArticle = $request_item_count->num_rows;
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// SEO & CONTENT
	//META
	$pagetitle = "Pièce détachée auto prix pas cher réf : ".$quest;
	$pagedescription = "Votre résultat de recherche pour : ".$quest;
	$pagekeywords = $quest;
	// CONTENT
	$pageh1 = "Résultat de recherche pour : ".$quest;
	// CLEAN SEO BEFORE PRINT
	$pagetitle = content_cleaner($pagetitle);
	$pagedescription = content_cleaner($pagedescription);
	$pagekeywords = content_cleaner($pagekeywords);
	$pageh1 = content_cleaner($pageh1);
// ROBOT
$pageRobots="noindex, nofollow";
$relfollow = 0;
// ARIANE
$arianetitle = $quest;
?>
<?php 
// parametres relatifs à la page
$arianefile="search";
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
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<!-- CSS -->
<style>
@import url('https://fonts.googleapis.com/css?family=Oswald:300,400,700&display=swap');
</style>
<link href="/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="/assets/css/<?php echo $hr;?>.css" rel="stylesheet" media="all">
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
	<div class="col-sm-4 col-lg-3 text-right">
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
			<h1><?php echo $pageh1; ?></h1>
			<?php echo $GammeCarCountArticle; ?> articles
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
		$query_get_pg_main_union_filter = "SELECT DISTINCT PG_ID, PG_NAME , COUNT(PIECE_ID) AS NBP 
			FROM PIECES_REF_SEARCH
			INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID  
			INNER JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
			WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 AND PG_DISPLAY = 1
			GROUP BY PIECE_PG_ID
			ORDER BY  PIECE_PG_ID , PIECE_SORT";
		$request_get_pg_main_union_filter = $conn->query($query_get_pg_main_union_filter);
		if ($request_get_pg_main_union_filter->num_rows > 1) 
		{
			?>
			<div class="col-sm-5 filter-block filter-cross">

			<select name="filterbyGamme" id="filterbyGamme" class="filterP" onChange="if (this.value) window.location.href=this.value">
			<?php
			$this_piece_fil_id = 0;
			$LinkGammeCar_pg_id_link_union = $domain."/find/".$questCleaned."/".$this_piece_fil_id."/".$filtre_pm_id;
			?>
			<option value='<?php echo $LinkGammeCar_pg_id_link_union; ?>' <?php if($this_piece_fil_id==$filtre_piece_fil_id) echo 'selected="selected"' ?> >
				Tous les articles (<?php echo $GammeCarCountArticle; ?>)
			</option>
			<?php
			while($result_get_pg_main_union_filter = $request_get_pg_main_union_filter->fetch_assoc())
			{
				$this_piece_fil_id = $result_get_pg_main_union_filter['PG_ID'];
				// LINK TO SEARCH FILTRE
				$LinkGammeCar_pg_id_link_union = $domain."/find/".$questCleaned."/".$this_piece_fil_id."/".$filtre_pm_id;
				?>
				<option value='<?php echo $LinkGammeCar_pg_id_link_union; ?>' <?php if($this_piece_fil_id==$filtre_piece_fil_id) echo 'selected="selected"' ?> >
					<?php echo $result_get_pg_main_union_filter['PG_NAME']." (".$result_get_pg_main_union_filter['NBP'].")"; ?>
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
		$query_get_eq_filter = "SELECT DISTINCT PM_ID, PM_NAME, PM_LOGO
			FROM PIECES_REF_SEARCH
			INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID
			INNER JOIN PIECES_MARQUE ON PM_ID = PIECE_PM_ID
			WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 AND PM_DISPLAY = 1
			ORDER BY PM_SORT";
		$request_get_eq_filter = $conn->query($query_get_eq_filter);
		if ($request_get_eq_filter->num_rows > 1) 
		{
			?>
			<div class="col-sm-5 filter-block filter-cross">

			<select name="filterbyEquip" id="filterbyEquip" class="filterP" onChange="if (this.value) window.location.href=this.value">
			<?php
			$this_pm_id = 0;
			$LinkGammeCar_pg_id_link_pm = $domain."/find/".$questCleaned."/".$filtre_piece_fil_id."/".$this_pm_id;
			?>
			<option value='<?php echo $LinkGammeCar_pg_id_link_pm; ?>' <?php if($this_pm_id==$filtre_pm_id) echo 'selected="selected"' ?> >
				Marque
			</option>
			<?php
			while($result_get_eq_filter = $request_get_eq_filter->fetch_assoc())
			{
				$this_pm_id = $result_get_eq_filter['PM_ID'];
				if($this_pm_id==$filtre_pm_id) $filter_selected = "class='filter-selected'"; else $filter_selected = "";
				// LINK TO SEARCH FILTRE
				$LinkGammeCar_pg_id_link_pm = $domain."/find/".$questCleaned."/".$filtre_piece_fil_id."/".$this_pm_id;
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
			<div class="col-sm-2 filter-block-catalog-filter">
				<a href='<?php echo $domain."/find/".$questCleaned."/0/0"; ?>'>réinitialiser<br>les filtres</a>
			</div>
	</div>
		
	</div>
	</div>
	<!-- / FILTER PIECES -->

	<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->
	<?php
	$query_item_list_bloc = "SELECT DISTINCT PG_ID, PG_NAME 
		FROM PIECES_REF_SEARCH
		INNER JOIN PIECES_REF_BRAND ON PRB_ID = PRS_PRB_ID
		INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID  
		INNER JOIN PIECES_GAMME ON PG_ID = PIECE_PG_ID
		WHERE PRS_SEARCH = '$questCleaned' AND PIECE_DISPLAY = 1 AND PG_DISPLAY = 1";
		// INJECTION FILTRE GAMME UNION
		if($filtre_piece_fil_id>0)
		{
			$query_item_list_bloc .= " 
			AND  PG_ID = $filtre_piece_fil_id";
		}
		// FIN INJECTION FILTRE GAMME UNION
		// INJECTION FILTRE ESSIEU
		/*if($filtre_psf_id>0)
		{
			$query_item_list_bloc .= " 
			AND PSF_ID = $filtre_psf_id";
		}*/
		// FIN INJECTION FILTRE ESSIEU
		// INJECTION FILTRE EQUIPEMENTIER
		if($filtre_pm_id>0)
		{
			$query_item_list_bloc .= " 
			AND PIECE_PM_ID = $filtre_pm_id";
		}
		// FIN INJECTION FILTRE EQUIPEMENTIER
		$query_item_list_bloc .= " 
		ORDER BY PRS_KIND, PIECE_PG_ID , PIECE_SORT";
	$request_item_list_bloc = $conn->query($query_item_list_bloc);
	if($request_item_list_bloc->num_rows)
	{
		while($result_item_list_bloc = $request_item_list_bloc->fetch_assoc())
		{
		$pg_id = $result_item_list_bloc['PG_ID'];
		$pg_name = $result_item_list_bloc['PG_NAME'];
		?>
		<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2">
					<h2><?php echo $pg_name; ?></h2>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">

<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->
<!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST --><!-- BLOC LIST -->				
	<?php
	$query_item_list = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_NAME, PIECE_NAME_COMP, PIECE_NAME_SIDE, 
		PRS_REF, PIECE_HAS_IMG, PIECE_HAS_OEM, PIECE_QTY_SALE, PIECE_QTY_PACK, PIECE_WEIGHT_KGM, 
		PM_ID, PM_NAME, PM_LOGO, COALESCE(PIECE_DES,PM_QUALITY) AS PM_QUALITY, PRB_NAME
		FROM PIECES_REF_SEARCH
		INNER JOIN PIECES_REF_BRAND ON PRB_ID = PRS_PRB_ID
		INNER JOIN PIECES ON PIECE_ID = PRS_PIECE_ID 
        INNER JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 		
		INNER JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID
		WHERE PRS_SEARCH = '$questCleaned' AND PIECE_PG_ID = $pg_id AND PIECE_DISPLAY = 1 AND PM_DISPLAY = 1 AND PRI_DISPO = 1";
		// INJECTION FILTRE EQUIPEMENTIER
		if($filtre_pm_id>0)
		{
			$query_item_list .= " 
			AND PIECE_PM_ID = $filtre_pm_id";
		}
		// FIN INJECTION FILTRE EQUIPEMENTIER
		$query_item_list .= " 
		ORDER BY PRS_KIND , PIECE_QTY_SALE*PRI_VENTE_TTC";
	$request_item_list = $conn->query($query_item_list);
	if($request_item_list->num_rows)
	{
	?>
	<div class="row p-0 m-0">
		<?php
		while($result_item_list = $request_item_list->fetch_assoc())
		{
			$piece_id_this = $result_item_list['PIECE_ID'];
			$piece_has_oem_this = $result_item_list['PIECE_HAS_OEM'];
			// PRICE
			$query_item_list_price = "SELECT PRI_CONSIGNE_TTC, PRI_VENTE_HT, PRI_VENTE_TTC, 
				PRI_FRS, PRI_REMISE, PRI_ACHAT_HT AS PAHT, PRI_MARGE 
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
			<img src="<?php echo $domain; ?>/upload/equipementiers-automobiles/<?php echo $result_item_list['PM_LOGO']; ?>" alt="<?php echo $result_item_list['PM_NAME']; ?>" title="<?php echo $result_item_list['PM_NAME']; ?>" class="w-100" style="max-width: 97px;" />
		</div>
		<div class="col-7 pieceQuality align-self-center">
			<b>Qualité<br><?php echo $result_item_list['PM_QUALITY']; ?></b>
		</div>
		<div class="col-12 pieceEquip pt-2">
			<?php echo $result_item_list['PM_NAME']; ?> <span><?php echo $result_item_list['PIECE_REF']; ?></span>
		</div>
		<div class="col-12 pieceWall">
			<img src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" alt="<?php echo $photo_alt; ?>" title="<?php echo $photo_title; ?>" class="wall w-100" />
		</div>
		<div class="col-12 pieceCriteria">
			<?php
			$query_item_list_technical = "SELECT DISTINCT PCL_CRI_ID, PCL_CRI_CRITERIA, PC_CRI_VALUE AS PCL_VALUE, 
				PCL_CRI_UNIT, PCL_SORT, PCL_LEVEL
				FROM PIECES_CRITERIA
				INNER JOIN PIECES_CRITERIA_LINK ON PCL_CRI_ID = PC_CRI_ID AND PCL_PG_PID = PC_PG_PID
				WHERE PC_PIECE_ID = $piece_id_this AND PCL_DISPLAY = 1 AND PCL_LEVEL IN (1,2) LIMIT 3";
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
    		data-whatever="<?php echo $piece_id_this; ?>">Fiche Détaillée</button>
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
			<button type="button" class="btn rounded-0 mt-3 ADDTOCART" data-toggle="modal" data-target="#addtomyCart" 
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
	<?php
	if(($_SERVER['REMOTE_ADDR']==$IpBureauTn)||($_SERVER['REMOTE_ADDR']==$IpBureauFr)||($_SERVER['REMOTE_ADDR']==$IpBureauAmin))
	{
	?>
	<div class="row p-0 m-0">
		<div class="col-3 TABMRD HELL">
	    R. Frs
	    </div>
	    <div class="col-3 TABMRD HELL">
	    Remise
	    </div>
	    <div class="col-3 TABMRD HELL">
	    PA NET HT
	    </div>
	    <div class="col-3 TABMRD HELL">
	    Piece id
	    </div>
	    <div class="col-3 TABMRD" style="color:#0033FF;">
	    <?php echo $result_item_list_price['PRI_FRS']; ?>
	    </div>
	    <div class="col-3 TABMRD">
	    <?php echo $result_item_list_price['PRI_REMISE']; ?> %
	    </div>
	    <div class="col-3 TABMRD" style="color:#FF0000;">
	    <?php echo $result_item_list_price['PAHT']*$result_item_list['PIECE_QTY_SALE']; ?> HT
	    </div>
	    <div class="col-3 TABMRD">
	    <?php echo $piece_id_this; ?>
	    </div>
	    <div class="col-4 TABMRD HELL">
	    Marge
	    </div>
	    <div class="col-8 TABMRD HELL">
	    Poids
	    </div>
	    <div class="col-4 TABMRD">
	    <?php echo $result_item_list_price['PRI_MARGE']; ?> %
	    </div>
	    <div class="col-8 TABMRD">
	    <?php echo $result_item_list['PIECE_WEIGHT_KGM']; ?> kg
	    </div>
	</div>
	<?php 
	}
	?>

</div>

			</div>
		<?php
		}
		?>
	</div>
	<?php
	}
	else
	{
		?>
		<div class="col-12">
		<div class="container-fluid noitemFound">
			
			Aucun articles en vente pour cette combinaison
			<br>
			<a href="<?php echo $domain."/find/".$questCleaned."/0/0"; ?>">Réinitialiser les filtres</a>
			
		</div>
		</div>
		<?php
	}
	?>
<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->
<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->

			</div>
		</div>
		<?php
		}
	}
	else
	{
		?>
		<div class="col-12">
		<div class="container-fluid noitemFound">
			
			Aucun articles en vente pour cette combinaison
			<br>
			<a href="<?php echo $domain."/find/".$questCleaned."/0/0"; ?>">Réinitialiser les filtres</a>
			
		</div>
		</div>
		<?php
	}
	?>
	<!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST --><!-- / BLOC LIST -->

</div>
</div>

<?php
require_once('global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="/system/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.php');
?>
<?php
// fichier de panier shopping add / print
include('global.mycart.call.inpage.php');
?>
<?php
// fichier de panier shopping add / print
include('search.fiche.call.php');
?>
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 200                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>