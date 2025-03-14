<!-- Navbar and Header -->
<nav class="nav-extended blue lighten-1">
    <div class="nav-background">
        <div class="pattern active" style="background-image: url('http://placehold.it/1400x300');"></div>
    </div>
    <div class="nav-wrapper container">
        <a href="index.html" class="brand-logo"><img src="https://www.automecanik.com/assets/img/automecanik.png" /></a>
        <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
            <li class="active"><a href="">Accueil</a></li>
            <li><a href="index-dark.html">Catalogue produit</a></li>
            <li><a href="blog.html">Blog automobile</a></li>
            <li><a href="docs.html">Mon panier</a></li>
            <li><a class='dropdown-trigger' href='#' data-target='feature-dropdown'>Mon compte<i class="material-icons right">arrow_drop_down</i></a></li>
        </ul>
            <!-- Dropdown Structure -->
            <ul id='feature-dropdown' class='dropdown-content'>
            <li><a href="horizontal.html">Connexion</a></li>
            <li><a href="full-header.html">Inscripton</a></li>
            </ul>

        <div class="nav-header center">
            <h1><?php echo $pageh1; ?></h1>
            <!--div class="tagline">Portfolio</div-->
        </div>
    </div>

    <!-- Fixed Masonry Filters -->
    <div class="categories-wrapper blue">
        <div class="categories-container">
            <ul class="categories container">
            <li class="active"><a href="#all">All</a></li>
            <li><a href="#polygon">Polygon</a></li>
            <li><a href="#bigbang">Big Bang</a></li>
            <li><a href="#sacred">Sacred Geometry</a></li>
            </ul>
        </div>
    </div>

</nav>


<ul class="sidenav" id="nav-mobile">
      <li class="active"><a href="index.html">Accueil</a></li>
      <li><a href="index-dark.html">Catalogue produit</a></li>
      <li><a href="blog.html">Blog automobile</a></li>
      <li><a href="docs.html">Mon panier</a></li>
      <li><a href="full-header.html">Connexion</a></li>
      <li><a href="horizontal.html">Inscription</a></li>
</ul>