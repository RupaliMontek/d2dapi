<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
    {
        @parent::__construct();
        $this->load->model('user/User_model');
    }
    
    public function index(){
        $this->load->view('common/header');
        $this->load->view('login');
        $this->load->view('common/footer');
    }
    
    
    	public function check_login()
	{       
	   $post=$this->input->post();
	   
	    $this->form_validation->set_rules('email', 'Email','required|valid_email');
        $this->form_validation->set_rules('password', 'Password','required');

        $mailid= $this->input->post('email',true);
	    $pass=$this->input->post('password',true);

            if($this->form_validation->run()==true)
            {
		        $mailid= $this->input->post('email',true);
		        $pass=$this->input->post('password',true);
		        $data = $this->User_model->login_check($mailid,$pass);
		       
              	if(!empty($data)) 
		        {
		        	$name = $data[0]['fullname'];
                    $this->session->set_userdata('user_id',$data[0]['lucrative_users_id']);
		    	    $this->session->set_userdata('user_name',$name);
		    	    $this->session->set_userdata('iec_no',$data[0]['iec_no']);
		    	    $this->session->set_userdata('user_email',$data[0]['email']);
		    	    $this->session->set_userdata('user_role',$data[0]['role']);
		    	    $this->session->set_userdata('master_db','symmetry_drawback_new');
		    	    $this->session->set_userdata('database_prefix','lucrativeesystem_D2D_');

			    	redirect('admin');
		        }
		        else
		        {
		    	    redirect('login');
		        }
            }
            else
            {
            	redirect('login');
            }
	}
	
	
   public function check_email_exists_registration()
	{
		$email = $this->input->post('email',TRUE);
		$result=$this->User_model->check_if_email_exists($email);
         if($result>=1)
		{
		    echo "true";
		}
		else
		{
		    echo "false";
		}

}
    
}