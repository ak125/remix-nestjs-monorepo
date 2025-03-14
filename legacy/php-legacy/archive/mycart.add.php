<?php
session_start();
// parametres relatifs à la page
$typefile="cart.function";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="add";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
// Shopping cart file
include("config/shopping_cart.function.php");


// article à ajouter
$piece_id = $_GET['piece_id'];

// Quantité
$piece_qte = 1;

// traitement
if (($piece_qte>0)&&($piece_id>0))
{
// GET ALL DATA
    // url to inject
    $urltakentoadd = str_replace($domain, '', $_SERVER["HTTP_REFERER"]);
    // piece id
    $pieceidtakentoadd = $piece_id;
    // quantite demandé par le client
    $qte = $piece_qte;
    // GET ITEM DATA
    $query_piece_for_cart = "SELECT DISTINCT PIECE_ID, PIECE_REF, PIECE_REF_CLEAN, PIECE_NAME, PIECE_NAME_COMP, 
    PIECE_PM_ID, PM_NAME, PIECE_PG_ID, 
    (PRI_VENTE_TTC * PIECE_QTY_SALE) AS PVTTC, (PRI_CONSIGNE_TTC * PIECE_QTY_SALE) AS PCTTC, 
    PRI_FRAIS_PORT_HT, PIECE_WEIGHT_KGM 
    FROM PIECES 
    JOIN PIECES_PRICE ON PRI_PIECE_ID = PIECE_ID 
    JOIN PIECES_MARQUE ON PM_ID = PRI_PM_ID 
    WHERE PIECE_ID = $pieceidtakentoadd AND PIECE_DISPLAY = 1 
    ORDER BY PIECE_SORT";
    $request_piece_for_cart = $conn->query($query_piece_for_cart);
    $result_piece_for_cart = $request_piece_for_cart->fetch_assoc();
        // prix
        $prixtakentoadd = $result_piece_for_cart['PVTTC'];
        // consigne
        $consignetakentoadd = $result_piece_for_cart['PCTTC'];
            
// CREATE SELECT[]
    // url pris pour l'insertion dans le panier
    $select['urltakentoadd'] = $urltakentoadd ;
    // piece id dans le panier
    $select['id'] = $pieceidtakentoadd;
    // qte dans le panier
    $select['qte'] = $qte ;
    // prix dans le panier
    $select['prix'] = $prixtakentoadd ;
    // consigne dans le panier
    $select['consigne'] = $consignetakentoadd ;

    // ajouter l'article
    if($prixtakentoadd>0)
    {
        ajout($select);
    }
}
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
<body class="BODY-MODAL">

<div class="container-fluid modalPage w-100">

   <div class="row">
        <div class="col-12 p-3">

                <div class="row p-0 m-0">
                    <div class="col-12 col-md-5 cart-recup-title">
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
        <?php $amcnkCart_count = @count($_SESSION['amcnkCart']['id_article']); ?>
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
            ?>
                <div class="row p-0 m-0">
                    <div class="col-12 col-md-5 cart-recup-line">
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

<a href="/mycart.add.qty.php?action=minus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="/assets/img/cart-qtyminus.jpg"  style="border:1px solid #FFFFFF;" /></a>  
                        &nbsp; <?php echo $piece_qte_this; ?> &nbsp;
<a href="/mycart.add.qty.php?action=plus&pieceidtakentoadd=<?php echo $i ; ?>&qte=<?php echo '1'; ?>"><img src="/assets/img/cart-qtyplus.jpg"  style="border:1px solid #FFFFFF;" /></a>

                    </div>
                    <div class="col-3 col-md-1 cart-recup-line text-center">

<a href="/mycart.add.qty.php?action=drop&pieceidtakentoadd=<?php echo $piece_id_this; ?>" style="text-decoration:underline; color:#e82042;"><img src="/assets/img/cart-supp.png" class="mw-100"></a>

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

</div>

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