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
<!-- favicon -->
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.cart.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('v7.global.header.section.php');
?>
<div class="container-fluid containerBanner">
    <div class="container-fluid mymaxwidth">

    	<div class="row d-flex flex-row-reverse">
			<div class="col-12 col-sm align-self-center">

				<h1><?php echo $pageh1; ?></h1>
				<div class="containerariane">
					<?php
					// fichier de recuperation et d'affichage des parametres de la base de données
					require_once('config/ariane.conf.php');
					?>
				</div>

			</div>
		</div>

    </div>
</div>

<?php
if($amcnkCart_count>0) // PANIER REMPLI
{
?>
<form action="<?php echo $domain; ?>/validation.html" method="post" role="form">

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth p-3">

		<div class="row p-0 m-0 lineCart">
	        <div class="col-md-1 caseCart text-center d-none d-md-block">
	        	Supp.
	        </div>
	        <div class="col-md-1 caseCart d-none d-md-block">
	        	Image
	        </div>
	        <div class="col-12 col-md-4 caseCart">
	        	Article
	        </div>
	        <div class="col-md-2 caseCart d-none d-md-block">
	        	Prix unitaire
	        </div>
	        <div class="col-md-2 caseCart text-center d-none d-md-block">
	        	Quantité
	        </div>
	        <div class="col-md-2 caseCart d-none d-md-block">
	        	Prix Total
	        </div>

			<?php
			$amcnkCart_total_amount = 0;
			$amcnkCart_total_consigne = 0;
			for($i = 0; $i < $amcnkCart_count; $i++) 
			{ 
			$piece_id_this = $_SESSION['amcnkCart']['id_article'][$i];
			$piece_qte_this = $_SESSION['amcnkCart']['qte'][$i];
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
		        <div class="col-2 col-md-1 caseCart text-center">
		        	<a href="/mycart.show.qty.php?action=drop&pieceidtakentoadd=<?php echo $piece_id_this; ?>"><i class="pe-7s-trash"></i></a>
		        </div>
		        <div class="col-2 col-md-1 caseCart p-0">
		        	<img src="<?php echo $domain; ?>/<?php echo $photo_link; ?>" class="img-fluid lazy mw-100" />
		        </div>
		        <div class="col-3 col-md-4 caseCart">
		        	<?php echo $result_piece['PIECE_NAME']; ?> <?php echo $result_piece['PM_NAME']; ?> 
		        	réf <?php echo $result_piece['PIECE_REF']; ?>
		        	<?php 
					if($result_piece['PCTTC']>0)
					{
						?><span><br>
						+ Consigne de <b><?php echo number_format($result_piece['PCTTC'], 2, '.', ''); ?></b> <?php echo $GlobalSiteCurrencyChar; ?> TTC</span>
						<?php
					}
					?>
		        </div>
		        <div class="col-md-2 caseCart d-none d-md-block">
		        	<?php echo number_format($result_piece['PVTTC'], 2, '.', ' '); ?> 
			    	<?php echo $GlobalSiteCurrencyChar; ?>
		        </div>
		        <div class="col-3 col-md-2 caseCart text-center">
		        	<a href="/mycart.show.qty.php?action=minus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><i class="pe-7s-less"></i></a>	
	                    	&nbsp; <b><?php echo $piece_qte_this; ?></b> &nbsp;
					<a href="/mycart.show.qty.php?action=plus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><i class="pe-7s-plus"></i></a>
		        </div>
		        <div class="col-2 col-md-2 caseCart">
		        	<?php echo number_format($result_piece['PVTTC']*$piece_qte_this, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
		        </div>
		    <?php
	        $amcnkCart_total_amount = $amcnkCart_total_amount + ($result_piece['PVTTC']*$piece_qte_this);
	        $amcnkCart_total_consigne = $amcnkCart_total_consigne + ($result_piece['PCTTC']*$piece_qte_this);
			}
			}
	        $amcnkCart_total = $amcnkCart_total_amount+$amcnkCart_total_consigne;
			?>

	    </div>

		<div class="row">
			<div class="col-12 col-md-6 lineCartTotal">
	        	
				<div class="row p-0 m-0 lineCart">
			        <div class="col-7 caseCart">
			        	Sous Total TTC
			        </div>
			        <div class="col-5 caseCart text-right">
			        	<b><?php echo number_format($amcnkCart_total_amount, 2, '.', ' '); ?></b> <?php echo $GlobalSiteCurrencyChar; ?>
			        </div>
			        <?php
	                if($amcnkCart_total_consigne>0)
	                {
	                ?>
			        <div class="col-7 caseCart">
			        	Consigne TTC
			        </div>
			        <div class="col-5 caseCart text-right">
			        	<b><?php echo number_format($amcnkCart_total_consigne, 2, '.', ' '); ?></b> <?php echo $GlobalSiteCurrencyChar; ?>
			        </div>
			        <?php
	                }
	                ?>
			        <div class="col-7 caseCart">
			        	Total TTC
			        </div>
			        <div class="col-5 caseCart text-right">
			        	<b><?php echo number_format($amcnkCart_total, 2, '.', ' '); ?></b> <?php echo $GlobalSiteCurrencyChar; ?>
			        </div>
			    </div>
	        	
	        </div>
			<div class="col-12 col-md-6 lineCartTotal">
				*Hors Frais de port
			</div>
	    </div>

    </div>
</div>

<div class="container-fluid containergrayPage">
    <div class="container-fluid mymaxwidth">

		<div class="row ">
			<div class="col-12">
				<h2>Pour un traitement rapide et la vérification de la compatibilité des pièces avec votre véhicule, veuillez remplir les champs suivants :</h2>
				<div class="divh2"></div>
			</div>
			<div class="col-12">
				
				<div class="row">
			        <div class="col-12 col-md-4 pb-3">
			            Immatriculation
			            <input type="text" name="cartimmat" class="cartshow" /> 
			        </div>
			        <div class="col-12 col-md-4 pb-3">
			            VIN (Numéro de chassis)
			            <input type="text" name="cartvin" class="cartshow" />
			        </div>
			        <div class="col-12 col-md-4 pb-3">
			            Réf d'origine ou commercial
			            <input type="text" name="oemcom" class="cartshow" />
			        </div>
			        <div class="col-12 pb-3">
			            Info complémentaires
			            <textarea rows="5" class="cartshow" name="infossup"></textarea>
			        </div>
			        <div class="col-12 pb-3">
				        <input name="equiv" type="checkbox"  value="oui" />&nbsp;&nbsp;J'accepte une pièce equivalente en cas de rupture usine ou non compatibilité avec mon véhicule.
				    </div>
			    	</div>
			    
				    <div class="row">
				        <div class="col-12 col-sm-6 text-center text-sm-right pb-3">
				            <button class="cartshow" onclick="window.location.href='<?php echo $domain; ?>'">continuer mes achats</button>  
				        </div>
				        <div class="col-12 col-sm-6 text-center text-sm-left pb-3">
				            <input type="submit" class="cartvalidate" value="Valider mon panier" />
				            <input type="hidden" name="ASK2VALIDATE" value="1">
				            <input type="hidden" name="ASK2VALIDATELINK" value="1">   
				        </div>
				    </div>

			</div>
		</div>

    </div>
</div>

</form>
<?php
}
else
{
?>
<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
		<div class="col-12 text-center">
			Votre panier est vide
		</div>
		</div>

	</div>
</div>
<?php
}
?>

<?php
require_once('v7.global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="<?php echo $domain; ?>/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(e){function t(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}for(var n=new Array,r=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,o=0,c=[],i=l.length;o<i;o++)l[o].currentStyle.f&&c.push(l[o]);r.removeRule(0),t=c}return t}("img.lazy"),l=function(){for(var r=0;r<n.length;r++)l=n[r],o=void 0,(o=l.getBoundingClientRect()).top>=0&&o.left>=0&&o.top<=(e.innerHeight||document.documentElement.clientHeight)&&t(n[r],function(){n.splice(r,r)});var l,o},o=0;o<r.length;o++)n.push(r[o]);l(),function(t,n){e.addEventListener?this.addEventListener(t,n,!1):e.attachEvent?this.attachEvent("on"+t,n):this["on"+t]=n}("scroll",l)}(this);
function scrollFunction(){20<document.body.scrollTop||20<document.documentElement.scrollTop?mybutton.style.display="block":mybutton.style.display="none"}function topFunction(){document.body.scrollTop=0,document.documentElement.scrollTop=0}mybutton=document.getElementById("myBtnTop"),window.onscroll=function(){scrollFunction()};
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/v7.analytics.track.php');
?>