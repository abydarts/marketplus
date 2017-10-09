<div class=" newerror container">
    <div class="container ">
	   
	     <div class="error_page">
		         <div class="row-fluid">
		           <div class="errimage span3">
						<img src="<?php echo theme_img('error_images.png')?>">
						</div>
		                <div class="error_number span5">
				      <span> 404,</span> <span> ERROR</span>  		
			          <p class="error_para"> <?php echo lang("Sorry, the page you were looking for doesn't exist Go back to ");?><a href="<?php echo site_url();?>"><?php echo lang("home");?></a> or <a href="mailto:<?php echo $this->auth->value('email');?>"> <?php echo lang("mail us ");?></a> <?php echo lang("about a problem");?></p>
		
		           </div>
				 
				</div>
	     </div>
    </div>
</div>