<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_menu extends CI_Model {

    function __construct() {
        parent::__construct();
    }


	function get() {
    $this->datatables->select('id,tipe,parent,id_menu,name,url,icon,seq');
    $this->datatables->from('sys_menu');
    $this->datatables->add_column('view', '<a href="#" data-level="$1"  class="btn bg-green btn-xs waves-effect edit "><i class = "fa fa-pencil"></i></a> <a href="#" data-target="#konfirmasi_hapus" data-toggle="modal" data-href="level/delete/$1" class="btn bg-red btn-xs waves-effect"><i class = "fa fa-trash-o"></i></a>', 'levels');
    return $this->datatables->generate();
  }


	function save(){

		$type 			= $this->input->post('type');
		$parent 		= $this->input->post('parent');
		$menu 			= $this->input->post('menu');
		$label_menu = $this->input->post('label_menu');
		$url 			  = $this->input->post('url');
		$icon 			= $this->input->post('icon');
		$order 			= $this->input->post('order');
		$data = array(
			'tipe'		 => $type,
			'parent'	 => $parent,
			'id_menu'	 => $menu,
			'name'	 	 => $label_menu,
			'url'	 	 => $url,
			'icon'	 	 => $icon,
			'seq'	 	 => $order
		);

		$save = $this->db->insert('sys_menu',$data);
		if($save){
			$this->session->set_flashdata('msg',

	        '<div class="alert alert-success alert-dismissible fade in" role="alert">

	              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

	                 <i class="fa fa-check"></i> Data Saved Succesfully !

	          </div>');

	    	redirect('menu');
		}
	}

  function get_menu($menu){
    $this->db->order_by('seq','asc');
    return $this->db->get_where('sys_menu',array('parent'=>$menu));
  }
  function getTT(){
	$query = "SELECT
			x.namaruangan,
			SUM (x.isi) AS isi,
			SUM (x.kosong) AS kosong,
		count(x.tt_id) as total
		FROM
			(
				SELECT
					CAST (tt.nomorbed AS INT) AS nomor,
					tt. ID AS tt_id,
					tt.nomorbed AS namabed,
					kmr. ID AS kmr_id,
					kmr.namakamar,
					ru. ID AS id_ruangan,
					ru.namaruangan,
					kls.namakelas,
					sb.statusbed,
					CASE 	WHEN sb. ID = 1 THEN	1	ELSE 0 END AS isi,
				CASE WHEN sb. ID = 2 THEN	1	ELSE 0	END AS kosong
			FROM
				tempattidurmt AS tt
			INNER JOIN statusbedmt AS sb ON sb. ID = tt.statusbedidfk
			INNER JOIN kamarmt AS kmr ON kmr. ID = tt.kamaridfk
			INNER JOIN kelasmt AS kls ON kls. ID = kmr.kelasidfk
			INNER JOIN ruanganmt AS ru ON ru. ID = kmr.ruanganidfk
			WHERE
				tt.koders =1
			AND tt.aktif = TRUE
			AND kmr.aktif = TRUE
			) AS x
		GROUP BY
			x.namaruangan
		";
   return $this->db->query($query);
}
function getTT2(){
	$query = "SELECT
	ru.id AS idruangan,
	ru.namaruangan,
	km.id AS idkamar,
	km.namakamar,
	tt.id AS idtempattidur,
	tt.reportdisplay,
	tt.nomorbed,
	sb.id AS idstatusbed,
	sb.statusbed,
	kl.id AS idkelas,
	kl.namakelas
FROM
	tempattidurmt AS tt
LEFT JOIN kamarmt AS km ON km.id = tt.kamaridfk
LEFT JOIN ruanganmt AS ru ON ru.id = km.ruanganidfk
LEFT JOIN statusbedmt AS sb ON sb.id = tt.statusbedidfk
LEFT JOIN kelasmt AS kl ON kl.id = km.kelasidfk
WHERE
	ru.instalasiidfk IN (select cast(nilaifield as INTEGER) from settingdatafixedmt where namafield='kdDepartemenRanapFix')
AND ru.aktif = true
AND km.aktif = true
AND tt.aktif = true
AND tt.koders = 1
		";
   return $this->db->query($query);
}
}