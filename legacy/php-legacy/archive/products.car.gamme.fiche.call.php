<div class="modal CART-MODAL fade" id="pieceTechnicalModal" tabindex="-1" role="dialog" aria-labelledby="pieceTechnicalModalLabel" aria-hidden="false">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<!--div class="modal-header">
<i><h5 class="modal-title" id="pieceTechnicalModalLabel">&nbsp;</h5></i>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div-->
<div class="modal-body p-0 m-0">
&nbsp;
</div>
<!--div class="modal-footer">
<button type="button" class="btn rounded-0 CLOSE-CART-MODAL" data-dismiss="modal">Fermer la fiche</button>
</div-->
</div>
</div>
</div>
<script type="text/javascript">
  $('#pieceTechnicalModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var recipient = button.data('whatever')
  var modal = $(this)
  //modal.find('.modal-title').text('Fiche article')
  modal.find('.modal-body input').val(recipient)
  var url = "<?php echo $domain; ?>/fiche/" + recipient
  $(".modal-body").html('<iframe width="100%"  height="497" frameborder="0" allowtransparency="true" src="'+url+'"></iframe>');
})
</script>