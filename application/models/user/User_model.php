<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model 
{
  public function login_check($mailid,$pass)
  {
    $this->db->select('*');
    $this->db->from('lucrative_users');
    $this->db->where('email',$mailid);
    $this->db->where('password',$pass);
    return $res= $this->db->get()->result_array();
  }
  
  public function check_if_email_exists($email){
    $query = $this->db->query("SELECT email FROM lucrative_users where email='$email'");
    return     $query->num_rows();
}

}
?>