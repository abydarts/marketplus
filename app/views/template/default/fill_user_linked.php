<?php 
//user session variables     
$firstname = $this->session->userdata('firstname');   
$lastname = $this->session->userdata('lastname');  
$username=$this->session->userdata('username');  
$linkedin_id=$this->session->userdata('lid');
?>
<div class="container wbg form_container martop25">
	<div class="form_page rightbarz30 "> 	
	<?php echo form_open(site_url('linked/confirm')); ?>
	<div class="title_head"><?php echo lang("Confirm the details");?></div>
    <div class="alertblock container_no">
<div class="alert alert-info"><i class="icon-ok-sign"></i>
    <?php echo lang('You need to provide an email address for your account to complete the registration.');?>
    </div>
</div>
<div class="control-group">
<label class="" for="email"><?php echo lang('email');?></label>
			<input type="text" name="email" id="email" value=""/><div  id="profile_urlInfos" class="popover right  error-popover" style="">
<span id="emailInfo"></span>
</div>
<p style="color: red;"><?php echo form_error('email'); ?><?php echo isset($errors['email'])?$errors['email']:''; ?></p>
</div>
	<input type="hidden" name="username" id="username" value="<?php echo $username.'linked'; ?>"/>
	<input type="hidden" name="firstname" value="<?php echo $firstname;?>">
	<input type="hidden" name="lastname" value="<?php echo $lastname;?>"> 
	<input type="hidden" name="linked_id" value="<?php echo $linkedin_id;?>">
	<input type="submit" name="submit" value="confirm" class="login_btn btn"/> 
<?php echo form_close(); ?>
</div></div>
<script type="text/javascript" >
function profile_urlformkeypresssubmit()
{
 var url = "<?php echo base_url() ?>secure/my_account_url_check_twit_username/"; // the script where you handle the form input.

    $.ajax({
           type: "POST",
           url: url,
           data: $("#emailverifyform").serialize(), // serializes the form's elements.
           success: function(data)
           {
     	//alert(data);
      if(data == 'Username Not Available')
   	{
	   $('#profile_url').addClass("error");
	   $("#profile_urlInfo .popover-content").text("<?php echo ('Username Not Available')?>");
	   $("#profile_urlInfo").addClass("errors");
	   $("#profile_urlInfo").removeClass("success-popover");
    	}
      else
      {   
	   $('#profile_url').removeClass("error");
	   $("#profile_urlInfo .popover-content").text("<?php echo ('Username Available')?>");
	   $("#profile_urlInfo").removeClass("errors");
	   $("#profile_urlInfo").addClass("success-popover");
       }
    },
    onComplete: function(data){
     }
         });
}
</script>   	  	