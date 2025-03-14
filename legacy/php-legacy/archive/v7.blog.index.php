<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// parametres relatifs à la page
$arianefile="home";
// fichier de recuperation et d'affichage des parametres du site
require_once('config/meta.conf.php');
// CANONICAL
$canonicalLink = $domain."/".$blog."/";
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
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<!-- DNS PREFETCHING -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- CSS Bootstrap -->
<link href="<?php echo $domain; ?>/system/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all">
<link rel="stylesheet" href="<?php echo $domain; ?>/system/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
<!-- CSS -->
<link href="<?php echo $domain; ?>/assets/css/v7.style.blog.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('v7.blog.header.section.php');
?>

<div class="container-fluid containerBanner">
    <div class="container-fluid mymaxwidth">

    	<div class="row">
			<div class="col-12">

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

<div class="container-fluid containerwhitePage">
    <div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-md-8 text-left">

				<?php
				$query_a_la_une = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_PREVIEW, BA_WALL, BA_CREATE, BA_UPDATE, 
						PG_NAME, PG_ALIAS, PG_PIC, PG_IMG, PG_WALL
						FROM __BLOG_ADVICE
						JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
						INNER JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
						INNER JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
						WHERE BA_PG_ID > 0 
						ORDER BY BA_UPDATE DESC, BA_CREATE DESC LIMIT 12";
				$request_a_la_une = $conn->query($query_a_la_une);
				if ($request_a_la_une->num_rows > 0) 
				{
				?>
				<div class="row">
					<div class="col-12">

						<h2>articles récents</h2>
						<div class="divh2"></div>

					</div>
					<div class="col-12 pb-3">

						<div class="row pb-3">
						<?php
						$query_advice = "SELECT DISTINCT BG_ID, BG_H1, BG_ALIAS, BG_PREVIEW, BG_WALL, BG_CREATE, BG_UPDATE 
								FROM __BLOG_GUIDE
								ORDER BY BG_UPDATE DESC, BG_CREATE DESC";
						$request_advice = $conn->query($query_advice);
						if ($request_advice->num_rows > 0) 
						{
						while($result_advice = $request_advice->fetch_assoc())
						{
							$this_bg_id = $result_advice['BG_ID'];
							$this_bg_h1 = $result_advice['BG_H1'];
							$this_bg_alias = $result_advice['BG_ALIAS'];
							$this_bg_preview = $result_advice['BG_PREVIEW'];
							$this_bg_wall =  $result_advice['BG_WALL'];
							?>
							<?php
							// photo article blog
							$this_bg_wall_link = $domain."/upload/blog/guide/mini/".$this_bg_wall;
							?>
							<div class="col-12 col-md-4 itemsBloc">

								

									<img data-src="<?php echo $this_bg_wall_link; ?>" src="/upload/loading-min.gif"
									alt="<?php echo $this_bg_h1; ?>"
									width="400" height="250" 
									class="mw-100 img-fluid lazy" />


							</div>
							<div class="col-12 col-md-8 itemsBloc">

								
									<h3><?php echo $this_bg_h1; ?></h3>
									<i>Publié le <?php echo date_format(date_create($result_advice['BG_UPDATE']), 'd/m/Y'); ?></i>
									<p>
										<?php echo content_cleaner($this_bg_preview); ?>
									<p>
									<a href="<?php echo $domain.'/'.$blog.'/'.$guide.'/'.$this_bg_alias; ?>">Lire plus</a>


							</div>
							<?php
						}
						}
						?>
						<?php
						while($result_a_la_une = $request_a_la_une->fetch_assoc())
						{
							$this_ba_id = $result_a_la_une['BA_ID'];
							$this_ba_h1 = $result_a_la_une['BA_H1'];
							$this_ba_alias = $result_a_la_une['BA_ALIAS'];
							$this_ba_preview = $result_a_la_une['BA_PREVIEW'];
							$this_pg_name_site = $result_a_la_une['PG_NAME'];
							$this_pg_alias = $result_a_la_une['PG_ALIAS'];
							?>
							<?php
							// photo article blog
							$this_ba_wall = $result_a_la_une['BA_WALL'];
							if($this_ba_wall=="no.jpg")
							{
							// image standard de la gamme
							if($isMacVersion == false)
							{
								$this_pg_img = $result_a_la_une['PG_IMG'];
							}
							else
							{
								$this_pg_img = str_replace(".webp",".jpg",$result_a_la_une['PG_IMG']);
							}
							$this_ba_wall_link = $domain."/upload/articles/gammes-produits/catalogue/".$this_pg_img;
							}
							else
							{
							// image de l'article
							$this_ba_wall_link = $domain."/upload/blog/conseils/mini/".$this_ba_wall;
							}
							?>
							<div class="col-12 col-md-4 itemsBloc">

								

									<img data-src="<?php echo $this_ba_wall_link; ?>" src="/upload/loading-min.gif"
									alt="<?php echo $this_ba_h1; ?>"
									width="400" height="250" 
									class="mw-100 img-fluid lazy" />


							</div>
							<div class="col-12 col-md-8 itemsBloc">

								
									<h3><?php echo $this_ba_h1; ?></h3>
									<b><?php echo $this_pg_name_site; ?></b>
									<i>&nbsp; &nbsp; Publié le <?php echo date_format(date_create($result_a_la_une['BA_UPDATE']), 'd/m/Y'); ?></i>
									<p>
										<?php echo content_cleaner($this_ba_preview); ?>
									<p>
									<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>">Lire plus</a>


							</div>
							<?php
						}
						?>
						</div>

					</div>
				</div>
				<?php
				}
				?>

			</div>
			<div class="col-md-4">

				<?php
				require_once('v7.blog.side.section.php');
				?>

			</div>
		</div>

	</div>
</div>

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