<article class="themedetail-container">
<div class="row-fluid">
<div class="whiteconainer1 compatability">
<div class="sidebarborder no_bor">
<h4><?php if($this->bse_tec->total_items() > 1)

									{
										echo sprintf (lang('multiple_items'), $this->bse_tec->total_items());
									}
									else
									{
										echo sprintf (lang('single_item'), $this->bse_tec->total_items());
									}
?></h4>
<div class="border_top paddingtop20">
<?php if ($this->bse_tec->total_items()==0):?>
	<div class="alert alert-info">
		<a class="close" data-dismiss="alert">×</a>
		<?php echo lang('empty_view_cart');?>
	</div>
<?php else: ?>
	<?php echo form_open('cart/update_cart', array('id'=>'update_cart_form'));?>
	<?php include('checkout/summary.php');?>
			<div style="text-align:center;">
				<input id="redirect_path" type="hidden" name="redirect" value=""/>	
				<?php if(!$this->Customer_model->is_logged_in(false,false)): ?>
					<input class="btn btn btn-large btn-primary" type="submit" onclick="$('#redirect_path').val('checkout/login');" value="<?php echo lang('login');?>"/>
				<?php endif; ?>
										
			<?php if ($this->Customer_model->is_logged_in(false,false) || !$this->config->item('require_login')): ?>
				<input class="btn btn-large btn-primary" type="submit" onclick="$('#redirect_path').val('checkout');" value="<?php echo lang('form_checkout');?>"/>
			<?php endif; ?>
		</div>
</form>
<?php endif; ?>
</div>
</div>
</div>
</div>
</article>