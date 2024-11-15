<?php
class Common_model extends CI_Model {



    function __construct() {
        parent::__construct();
        $this->load->helper('string');
    }
    
    public function insert_iec_user_entry($data){
        $this->db->insert('lucrative_users',$data);
		return	$result = $this->db->insert_id();
    }
    
    	function get_entity_data($table,$where)
	{ 
		if($where == NULL )
		{ 
			return $this->db->select('*')->get($table)->result_array();
		} 
		else 
		{  
	
			return $this->db->select('*')->where($where)->get($table)->result_array();
		}
	}


	function insert_data($table,$Insertdata,$where = NULL)
	{


		
		if($where == NULL)
		{
			$this->db->insert($table,$Insertdata); 
		}
		else 
		{ 
			$this->db->where($where)->insert($table,$Insertdata); 
		}
        echo $this->db->last_query();
	//	return $this->db->insert_id(); 
	}
}