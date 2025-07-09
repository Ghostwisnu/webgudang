<script type="text/javascript">
  var comment_id;
  $(document).on("click", ".konfirmasiHapus-comment", function() {
    comment_id = $(this).attr("data-id");
  })
  $(document).on("click", ".hapus-datacomment", function() {
    var id = comment_id;
    
    $.ajax({
      method: "POST",
      url: "<?php echo base_url('comment/delete'); ?>",
      data: "id=" +id
    })
    .done(function(data) {
      $('#konfirmasiHapus').modal('hide');
      tampilcomment();
      $('.msg').html(data);
      effect_msg();
    })
  })

</script>