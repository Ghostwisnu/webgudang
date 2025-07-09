<br><br><br>
    <div class="container text-center" style="margin: 2em auto;">
    <h2 class="tex-center">Tabel Barang Masuk</h2>
    <table class="table table-bordered table-striped" style="margin: 2em auto;" id="tabel_barangmasuk">
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Brand</th>
        <th>PO Number</th>
        <th>Art</th>
        <th>Color</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Jumlah</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php if(is_array($list_data)){ ?>
        <?php $no = 1;?>
        <?php foreach($list_data as $dd): ?>
          <td><?=$no?></td>
          <td><?=$dd->tanggal?></td>
          <td><?=$dd->brand?></td>
          <td><?=$dd->po_number?></td>
          <td><?=$dd->art?></td>
          <td><?=$dd->color?></td>
          <td><?=$dd->nama_barang?></td>
          <td><?=$dd->satuan?></td>
          <td><?=$dd->jumlah?></td>
      </tr>
    <?php $no++; ?>
    <?php endforeach;?>
    <?php }else { ?>
          <td colspan="7" align="center"><strong>Data Kosong</strong></td>
    <?php } ?>
    </tbody>
  </table>
  </div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#tabel_barangmasuk').DataTable();
  });
</script>
