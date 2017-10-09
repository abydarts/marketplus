<!--<div class="clstheme category_title">
<div class="container"> 
	<div class="title_inner">
	<h2><?php echo "Confirm the details";?></h2> 
    </div>
 </div>
	</div>
    <div class="cross_border"></div>-->

<?php 
//user session variables  
		$username= $this->session->userdata('screenname'); 
		//echo $username;
		$firstname =$username;  
?>
<div class="container wbg form_container martop25 settheheight">
	<div class="form_page rightbarz30"> 	
	<?php echo form_open(site_url('tumblr/confirm')); ?>
	<div class="title_head"><?php echo "Confirm the details";?></div>
    <div class="alertblock container_no">
<div class="alert alert-info"><i class="icon-ok-sign"></i>
    You need to provide an e-mail address for your account to complete the registration.
    </div>
</div>
<div class="control-group">
<label class="" for="email"><?php echo lang('email');?></label>
<input type="text" name="email" id="email" value=""/>
<span id="emailInfo"></span>
</div>
<p style="color: red;"><?php echo form_error('email'); ?><?php echo isset($errors['email'])?$errors['email']:''; ?></p>
	<input type="hidden" name="username" id="username" value="<?php echo $username.'tumblr'; ?>"/>
	<input type="hidden" name="firstname" value="">
	<input type="hidden" name="lastname" value=""> 
	<input type="submit" name="submit" value="confirm" class="login_btn btn"/> 
<?php echo form_close(); ?>
</div></div>  	