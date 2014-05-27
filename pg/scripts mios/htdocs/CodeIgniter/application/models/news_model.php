<?php
class News_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_news($slug = FALSE)
	{
		if ($slug === FALSE)
		{
			$query = $this->db->get('NEWS');
			return $query->result_array();
		}

		$query = $this->db->get_where('NEWS', array('SLUG' => $slug));
		return $query->row_array();
	}

	public function set_news()
	{
		$this->load->helper('url');

		$slug = url_title($this->input->post('title'), 'dash', TRUE);

		$data = array(
			'TITLE' => $this->input->post('title'),
			'SLUG' => $slug,
			'TEXT' => $this->input->post('text')
		);

		return $this->db->insert('NEWS', $data);
	}
}