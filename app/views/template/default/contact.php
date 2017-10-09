<?php
$additional_header_info = '<style type="text/css">#gc_page_title {text-align:center;}</style>';
include('header.php'); ?>
	<!--<Script type="text/javascript" src="<?php echo base_url();?>assets/menu/jquery.js"></Script>-->
	<Script type="text/javascript" src="<?php echo base_url();?>assets/js/validation.js"></Script>
	<Script type="text/javascript" src="<?php echo base_url();?>assets/css/general.css"></Script>
<?php 
$cname	= array('id'=>'name', 'class'=>'', 'name'=>'cname', 'value'=> $customer);
$cmail		= array('id'=>'email', 'class'=>'', 'name'=>'cmail', 'value'=> $email);  
$subject		= array('id'=>'subject', 'class'=>'', 'name'=>'subject', 'value'=> set_value('subject'));
$message		= array('id'=>'message', 'class'=>'', 'name'=>'message', 'value'=>set_value('message')); 
?>
<div class="clstheme category_title">
<div class="container">
<?php include('breadcrumb.php'); ?>
	<div class="title_inner">
	<h2><?php echo lang('contact');?></h2> 
    </div>
 </div>
	</div>
    <div class="cross_border"></div> 

<div class="contactus_page_con">
	<div class="contactus_page">
    <div class="container">
    <div class="row-fluid contact_con">
    <div class="white_bg form_common span8" style="max-width: 1170px;">
    <div class="contact_us">
    <p class="contact_desc"><?php echo $this->auth->value('description');?></p>
    </div>
    <div class="contact_form">
    <p class="leave_msg"><?php echo lang('leave_message');?></p>
<?php // echo lang('contact_send'); ?>
		<?php echo form_open('contact/index'); ?>
			<input type="hidden" name="submitted" value="submitted" /> 
			<fieldset>
				<div class="form2input marright23">
				<p><label for="name"><?php echo lang('customer_name');?></label></p>
				<?php echo form_input($cname);?>
				<span id="nameInfo"></span>
				</div>
				<div class="form2input form21input" style="margin-right: 0px;">
				<p><label for="email"><?php echo lang('customer_email');?></label></p>
				<?php echo form_input($cmail);?>
				<span id="emailInfo"></span>
				</div>  
				<div style="clear:both;">
				<p><label for="subject"><?php echo lang('subject');?></label></p>
				<?php echo form_input($subject);?>
				<span id="subjectInfo"></span>
				</div>
				<div>
				<p><label for="message">
				<?php echo lang('message');?></label>
				<?php echo form_textarea($message);?>
				<span id="messageInfo"></span>
				</p> 
				</div>
				<input type="hidden" value="<?php echo $id; ?>" name="id">
				<input type="submit" value="<?php echo 'Send Message';?>" class="btn btn-primary login_btn" id="contact_send" name="contact" style="float:left;width: 150px;"/>
			</fieldset>
		</form>
        </div>
        </div>
            <div class="span4 sidebar rightside" style="float:right;">
    	<div class="blog-contact">
        <div class="round_t">
<div class="round_r">
<div class="round_b">
<div class="round_l">
<div class="round_lt">
<div class="round_rt">
<div class="round_lb">
<div class="round_rb">
<div class="cls100_p">
                    
                    <div class="content"> 
                        <div class="other_contact"><p><label><?php echo lang('mail'); ?>:</label><span class="mail"><a href="mailto:support@memeberplus.com"><?php echo $this->auth->value('mail');?></a></span></p>
                        <p><label><?php echo lang('skype'); ?>:</label><span class="skype"><a href="#"><?php echo lang('skypeid'); ?>: <?php echo $this->auth->value('skype_name');?></a></span></p>
                        <p><label><?php echo lang('phone'); ?>:</label><span class="phone"><?php echo $this->auth->value('phone');?></span></p>
                        <p><label><?php echo lang('fax'); ?>:</label><span class="fax"><?php echo $this->auth->value('fax');?></span></p>
                        <p class="last"><label><?php echo lang('info'); ?>:</label><span class="infor"><?php echo $this->auth->value('info');?></span></p></div>
                    </div></div>
                    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
            
            <div class="blog-contact" style="clear: both; float: left; margin-top: 50px; padding: 0px; width: 100%;">
                    <div class="round_t">
<div class="round_r">
<div class="round_b">
<div class="round_l">
<div class="round_lt">
<div class="round_rt">
<div class="round_lb">
<div class="round_rb">
<div class="cls100_p">
<iframe frameborder="0" width="100%" height="300px" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.co.in/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Plot+number+3780,+TNHB+colony,+Villapuram,+Madurai+-+625011+Tamilnadu,+India&amp;aq=&amp;sll=13.05915,79.94658&amp;sspn=0.719717,1.352692&amp;ie=UTF8&amp;hq=Plot+number+3780,+TNHB+colony,&amp;hnear=Villapuram,+Madurai,+Tamil+Nadu&amp;t=m&amp;ll=9.895183,78.120808&amp;spn=0.006295,0.006295&amp;output=embed"></iframe>
</div></div></div></div></div></div></div></div></div>
</div>
            </div></div>
            </div>
        </div>
	</div>
</div>
<?php include('footer.php'); ?>
