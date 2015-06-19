<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {


    function __construct()
    {

        parent::__construct();
		$this->load->helper('url');
        $this->load->helper('form');
        $this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->model('hmvc/lithefire_model','lithefire',TRUE);
		$this->load->model('hmvc/commonmodel','',TRUE);		
		$this->load->model('hmvc/faculty_model','',TRUE);
		
		$this->load->library('layout', array('layout'=>$this->config->item('layout_file'))); 
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			redirect('main/login', 'refresh');
		}
		
		if (!$this->ion_auth->check_access())
		{
			//redirect them to the login page
			redirect('main/accessdenied', 'refresh');
		}
        //Initialization code that affects all controllers
    }

}