<?php $this->load->view(THEME_FOLDER.'/includes/header'); 

	$this->load->view(THEME_FOLDER.'/'.$body_content);
	
	$this->load->view(THEME_FOLDER.'/includes/footer');
?>