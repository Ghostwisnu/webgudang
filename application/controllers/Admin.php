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
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['list_item'] = $this->M_admin->select('tb_itemlist');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_insert',$data);
  }

  public function tabel_barangmasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_listcons'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_barangmasuk',$data);
  }

  public function update_barang($id_cons)
  {
    $where = array('id_cons' => $id_cons);
    $data['data_barang_update'] = $this->M_admin->get_data('tb_listcons',$where);
    $data['list_item'] = $this->M_admin->select('tb_itemlist');
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_update',$data);
  }

  public function delete_barang($id_cons)
  {
    $where = array('id_cons' => $id_cons);
    $this->M_admin->delete('tb_listcons',$where);
    redirect(base_url('admin/tabel_barangmasuk'));
  }



  public function proses_databarang_masuk_insert()
  {
    $this->form_validation->set_rules('artcolor_name','Art Color','required');
    $this->form_validation->set_rules('item_name','Nama Item','required');
    $this->form_validation->set_rules('unit_name','Nama Unit','required');
    $this->form_validation->set_rules('cons_rate','Cons','required');

    if($this->form_validation->run() == TRUE)
    {
      $artcolor_name = $this->input->post('artcolor_name',TRUE);
      $item_name      = $this->input->post('item_name',TRUE);
      $unit_name       = $this->input->post('unit_name',TRUE);
      $cons_rate       = $this->input->post('cons_rate',TRUE);

      $data = array(
            'artcolor_name' => $artcolor_name,
            'item_name'      => $item_name,
            'unit_name'       => $unit_name,
            'cons_rate'       => $cons_rate
      );
      $this->M_admin->insert('tb_listcons',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_barangmasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_barangmasuk/form_insert',$data);
    }
  }

  public function proses_databarang_masuk_update()
  {
    $this->form_validation->set_rules('artcolor_name','Art Color','required');
    $this->form_validation->set_rules('item_name','Nama Item','required');
    $this->form_validation->set_rules('unit_name','Nama Unit','required');
    $this->form_validation->set_rules('cons_rate','Cons','required');


    if($this->form_validation->run() == TRUE)
    {
      $id_listcons = $this->input->post('id_listcons',TRUE);
      $artcolor_name = $this->input->post('artcolor_name',TRUE);
      $item_name      = $this->input->post('item_name',TRUE);
      $unit_name       = $this->input->post('unit_name',TRUE);
      $cons_rate       = $this->input->post('cons_rate',TRUE);

      $where = array('id_listcons' => $id_listcons);
      $data = array(
            'id_listcons' => $id_listcons,
            'artcolor_name' => $artcolor_name,
            'item_name'      => $item_name,
            'unit_name'       => $unit_name,
            'cons_rate'       => $cons_rate
      );
      $this->M_admin->update('tb_listcons',$data,$where);
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
        // DATA ART & COLOR
  ####################################

  public function form_art_color()
  {
    $data['list_art'] = $this->M_admin->select('tb_art');
    $data['list_color'] = $this->M_admin->select('tb_color');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_art_color/form_insert',$data);
  }

  public function tabel_art_color()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_art_color'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_art_color',$data);
  }

  public function update_art_color($id_artcolor)
  {
    $where = array('id_artcolor' => $id_artcolor);
    $data['data_artcolor_update'] = $this->M_admin->get_data('tb_art_color',$where);
    $data['list_art'] = $this->M_admin->select('tb_art');
    $data['list_color'] = $this->M_admin->select('tb_color');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_art_color/form_update',$data);
  }

  public function delete_art_color($id_artcolor)
  {
    $where = array('id_artcolor' => $id_artcolor);
    $this->M_admin->delete('tb_art_color',$where);
    redirect(base_url('admin/tabel_art_color'));
  }

  public function proses_artcolor_masuk_insert()
  {
    $this->form_validation->set_rules('art_name','Nama Art','required');
    $this->form_validation->set_rules('color_name','Nama Color','required');

    if($this->form_validation->run() == TRUE)
    {
      $art      = $this->input->post('art_name',TRUE);
      $color      = $this->input->post('color_name',TRUE);       
      $art_color = $art." ".$color;       

      $data = array(
            'art_name' => $art,
            'color_name'       => $color,
            'artcolor_name'       => $art_color
      );
      $this->M_admin->insert('tb_art_color',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_art_color'));
    }else {
      $data['list_data'] = $this->M_admin->select('tb_art_color');
      $this->load->view('admin/form_art_color/form_insert',$data);
    }
  }

  public function proses_artcolor_masuk_update()
  {
    $this->form_validation->set_rules('art_name','Nama Art','required');
    $this->form_validation->set_rules('color_name','Nama Color','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_artcolor      = $this->input->post('id_artcolor',TRUE);
      $art      = $this->input->post('art_name',TRUE);
      $color      = $this->input->post('color_name',TRUE);       
      $art_color = $art." ".$color;       

      $where = array('id_artcolor' => $id_artcolor);
      $data = array(
            'art_name' => $art,
            'color_name'       => $color,
            'artcolor_name'       => $art_color
      );
      $this->M_admin->update('tb_art_color',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_art_color'));
    }else{
      $this->load->view('admin/form_art_color/form_update');
    }
  }
  ####################################
      // END DATA ART & COLOR
  ####################################

  ####################################
              // SIZE
  ####################################

  public function form_size()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_size/form_insert',$data);
  }

  public function tabel_size()
  {
    $data['list_data'] = $this->M_admin->select('tb_size');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_size',$data);
  }

  public function update_size()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_size' => $uri);
    $data['data_size'] = $this->M_admin->get_data('tb_size',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_size/form_update',$data);
  }

  public function delete_size()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_size' => $uri);
    $this->M_admin->delete('tb_size',$where);
    redirect(base_url('admin/tabel_size'));
  }

  public function proses_size_insert()
  {
    $this->form_validation->set_rules('size_name','Nama Size','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $size_name = $this->input->post('size_name' ,TRUE);

      $data = array(
            'size_name' => $size_name,
      );
      $this->M_admin->insert('tb_size',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_size'));
    }else {
      $this->load->view('admin/form_size/form_insert');
    }
  }

  public function proses_size_update()
  {
    $this->form_validation->set_rules('size_name','Nama Unit','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_size   = $this->input->post('id_size' ,TRUE);
      $nama_size = $this->input->post('size_name' ,TRUE);

      $where = array(
            'id_size' => $id_size
      );

      $data = array(
            'size_name' => $nama_size
      );
      $this->M_admin->update('tb_size',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_size'));
    }else {
      $this->load->view('admin/form_size/form_update');
    }
  }

  ####################################
            // END UNIT
  ####################################

  ####################################
              // UNIT
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
      redirect(base_url('admin/tabel_satuan'));
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
            // END UNIT
  ####################################

####################################
              // ART&COLOR
  ####################################

  public function form_art()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_art/form_insert',$data);
  }
  public function form_color()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_color/form_insert',$data);
  }

  public function tabel_art()
  {
    $data['list_data'] = $this->M_admin->select('tb_art');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_art',$data);
  }

  public function tabel_color()
  {
    $data['list_data'] = $this->M_admin->select('tb_color');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_color',$data);
  }


  public function update_art()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_art' => $uri);
    $data['data_art'] = $this->M_admin->get_data('tb_art',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_art/form_update',$data);
  }

  public function delete_art()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_art' => $uri);
    $this->M_admin->delete('tb_art',$where);
    redirect(base_url('admin/tabel_art'));
  }

  public function proses_art_insert()
  {
    $this->form_validation->set_rules('art_name','Nama Art','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $art_name = $this->input->post('art_name' ,TRUE);

      $data = array(
            'art_name' => $art_name,
      );
      $this->M_admin->insert('tb_art',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_art'));
    }else {
      $this->load->view('admin/form_art/form_insert');
    }
  }

  public function proses_art_update()
  {
    $this->form_validation->set_rules('art_name','Nama Art','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_art   = $this->input->post('id_art' ,TRUE);
      $art_unit = $this->input->post('art_name' ,TRUE);

      $where = array(
            'id_art' => $id_art
      );

      $data = array(
            'art_name' => $art_unit
      );
      $this->M_admin->update('tb_art',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_art'));
    }else {
      $this->load->view('admin/form_art/form_update');
    }
  }
  public function update_color()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_color' => $uri);
    $data['data_color'] = $this->M_admin->get_data('tb_color',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_color/form_update',$data);
  }

  public function delete_color()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_color' => $uri);
    $this->M_admin->delete('tb_color',$where);
    redirect(base_url('admin/tabel_art_color'));
  }

  public function proses_color_insert()
  {
    $this->form_validation->set_rules('color_name','Nama Color','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $color_name = $this->input->post('color_name' ,TRUE);

      $data = array(
            'color_name' => $color_name,
      );
      $this->M_admin->insert('tb_color',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_color'));
    }else {
      $this->load->view('admin/form_color/form_insert');
    }
  }

  public function proses_color_update()
  {
    $this->form_validation->set_rules('color_name','Nama Color','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_color   = $this->input->post('id_color' ,TRUE);
      $color_name = $this->input->post('color_name' ,TRUE);

      $where = array(
            'id_color' => $id_color
      );

      $data = array(
            'color_name' => $color_name
      );
      $this->M_admin->update('tb_color',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_art_color'));
    }else {
      $this->load->view('admin/form_color/form_update');
    }
  }

  ####################################
            // END ART&COLOR
  ####################################

  ####################################
              // JENIS ITEM
  ####################################

  public function form_jenisitem()
  {
    $data['list_unit'] = $this->M_admin->select('tb_unitlist');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_jenisitem/form_insert',$data);
  }

  public function tabel_jenisitem()
  {
    $data['list_data'] = $this->M_admin->select('tb_itemlist');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_jenisitem',$data);
  }

  public function update_jenisitem()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_itemlist' => $uri);
    $data['data_jenisitem'] = $this->M_admin->get_data('tb_itemlist',$where);
    $data['list_unit'] = $this->M_admin->select('tb_unitlist');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_jenisitem/form_update',$data);
  }

  public function delete_jenisitem()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_itemlist' => $uri);
    $this->M_admin->delete('tb_itemlist',$where);
    redirect(base_url('admin/tabel_jenisitem'));
  }

  public function proses_jenisitem_insert()
  {
    $this->form_validation->set_rules('item_name','Nama Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('unit_name','Nama Unit','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $item_name = $this->input->post('item_name' ,TRUE);
      $unit_name = $this->input->post('unit_name' ,TRUE);

      $data = array(
            'item_name' => $item_name,
            'unit_name' => $unit_name
      );
      $this->M_admin->insert('tb_itemlist',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_jenisitem'));
    }else {
      $this->load->view('admin/form_jenisitem/form_insert');
    }
  }

  public function proses_jenisitem_update()
  {
    $this->form_validation->set_rules('item_name','Nama Jenis Item','trim|required|max_length[100]');
    $this->form_validation->set_rules('unit_name','Nama Unit','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_itemlist   = $this->input->post('id_itemlist' ,TRUE);
      $item_name = $this->input->post('item_name' ,TRUE);
      $unit_name = $this->input->post('unit_name' ,TRUE);

      $where = array(
            'id_item' => $id_item
      );

      $data = array(
            'item_name' => $item_name,
            'unit_name' => $unit_name
      );
      $this->M_admin->update('tb_itemlist',$data,$where);

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
    $where = array('id_brand' => $uri);
    $this->M_admin->delete('tb_brand',$where);
    redirect(base_url('admin/tabel_brand'));
  }

  public function proses_brand_insert()
  {
    $this->form_validation->set_rules('brand_name','Nama Brand','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $brand_name = $this->input->post('brand_name' ,TRUE);

      $data = array(
            'brand_name' => $brand_name
      );
      $this->M_admin->insert('tb_brand',$data);

      $this->session->set_flashdata('msg_berhasil','Data Brand Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_brand'));
    }else {
      $this->load->view('admin/form_brand/form_insert');
    }
  }

  public function proses_brand_update()
  {
    $this->form_validation->set_rules('brand_name','Nama Brand','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_brand   = $this->input->post('id_brand' ,TRUE);
      $brand_name = $this->input->post('brand_name' ,TRUE);

      $where = array(
            'id_brand' => $id_brand
      );

      $data = array(
            'brand_name' => $brand_name
      );
      $this->M_admin->update('tb_brand',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_brand'));
    }else {
      $this->load->view('admin/form_brand/form_update');
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
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['list_brand'] = $this->M_admin->select('tb_brand');
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
    $where = array('id_po' => $uri);
    $data['data_po_number'] = $this->M_admin->get_data('tb_po_number',$where);
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['list_brand'] = $this->M_admin->select('tb_brand');
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
  public function add_po_item()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_po' => $uri);
    $data['data_po_number'] = $this->M_admin->get_data('tb_po_number',$where);
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['list_brand'] = $this->M_admin->select('tb_brand');
    $data['list_item'] = $this->M_admin->select('tb_listcons');
    $data['list_size'] = $this->M_admin->select('tb_size');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_po_number/form_update',$data);
  }

  public function add_po_size()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_po' => $uri);
    $data['data_po_number'] = $this->M_admin->get_data('tb_po_number',$where);
    $data['list_artcolor'] = $this->M_admin->select('tb_art_color');
    $data['list_brand'] = $this->M_admin->select('tb_brand');
    $data['list_item'] = $this->M_admin->select('tb_listcons');
    $data['list_size'] = $this->M_admin->select('tb_size');
    $data['list_size_run'] = $this->M_admin->select('tb_size_run');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_po_number/form_add_size',$data);
  }

  public function proses_po_size_insert()
  {
    $this->form_validation->set_rules('po_number','PO Number','trim|required|max_length[100]');
    $this->form_validation->set_rules('brand_name','Nama Brand','trim|required|max_length[100]');
    $this->form_validation->set_rules('artcolor_name','Art Color','trim|required|max_length[100]');
    $this->form_validation->set_rules('xfd','Xfd','trim|required|max_length[100]');
    $this->form_validation->set_rules('size_name','Size Run','trim|required|max_length[100]');
    $this->form_validation->set_rules('size_run','QTY Size','trim|required|max_length[100]');
    $this->form_validation->set_rules('qty_total','QTY Total','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_po   = $this->input->post('id_po' ,TRUE);
      $po_number = $this->input->post('po_number' ,TRUE);
      $brand_name = $this->input->post('brand_name' ,TRUE);
      $artcolor_name = $this->input->post('artcolor_name' ,TRUE);
      $xfd = $this->input->post('xfd' ,TRUE);
      $qty_total = $this->input->post('qty_total' ,TRUE);
      $size_name = $this->input->post('size_name' ,TRUE);
      $size_run = $this->input->post('size_run' ,TRUE);

      $data = array(
            'id_po' => $id_po,
            'po_number' => $po_number,
            'brand_name' => $brand_name,
            'artcolor_name' => $artcolor_name,
            'xfd' => $xfd,
            'qty_total' => $qty_total,
      );

      $data1 = array(
            'po_number' => $po_number,
            'size_name' => $size_name,
            'size_run' => $size_run,
            
      );

      $this->M_admin->update('tb_po_number',$data);
      $this->M_admin->insert('tb_size_run',$data1);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_po_number/form_add_size'));
    }else {
      $this->load->view('admin/form_po_number/form_insert');
    }
  }

  public function proses_po_number_insert()
  {
    $this->form_validation->set_rules('po_number','PO Number','trim|required|max_length[100]');
    $this->form_validation->set_rules('brand_name','Nama Brand','trim|required|max_length[100]');
    $this->form_validation->set_rules('artcolor_name','Art Color','trim|required|max_length[100]');
    $this->form_validation->set_rules('xfd','Xfd','trim|required|max_length[100]');
    $this->form_validation->set_rules('qty_total','QTY Total','trim|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $po_number = $this->input->post('po_number' ,TRUE);
      $brand_name = $this->input->post('brand_name' ,TRUE);
      $artcolor_name = $this->input->post('artcolor_name' ,TRUE);
      $xfd = $this->input->post('xfd' ,TRUE);
      $qty_total = $this->input->post('qty_total' ,TRUE);

      $data = array(
            'po_number' => $po_number,
            'brand_name' => $brand_name,
            'artcolor_name' => $artcolor_name,
            'xfd' => $xfd,
            'qty_total' => $qty_total,
      );
      $this->M_admin->insert('tb_po_number',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/tabel_po_number'));
    }else {
      $this->load->view('admin/form_po_number/form_insert');
    }
  }

  public function proses_po_number_update()
  {
    $this->form_validation->set_rules('po_number','PO Number','trim|required|max_length[100]');
    $this->form_validation->set_rules('brand_name','Nama Brand','trim|required|max_length[100]');
    $this->form_validation->set_rules('artcolor_name','Art & Color','trim|required|max_length[100]');
    $this->form_validation->set_rules('xfd','Xfd','trim|required|max_length[100]');
    $this->form_validation->set_rules('qty_total','QTY Total','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_po   = $this->input->post('id_po' ,TRUE);
      $po_number = $this->input->post('po_number' ,TRUE);
      $brand_name = $this->input->post('brand_name' ,TRUE);
      $artcolor_name = $this->input->post('artcolor_name' ,TRUE);
      $xfd = $this->input->post('xfd' ,TRUE);
      $qty_total = $this->input->post('qty_total' ,TRUE);

      $where = array(
            'id_po' => $id_po
      );

      $data = array(
            'po_number' => $po_number,
            'brand_name' => $brand_name,
            'artcolor_name' => $artcolor_name,
            'xfd' => $xfd,
            'qty_total' => $qty_total,
      );
      $this->M_admin->update('tb_po_number',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_po_number'));
    }else {
      $this->load->view('admin/form_po_number/form_update');
    }
  }

  ####################################
            // END PO NUMBER
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
