<?php
class Capacities_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_data($host = FALSE)
	{
		if ($host === FALSE)
		{
			$query = $this->db->get('USERBK');
			return $query->result_array();
		}

		$query = $this->db->get_where('USERBK', array('HOST' => $host));
		return $query->row_array();
	}

	public function set_data()
	{
		$this->load->helper('url');

		$APP  = empty($this->input->post('app')) ? NULL : $this->input->post('app');
		$TPU  = empty($this->input->post('tpu')) ? NULL : $this->input->post('tpu');
		$RAM  = empty($this->input->post('ram')) ? NULL : $this->input->post('ram');
		$TIPE  = empty($this->input->post('tipe')) ? NULL : $this->input->post('tipe');
		$SERVER  = empty($this->input->post('server')) ? NULL : $this->input->post('server');
		$CLASE  = empty($this->input->post('clase')) ? NULL : $this->input->post('clase');
		$OS  = empty($this->input->post('os')) ? NULL : $this->input->post('os');
		$SWAP  = empty($this->input->post('swap')) ? NULL : $this->input->post('swap');
		$TPUMIN  = empty($this->input->post('tpumin')) ? NULL : $this->input->post('tpumin');


		$data = array(
			 'HOST' => $this->input->post('host'),
			 'USERBK' => $this->input->post('userbk'),
			 'APP' => $APP,
			 'TPU' => $TPU,
			 'RAM' => $RAM,
			 'TIPE' => $TIPE,
			 'SERVER' => $SERVER,
			 'CLASE' => $CLASE,
			 'OS' => $OS,
			 'SWAP' => $SWAP,
			 'TPUMIN' => $TPUMIN,
			 'BASE' =>  $this->input->post('base'),
		);

		return $this->db->insert('USERBK', $data);
	}
}