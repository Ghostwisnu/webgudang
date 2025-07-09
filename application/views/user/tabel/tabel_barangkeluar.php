<br><br><br>
    <div class="container text-center" style="margin: 2em auto;">
    <h2 class="tex-center">Tabel Barang Keluar</h2>
    <a href="<?=base_url('report/barangKeluarManual')?>" style="margin-bottom:10px;float:left;" type="button" class="btn btn-danger" name="laporan_data"><i class="fa fa-file-text" aria-hidden="true"></i> Invoice Manual</a>
    <div class="tabel" style="margin-top:80px">
    <table class="table table-bordered table-striped" style="margin: 2em auto;" id="tabel_barangkeluar">
    <thead>
      <tr>
        <th>No</th>
        <th>Destinasi</th>
        <th>Tanggal Keluar</th>
        <th>Brand</th>
        <th>PO Number</th>
        <th>Art</th>
        <th>Nama Barang</th>
        <th>Satuan</th>
        <th>Jumlah</th>
        <th>Invoice</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php if(is_array($list_data)){ ?>
        <?php $no = 1;?>
        <?php foreach($list_data as $dd): ?>
          <td><?=$no?></td>
          <td><?=$dd->dept_tujuan?></td>
          <td><?=$dd->tanggal_keluar?></td>
          <td><?=$dd->brand?></td>
          <td><?=$dd->po_number?></td>
          <td><?=$dd->art?></td>
          <td><?=$dd->nama_barang?></td>
          <td><?=$dd->satuan?></td>
          <td><?=$dd->jumlah?></td>
          <td><a type="button" class="btn btn-danger btn-report"  href="<?=base_url('report/barangKeluar/'.$dd->id_transaksi.'/'.$dd->tanggal_keluar)?>" name="btn_report" style="margin:auto;"><i class="fa fa-file-text" aria-hidden="true"></i></a></td>
      </tr>
    <?php $no++; ?>
    <?php endforeach;?>
    <?php }else { ?>
          <td colspan="7" align="center"><strong>Data Kosong</strong></td>
    <?php } ?>
    </tbody>
  </table>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#tabel_barangkeluar').DataTable();
  });
</script>
