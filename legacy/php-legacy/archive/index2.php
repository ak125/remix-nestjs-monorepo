<?php 
session_start();
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('../config/sql.conf.php');
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
?>
<!doctype html>
<html lang="<?php echo $hr; ?>">
<head>
<meta charset="utf-8">
<title><?php echo $domaincorename; ?></title>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<!-- Données de la page d'accueil -->
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,700" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domainparent; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="<?php echo $domainparent; ?>/system/bootstrap/js/bootstrap.min.js"></script>
<link href="<?php echo $domain; ?>/assets/css/intern.css" rel="stylesheet">
</head>
<body>

<?php
if($_GET['denied']||$_GET['expired']||$_GET['suspended']||$_GET['login'])
{
?>

<div class="row m-0">
    <div class="col-12 MASSDOC-TOP-PANEL d-block d-lg-none">


<img src="<?php echo $domain; ?>/assets/img/mini-logo-icon-gray-connect.png" />


    </div>
    <div class="col-12 col-lg-6 MASSDOC-LEFT-PANEL">



    <div class="row">
        <div class="col-12 MASSDOC-WELCOME-PANEL">
            welcome to the massdoc
            <br><span>reseller plateform</span>
            <br>
            <p>This is a secure system and you will need to provide your login information to access the site.</p>

        </div>
        <div class="col-12 CONNECTION-PANEL">

            <form method="post" action="accessRequest" role="form">

                    <div class="row">
                        <div class="col-12">
                            <span class="CONNECTION-USER-LABEL">UsER name</span><br>
                            <input name="userlog" type="text" class="CONNECTION-USER-INPUT" autocomplete="off" />
                        </div>
                        <div class="col-12">
                            <br><span class="CONNECTION-USER-LABEL">password</span><br>
                            <input name="userpswd" type="password" class="CONNECTION-USER-INPUT" autocomplete="off" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input type="submit" name="" value="Log in to my account" class="CONNECTION-USER-CONNECT" />
                        </div>
                        <div class="col-12 col-xl-8 LOGIN-ERROR">
                            <?php if($_GET['denied']) { echo "Please check your access accounts."; } ?>
                            <?php if($_GET['expired']) { echo "Your session has expired, please reconnect."; } ?>
                            <?php if($_GET['suspended']) { echo "Your account is suspended."; } ?>
                        </div>
                        <div class="col-12 col-xl-4 MASSDOC-RESET-PASSWORD">
                            Forget Password ?
                            <br>
                            Create an account ?
                        </div>
                    </div>

            </form>

        </div>
    </div>




    </div>
    <div class="col-6 MASSDOC-RIGHT-PANEL p-0 d-none d-lg-block">



<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/next100.jpg" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/bmw.jpg" alt="Second slide">
    </div>
  </div>
</div>

<div class="MASSDOC-LOGO-PANEL">
<img src="<?php echo $domain; ?>/assets/img/mini-logo-icon-gray-connect.png" />
</div>


    </div>
</div> 

<?php
}
else
{
?>

<div class="row m-0">
    <div class="col-12 MASSDOC-RIGHT-PANEL p-0" style="max-height: 57vh;">



<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel"  style="max-height: 57vh;">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/1.jpg" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100 h-100" src="<?php echo $domain; ?>/upload/2.jpg" alt="Second slide">
    </div>
  </div>
</div>

<div class="MASSDOC-LOGO-PANEL-HOME">

    <div class="row">
        <div class="col-12 text-center pt-0">
<img src="<?php echo $domain; ?>/assets/img/welcome-logo-icon-gray-connect.png" style="max-height: 117px;" />
        </div>
        <!--div class="col-6 MASSDOC-WELCOME-PANEL text-left pt-3">
            welcome to the massdoc
            <br><span>reseller plateform</span>
        </div-->
    </div>
    <!--div class="row">
        <div class="col-12 text-center">
            <button type="button" class="btn rounded-0 LOGIN-HOME" onclick="window.location.href='< ?php echo $domain; ?>/login'">LOG IN</button>
        </div>
    </div-->

</div>


    </div>
</div> 


<div class="container-fluid MASSDOC-HOME-PANEL">

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            qatari spare-parts market

    </div>
    <!--div class="col-md-12 MASSDOC-HOME-PANEL-WALL">

            <img src="< ?php echo $domain; ?>/upload/flag.jpg" class="w-100" />

    </div-->
    <div class="col-md-12 MASSDOC-HOME-PANEL-CONTENT">

            <p class="text-justify">
AUTO PIECES EQUIPEMENTS is a leader in the European automotive parts market thanks to our privileged relationship with our suppliers and our long experience in the distribution of automotive parts. We have been able to evolve over the years on the market and place ourselves as an efficient intermediary between parts suppliers and the end customer on several sales channels for individuals and professionals, by developing our own IT solution which makes us independent in the management and marketing of our business.
<br>
Today Qatar benefits from a great business opportunity of the auto parts market is the market of the future and is encouraged by the government to invest and develop the market
<br>
So this is the best potential industrial sector for Qatar. Based on market studies and commercial data.
Asian brands dominated the market with the following market shares: Toyota (33.1%), Nissan (15%), Mitsubishi (8.4%), Kia (7.1%), Lexus (6.3%). ) Hyundai (3.1%) and Honda (3.2%). American brands follow with an approximate market share of 13%, divided between Chevrolet, GMC, Cadillac, and Ford as the most popular American brands.
<br>
The demand for spare parts is expected to continue to grow in the coming years as people seek to repair, modify or upgrade their vehicles. The high demand for worn spare parts such as brake linings, transmission controls, air conditioning functions, coolant, belts continue to prevail due to overall economic growth.
 Demand for spare parts, car care products and accessories has also increased as the number of used vehicles increases each year.
