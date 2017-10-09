<H3><?php echo lang('order_number')?>: <?php echo $order_id ?></H3>
{download_section}
<div class="confirm_customer">
<?php
if($customer['company'] != '')
{
	echo '<div class="company_name">'.$customer['company'].'</div>';
}
?>
<?php echo $customer['user_firstname'];?> <?php echo $customer['user_lastname'];?> |
<?php echo $customer['user_email'];?> |
<?php echo $customer['phone'];?>
<br/><br/>
<table width="100%" cellpadding="10" border="0">
	<tr>
		<td>
			<strong><?php echo lang('billing_address');?></strong><br/>
			<?php 		@$bill = $customer['bill_address'];
					  	 
					  	 if(!empty($bill['company'])) echo $bill['company'].'<br>';
					  	 echo $bill['user_firstname'].' '.$bill['user_lastname'].' &lt;'.$bill['user_email'].'&gt;<br>';
					  	 echo $bill['phone'].'<br>';
					  	 echo $bill['address1'].'<br>';
					  	 if(!empty($bill['address2'])) echo $bill['address2'].'<br>';
					  	 echo $bill['city'].', '.$bill['zone'].' '.$bill['zip'];
			?> <br/>
		</td>
		<td>
			<strong><?php echo lang('payment_information');?></strong><br/>
			<?php echo $payment['description']; ?>
		</td>
	</tr>
</table>
</div>
<div class="gc_view_cart_wrapper">	
	<table class="gc_view_cart" cellpadding="5" cellspacing="5" border="0">
		<thead>
			<tr>
				<th class="header_left">&nbsp;</th>
				<th class="product_info" colspan="2"><?php echo lang('product_information');?></th>
				<th colspan="2"><?php echo lang('price_and_quantity');?></th>
				<th class="header_right">&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3" class="gc_view_cart_totals"><?php echo lang('subtotal');?></td>
				<td colspan="3" class="gc_total">
					<?php echo format_currency($this->bse_tec->subtotal()); ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="gc_view_cart_totals"><?php echo lang('shipping');?>: <?php echo $shipping['method'] ?></td>
				<td colspan="3" class="gc_total"><?php echo format_currency($shipping['price']) ?></td>
			<tr>
		<?php if($this->bse_tec->coupon_discount() > 0)  :?> 
			<tr>
				<td colspan="3" class="gc_view_cart_totals"><?php echo lang('coupon_discount');?></td>
				<td colspan="3" class="gc_total"><?php echo format_currency(0-$this->bse_tec->coupon_discount()); ?>                </td>
			</tr>
			<?php if($this->bse_tec->order_tax() != 0) :// Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from) ?> 
			<tr>
				<td colspan="3" class="gc_view_cart_totals"><?php echo lang('discounted_subtotal');?></td>
				<td colspan="3" class="gc_total"><?php echo format_currency($this->bse_tec->discounted_subtotal(), 2, '.', ','); ?>                </td>
			</tr>

<?php 
			endif;
		endif;
?>
           <?php if($this->bse_tec->order_tax() != 0) : ?> 
         	<tr>
				<td colspan="3"class="gc_view_cart_totals"><?php echo lang('taxes');?></td>

				<td colspan="3" class="gc_total"><?php echo format_currency($this->bse_tec->order_tax()); ?>                </td>
			</tr>
          <?php endif;   ?>

           <?php if($this->bse_tec->gift_card_discount() != 0) : ?> 
         	<tr>
				<td colspan="3"class="gc_view_cart_totals"><?php echo lang('gift_card');?></td>

				<td colspan="3" ><?php echo format_currency($this->bse_tec->gift_card_discount()); ?>                </td>
			</tr>
          <?php endif;   ?>
            <tr class="cart_grand_total"> 
				<td colspan="3" class="gc_view_cart_totals">
					<div class="cart_total_line_left"></div>
					<?php echo lang('grand_total');?>
				</td>
				<td colspan="3" class="gc_total">
					<div class="cart_total_line_right"></div>
					<span id="gc_total_price"><?php echo format_currency($this->bse_tec->total()); ?></span>
				</td>
			</tr>
		</tfoot>
		<tbody class="cart_items">
		<?php
		$td	= 'class="gc_even"';
		$subtotal = 0;
		foreach ($this->bse_tec->contents() as $cartkey=>$product):?>	
			<tr <?php echo $td;?>>
				<td class="table_left">&nbsp;</td>
				<td class="cart_product_info">
					<span class="cart_product_name"><?php echo $product['name']; ?></span><br/>
				</td>
				<td class="cart_product_description">
					
					<?php echo $product['excerpt'];
						if(isset($product['options'])) {
							echo '<table cellspacing="0" cellpadding="0">';
							foreach ($product['options'] as $name=>$value)
							{
								echo '<tr class="cart_options">';
								if(is_array($value))
								{
									echo '<td class="cart_option"><strong>'.$name.':</strong></td><td class="cart_option">';
									foreach($value as $item)
									{
										echo '<div>'.$item.'</div>';
									}
									echo '</td>';
								} 
								else 
								{
									echo '<td class="cart_option"><strong>'.$name.':</strong></td><td class="cart_option" > '.$value.'</td>';
								}
								echo '</tr>';
							}
							echo '</table>';
						}
						?>
					
				</td>
				<td class="cart_quantity">
					<?php echo format_currency($product['price']);?>
				</td>
				<td class="total"><?php echo format_currency($product['price']); ?></td>
				<td class="table_right">&nbsp;</td>
			</tr>
		<?php	
		if ($td == 'class="gc_even"')
		{
			$td = 'class="gc_odd"';
		}
		else
		{
			$td = 'class="gc_even"';
		}
		?>
		<?php endforeach;?>
		</tbody>
	</table>
</div>