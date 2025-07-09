<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="<?php echo base_url(); ?>assets/jquery-3.3.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css'); ?>">
    <script src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery-ui.js'); ?>"></script>
    <script>
    $(function() {
        $("#po_number").autocomplete({
            source: "<?php echo site_url('Admin/autofill'); ?>", // URL untuk mengambil data
            minLength: 2, // Minimal karakter sebelum autocomplete aktif
            select: function( event, ui ) {
                // Aksi yang dilakukan saat item dipilih
                //  Contoh:
                //  $('#product_id').val(ui.item.id);
                //  $('#product_name').val(ui.item.value);
                //  return false;
            }
        });
    });
    </script>
</head>
<body>

    <label for="product_name">Nama Produk:</label>
    <input type="text" id="po_number" name="po_number">
    <input type="hidden" id="brand" name="brand">

</body>