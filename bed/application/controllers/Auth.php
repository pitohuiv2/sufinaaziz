<?php

Class Auth extends CI_Controller{

    function __construct() {
        parent:: __construct();
        $this->load->model(array('Model_menu'));
         $this->load->model("user/model","mod");
    
         $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = 'Login';
        $data = $this->Model_menu->getTT()->result();
        $totalKamar = count($data);
    
        $totalBed = 0;
        $totalIsi = 0;
        $totalKosong = 0;
        foreach ($data as $item){
            $totalBed =    $totalBed + (float) $item->total;
            $totalIsi =    $totalIsi + (float) $item->isi;
            $totalKosong =    $totalKosong + (float) $item->kosong;
        }
        $tt = $this->Model_menu->getTT2()->result();
  

        $data10 =[];
        $sama =false;
        $bed = 0 ;
        $isi = 0;
        $kosong = 0;
        foreach ($tt as $item) {
            $sama = false;
            $i = 0;
            foreach ($data10 as $hideung) {
                if ($item->namaruangan == $data10[$i]['namaruangan']) {
                    $sama = 1;
                    $jml = (float)$hideung['bed'] + 1;
                    $data10[$i]['bed'] = $jml;
                    if ($item->idstatusbed == 1) {
                        $data10[$i]['isi'] = (float)$hideung['isi'] + 1;
                    }
                    if ($item->idstatusbed == 2) {
                        $data10[$i]['kosong'] = (float)$hideung['kosong'] + 1;
                    }
                }
                $i = $i + 1;
            }
            if ($sama == false) {
                if ($item->idstatusbed ==1 ) {
                    $isi = 1;
                    $kosong = 0;
                }
                if ($item->idstatusbed ==2 ) {
                    $isi = 0;
                    $kosong = 1;
                }

                $data10[] =array(
                    'idruangan' => $item->idruangan,
                    'namaruangan' => $item->namaruangan,
                    'idstatusbed' => $item->idstatusbed,
                    'bed' => 1,
                    'kosong' => $kosong,
                    'isi' => $isi,
                );
            }
            }

        $res['totalKamar'] =$totalKamar;
        $res['totalBed'] =$totalBed;
        $res['totalIsi'] =$totalIsi;
        $res['totalKosong'] =$totalKosong;

        $res['tt'] =$data10;
        $result['res'] =$res;
        $this->template->display('dashboard/view_bed',$result);
        // $this->load->view('dashboard/view_bed', $data);
        // $akses = $this->access->get_level();
        // if (!$this->access->is_login()) {
        //     $this->load->view('Auth/frm_login', $data);
        // } else {
        //     redirect('dashboard');
        // }
    }
    function login() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|strip_tags');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            $this->form_validation->set_rules('token', 'token', 'callback_check_login');
            if ($this->form_validation->run() == FALSE) {
                $status['status'] = 0;
                $status['error'] = validation_errors();
            } else {
              $status['status'] = 1;
            }
        } else {
            $status['status'] = 0;
            $status['error'] = validation_errors();
        }

        echo json_encode($status);
    }

    function logout() {
        $this->access->logout();
        redirect('Auth');
    }



    function check_login() {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $login = $this->access->login($username, $password);

        if ($login == 1) {
            return TRUE;
        } else if ($login == 2) {
            $this->form_validation->set_message('check_login', 'Wrong Password');
            return FALSE;
        } else {
            $this->form_validation->set_message('check_login', 'unknown User');
            return FALSE;
        }
    }





}