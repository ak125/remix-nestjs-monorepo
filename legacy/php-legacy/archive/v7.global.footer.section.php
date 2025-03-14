<div class="container-fluid footermenu">
	<div class="container-fluid mymaxwidth">

		<div class="row">
			<div class="col-md-3 text-center text-md-left">
				<span>automecnik</span>
				<br>
				Pièces détachées auto à un prix pas cher sur Automecanik.com
				<br>
				<br>
				<a href="https://www.automecanik.com/blog-pieces-auto/guide/pieces-auto-comment-s-y-retrouver" target="_blank">Achat de pièces de voiture en ligne</a>
			</div>
			<div class="col-md-3 text-center text-md-left">
				<span>politiques</span>
				<?php
				$query_footer_first_menu = "SELECT * 
						FROM ___FOOTER_MENU
						WHERE FM_LEVEL = 1 AND FM_RELFOLLOW = 0
						ORDER BY FM_ID ASC";
				$request_footer_first_menu = $conn->query($query_footer_first_menu);
				if ($request_footer_first_menu->num_rows > 0) 
				{
					while($result_footer_first_menu = $request_footer_first_menu->fetch_assoc())
					{
					$footerlinktoPage = $domain."/".$result_footer_first_menu["FM_ALIAS"];
					?>
						<br><span data-obfsq="<?php echo base64_encode($footerlinktoPage); ?>" class="obfsq"><?php echo $result_footer_first_menu["FM_TITLE"]; ?></span>
					<?php
					}
				}
				?>
			</div>
			<div class="col-md-3 text-center text-md-left">
				<span>Support</span>
				<?php
				$query_footer_second_menu = "SELECT * 
						FROM ___FOOTER_MENU
						WHERE FM_LEVEL = 2 AND FM_RELFOLLOW = 0 
						ORDER BY FM_ID ASC";
				$request_footer_second_menu = $conn->query($query_footer_second_menu);
				if ($request_footer_second_menu->num_rows > 0) 
				{
					while($result_footer_second_menu = $request_footer_second_menu->fetch_assoc())
					{
					$footerlinktoPage = $domain."/".$result_footer_second_menu["FM_ALIAS"];
					?>
						<br><span data-obfsq="<?php echo base64_encode($footerlinktoPage); ?>" class="obfsq"><?php echo $result_footer_second_menu["FM_TITLE"]; ?></span>
					<?php
					}
				}
				?>
			</div>
			<div class="col-md-3 text-center text-md-right">
				<span>inscription newsletter</span>
				<br>
				Découvrez comme il est simple et rapide de rester informé sur les dernières nouveautés.
				<?php /*br><br>
				<a href="" class="globalsubscribelink">inscription immédiate</a */ ?>
			</div>
		</div>

	</div>
</div>

<div class="container-fluid footercopy">
	<div class="container-fluid mymaxwidth text-center text-md-left">

		<div class="row">
			<div class="col-md-6">
				<u>&copy; <?php echo date("Y"); ?> - tout droits réservés Automecanik.com</u>
			</div>
			<div class="col-md-6 text-md-right">
				Powered by &nbsp; &nbsp; <a target="_blank" href="<?php echo $ownerdomain; ?>"><?php echo $ownername; ?></a>
			</div>
		</div>
		
	</div>
</div>
<script>
function openMyQuickCart() {
  document.getElementById("myquickcart").style.width = "375px";
}
function closeMyQuickCart() {
  document.getElementById("myquickcart").style.width = "0";
}
function openMyQuickSearch() {
  document.getElementById("myquicksearch").style.height = "100%";
}
function closeMyQuickSearch() {
  document.getElementById("myquicksearch").style.height = "0";
}
function openMyFilters() {
  document.getElementById("myfilters").style.height = "100%";
}
function closeMyFilters() {
  document.getElementById("myfilters").style.height = "0";
}
function openMyRide() {
  document.getElementById("myride").style.height = "auto";
}
function closeMyRide() {
  document.getElementById("myride").style.height = "0";
}
</script>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) {
	var classname = document.getElementsByClassName("obfsq");
	for (var i = 0; i < classname.length; i++) {
		//click gauche
		classname[i].addEventListener('click', myFunction, false);
		//click droit
		classname[i].addEventListener('contextmenu', myRightFunction, false);
	}
});
//fonction du click gauche
var myFunction = function(event) {
	var attribute = this.getAttribute("data-obfsq");               
			if(event.ctrlKey) {                   
				 var newWindow = window.open(decodeURIComponent(window.atob(attribute)), '_blank');                    
				 newWindow.focus();               
			} else {                    
				 document.location.href= decodeURIComponent(window.atob(attribute));
			}
	};
//fonction du click droit
var myRightFunction = function(event) {
	var attribute = this.getAttribute("data-obfsq");               
		if(event.ctrlKey) {                   
			 var newWindow = window.open(decodeURIComponent(window.atob(attribute)), '_blank');                    
			 newWindow.focus();               
		} else {      
			 window.open(decodeURIComponent(window.atob(attribute)),'_blank');	
		}
} 
</script>
<button class="btn goBacktoTop" onclick="topFunction()" id="myBtnTop" title="Vers le haut">
<i class="pe-7s-angle-up"></i>
</button>