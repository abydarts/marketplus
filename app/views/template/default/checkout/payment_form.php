<?php if (validation_errors()):?>
	<div class="alert alert-error">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo validation_errors();?>
	</div>
<?php endif;?>

<article class="themedetail-container">
<div class="row-fluid">
			<div class="tabbable tabs-left">
            <div class="span3">
            <div class="whiteconainer compatability">
            <div class="sidebarborder">
            <h4><?php echo lang('payment_method');?></h4>
             <div class="border_top padd_top10">
				<ul class="nav nav-tabs">
				<?php
				if(empty($payment_method))
				{
					$selected	= key($payment_methods);
				}
				else
				{
					$selected	= $payment_method['module'];
				}
								?>	
				<?php 				
				$i=0;
				foreach($payment_methods as $method=>$info):
				if($i==0){?>
					<li><a href="#payment-deposit" data-toggle="tab"><?php echo "Purchase from deposit amount";?></a></li>
					<?php }?>
					<li <?php echo ($selected == $method)?'class="active"':'';?>><a href="#payment-<?php echo $method;?>" data-toggle="tab"><?php echo $info['name'];?></a></li>
				<?php $i++; endforeach;?>
				</ul>
                </div></div></div>
                <?php //include('order_details.php');?>
                </div>
                <div class="span9">
                <div class="whiteconainer accountpage">
                <div class="sidebarborder">
               
<h4><?php echo lang('form_checkout');?></h4>
 <div class="border_top padd_top10">
				<div class="tab-content">
					<?php foreach ($payment_methods as $method=>$info):?>
						<div id="payment-<?php echo $method;?>" class="tab-pane<?php echo ($selected == $method)?' active':'';?>">
                        <div class="product15">
							<?php echo form_open('checkout/step_3', 'id="form-'.$method.'"');?>
								<input type="hidden" name="module" value="<?php echo $method;?>" />
								<?php echo $info['form'];?>
								<input class="btn btn-primary margintop20" type="submit" value="<?php echo lang('form_continue');?>"/>
							</form>
                            </div>
						</div>
					<?php endforeach;?>
						<div id="payment-deposit" class="tab-pane<?php echo ($selected == 'payment-deposit')?' active':'';?>">
                        <div class="product15">
										<h3>Buy with Prepaid Credit</h3>
										
										<p>Pay <?php echo format_currency($this->bse_tec->total()); ?> from your prepaid credit balance: <?php 
										echo format_currency($this->customer_balance);
										//if(@$user_balance->balance=='') echo '0.00';
										//else echo $user_balance->balance ;?></p>
										<a href="<?php echo site_url('user/my_deposit') ?>">(MAKE A DEPOSIT)</a> 
<?php echo form_open('checkout/place_order/', 'id="form-desposit"');?>
								<input type="hidden" name="module" value="<?php echo 'desposit';?>" />
								<input onclick="return confirm('By clicking okay you will immediately purchase this item.');" class="btn btn-primary margintop20" type="submit" value="<?php echo lang('form_continue');?>"/>
							</form>
								</div>

                        </div>
				</div>
                </div></div>
</div>
			</div>
		</div>
	</div>
</article>