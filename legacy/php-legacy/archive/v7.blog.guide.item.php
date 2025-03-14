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
			<div class="col-md-8 text-left itemdata">

				<div class="row">
					<div class="col-12 pb-3 text-center">

						<img data-src="<?php echo $bg_wall_link; ?>" src="/upload/loading-min.gif"
						alt="<?php echo $bg_h1; ?>"
						class="mw-100  img-fluid lazy" />
						<h1><?php echo $pageh1; ?></h1>
						<p class="date">
						Publié le <?php echo date_format(date_create($result_item['BG_CREATE']), 'd/m/Y à H:i'); ?>
						&nbsp; | &nbsp; 
						Modifié le <?php echo date_format(date_create($result_item['BG_UPDATE']), 'd/m/Y à H:i'); ?>
						</p>

					</div>
					<div class="col-12 pb-3">

						<p><?php echo $pagecontent; ?></p>

					</div>
					<?php 
					if(($result_item['BG_CTA_LINK']!=NULL)&&($result_item['BG_CTA_LINK']!=''))
					{
					?>
						<div class="col-12 pb-3 text-center">
						<a class="buyNow" target="_blank" href="<?php echo $result_item['BG_CTA_LINK']; ?>"><?php echo $result_item['BG_CTA_ANCHOR']; ?><br>maintenant</a>
						</div>
					<?php 
					}
					?>
					<div class="col-12 pb-3">

						<p class="sommary">
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
						<a href="<?php echo $this_bg2_marker_get; ?>"><?php echo $result_h2['BG2_H2']; ?></a><br>
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
								&nbsp; &nbsp; &nbsp; &nbsp; <a href="<?php echo $this_bg3_marker_get; ?>"><?php echo $result_h3['BG3_H3']; ?></a><br>
								<?php
							}
							}
						}
						}
						?>
						</p>

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
						<div class="col-12">

							<h2 id="<?php echo $this_bg2_marker; ?>"><?php echo $result_h2['BG2_H2']; ?></h2>
							<div class="divh2"></div>

						</div>
						<div class="col-12 pb-3">
							<?php
							if($bg2_wall!="no.jpg")
							{
							?>
							<img src="<?php echo $bg2_wall_link; ?>" alt="<?php echo $result_h2['BG2_H2']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
							<?php
							}
							?>
							<p><?php echo strip_tags($result_h2['BG2_CONTENT'],'<a><b><ul><li><br>'); ?></p>
							<?php 
							if(($result_h2['BG2_CTA_LINK']!=NULL)&&($result_h2['BG2_CTA_LINK']!=''))
							{
							?>
							<center><a class="buyNow" target="_blank" href="<?php echo $result_h2['BG2_CTA_LINK']; ?>"><?php echo $result_h2['BG2_CTA_ANCHOR']; ?><br>maintenant</a></center>
							<?php 
							}
							?>
						</div>

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
							<div class="col-12">

								<h3 id="<?php echo $this_bg3_marker; ?>"><?php echo $result_h3['BG3_H3']; ?></h3>

							</div>
							<div class="col-12 pb-3">
								<?php
								if($bg3_wall!="no.jpg")
								{
								?>
								<img src="<?php echo $bg3_wall_link; ?>" alt="<?php echo $result_h3['BG3_H3']; ?>" width="225" height="165" style="float: left; margin-right: 27px; border : 4px solid #e7e8e9;" />
								<?php
								}
								?>
								<p><?php echo strip_tags($result_h3['BG3_CONTENT'],'<a><b><ul><li><br>'); ?></p>
								<?php 
								if(($result_h3['BG3_CTA_LINK']!=NULL)&&($result_h3['BG3_CTA_LINK']!=''))
								{
								?>
								<center><a class="buyNow" target="_blank" href="<?php echo $result_h3['BG3_CTA_LINK']; ?>"><?php echo $result_h3['BG3_CTA_ANCHOR']; ?><br>maintenant</a></center>
								<?php 
								}
								?>
							</div>
						<?php 
						}
						} 
						?>

					<?php 
					}
					} 
					?>
				</div>

			</div>
			<div class="col-md-4">

				<?php
				//require_once('v7.blog.side.section.php');
				?>
				<div class="container-fluid sideBars">
				<?php
				/*$query_side = "SELECT DISTINCT BG_ID, BG_H1, BG_ALIAS, BG_PREVIEW, BG_WALL, BG_CREATE, BG_UPDATE 
					FROM __BLOG_GUIDE 
					WHERE BG_ID != $bg_id
					ORDER BY BG_UPDATE DESC, BG_CREATE DESC"; */
				$query_side = "SELECT DISTINCT BG_ID, BG_H1, BG_ALIAS, BG_PREVIEW, BG_WALL, BG_CREATE, BG_UPDATE 
					FROM __BLOG_GUIDE 
					ORDER BY BG_UPDATE DESC, BG_CREATE DESC";
				$request_side = $conn->query($query_side);
				if ($request_side->num_rows > 0) 
				{
				?>
				<div class="row">
					<div class="col-12">

						<h2>On vous propose</h2>
						<div class="sideh2"></div>

					</div>
					<div class="col-12">

						<div class="row">
						<?php
						while($result_side = $request_side->fetch_assoc())
						{
							$this_bg_id = $result_side['BG_ID'];
							$this_bg_h1 = $result_side['BG_H1'];
							$this_bg_alias = $result_side['BG_ALIAS'];
							$this_bg_preview = $result_side['BG_PREVIEW'];
							?>
							<?php
							// photo article blog
							$this_bg_wall = $result_side['BG_WALL'];
							// image de l'article
							$this_bg_wall_link = $domain."/upload/blog/guide/mini/".$this_bg_wall;
							?>
							<div class="col-4 itemsBloc">

								<img data-src="<?php echo $this_bg_wall_link; ?>" src="/upload/loading-min.gif"
									alt="<?php echo $this_bg_h1; ?>"
									width="400" height="250" 
									class="mw-100 img-fluid lazy" />

							</div>
							<div class="col-8 itemsBloc">

								<a href="<?php echo $domain.'/'.$blog.'/'.$guide.'/'.$this_bg_alias; ?>" class="sideread"><?php echo $this_bg_h1; ?></a>
								<i>Publié le <?php echo date_format(date_create($result_side['BG_UPDATE']), 'd/m/Y'); ?></i>

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