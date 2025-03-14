<div class="modal CART-MODAL fade" id="addtomyCart" tabindex="-1" role="dialog" aria-labelledby="addtomyCartLabel" aria-hidden="false">
  <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <i><h5 class="modal-title" id="addtomyCartLabel">&nbsp;</h5></i>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0 m-0">
        &nbsp;
      </div>
      <div class="modal-footer">
        <button type="button" class="btn modalmycartcontinue" data-dismiss="modal">Continuer mes achats</button>
        &nbsp;
        <button type="button" class="btn modalmycartvaliate" onclick="window.location.href='<?php echo $domain; ?>/panier.html'">valider ma commande</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('#addtomyCart').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var recipient = button.data('whatever')
  var modal = $(this)
  modal.find('.modal-title').text('Articles de mon panier')
  modal.find('.modal-body input').val(recipient)
  var url = "<?php echo $domain; ?>/cart/add/" + recipient
  $(".modal-body").html('<iframe width="100%" height="387" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>