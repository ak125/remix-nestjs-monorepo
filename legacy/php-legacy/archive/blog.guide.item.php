<?php 
session_start();
// parametres relatifs à la page
$typefile="blog";
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/sql.conf.php');
// Get Datas
$bg_alias=$_GET["bg_alias"];
?>
<?php
// QUERY SELECTOR
$query_selector = "SELECT BG_ID 
	FROM __BLOG_GUIDE 
	WHERE BG_ALIAS = '$bg_alias'";
$request_selector = $conn->query($query_selector);
if ($request_selector->num_rows > 0) 
{
	$result_selector = $request_selector->fetch_assoc();
	$bg_id = $result_selector['BG_ID'];
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                           200                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
<?php
// QUERY PAGE
$query_item = "SELECT DISTINCT BG_ID, BG_H1, BG_ALIAS, BG_PREVIEW, BG_CONTENT, BG_CTA_ANCHOR, BG_CTA_LINK,  
	BG_WALL, BG_CREATE, BG_UPDATE, BG_TITLE, BG_DESCRIP, BG_KEYWORDS 
	FROM __BLOG_GUIDE
	WHERE BG_ID = $bg_id 
	ORDER BY BG_UPDATE DESC, BG_CREATE DESC LIMIT 1";
$request_item = $conn->query($query_item);
$result_item = $request_item->fetch_assoc();
    // ITEM DATA
    $bg_id = $result_item['BG_ID'];
	$bg_h1 = $result_item['BG_H1'];
	$bg_alias = $result_item['BG_ALIAS'];
	$bg_preview = $result_item['BG_PREVIEW'];
	$bg_content = $result_item['BG_CONTENT'];
    // WALL
    $bg_wall = $result_item['BG_WALL'];
		$bg_wall_link = $domain."/upload/blog/guide/large/".$bg_wall;
		$bg_wall_link_social = $domain."/upload/blog/guide/medium/".$bg_wall;
    // SEO & CONTENT
		// META
		$pagetitle = $result_item['BG_TITLE'];
        $pagedescription = $result_item['BG_DESCRIP'];
        $pagekeywords = $result_item["BG_KEYWORDS"];
        // CONTENT
        $pageh1 = $bg_h1;
        $pagecontent = strip_tags($bg_content);	
	// CLEAN SEO BEFORE PRINT
		$pagetitle = content_cleaner($pagetitle);
	    $pagedescription = content_cleaner($pagedescription);
	    $pagekeywords = content_cleaner($pagekeywords);
	    $pageh1 = content_cleaner($pageh1);
		$pagecontent = content_cleaner($pagecontent);

	// ROBOT
    $relfollow = 1;
    /*if($pg_relfollow==1)
  	{
  		$pageRobots="index, follow";
    	$relfollow = 1;
  	}
  	else
  	{
  		$pageRobots="noindex, nofollow";
    	$relfollow = 0;
  	}*/
  	// CANONICAL
  	$canonicalLink = $domain."/".$blog."/".$guide."/".$bg_alias;
		// NOT CANONICAL TO NOINDEX
		if ($relfollow == 1) 
		{
			$accessLink = $domain.$_SERVER['REQUEST_URI'];
			if (strcmp($canonicalLink, $accessLink) !== 0) 
			{
				$pageRobots="noindex, follow";
			}
		}
	// ARIANE
	$parent_arianelink = $guide;
	$parent_arianetitle = $guide_title;
	$arianetitle = $bg_h1;
?>
<?php 
// parametres relatifs à la page
$arianefile="guide.item";
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
<!-- Social network -->
<meta property="og:title" content="<?php  echo $pageh1; ?>">
<meta property="og:image" content="<?php echo $bg_wall_link_social; ?>">
<!-- Url de base du site -->
<base href="<?php echo $domain; ?>">
<link rel="canonical" href="<?php echo $canonicalLink; ?>">
<link rel="dns-prefetch" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<!-- CSS -->
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-700.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-300.woff2" type="font/woff2" crossorigin="anonymous">
<link rel="preload" as="font" href="/assets/fonts/oswald-v35-latin-regular.woff2" type="font/woff2" crossorigin="anonymous">
<link href="/assets/css/style.blog.advice.gamme.min.css" rel="stylesheet" media="all">
</head>
<body>

<?php
require_once('blog.global.header.section.php');
?>

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
	<div class="row">
	<div class="col-md-8 col-lg-9">
	<!-- COL LEFT -->

			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh1">
					<h1><?php echo $pageh1; ?></h1>
					Publié le <?php echo date_format(date_create($result_item['BG_CREATE']), 'd/m/Y à H:i'); ?>
					&nbsp; | &nbsp; 
					Modifié le <?php echo date_format(date_create($result_item['BG_UPDATE']), 'd/m/Y à H:i'); ?>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				<p class="text-justify contenth2"><?php echo $pagecontent; ?></p>
			</div>
			<div class="col-12 pb-3 text-center">
				<img data-src="<?php echo $bg_wall_link; ?>" src="/upload/loading-min.gif"
				alt="<?php echo $bg_h1; ?>"
				class="mw-100 lazy" />
			</div>
			<div class="container-fluid p-3 text-center">
				<?php 
				if(($result_item['BG_CTA_LINK']!=NULL)&&($result_item['BG_CTA_LINK']!=''))
				{
				?>
				<center>
				<a class="calltoaction" target="_blank" href="<?php echo $result_item['BG_CTA_LINK']; ?>"><?php echo $result_item['BG_CTA_ANCHOR']; ?><br>maintenant</a>
				</center>
				<?php 
				}
				?>
			</div>
			<div class="col-12 pt-3 pb-3">
				<?php
				$query_h2 = "SELECT *
						FROM __BLOG_GUIDE_H2
						WHERE BG2_BG_ID = $bg_id
						ORDER BY BG2_ID ASC";
				$request_h2 = $conn->query($query_h2);
				if ($request_h2->num_rows > 0) 
				{
				while($result_h2 = $request_h2->fetch_assoc())
				{
				$this_bg2_id = $result_h2['BG2_ID'];
				$this_bg2_marker_get = $canonicalLink."#".url_title($result_h2['BG2_H2']);
				?>
				<a class="sommary" href="<?php echo $this_bg2_marker_get; ?>"><?php echo $result_h2['BG2_H2']; ?></a><br>
				<?php
					$query_h3 = "SELECT *
							FROM __BLOG_GUIDE_H3
							WHERE BG3_BG2_ID = $this_bg2_id
							ORDER BY BG3_ID ASC";
					$request_h3 = $conn->query($query_h3);
					if ($request_h3->num_rows > 0) 
					{
					while($result_h3 = $request_h3->fetch_assoc())
					{
						$this_bg3_marker_get = $canonicalLink."#".url_title($result_h3['BG3_H3']);
						?>
						&nbsp; &nbsp; &nbsp; &nbsp; <a class="sommary" href="<?php echo $this_bg3_marker_get; ?>"><?php echo $result_h3['BG3_H3']; ?></a><br>
						<?php
					}
					}
				}
				}
				?>
			</div>
			</div>
			<?php
			$query_h2 = "SELECT *
					FROM __BLOG_GUIDE_H2
					WHERE BG2_BG_ID = $bg_id
					ORDER BY BG2_ID ASC";
			$request_h2 = $conn->query($query_h2);
			if ($request_h2->num_rows > 0) 
			{
			while($result_h2 = $request_h2->fetch_assoc())
			{
			$bg2_id = $result_h2['BG2_ID'];
			// WALL
		    $bg2_wall = $result_h2['BG2_WALL'];
			// image H2
			$bg2_wall_link = $domain."/upload/blog/guide/mini/".$bg2_wall;
			//SOMMAIRE
			$this_bg2_marker = url_title($result_h2['BG2_H2']);
			?>
			<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
			<div class="row" id="<?php echo $this_bg2_marker; ?>">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2">
					<h2><?php echo $result_h2['BG2_H2']; ?></h2>
				</span>
			</div>
			<div class="col-12 pt-3 pb-3">
				
				<p class="text-justify contenth2">
					
					<?php
					if($bg2_wall!="no.jpg")
					{
					?>
					<img src="<?php echo $bg2_wall_link; ?>" alt="<?php echo $result_h2['BG2_H2']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
					<?php
					}
					?>
					<?php echo strip_tags($result_h2['BG2_CONTENT'],'<a><b><ul><li><br>'); ?>
				</p>

				<?php 
				if(($result_h2['BG2_CTA_LINK']!=NULL)&&($result_h2['BG2_CTA_LINK']!=''))
				{
				?>
				<center>
				<a class="calltoaction" target="_blank" href="<?php echo $result_h2['BG2_CTA_LINK']; ?>"><?php echo $result_h2['BG2_CTA_ANCHOR']; ?><br>maintenant</a>
				</center>
				<?php 
				}
				?>

				<?php
				$query_h3 = "SELECT *
						FROM __BLOG_GUIDE_H3
						WHERE BG3_BG2_ID = $bg2_id
						ORDER BY BG3_ID ASC";
				$request_h3 = $conn->query($query_h3);
				if ($request_h3->num_rows > 0) 
				{
				while($result_h3 = $request_h3->fetch_assoc())
				{
				// WALL
			    $bg3_wall = $result_h3['BG3_WALL'];
				// image H2
				$bg3_wall_link = $domain."/upload/blog/guide/mini/".$bg3_wall;
				//SOMMAIRE
				$this_bg3_marker = url_title($result_h3['BG3_H3']);
				?>
						<h3 class="blog" id="<?php echo $this_bg3_marker; ?>"><?php echo $result_h3['BG3_H3']; ?></h3>
						<p class="text-justify contenth3">
						<?php
						if($bg3_wall!="no.jpg")
						{
						?>
						<img src="<?php echo $bg3_wall_link; ?>" alt="<?php echo $result_h3['BG3_H3']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
						<?php
						}
						?>
						<?php echo strip_tags($result_h3['BG3_CONTENT'],'<a><b><ul><li><br>'); ?>
						</p>

						<?php 
						if(($result_h3['BG3_CTA_LINK']!=NULL)&&($result_h3['BG3_CTA_LINK']!=''))
						{
						?>
						<center>
						<a class="calltoaction" target="_blank" href="<?php echo $result_h3['BG3_CTA_LINK']; ?>"><?php echo $result_h3['BG3_CTA_ANCHOR']; ?><br>maintenant</a>
						</center>
						<?php 
						}
						?>
				<?php
				}
				}
				?>

			</div>
			</div>
			<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
			<?php
			}
			}
			?>

	<!-- / COL LEFT -->
	</div>
	<div class="col-md-4 col-lg-3">
	<!-- COL RIGHT -->
			<?php
			$query_cross = "SELECT DISTINCT BG_ID, BG_H1, BG_ALIAS, BG_PREVIEW, BG_WALL, BG_CREATE, BG_UPDATE 
				FROM __BLOG_GUIDE 
				WHERE BG_ID != $bg_id
				ORDER BY BG_UPDATE DESC, BG_CREATE DESC";
			$request_cross = $conn->query($query_cross);
			if ($request_cross->num_rows > 0) 
			{
			?>
			<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
			<div class="row">
			<div class="col-12 pt-3 pb-3">
				<span class="containerh2panel">
					<h2>on vous propose</h2>
				</span>
			</div>
			</div>
			<div class="row p-0 m-0">
				<?php
				while($result_cross = $request_cross->fetch_assoc())
				{
					$this_bg_id = $result_cross['BG_ID'];
					$this_bg_h1 = $result_cross['BG_H1'];
					$this_bg_alias = $result_cross['BG_ALIAS'];
					$this_bg_preview = $result_cross['BG_PREVIEW'];
					?>
					<?php
					// photo article blog
					$this_bg_wall = $result_cross['BG_WALL'];
					// image de l'article
					$this_bg_wall_link = $domain."/upload/blog/guide/mini/".$this_bg_wall;
					?>
					<div class="col-12 blocToBlogItemDark">

						<div class="container-fluid p-3 pb-0">
							<i><?php echo $this_bg_h1; ?></i>
							<br>
							<span><?php echo date_format(date_create($result_cross['BG_UPDATE']), 'd/m/Y'); ?></span>
						</div>
						<div class="container-fluid p-3 mh167 text-center">
							<img data-src="<?php echo $this_bg_wall_link; ?>" src="/upload/loading-min.gif" 
							alt="<?php echo $this_bg_h1; ?>"
							class="w-100 lazy" />
						</div>
						<div class="container-fluid regularContentDark p-3">
							<?php echo content_cleaner($this_bg_preview); ?>
						</div>
						<div class="container-fluid regularContentBordered text-center pb-3">
							<a href="<?php echo $domain.'/'.$blog.'/'.$guide.'/'.$this_bg_alias; ?>" class="blog-read-more">Lire plus</a>
						</div>

					</div>
					<?php
				}
				?>
			</div>
			<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->
			<?php
			}
			?>
	<!-- / COL RIGHT -->
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	
</div>
</div>

<?php
require_once('global.footer.section.php');
?>

</body>
</html>
<!-- JS Bootstrap -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="/assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
!function(r){function e(){for(var e,t,n=0;n<l.length;n++)e=l[n],0<=(t=e.getBoundingClientRect()).top&&0<=t.left&&t.top<=(r.innerHeight||document.documentElement.clientHeight)&&function(e,t){var n=new Image,r=e.getAttribute("data-src");n.onload=function(){e.parent?e.parent.replaceChild(n,e):e.src=r,t&&t()},n.src=r}(l[n],function(){l.splice(n,n)})}for(var l=new Array,t=function(e,t){if(document.querySelectorAll)t=document.querySelectorAll(e);else{var n=document,r=n.styleSheets[0]||n.createStyleSheet();r.addRule(e,"f:b");for(var l=n.all,c=0,o=[],i=l.length;c<i;c++)l[c].currentStyle.f&&o.push(l[c]);r.removeRule(0),t=o}return t}("img.lazy"),n=0;n<t.length;n++)l.push(t[n]);e(),function(e,t){r.addEventListener?this.addEventListener(e,t,!1):r.attachEvent?this.attachEvent("on"+e,t):this["on"+e]=t}("scroll",e)}(this);
</script>
<?php
// fichier de recuperation et d'affichage des parametres de la base de données
require_once('config/analytics.track.min.php');
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
/* ////////////                           410                       ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
?>
410
<?php
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* ////////////                         FIN 410                     ////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
/* /////////////////////////////////////////////////////////////////////////////////////// */
}
?>