	<?php if(!empty($customer['bill_address'])):?>
	<div class="whiteconainer compatability">
    <div class="sidebarborder">
<h4>Customer info</h4>
<div class="border_top padd_top10">
		<p class="margintop10">
			<?php echo $customer['user_firstname'].' '.$customer['user_lastname'];?><br/>
			<?php echo $customer['phone'];?><br/>
			<?php echo $customer['user_email'];?>
		</p>
</div></div>
	</div>
	<?php endif;?>

<?php if(config_item('require_shipping')):?>
	<?php if($this->bse_tec->requires_shipping()):?>
		<div class="whiteconainer compatability">
	<div class="sidebarborder">
			<p><?php echo $customer['user_firstname'].' '.$customer['user_lastname'];?><br/>
			<?php echo $customer['phone'];?><br/>
			<?php echo $customer['user_email'];?>
			</p></div>
		</div>

		<?php
		
		if(!empty($shipping_method) && !empty($shipping_method['method'])):?>
		<div class="whiteconainer compatability">
        <div class="sidebarborder">
			<p><a href="<?php echo site_url('checkout/step_2');?>" class="btn btn-block"><?php echo lang('shipping_method_button');?></a></p>
			<strong><?php echo lang('shipping_method');?></strong><br/>
			<?php echo $shipping_method['method'].': '.format_currency($shipping_method['price']);?>
            </div>
		</div>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>

<?php if(!empty($payment_method)):?>
	<div class="whiteconainer compatability">
    <div class="sidebarborder">
<h4>Billing method</h4>
<div class="border_top padd_top10">
<p class="margintop10"><?php echo $payment_method['description'];?></p>
<p><a href="<?php echo site_url('ChangePay');?>" class="productbtn"><?php echo lang('billing_method_button');?></a></p>
</div>
</div>
	</div>
<?php endif;?>