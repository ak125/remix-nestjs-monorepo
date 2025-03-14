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
if(isset($_SESSION['myaklog'])) // le client est deja connecté
{
// ON CHANGE LA CONNEXION
mysqli_close($sqlConn);
include('config/sql.extranet.conf.php');
// GET CLIENT DATA
    $com_id = $_GET['com_id'];
    $ssid = $_SESSION['myakid'];
    //$query_invoice_data = "SELECT * FROM backofficeplateform_facture 
        //WHERE id_com = '$com_id' AND id_clt = '$ssid'";
    $query_invoice_data = "SELECT * FROM backofficeplateform_facture 
        WHERE id_com = '$com_id' ";
    $request_invoice_data = $conn->query($query_invoice_data);
    if ($request_invoice_data->num_rows > 0) 
    {
        $result_invoice_data = $request_invoice_data->fetch_assoc();
        // INVOICE DATA
        $commande_id = $result_invoice_data['id_com'];
        $invoice_id = $result_invoice_data['id_fact'];
// FIN CHANG. CONNEXION
?>
    <!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
   <div class="row p-0 m-0">
        <div class="col-12 invoicePageHolder">

                    <div class="row">
                        <div class="col-3 holder-logo">
                        
                            <div class="row p-0 m-0">
                                <div class="col-12 holder-logo-box">
                                    <img src="/assets/img/automecanik.png" class="mw-100" />
                                </div>
                            </div>

                        </div>
                        <div class="col-4 p-3">&nbsp;</div>
                        <div class="col-5 holder-resume"> 

                        	<div class="row p-0 m-0">
                        		<div class="col-7 myinvoice-title">
									<b>Facture n°</b>
								</div>
								<div class="col-5 myinvoice-line text-right">
									<?php echo $result_invoice_data['id_fact']; ?>/F
								</div>
                        		<div class="col-7 myinvoice-title">
									<b>Date</b>
								</div>
								<div class="col-5 myinvoice-line text-right">
									<?php echo date('d/m/Y',strtotime($result_invoice_data['date_pay']));?>
								</div>
                                <div class="col-7 myinvoice-title">
                                    <b>Bon Commande n°</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo $result_invoice_data['id_com']; ?>/A
                                </div>
                                <div class="col-7 myinvoice-title">
                                    <b>Total</b>
                                </div>
                                <div class="col-5 myinvoice-line text-right">
                                    <?php echo number_format(($result_invoice_data['montant_tt']+$result_invoice_data['tconsigne']+$result_invoice_data['frais_port']),2,'.',''); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                                </div>
                        	</div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 p-1 myinvoice-line-2">

                            <div class="row p-0 m-0">
                                <div class="col-12">
                                    <b>Facuré à</b>
                                    <br>
                                    <?php if($result_invoice_data['raison_social_f']!="") echo $result_invoice_data['raison_social_f']."<br>"; ?>
                                    <?php echo $result_invoice_data['civilite_f']." ".$result_invoice_data['nom_f']." ".$result_invoice_data['prenom_f']; ?>
                                    <br>
                                    <?php
                                    echo $result_invoice_data['adr1_f']." - ".$result_invoice_data['code_post_f']." - ";
                                    echo $result_invoice_data['commune_f'].", ".$result_invoice_data['pays_f'].".";
                                    ?>
                                    <br>
                                    <?php
                                    echo "T. ".$result_invoice_data['tel_f']."<br>";
                                    echo "M. ".$result_invoice_data['mail_f'];
                                    ?>

                                </div>
                            </div>

                        </div>
                        <div class="col-6 p-1 myinvoice-line-2">

                            <div class="row p-0 m-0">
                                <div class="col-12">
                                    <b>Livré à</b>
                                    <br>
                                    <?php 
                                    if($result_invoice_data['type_liv']=="DOM")
                                    {
                                    	if($result_invoice_data['raison_social_l']!="") echo $result_invoice_data['raison_social_l']."<br>";
                                    	echo $result_invoice_data['civilite_l']." ".$result_invoice_data['nom_l']." ".$result_invoice_data['prenom_l']."<br>";
                                    	echo $result_invoice_data['type_liv']."<br>";
                                    	echo $result_invoice_data['adr1_l']." - ".$result_invoice_data['code_post_l']." - ";
                                    	echo $result_invoice_data['commune_l'].", ".$$result_invoice_data['pays_l'].".";
                                    ?>
                                    <br>
                                    <?php
	                                    echo "T. ".$result_invoice_data['tel_l']."<br>";
	                                    echo "M. ".$result_invoice_data['mail_l'];
                                    } 
                                    else
                                    {
                                    	echo $result_invoice_data['type_liv']." ".$result_invoice_data['num_PR']."<br>";
                                    	echo $result_invoice_data['adr_PR'];
                                    }
                                    ?>

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
                    $query_invoice_line = "SELECT * FROM backofficeplateform_ligne_facture 
                        WHERE id_fact = '$invoice_id' ";
                    $request_invoice_line = $conn->query($query_invoice_line);
                    if ($request_invoice_line->num_rows > 0) 
                    {
                        while($result_invoice_line = $request_invoice_line->fetch_assoc())
                        {
                        ?>
                        <div class="row p-0 m-0">
                        <div class="col-5 cart-recup-line">
                            <?php echo $result_invoice_line['nom_gamme']; ?> <?php echo $result_invoice_line['nom_equip']; ?> réf <?php echo $result_invoice_line['ref']; ?><br>
                            <span>
                                <?php 
                                if($result_invoice_line['prix_consigne']>0)
                                {
                                    ?>
                                    + Consigne de <b><?php echo number_format($result_invoice_line['prix_consigne'], 2, '.', ''); ?></b> <?php echo $GlobalSiteCurrencyChar; ?> TTC
                                    <?php
                                }
                                ?>
                            </span>
                        </div>
                        <div class="col-3 cart-recup-line text-right">
                            <?php echo number_format($result_invoice_line['prix_UTTC'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        <div class="col-1 cart-recup-line text-center">
                            <?php echo $result_invoice_line['quantite']; ?>
                        </div>
                        <div class="col-3 cart-recup-line-last text-right">
                            <?php echo number_format($result_invoice_line['prix_TTTC'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        </div>
                        <?php
                        $amcnkCart_total_amount = $amcnkCart_total_amount + ($result_invoice_line['prix_UTTC']*$result_invoice_line['quantite']);
                        $amcnkCart_total_consigne = $amcnkCart_total_consigne + ($result_invoice_line['prix_consigne']*$result_invoice_line['quantite']);
                        }
                        ?>
                    <?php
                    }
                    $amcnkCart_total = $amcnkCart_total_amount+$amcnkCart_total_consigne;
                    ?>
                    <div class="row p-0 pt-2 m-0">
                        <div class="col-sm-2 col-md-7 d-none d-sm-block">
                            &nbsp;
                        </div>
                        <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total">
                            Sous total TTC
                        </div>
                        <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total">
                            <?php echo number_format($amcnkCart_total_amount, 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        <?php
                        if($amcnkCart_total_consigne>0)
                        {
                        ?>
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
                            Frais de port
                        </div>
                        <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total-last">
                            <?php echo number_format($result_invoice_data['frais_port'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
                        </div>
                        <div class="col-sm-2 col-md-7 d-none d-sm-block">
                            &nbsp;
                        </div>
                        <div class="col-6 col-sm-6 col-md-3 cart-recup-title-total-last">
                            Total TTC
                        </div>
                        <div class="col-6 col-sm-4 col-md-2 text-right cart-recup-line-total-last">
                            <?php echo number_format($amcnkCart_total+$result_invoice_data['frais_port'], 2, '.', ' '); ?> <?php echo $GlobalSiteCurrencyChar; ?>
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
    <?php
    // re connect
    $ask2connectResponseLinkCode = 1;
    require_once('myspace.connect.try.php');
    ?>
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