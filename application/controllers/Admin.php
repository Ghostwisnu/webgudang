<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{

  public function __construct(){
		parent::__construct();
    $this->load->model('M_admin');
    $this->load->library('upload');
    $this->load->helper('url');
	}

  public function index(){
    if($this->session->userdata('status') == 'login' && $this->session->userdata('role') == 1){
      $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
      $data['stokBarangMasuk'] = $this->M_admin->sum('tb_barang_masuk','jumlah');
      $data['stokBarangKeluar'] = $this->M_admin->sum('tb_barang_keluar','jumlah');      
      $data['dataUser'] = $this->M_admin->numrows('user');
      $data['list_po_number'] = $this->M_admin->get_po_data('tb_po_number');
      
      $this->load->view('admin/index',$data);
    }else {
      $this->load->view('login/login');
    }
  }

  public function sigout(){
    session_destroy();
    redirect('login');
  }

  ####################################
              // Profile
  ####################################

  public function profile()
  {
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/profile',$data);
  }

  public function token_generate()
  {
    return $tokens = md5(uniqid(rand(), true));
  }

  private function hash_password($password)
  {
    return password_hash($password,PASSWORD_DEFAULT);
  }

  public function proses_new_password()
  {
    $this->form_validation->set_rules('email','Email','required');
    $this->form_validation->set_rules('new_password','New Password','required');
    $this->form_validation->set_rules('confirm_new_password','Confirm New Password','required|matches[new_password]');

    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $new_password = $this->input->post('new_password');

        $data = array(
            'email'    => $email,
            'password' => $this->hash_password($new_password)
        );

        $where = array(
            'id' =>$this->session->userdata('id')
        );

        $this->M_admin->update_password('user',$where,$data);

        $this->session->set_flashdata('msg_berhasil','Password Telah Diganti');
        redirect(base_url('admin/profile'));
      }
    }else {
      $this->load->view('admin/profile');
    }
  }

  public function proses_gambar_upload()
  {
    $config =  array(
                   'upload_path'     => "./assets/upload/user/img/",
                   'allowed_types'   => "gif|jpg|png|jpeg",
                   'encrypt_name'    => False, //
                   'max_size'        => "50000",  // ukuran file gambar
                   'max_height'      => "9680",
                   'max_width'       => "9024"
                 );
      $this->load->library('upload',$config);
      $this->upload->initialize($config);

      if( ! $this->upload->do_upload('userpicture'))
      {
        $this->session->set_flashdata('msg_error_gambar', $this->upload->display_errors());
        $this->load->view('admin/profile',$data);
      }else{
        $upload_data = $this->upload->data();
        $nama_file = $upload_data['file_name'];
        $ukuran_file = $upload_data['file_size'];

        //resize img + thumb Img -- Optional
        $config['image_library']     = 'gd2';
				$config['source_image']      = $upload_data['full_path'];
				$config['create_thumb']      = FALSE;
				$config['maintain_ratio']    = TRUE;
				$config['width']             = 150;
				$config['height']            = 150;

        $this->load->library('image_lib', $config);
        $this->image_lib->initialize($config);
				if (!$this->image_lib->resize())
        {
          $data['pesan_error'] = $this->image_lib->display_errors();
          $this->load->view('admin/profile',$data);
        }

        $where = array(
                'username_user' => $this->session->userdata('name')
        );

        $data = array(
                'nama_file' => $nama_file,
                'ukuran_file' => $ukuran_file
        );

        $this->M_admin->update('tb_upload_gambar_user',$data,$where);
        $this->session->set_flashdata('msg_berhasil_gambar','Gambar Berhasil Di Upload');
        redirect(base_url('admin/profile'));
      }
  }

  ####################################
           // End Profile
  ####################################



  ####################################
              // Users
  ####################################
  public function users()
  {
    $data['list_users'] = $this->M_admin->kecuali('user',$this->session->userdata('name'));
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/users',$data);
  }

  public function form_user()
  {
    $data['list_jenisitem'] = $this->M_admin->select('tb_jenis_item');
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/form_users/form_insert',$data);
  }

  public function update_user()
  {
    $id = $this->uri->segment(3);
    $where = array('id' => $id);
    $data['token_generate'] = $this->token_generate();
    $data['list_data'] = $this->M_admin->get_data('user',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/form_users/form_update',$data);
  }

  public function proses_delete_user()
  {
    $id = $this->uri->segment(3);
    $where = array('id' => $id);
    $this->M_admin->delete('user',$where);
    $this->session->set_flashdata('msg_berhasil','User Behasil Di Delete');
    redirect(base_url('admin/users'));

  }

  public function proses_tambah_user()
  {
    $this->form_validation->set_rules('username','Username','required');
    $this->form_validation->set_rules('email','Email','required|valid_email');
    $this->form_validation->set_rules('password','Password','required');
    $this->form_validation->set_rules('confirm_password','Confirm password','required|matches[password]');

    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {

        $username     = $this->input->post('username',TRUE);
        $email        = $this->input->post('email',TRUE);
        $password     = $this->input->post('password',TRUE);
        $role         = $this->input->post('role',TRUE);

        $data = array(
              'username'     => $username,
              'email'        => $email,
              'password'     => $this->hash_password($password),
              'role'         => $role,
        );
        $this->M_admin->insert('user',$data);

        $this->session->set_flashdata('msg_berhasil','User Berhasil Ditambahkan');
        redirect(base_url('admin/form_user'));
        }
      }else {
        $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
        $this->load->view('admin/form_users/form_insert',$data);
    }
  }

  public function proses_update_user()
  {
    $this->form_validation->set_rules('username','Username','required');
    $this->form_validation->set_rules('email','Email','required|valid_email');

    
    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {
        $id           = $this->input->post('id',TRUE);        
        $username     = $this->input->post('username',TRUE);
        $email        = $this->input->post('email',TRUE);
        $role         = $this->input->post('role',TRUE);

        $where = array('id' => $id);
        $data = array(
              'username'     => $username,
              'email'        => $email,
              'role'         => $role,
        );
        $this->M_admin->update('user',$data,$where);
        $this->session->set_flashdata('msg_berhasil','Data User Berhasil Diupdate');
        redirect(base_url('admin/users'));
       }
    }else{
        $this->load->view('admin/form_users/form_update');
    }
  }


  ####################################
           // End Users
  ####################################



  ####################################
        // DATA BARANG MASUK
  ####################################

  public function form_barangmasuk()
  {
    $data['list_po_number'] = $this->M_admin->select('tb_po_number');
    $data['list_jenisitem'] = $this->M_admin->select('tb_jenis_item');
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['list_art'] = $this->M_admin->select('tb_po_number');
    $data['list_color'] = $this->M_admin->select('tb_po_number');
    $data['list_brand'] = $this->M_admin->select('tb_po_number');
    $data['list_qty_order'] = $this->M_admin->select('tb_po_number');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_insert',$data);
  }

  public function tabel_barangmasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_barang_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_barangmasuk',$data);
  }

  public function update_barang($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $data['data_barang_update'] = $this->M_admin->get_data('tb_barang_masuk',$where);
    $data['list_jenisitem'] = $this->M_admin->select('tb_jenis_item');
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_update',$data);
  }

  public function delete_barang($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_barang_masuk',$where);
    redirect(base_url('admin/tabel_barangmasuk'));
  }



  public function proses_databarang_masuk_insert()
  {
    $this->form_validation->set_rules('lokasi','Lokasi','required');
    $this->form_validation->set_rules('kode_barang','Kode Material','required');
    $this->form_validation->set_rules('nama_barang','Nama Barang','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $tanggal      = $this->input->post('tanggal',TRUE);
      $lokasi       = $this->input->post('lokasi',TRUE);
      $brand       = $this->input->post('brand',TRUE);
      $po_number = $this->input->post('po_number',TRUE);
      $art      = $this->input->post('art',TRUE);
      $color       = $this->input->post('color',TRUE);
      $jenis_item       = $this->input->post('jenis_item',TRUE);
      $kode_barang  = $this->input->post('kode_barang',TRUE);
      $nama_barang  = $this->input->post('nama_barang',TRUE);
      $satuan       = $this->input->post('satuan',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'tanggal'      => $tanggal,
            'lokasi'       => $lokasi,
            'brand' => $brand,
            'po_number' => $po_number,
            'art'      => $art,
            'color'       => $color,
            'jenis_item'       => $jenis_item,
            'kode_barang'  => $kode_barang,
            'nama_barang'  => $nama_barang,
            'satuan'       => $satuan,
            'jumlah'       => $jumlah
      );
      $this->M_admin->insert('tb_barang_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_barangmasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_barangmasuk/form_insert',$data);
    }
  }

  public function proses_databarang_masuk_update()
  {
    $this->form_validation->set_rules('lokasi','Lokasi','required');
    $this->form_validation->set_rules('kode_barang','Kode Barang','required');
    $this->form_validation->set_rules('nama_barang','Nama Barang','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $tanggal      = $this->input->post('tanggal',TRUE);
      $lokasi       = $this->input->post('lokasi',TRUE);
      $kode_barang  = $this->input->post('kode_barang',TRUE);
      $nama_barang  = $this->input->post('nama_barang',TRUE);
      $satuan       = $this->input->post('satuan',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'tanggal'      => $tanggal,
            'lokasi'       => $lokasi,
            'kode_barang'  => $kode_barang,
            'nama_barang'  => $nama_barang,
            'satuan'       => $satuan,
            'jumlah'       => $jumlah
      );
      $this->M_admin->update('tb_barang_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_barangmasuk'));
    }else{
      $this->load->view('admin/form_barangmasuk/form_update');
    }
  }
  ####################################
      // END DATA BARANG MASUK
  ####################################


  ####################################
              // SATUAN
  ####################################

  public function form_satuan()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_satuan/form_insert',$data);
  }

  public function tabel_satuan()
  {
    $data['list_data'] = $this->M_admin->select('tb_unitlist');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_satuan',$data);
  }

  public function update_satuan()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_unit' => $uri);
    $data['data_satuan'] = $this->M_admin->get_data('tb_unitlist',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_satuan/form_update',$data);
  }

  public function delete_satuan()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_unit' => $uri);
    $this->M_admin->delete('tb_unitlist',$where);
    redirect(base_url('admin/tabel_satuan'));
  }

  public function proses_satuan_insert()
  {
    $this->form_validation->set_rules('unit_name','Nama Unit','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $unit_name = $this->input->post('unit_name' ,TRUE);

      $data = array(
            'unit_name' => $unit_name,
      );
      $this->M_admin->insert('tb_unitlist',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_satuan'));
    }else {
      $this->load->view('admin/form_satuan/form_insert');
    }
  }

  public function proses_satuan_update()
  {
    $this->form_validation->set_rules('unit_name','Nama Unit','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_unit   = $this->input->post('id_unit' ,TRUE);
      $nama_unit = $this->input->post('unit_name' ,TRUE);

      $where = array(
            'id_unit' => $id_unit
      );

      $data = array(
            'unit_name' => $nama_unit
      );
      $this->M_admin->update('tb_unitlist',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_satuan'));
    }else {
      $this->load->view('admin/form_satuan/form_update');
    }
  }

  ####################################
            // END SATUAN
  ####################################



  ####################################
              // JENIS ITEM
  ####################################

  public function form_jenisitem()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_jenisitem/form_insert',$data);
  }

  public function tabel_jenisitem()
  {
    $data['list_data'] = $this->M_admin->select('tb_jenis_item');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_jenisitem',$data);
  }

  public function update_jenisitem()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_item' => $uri);
    $data['data_jenisitem'] = $this->M_admin->get_data('tb_item',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_jenisitem/form_update',$data);
  }

  public function delete_jenisitem()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_item' => $uri);
    $this->M_admin->delete('tb_jenis_item',$where);
    redirect(base_url('admin/tabel_jenisitem'));
  }

  public function proses_jenisitem_insert()
  {
    $this->form_validation->set_rules('kode_item','Kode Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('jenis_item','Nama Item','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $kode_item = $this->input->post('kode_item' ,TRUE);
      $jenis_item = $this->input->post('jenis_item' ,TRUE);

      $data = array(
            'kode_item' => $kode_item,
            'jenis_item' => $jenis_item
      );
      $this->M_admin->insert('tb_jenis_item',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_jenisitem'));
    }else {
      $this->load->view('admin/form_jenisitem/form_insert');
    }
  }

  public function proses_jenisitem_update()
  {
    $this->form_validation->set_rules('kode_item','Kode Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('jenis_item','Jenis Item','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_satuan   = $this->input->post('id_satuan' ,TRUE);
      $kode_item = $this->input->post('kode_item' ,TRUE);
      $jenis_item = $this->input->post('jenis_item' ,TRUE);

      $where = array(
            'id_item' => $id_item
      );

      $data = array(
            'kode_item' => $kode_item,
            'jenis_item' => $jenis_item
      );
      $this->M_admin->update('tb_jenis_item',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_jenisitem'));
    }else {
      $this->load->view('admin/form_jenisitem/form_update');
    }
  }

  ####################################
            // END JENIS ITEM
  ####################################



  ####################################
     // DATA MASUK KE DATA KELUAR
  ####################################

  public function barang_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_barang_masuk',$where);
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update',$data);
  }

  public function proses_data_keluar()
  {
    $this->form_validation->set_rules('tanggal_keluar','Tanggal Keluar','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi   = $this->input->post('id_transaksi',TRUE);
      $tanggal_masuk  = $this->input->post('tanggal',TRUE);
      $tanggal_keluar = $this->input->post('tanggal_keluar',TRUE);
      $lokasi         = $this->input->post('lokasi',TRUE);
      $dept_tujuan    = $this->input->post('dept_tujuan',TRUE);
      $jenis_item     = $this->input->post('jenis_item',TRUE);
      $brand          = $this->input->post('brand',TRUE);
      $po_number      = $this->input->post('po_number',TRUE);
      $art            = $this->input->post('art',TRUE);
      $color          = $this->input->post('color',TRUE);
      $kode_barang    = $this->input->post('kode_barang',TRUE);
      $nama_barang    = $this->input->post('nama_barang',TRUE);
      $satuan         = $this->input->post('satuan',TRUE);
      $jumlah         = $this->input->post('jumlah',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
              'id_transaksi' => $id_transaksi,
              'tanggal_masuk' => $tanggal_masuk,
              'tanggal_keluar' => $tanggal_keluar,
              'lokasi' => $lokasi,
              'dept_tujuan' => $dept_tujuan,
              'jenis_item' => $jenis_item,
              'brand' => $brand,
              'po_number' => $po_number,
              'art' => $art,
              'color' => $color,
              'kode_barang' => $kode_barang,
              'nama_barang' => $nama_barang,
              'satuan' => $satuan,
              'jumlah' => $jumlah
      );
        $this->M_admin->insert('tb_barang_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_barangmasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update/'.$id_transaksi);
    }

  }
  ####################################
    // END DATA MASUK KE DATA KELUAR
  ####################################

















  ####################################
              // BRAND
  ####################################

  public function form_brand()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_brand/form_insert',$data);
  }

  public function tabel_brand()
  {
    $data['list_data'] = $this->M_admin->select('tb_brand');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_brand',$data);
  }

  public function update_brand()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_brand' => $uri);
    $data['data_brand'] = $this->M_admin->get_data('tb_brand',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_brand/form_update',$data);
  }

  public function delete_brand()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_item' => $uri);
    $this->M_admin->delete('tb_brand',$where);
    redirect(base_url('admin/tabel_brand'));
  }

  public function proses_brand_insert()
  {
    $this->form_validation->set_rules('kode_brand','Kode Brand','trim|required|max_length[100]');
    $this->form_validation->set_rules('nama_brand','Nama Brand','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $kode_brand = $this->input->post('kode_brand' ,TRUE);
      $nama_brand = $this->input->post('nama_brand' ,TRUE);

      $data = array(
            'kode_brand' => $kode_brand,
            'nama_brand' => $nama_brand
      );
      $this->M_admin->insert('tb_brand',$data);

      $this->session->set_flashdata('msg_berhasil','Data Brand Berhasil Ditambahkan');
      redirect(base_url('admin/form_brand'));
    }else {
      $this->load->view('admin/form_brand/form_insert');
    }
  }

  public function proses_brand_update()
  {
    $this->form_validation->set_rules('kode_item','Kode Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('jenis_item','Jenis Item','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_satuan   = $this->input->post('id_satuan' ,TRUE);
      $kode_item = $this->input->post('kode_item' ,TRUE);
      $jenis_item = $this->input->post('jenis_item' ,TRUE);

      $where = array(
            'id_item' => $id_item
      );

      $data = array(
            'kode_item' => $kode_item,
            'jenis_item' => $jenis_item
      );
      $this->M_admin->update('tb_brand',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_jenisitem'));
    }else {
      $this->load->view('admin/form_jenisitem/form_update');
    }
  }

  ####################################
            // END BRAND
  ####################################
















  ####################################
        // DATA BARANG KELUAR
  ####################################

  public function tabel_barangkeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_barang_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_barangkeluar',$data);
  }

















  ####################################
              // PO NUMBER
  ####################################

  public function form_po_number()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_po_number/form_insert',$data);
  }

  public function tabel_po_number()
  {
    $data['list_data'] = $this->M_admin->select('tb_po_number');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_po_number',$data);
  }

  public function update_po_number()
  {
    $uri = $this->uri->segment(3);
    $where = array('po_number' => $uri);
    $data['data_brand'] = $this->M_admin->get_data('tb_po_number',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_po_number/form_update',$data);
  }

  public function delete_po_number()
  {
    $uri = $this->uri->segment(3);
    $where = array('po_number' => $uri);
    $this->M_admin->delete('tb_po_number',$where);
    redirect(base_url('admin/tabel_po_number'));
  }

  public function proses_po_number_insert()
  {
    $this->form_validation->set_rules('po_number','PO Number','trim|required|max_length[100]');
    $this->form_validation->set_rules('brand','Nama Brand','trim|required|max_length[100]');
    $this->form_validation->set_rules('art','Art','trim|required|max_length[100]');
    $this->form_validation->set_rules('color','Color','trim|required|max_length[100]');
    $this->form_validation->set_rules('qty_order','QTY','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $po_number = $this->input->post('po_number' ,TRUE);
      $brand = $this->input->post('brand' ,TRUE);
      $art = $this->input->post('art' ,TRUE);
      $color = $this->input->post('color' ,TRUE);
      $qty_order = $this->input->post('qty_order' ,TRUE);

      $data = array(
            'po_number' => $po_number,
            'brand' => $brand,
            'art' => $art,
            'color' => $color,
            'qty_order' => $qty_order,
      );
      $this->M_admin->insert('tb_po_number',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_po_number'));
    }else {
      $this->load->view('admin/form_po_number/form_insert');
    }
  }

  public function proses_po_number_update()
  {
    $this->form_validation->set_rules('kode_item','Kode Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('jenis_item','Jenis Item','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_satuan   = $this->input->post('id_satuan' ,TRUE);
      $kode_item = $this->input->post('kode_item' ,TRUE);
      $jenis_item = $this->input->post('jenis_item' ,TRUE);

      $where = array(
            'id_item' => $id_item
      );

      $data = array(
            'kode_item' => $kode_item,
            'jenis_item' => $jenis_item
      );
      $this->M_admin->update('tb_jenis_item',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_jenisitem'));
    }else {
      $this->load->view('admin/form_jenisitem/form_update');
    }
  }

  ####################################
            // END JENIS ITEM
  ####################################

















  ####################################
    // DATA AUTOFILL UNTUK PO NUMBER
  ####################################

  public function edit($po_number) {
        $data['list_po_number'] = $this->M_admin->get_po_data('tb_po_number');
       
        $this->load->view('admin/form_barangmasuk/form_insert', $data);
    }

  public function get_po_details($po_number) {
        $po_number = $this->input->post('po_number');
        $po_data = $this->M_admin->get_po_data($po_number);
        echo json_encode($po_data);
    }

    


}
?>
