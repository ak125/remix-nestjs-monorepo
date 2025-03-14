<div class="modal fade" id="pieceTechnicalModal" tabindex="-1" role="dialog" aria-labelledby="pieceTechnicalModalTitle" aria-hidden="true">
  <div class="modal-dialog w-100" role="document">
    <div class="modal-content">
      <div class="modal-body">
        Chargement en cours...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn modalmycartcontinue" data-dismiss="modal">FERMER</button>
      </div>
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
  $(".modal-body").html('<iframe width="100%" frameborder="0" allowtransparency="true" src="'+url+'" onload="resizeIframe(this)"></iframe>');
})
</script>
<script>
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
  }
</script>