<br>
The market volume in Qatar is about 200 million dollars, which can increase significantly if the demand was met the supply of parts is the main slowdown of the market.
Bosch and AC Deco are the only suppliers of maintenance parts (filtration, cooling, and air conditioning) as well as some independent importers. The market suffers from the lack of automotive spare parts and the delivery time of ordered parts. Today, the automotive sector is one of the best investment sectors and growing year on year. Asian vehicles represent 51% of the market and American vehicles 13% of the market, the rest being European vehicles in constant growth (Audi, BMW, Mercedes, Porshe...).
<br>
We are constantly striving to add value by developing supplier networks and pursuing appropriate strategies. We have enough networks to be able to source parts at a lower cost, we reduce the indirect cost structure to help us maintain competitiveness through our Massdoc solution.
            </p>


    </div>
</div>


            <div class="row">
            	<div class="col-12 MASSDOC-HOME-PANEL-TITLE">
            		Equipementiers automobiles
            	</div>
				<?php
				$queryEquip=mysql_query("SELECT pm_logo 
				FROM $sqltable_Piece_marque 
				WHERE pm_affiche = 1 AND pm_preview IS NOT NULL
				ORDER BY pm_tri LIMIT 0,6");
				while($resultEquip=mysql_fetch_array($queryEquip))
				{
				?>
			    <div class="col-2 MASSDOC-HOME-PANEL-CONTENT text-center">
									<img 
			                        src="<?php  echo $domainparent; ?>/upload/equipementiers-automobiles/<?php  echo $resultEquip['pm_logo']; ?>" 
			                        style="border:0px;" class="mw-100" />
			    </div>
			    <?php
				}
				?>
			</div>
<!--div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            qatari spare-parts market

    </div>
    <div class="col-md-12 MASSDOC-HOME-PANEL-WALL">

            <img src="< ?php echo $domain; ?>/upload/flag.jpg" class="w-100" />

    </div>
    <div class="col-md-12 MASSDOC-HOME-PANEL-CONTENT">

            <p class="text-center">Market volume $ 200 million.
            	<br>
Bosch and AC Deco are the only suppliers of maintenance parts (filtration, cooling and air conditioning) as well as some independent importers.<br>
The market suffers from lack of auto parts, unavailability of spare parts and the length of delivery of the parts ordered.<br>
Today the auto part is one of the best investment sector and growing year by year.<br>
Asian vehicles represent 51% of the market and US vehicles 12% of the market and the rest are the European vehicles that are constantly increasing (Audi, BMW, Mercedes, Porshe ...).<br>
The regional market (Middle East) represents $ 14 million in turnover.<br>
The regional market (Middle East) and bordering countries (Pakistan .....) and the African countries will account for 2023 according to AUtomecanika estimates a business volume of 220 billion dollars.</p>

    </div>
</div-->

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            MASSDOc

    </div>
    <div class="col-md-8 MASSDOC-HOME-PANEL-WALL">

            <img src="<?php echo $domain; ?>/upload/massdoc.jpg" class="w-100" />

    </div>
    <div class="col-md-4 MASSDOC-HOME-PANEL-CONTENT">

            <p>“ MASSDOC ” is an online application specialized in the cars spare-parts(aftermarket) market since 2008. Our company has developed“MASSDOC” platformsolution to manage our multi-brandscars spare parts distribution on the UE market. The high skilled team consists of IT-developers, products managers and experts who can gives an accurate and valid information for the cars spare parts/accessories sector. We are able to offer a professional solutionto effeminately manage the spare-part storage and distribution.</p>

    </div>
</div>

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            team

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-CONTENT">

            <p>MassDoc is a complete range of process data, integrated solution and consulting services for the automotive aftermarket. Our solution automates and optimizes processes for car dealerships, web sites, spare parts distributors, repair shops, insurance companies, car rentals and leasing companies. options adapt to his request.<br>
All this has been developed through a professional team that offers you an easy-to-use interface and powerful built-in features, you can manage all your product information in one place, including prices, digital assets and more.</p>

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-WALL">

            <img src="<?php echo $domain; ?>/upload/team.jpg" class="w-100" />

    </div>
</div>

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            added values

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-WALL">

            <img src="<?php echo $domain; ?>/upload/value.jpg" class="w-100" />

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-CONTENT">

            <p>MASSDOC has an online catalog that allows access and timely search of cars and spare-parts. With this powerful/efficient solution, we provide garages, distributors and cars parts dealers with an optimal database for quick and accurate spare-parts identification.
            <br>
            Effective marketing/distribution and smooth cooperation.
            <br>
            Whatever your query on our online application, MASSDOCdatabase offers the personalized solution to your request.
            <br>
			MASSDOConline catalog allows you to have an accurate identification of cars and a quick search of the appropriate spare-parts, supplemented timely by an updated information.<br>
			All processed information on spare parts, MASSDOC gives a quality information on the original cars spare-parts. All data is available to you in an efficient and quick manner.<br>
			Dealers of cars spare-parts will be able to increase their sales and gives access to new markets with the help of database which provides complete information and a professional marketing. The outlets will have a powerful tool tomanageorders, products information and referencesdata for cars to ensure optimal visibility and a strong presence in the market.
			</p>

    </div>
</div>

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            organization

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-CONTENT">

            <p>Our solution gives to customers access to all updated information of articles and communicate directly to our central platform/database which maintains the supply chain and timely process of orders with high reliability and efficiency due to the real time information availability. We also manage all orders and dispatch transactions in one system.<br>
Our module’s benefitsof an easy/fast orders management: information on warehouse, prices, availability and billing.<br>
This solution allows you to overall manage and track the order’s process request by your re-sellers through MASSDOC.<br>
Request management simplifies communication with our business partners in logistics chain and establishes an effective relationship with.<br> Manage the entire ordering process up to invoicing that will save time and reduce cost through well-coordinated/efficient communication channels that will provide long-term customer satisfaction.
</p>

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-WALL">

            <img src="<?php echo $domain; ?>/upload/dashboard.jpg" class="w-100" />

    </div>
</div>

<div class="row">
    <div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            business

    </div>
    <div class="col-md-4 MASSDOC-HOME-PANEL-WALL">

            <img src="<?php echo $domain; ?>/upload/qnetwork.jpg" class="w-100" />

    </div>
    <div class="col-md-8 MASSDOC-HOME-PANEL-CONTENT">

            <p>To establish a multi-brand warehouse platform managed by MASSDOC with centralized information that ensures communication for the entire supply chain, reliable processing of orders. Customers will be able to order parts directly from the control module at their end.<br>
MASSDOC provides an easy, cost-effective, accurate, fast, and powerful way to validate catalog and market in real time.<br>
MASSDOC has a dedicate database of more than 100,000 cars type with 6 million items, 700 Original Equipment Manufacturers and aftermarket.<br>
The timely search on our intuitive catalog can quickly identify the car or the corresponding spare-parts.<br>
This means that when you join the MASSDOC network, you join a new partner who offers you fast information, real-time availability and attractive prices for car parts.<br>
MASSDOC is the best possible access to high quality spare-parts database and delivery options tailored to your needs.<br>
The online application that will be integrated directly with carsspare-parts dealers through which search and placing orders directly on the interface.<br>
The customized functionality of the requests managed according to your needs and easily processinvoices, stock information and logistics.<br>
The solution supports the supply chain process with motto of the quality of service by optimizing the ordering process, so we increase the efficiency and reduce the blows and expenses which will allow to increase sales opportunities.<br>
MASSDOC provides these customers with technical support and commercial support for orders verification and tracking.<br>
MASSDOC offers you an online portal and a mobile application:<br>
- A spare-parts database.<br>
- Online sales.<br>
- Stock/warehouse management.<br>
- Orders management.<br>
- Invoice management.<br>
- Updated references and prices.<br>
- Pay-back and investment returns solution.
</p>

    </div>
</div>

<div class="row">
	<div class="col-12 MASSDOC-HOME-PANEL-TITLE">

            demo

    </div>

    <div class="col-md-6 MASSDOC-HOME-PANEL-WALL p-1">

<a href="<?php echo $domain; ?>/login"><img src="<?php echo $domain; ?>/assets/img/demo-app.jpg" class="w-100" /></a>

    </div>
    <div class="col-md-6 MASSDOC-HOME-PANEL-WALL p-1">

<a href="<?php echo $domainparent; ?>" target="_blank"><img src="<?php echo $domain; ?>/assets/img/demo-site.jpg" class="w-100" /></a>

    </div>
</div>

<div class="row pt-3">
    <div class="col-12 MASSDOC-HOME-PANEL-FOOTER">

            <img src="<?php echo $domain; ?>/assets/img/welcome-logo-icon-gray-connect.png" style="max-height: 77px;" /><br /><br />&copy; <?php echo date("Y"); ?> All Rights reserved - <?php echo $domaincorename; ?>

    </div>
</div>

</div>
<?php
}
?>


</body>
</html>