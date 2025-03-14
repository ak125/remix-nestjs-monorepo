<?php /*div class="container-fluid menublogContainer">
				
	<div class="row">
		<div class="col-12">

			<h2>catégories</h2>
			<div class="sideh2"></div>

		</div>
		<div class="col-12">
						
			<a class="sideread" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $entretien; ?>"><?php echo $entretien_title; ?></a>
			<br>
			<a class="sideread" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $constructeurs; ?>"><?php echo $constructeurs_title; ?></a>
			<br>
			<a class="sideread" href="<?php echo $domain; ?>/<?php echo $blog; ?>/<?php echo $guide; ?>"><?php echo $guide_title; ?></a>

		</div>
	</div>

</div */ ?>

<div class="container-fluid sideBars">
<?php
$query_a_la_une = "SELECT DISTINCT BA_ID, BA_H1, BA_ALIAS, BA_WALL, BA_CREATE, BA_UPDATE, 
	PG_NAME, PG_ALIAS, PG_PIC, PG_IMG, PG_WALL
	FROM __BLOG_ADVICE
	JOIN PIECES_GAMME ON PG_ID = BA_PG_ID
	INNER JOIN CATALOG_GAMME ON MC_PG_ID = PG_ID
	INNER JOIN CATALOG_FAMILY ON MF_ID = MC_MF_PRIME
	WHERE BA_PG_ID > 0 
	ORDER BY BA_VISIT DESC LIMIT 6";
$request_a_la_une = $conn->query($query_a_la_une);
if ($request_a_la_une->num_rows > 0) 
{
?>
<div class="row">
	<div class="col-12">

		<h2>articles les plus lus</h2>
		<div class="sideh2"></div>

	</div>
	<div class="col-12">

		<div class="row">
		<?php
		while($result_a_la_une = $request_a_la_une->fetch_assoc())
		{
			$this_ba_id = $result_a_la_une['BA_ID'];
			$this_ba_h1 = $result_a_la_une['BA_H1'];
			$this_ba_alias = $result_a_la_une['BA_ALIAS'];
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
			<div class="col-4 itemsBloc">

				<img data-src="<?php echo $this_ba_wall_link; ?>" src="/upload/loading-min.gif"
					alt="<?php echo $this_ba_h1; ?>"
					width="400" height="250" 
					class="mw-100 img-fluid lazy" />

			</div>
			<div class="col-8 itemsBloc">

				<a href="<?php echo $domain.'/'.$blog.'/'.$entretien.'/'.$this_pg_alias; ?>" class="sideread"><?php echo $this_ba_h1; ?></a>
				<b><?php echo $this_pg_name_site; ?></b><br>
				<i>Publié le <?php echo date_format(date_create($result_a_la_une['BA_UPDATE']), 'd/m/Y'); ?></i>

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