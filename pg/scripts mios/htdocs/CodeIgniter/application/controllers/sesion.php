<?php
class Sesion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('sesion_model');
	}

	public function index()
	{
		$data['selected'] = 'sesiones';
		$data['title'] = 'Sesiones';
		$this->load->view('templates/head', $data);
		$this->load->view('sesion/session_header', $data);
		$this->load->view('templates/header', $data);
		$this->load->view('templates/footer');
	}

	public function view()
	{
		$data['hosts'] = $this->sesion_model->get_host();
		$data['selected'] = 'sesiones';

		$this->load->view('templates/head', $data);
		$this->load->view('sesion/sesion_head', $data);
		$this->load->view('templates/header', $data);
		
		foreach($this->sesion_model->get_host_ses() as $ses_item){
			$mi_host_upr=$ses_item['HOST'];
			$mi_sesion=$ses_item['SESION'];
			$data_upr['host']=$mi_host_upr;
			$data_upr['sesion']=$mi_sesion;
			$data_upr['uprs']= $this->sesion_model->get_upr($mi_host_upr,$mi_sesion);
			$this->load->view('sesion/index_upr', $data_upr);
			//print_r($data_upr);
		}
		foreach($data['hosts'] as $host_item) {
			$mi_host=$host_item['ID'];
			$data_sesiones['id']=$mi_host;
			$data_sesiones['host']=$host_item['HOST']." - ".$host_item['APP'];
			$data_sesiones['sessions']=$this->sesion_model->get_sesion($mi_host);
			$this->load->view('sesion/index', $data_sesiones);
		}
		$data['title'] = 'Listado de hosts';


		$this->load->view('templates/footer');
	}

	/*public function view($host)
	{
		$data['host_items'] = $this->sesion_model->get_data($host);

		if (empty($data['host_items']))
		{
			show_404();
		}

		$data['title'] = $data['host_items']['HOST'];

		$this->load->view('templates/header', $data);
		$this->load->view('capacity/view', $data);
		$this->load->view('templates/footer');
	}

	*/
}