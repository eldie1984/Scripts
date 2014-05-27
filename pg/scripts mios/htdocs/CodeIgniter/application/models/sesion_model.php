<?php
class Sesion_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_host($get_host = FALSE)
	{
		if ($get_host === FALSE)
		{
			$query = $this->db->query('select host,id,app from userbk');
			return $query->result_array();
		}

		$query = $this->db->query('select distinct(HOST) from EJECUCIONES where HOST like \''.$get_host.'\'');
		return $query->row_array();
	}

	public function get_sesion($host = FALSE)
	{
		$query = $this->db->query("select sesion,s.descripcion,estado,TO_CHAR(min(inicio),'DD-MM-YY HH24:MI:SS') as inicio, TO_CHAR(max(fin),'DD-MM-YY HH24:MI:SS') as fin 
			from ejecuciones e  
			inner join sesiones s on s.codigo=e.sesion
			where host='".$host."'
			and sesion not in (select sesion from ejecuciones where estado <> 3) group by sesion, s.descripcion, estado
			union 
			select sesion,s.descripcion,estado,TO_CHAR(min(inicio),'DD-MM-YY HH24:MI:SS') as inicio, '00-00-00 00:00:00' as fin 
			from ejecuciones e
			inner join sesiones s on s.codigo=e.sesion
			where host='".$host."'
			and estado <> 3 group by sesion, s.descripcion, estado ");
		
		return $query->result_array();
	}

	public function get_upr($host,$sesion)
	{
		$query = $this->db->query("select uproc,estado,TO_CHAR(inicio,'DD-MM-YY HH24:MI:SS') as inicio, TO_CHAR(fin,'DD-MM-YY HH24:MI:SS') as fin 
			from ejecuciones
			where host='".$host."'
			and sesion='".$sesion."'");
		return $query->result_array();
	}

	public function get_host_ses()
	{
		$query = $this->db->query("select host,sesion from ejecuciones group by host,sesion");
		return $query->result_array();
	}
	
}