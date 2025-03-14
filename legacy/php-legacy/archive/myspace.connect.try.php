	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12">
		<?php
		// fichier de recuperation et d'affichage des parametres de la base de données
		require_once('config/ariane.conf.php');
		?>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row d-none d-lg-block">
	<div class="col-12 pt-3 pb-3">
		<span class="containerh1">
			<h1>Bienvenu sur automecanik</h1>
		</span>
	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->

	<!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE --><!-- LINE -->
	<div class="row">
	<div class="col-12">   

	<div class="row">
	<div class="col-12 col-md-6">
	<!-- COL LEFT -->
	
			<!-- FORMULAIRE -->     
			<form action="<?php echo $domain; ?>/connexion.html" method="post" role="form">

			    <div class="row">
				    <div class="col-12 pt-3 pb-3">
						<h2 class="TXT">Déjà Client sur Automecanik.com</h2>
					</div>
				    <div class="col-12 pt-3 pb-3">
						Connectez-vous sur votre espace client et payez en toute sécurité votre panier d'achat. Livraison express en 24/48H.
					</div>
			    </div>

			    <div class="row">
			        <div class="col-12 pt-3 pb-3">
			            Email *
			            <input type="email" name="requestedlog" class="subscribe" required autocomplete="off"/> 
			        </div>
			        <div class="col-12 pb-3">
			            Mot de passe
			            <input type="password" name="requestedmp" class="subscribe" required autocomplete="off"/>
			        </div>
			        <div class="col-12 text-center pb-3">
			            <input type="submit" class="subscribeSubmit" value="Connectez-vous" />
			            <input type="hidden" name="ASK2CONNECT" value="1">
			            <input type="hidden" name="ASK2CONNECTLINK" value="<?php echo $ask2connectResponseLinkCode; ?>">   
			        </div>
			    </div>

			</form>
			<!-- FIN FORMULAIRE -->

	<!-- / COL LEFT -->
	</div>
	<div class="col-12 col-md-1 d-none d-md-block">
	</div>
	<div class="col-12 col-md-5">
	<!-- COL RIGHT -->
			
			<div class="row">
			    <div class="col-12 pt-3 pb-3">
					<h2 class="TXT">Nouveau Client sur Automecanik.com</h2>
				</div>
			    <div class="col-12 pt-3 pb-3">
					Créez un compte client et bénéficiez d'un espace personnel afin d'ajouter toutes vos pièces auto commandées en toute sécurité et gérer toutes vos commandes.
					<br>
					L'inscription est gratuite. Vous pourrez modifier vos données quand vous le souhaitez.
				</div>
			    <div class="col-12 pt-3 pb-3 text-center text-md-right">
					<a href="/inscription.html" class="ask-subscribe">Créer mon profil</a>
				</div>
		    </div>
	<!-- / COL RIGHT -->
	</div>
	</div>

	</div>
	</div>
	<!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE --><!-- / LINE -->