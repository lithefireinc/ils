<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Home extends CI_Controller{

    function Home(){
        parent::__construct();
		$this->load->helper('url');
        $this->load->helper('form');
        $this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->model('lithefire_model','lithefire',TRUE);
		$this->load->library('layout', array('layout'=>$this->config->item('layout_file'))); 
       // $this->load->scaffolding('entries');
    }
    function index(){
        
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('home/login', 'refresh');
		}
		else
		{
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$data['header'] = 'Header Section';
            $data['footer'] = 'Footer Section';
			$data['title'] = "ILS: Dashboard";
            $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
            $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;

            
            $this->layout->view('home/dashboard_view', $data);
            
		}
    }
    
    //log the user in
	function login()
	{
		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{ //check to see if the user is logging in
			//check for "remember me"
			//$remember = (bool) $this->input->post('remember');
			$remember = FALSE;

			//die("darryl");
			if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember))
			{ //if the login is successful
				//redirect them back to the home page
				//$this->session->set_flashdata('message', $this->ion_auth->messages());
				$data['success'] = true;
           		$data['errorMsg'] = "Login Successful. Redirecting...";
           		$data['link'] = site_url("home");
           		die(json_encode($data));
			}
			else
			{ //if the login was un-successful
				//redirect them back to the login page
				$data['success'] = false;
           		$data['errorMsg'] = "You have entered an invalid username/password";
          		// $data['link'] = "http://www.pixelcatalyst.net/hrisv2/dashboard/";
           		die(json_encode($data));
			}
		}
		else
		{  //the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);

			$this->load->view('home/login_view', $this->data);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them back to the page they came from
		redirect('home/login', 'refresh');
	}
   
}
?>