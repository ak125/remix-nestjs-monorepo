<?php 
session_start();
// parametres relatifs à la page
$typefile="standard";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="mycart";
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
<div class="container-fluid containerinPage txt18">

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 d-none d-md-block">
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('config/ariane.conf.php');
		?>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
	
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<h1><?php echo $pageh1; ?></h1>
		</span>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

<?php $amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']); ?>
<?php
if($amcnkCart_count>0) // PANIER REMPLI
{
?>
<!-- FORMULAIRE -->    
<form action="<?php echo $domain; ?>/validation.html" method="post" role="form">

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12">

		<div class="row">
		<div class="col-12 p-3">

				<div class="row p-0 m-0">
                    <div class="col-3 col-md-1 cart-recup-title">
                    	&nbsp;
                    </div>
                    <div class="col-9 col-md-4 cart-recup-title">
                    	Article
                    </div>
                    <div class="col-3 col-md-2 cart-recup-title text-center">
                    	Prix unitaire
                    </div>
                    <div class="col-3 col-md-2 cart-recup-title text-center">
                    	Quantité
                    </div>
                    <div class="col-3 col-md-1 cart-recup-title text-center">
                    	Supp.
                    </div>
                    <div class="col-3 col-md-2 cart-recup-title-last text-center">
                    	Prix Total
                    </div>
                </div>
		<?php
        $amcnkCart_total_amount = 0;
        $amcnkCart_total_consigne = 0;
        for($i = 0; $i < $amcnkCart_count; $i++) 
        { 
        $piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
        $piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
        ?>
			<?php 
			$query_piece = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_NAME, 
			PIECE_PM_ID, PM_NAME, 
			(PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, (PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC, 
			PIECE_HAS_IMG 
			FROM PIECES 
			JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
			JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
			WHERE PIECE_ID = $piece_id_this AND PIECE_DISPLAY = 1 
			ORDER BY PIECE_SORT";
			$request_piece = $conn->query($query_piece);
			if ($request_piece->num_rows > 0) 
			{
			$result_piece = $request_piece->fetch_assoc();
			// PHOTO
			if($result_piece['PIECE_HAS_IMG']==1)
			{
				$query_item_list_img = "SELECT CONCAT('rack/',PMI_FOLDER,'/',PMI_NAME,'.webp') AS PIECE_IMG 
					FROM PIECES_MEDIA_IMG WHERE PMI_PIECE_ID = $piece_id_this AND PMI_DISPLAY = 1";
				$request_item_list_img = $conn->query($query_item_list_img);
				if($request_item_list_img->num_rows)
				{
					$result_item_list_img = $request_item_list_img->fetch_assoc();
					$photo_link = $result_item_list_img['PIECE_IMG'];
				}
				else
				{
                    $photo_link = "upload/articles/no.png";
				}
			}
			else
			{
                $photo_link = "upload/articles/no.png";
			}
			?>
                <div class="row p-0 m-0">
                    <div class="col-3 col-md-1 cart-recup-line p-0">
                    	<img src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" class="wall mw-100" />
                    </div>
                    <div class="col-9 col-md-4 cart-recup-line">
                    	<?php echo $result_piece['PIECE_NAME']; ?> <?php echo $result_piece['PM_NAME']; ?> réf <?php echo $result_piece['PIECE_REF']; ?><br>
                    	<span>
                    		<?php 
							if($result_piece['PCTTC']>0)
							{
								?>
								+ Consigne de <b><?php echo number_format($result_piece['PCTTC'], 2, '.', ''); ?></b> <?php echo $GlobalSiteCurrencyChar; ?> TTC
								<?php
							}
							?>
                    	</span>
                    </div>
                    <div class="col-3 col-md-2 cart-recup-line text-right">
                    	<?php echo number_format($result_piece['PVTTC'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                    </div>
                    <div class="col-3 col-md-2 cart-recup-line text-center pl-0 pr-0">

<a href="/mycart.show.qty.php?action=minus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="/assets/img/cart-qtyminus.jpg"  style="border:1px solid #FFFFFF;" /></a>	
                    	&nbsp; <?php echo $piece_qte_this; ?> &nbsp;
<a href="/mycart.show.qty.php?action=plus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="/assets/img/cart-qtyplus.jpg"  style="border:1px solid #FFFFFF;" /></a>

                    </div>
                    <div class="col-3 col-md-1 cart-recup-line text-center">

<a href="/mycart.show.qty.php?action=drop&pieceidtakentoadd=<?php echo $piece_id_this; ?>" style="text-decoration:underline; color:#e82042;"><img src="/assets/img/cart-supp.png" class="mw-100"></a>

                    </div>
                    <div class="col-3 col-md-2 cart-recup-line-last text-right">
                    	<?php echo number_format($result_piece['PVTTC']*$piece_qte_this, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                    </div>
                </div>
            <?php
            $amcnkCart_total_amount = $amcnkCart_total_amount + ($result_piece['PVTTC']*$piece_qte_this);
            $amcnkCart_total_consigne = $amcnkCart_total_consigne + ($result_piece['PCTTC']*$piece_qte_this);
	        }
	        ?>
		<?php
        }
        $amcnkCart_total = $amcnkCart_total_amount+$amcnkCart_total_consigne;
        ?>
	        	<div class="row p-0 pt-2 m-0">
                    <?php
                    if($amcnkCart_total_consigne>0)
                    {
                    ?>
                    <div class="col-sm-2 col-md-7 d-none d-sm-block">
                    	&nbsp;
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total">
                    	Sous total TTC
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total">
                    	<?php echo number_format($amcnkCart_total_amount, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                    </div>
                    <div class="col-sm-2 col-md-7 d-none d-sm-block">
                    	&nbsp;
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total">
                    	Consigne TTC
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total">
                    	<?php echo number_format($amcnkCart_total_consigne, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                    </div>
                    <?php
                    }
                    ?>
                    <div class="col-sm-2 col-md-7 d-none d-sm-block">
                    	&nbsp;
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total-last">
                    	Total TTC
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total-last">
                    	<?php echo number_format($amcnkCart_total, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                    </div>
                </div>
		</div>
		</div>

	    <div class="row pt-3">
		    <div class="col-12 pt-3 pb-3">
				<h2 class="TXTRED">Pour un traitement rapide et la vérification de la compatibilité des pièces avec votre véhicule, veuillez remplir les champs suivants :</h2>
			</div>
	    </div>

		<div class="row">
		<div class="col-12">

			<div class="row">
	        <div class="col-12 col-md-4 pb-3">
	            Immatriculation
	            <input type="text" name="cartimmat" class="subscribe" /> 
	        </div>
	        <div class="col-12 col-md-4 pb-3">
	            VIN (Numéro de chassis)
	            <input type="text" name="cartvin" class="subscribe" />
	        </div>
	        <div class="col-12 col-md-4 pb-3">
	            Réf d'origine ou commercial
	            <input type="text" name="oemcom" class="subscribe" />
	        </div>
	        <div class="col-12 pb-3">
	            Info complémentaires
	            <textarea rows="3" class="subscribe" name="infossup"></textarea>
	        </div>
	        <div class="col-12 pb-3">
		        <input name="equiv" type="checkbox"  value="oui" />&nbsp;&nbsp;J'accepte une pièce equivalente en cas de rupture usine ou non compatibilité avec mon véhicule.
		    </div>
	    	</div>
	    
		    <div class="row">
		        <div class="col-12 col-sm-6 text-center text-sm-right pb-3">
		            <a href="<?php echo $domain; ?>" class="ask-subscribe">Continer mes achats</a>  
		        </div>
		        <div class="col-12 col-sm-6 text-center text-sm-left pb-3">
		            <input type="submit" class="subscribeSubmit" value="Valider mon panier" />
		            <input type="hidden" name="ASK2VALIDATE" value="1">
		            <input type="hidden" name="ASK2VALIDATELINK" value="1">   
		        </div>
		    </div>

		</div>
		</div>

	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

</form>
<!-- / FORMULAIRE -->
<?php
}
else
{
?>
	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12 text-center">
		Votre panier est vide
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php
}
?>

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