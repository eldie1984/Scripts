<?php
class Capacity extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('capacities_model');
	}

	public function index()
	{
		$data['hosts'] = $this->capacities_model->get_data();
		$data['title'] = 'Listado de hosts';
		$data['selected'] = 'capacity';

		$this->load->view('templates/head', $data);
		$this->load->view('capacity/head_capacity', $data);
		$this->load->view('templates/header', $data);
		$this->load->view('capacity/index', $data);
		$this->load->view('templates/footer');
	}

	public function view($host)
	{
		$data['host_items'] = $this->capacities_model->get_data($host);

		if (empty($data['host_items']))
		{
			show_404();
		}

		$data['title'] = $data['host_items']['HOST'];
		$data['selected'] = 'capacity';

		$this->load->view('templates/head', $data);
		$this->load->view('templates/header', $data);
		$this->load->view('capacity/view', $data);
		$this->load->view('templates/footer');
	}

	public function create()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Create a Capacity item';
		$data['selected'] = 'capacity';

		$this->form_validation->set_rules('userbk', 'userbk', 'required');
		$this->form_validation->set_rules('host', 'host', 'required');
		$this->form_validation->set_rules('base', 'base', 'required');
		$this->form_validation->set_rules('server', 'server', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/head', $data);
			$this->load->view('templates/header', $data);
			$this->load->view('capacity/create');
			$this->load->view('templates/footer');

		}
		else
		{
			$this->capacities_model->set_data();
			$this->load->view('capacity/success');
		}
	}

	public function modify()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Create a Capacity item';
		$data['selected'] = 'capacity';

		$this->form_validation->set_rules('userbk', 'userbk', 'required');
		$this->form_validation->set_rules('host', 'host', 'required');
		$this->form_validation->set_rules('base', 'base', 'required');
		$this->form_validation->set_rules('server', 'server', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/head', $data);
			$this->load->view('templates/header', $data);
			$this->load->view('capacity/modify');
			$this->load->view('templates/footer');

		}
		else
		{
			$this->capacities_model->set_data();
			$this->load->view('capacity/success');
		}
	}
}