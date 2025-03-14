<?php 
session_start();
// parametres relatifs à la page
$typefile="my";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="order";
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

<div class="container-fluid invoicePage">
<?php
$com_id = $_GET['com_id'];
if(isset($_SESSION['myaklog'])) // le client est deja connecté
{
    // GET CLIENT DATA
    $ssid = $_SESSION['myakid'];

    $query_invoice_data = "SELECT ORD_ID, ORD_DATE, ORD_DATE_PAY, ORD_INFO, ORD_PARENT,  
        ORD_AMOUNT_TTC, ORD_DEPOSIT_TTC, ORD_SHIPPING_FEE_TTC, ORD_TOTAL_TTC, 
        ORD_CST_ID, CST_CIVILITY, CST_NAME, CST_FNAME, CST_ADDRESS, CST_ZIP_CODE, CST_CITY, CST_COUNTRY, 
        CST_TEL, CST_GSM, CST_MAIL, 
        ORD_CBA_ID, CBA_CIVILITY, CBA_NAME, CBA_FNAME, CBA_ADDRESS, CBA_ZIP_CODE, CBA_CITY, CBA_COUNTRY, 
        ORD_CDA_ID, CDA_CIVILITY, CDA_NAME, CDA_FNAME, CDA_ADDRESS, CDA_ZIP_CODE, CDA_CITY, CDA_COUNTRY
        FROM ___XTR_ORDER 
        JOIN ___XTR_CUSTOMER ON CST_ID = ORD_CST_ID AND CST_ACTIV = 1
        JOIN ___XTR_CUSTOMER_BILLING_ADDRESS ON CBA_ID = ORD_CBA_ID 
        JOIN ___XTR_CUSTOMER_DELIVERY_ADDRESS ON CDA_ID = ORD_CDA_ID 
        WHERE md5(ORD_ID) = '$com_id' AND ORD_IS_PAY = 0";
    $request_invoice_data = $conn->query($query_invoice_data);
    if ($request_invoice_data->num_rows > 0) 
    {
        $result_invoice_data = $request_invoice_data->fetch_assoc();
        // ORDER DATA
        $commande_id = $result_invoice_data['ORD_ID'];
// FIN CHANG. CONNEXION
?>
<form action="<?php echo $domain; ?>/mycart.proceed.to.pay.supplement.php" method="post" role="form">
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row p-0 m-0">
        <div class="col-12 invoicePageHolder">

                    <div class="row">
                        <div class="col-12 col-md-3 holder-logo">
                        
                            <div class="row p-0 m-0">
                                <div class="col-12 holder-logo-box">
                                    <img src="/assets/img/automecanik.png" class="mw-100" />
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-md-4 p-3">&nbsp;</div>
                        <div class="col-12 col-md-5 holder-resume"> 

                            <div class="row p-0 m-0">
                                <div class="col-7 myinvoice-title">
                                    <b>Commande n°</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo $result_invoice_data['ORD_PARENT']; ?>/A
                                </div>
                                <div class="col-7 myinvoice-title">
                                    <b>Date</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo date('d/m/Y',strtotime($result_invoice_data['ORD_DATE']));?>
                                </div>
                                <div class="col-7 myinvoice-title">
                                    <b>Supplement n°</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo $result_invoice_data['ORD_ID']; ?>/A
                                </div>
                                <div class="col-7 myinvoice-title">
                                    <b>Total</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo number_format(($result_invoice_data['ORD_TOTAL_TTC']),2,'.',''); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 p-1 myinvoice-line-2">

                            <div class="row p-0 m-0">
                                <div class="col-12">

                <b><u>Facturée à :</u></b><br>
                <?php echo $result_invoice_data['CBA_CIVILITY']; ?> <?php echo $result_invoice_data['CBA_NAME']; ?> 
                <?php echo $result_invoice_data['CBA_FNAME']; ?>
                <br>
                <?php echo $result_invoice_data['CBA_ADDRESS']; ?> - <?php echo $result_invoice_data['CBA_ZIP_CODE']; ?> 
                <?php echo $result_invoice_data['CBA_CITY']; ?>, <?php echo $result_invoice_data['CBA_COUNTRY']; ?>

                                </div>
                            </div>

                        </div>
                        <div class="col-6 p-1 myinvoice-line-2">

                            <div class="row p-0 m-0">
                                <div class="col-12">

                <b><u>Livrée à :</u></b><br>
                <?php echo $result_invoice_data['CDA_CIVILITY']; ?> <?php echo $result_invoice_data['CDA_NAME']; ?> 
                <?php echo $result_invoice_data['CDA_FNAME']; ?>
                <br>
                <?php echo $result_invoice_data['CDA_ADDRESS']; ?> - <?php echo $result_invoice_data['CDA_ZIP_CODE']; ?> 
                <?php echo $result_invoice_data['CDA_CITY']; ?>, <?php echo $result_invoice_data['CDA_COUNTRY']; ?>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row m-0 p-0">
                        <div class="col-5 invoice-data-title text-center">
                        Désignation
                        </div>
                        <div class="col-3 invoice-data-title text-center">
                        PU TTC
                        </div>
                        <div class="col-1 invoice-data-title text-center">
                        QTE
                        </div>
                        <div class="col-3 invoice-data-title-last text-center">
                        PT TTC
                        </div>
                    </div>
                    <?php
                    $amcnkCart_total_amount = 0;
                    $amcnkCart_total_consigne = 0;
                    $query_invoice_line = "SELECT * FROM ___XTR_ORDER_LINE 
                        WHERE ORL_ORD_ID = $commande_id";
                    $request_invoice_line = $conn->query($query_invoice_line);
                    if ($request_invoice_line->num_rows > 0) 
                    {
                        while($result_invoice_line = $request_invoice_line->fetch_assoc())
                        {
                        ?>
                        <div class="row p-0 m-0">
                        <div class="col-5 cart-recup-line">
                            <?php echo $result_invoice_line['ORL_PG_NAME']; ?>
                        </div>
                        <div class="col-3 cart-recup-line text-right">
                            <?php echo number_format($result_invoice_line['ORL_ART_PRICE_SELL_UNIT_TTC'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        <div class="col-1 cart-recup-line text-center">
                            <?php echo $result_invoice_line['ORL_ART_QUANTITY']; ?>
                        </div>
                        <div class="col-3 cart-recup-line-last text-right">
                            <?php echo number_format($result_invoice_line['ORL_ART_PRICE_SELL_TTC'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        </div>
                        <?php
                        $amcnkCart_total_amount = $amcnkCart_total_amount + ($result_invoice_line['ORL_ART_PRICE_SELL_UNIT_TTC']*$result_invoice_line['ORL_ART_QUANTITY']);
                        }
                        ?>
                    <?php
                    }
                    ?>
                    <div class="row p-0 pt-2 m-0">
                        <div class="col-sm-2 col-md-7 d-none d-sm-block">
                            &nbsp;
                        </div>
                        <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total">
                            Total TTC
                        </div>
                        <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total">
                            <?php echo number_format($amcnkCart_total_amount, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                    </div>
                    <div class="row p-0 m-0 pt-3">
                        <div class="col-6 pb-3">

                            <div class="container-fluid containerShipping">
                                
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="<?php echo $domain; ?>/assets/img/pay-paybox.jpg" class="mw-100" />
                                        <br />
                                        Carte bancaire
                                        <br />
                                        <input name="paymethod" type="radio" value="PAYBOX" checked="checked" />
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-6 pb-3">

                            <div class="container-fluid containerShipping">
                                
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="<?php echo $domain; ?>/assets/img/pay-paypal.jpg" class="mw-100" />
                                        <br />
                                        Paypal
                                        <br />
                                        <input name="paymethod" type="radio" value="PAYBOX" />
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-12 pt-3 pb-3">

                            <div class="container-fluid containerReadCGV">
                                
                                <div class="row">
                                    <div class="col-12">
                                    En cliquant sur le bouton « Payer maintenant », vous acceptez de vous conformer aux <a href="<?php echo $domain; ?>/conditions-generales-de-vente.html" target="_blank"><i><u>Conditions générales de vente</u></i></a> que vous reconnaissez avoir lus, comprises et acceptées dans leur intégralité.
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-12 mycarttotal-submit text-center pl-0 pr-0">

                        <input type="submit" class="subscribeSubmit" value="Payer maintenant" />
                        <input type="hidden" name="ORD_ID" value="<?php echo $commande_id; ?>">
                        <input type="hidden" name="ASK2PAY" value="1">
                        <input type="hidden" name="ASK2PAYLINK" value="1">  

                        </div>
                    </div>
                    <div class="row p-0 m-0 pt-3">
                        <div class="col-12 pt-3 text-center">
                            <hr>AUTO PIECES EQUIPEMENTS<br>
                            184 AVENUE ARISTIDE BRIAND 93320 LES PAVILLIONS SOUS BOIS<br> 
                            TEL 0177695892 SASU au capital de 10000 euro<br>
                            RCS Bobigny siret 82049999400010 N° tva FR58820499994 CODE APE4531Z<br>
                            WWW.AUTOMECANIK.COM
                        </div>
                    </div>

        </div>
    </div>
    <!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
</form>
<?php
    }
    else
    {
?>
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row">
        <div class="col-12 p-3 text-center">

                PAS LE DROIT / NON PAYEE / FACTURE NON DISPO

        </div>
    </div>
    <!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php       
    }
}
else
{
?>
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row">
        <div class="col-12 p-3 text-center">

                CONNECTEZ VOUS AVANT DE PROCEDER AU PAIEMENT DU SUPPLEMENT

        </div>
    </div>
    <!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
<?php
}
?>
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