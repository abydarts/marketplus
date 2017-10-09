<?php 
/* 
* Company name  : BseTec Pvt Ltd.
* Product  name  : Themeforest clone for ecommerce  
* Framework name : Codeigniter
* Dev Team : Gokulnath,Balaji,Suchitra.
* File name : user.php (controller)
*/

class Not_found extends Front_Controller {
 
    public function __construct() {
            parent::__construct();    
          $this->config->set_item('language',$this->auth->language());         
    }
 
    //
    // @ start 404 page code
    //
    public function index() {
 
 
	 $data['body_content']			= '404_error_page';
		
		$this->load->view('template', $data);

    }
}