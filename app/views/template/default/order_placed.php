<article class="themedetail-container">
<div class="row-fluid">
<div class="span3">
<div class="whiteconainer marbot15">
<div class="sidebarborder">
<h4><?php echo 'Downloads';?></h4>
        <div class="sidebarlist border_top paddingtop10">
        <?php
// content defined in canned messages
echo $download_section;
?>
        </div>
        </div></div>

<div class="whiteconainer marbot15">
<div class="sidebarborder">
		<h4><?php 
		echo lang('account_information');?></h4>
        <div class="sidebarlist border_top paddingtop10">
		<?php echo (!empty($customer['company']))?$customer['company'].'<br/>':'';?>
		<?php echo $customer['user_firstname'];?> <?php echo $customer['user_lastname'];?><br/>
		<?php echo $customer['user_email'];?> <br/>
		<?php echo $customer['phone'];?>

	<?php
	@$ship = $customer['ship_address'];
	@$bill = $customer['bill_address'];
	?>
</div></div></div>
	
<div class="whiteconainer marbot15">
<div class="sidebarborder">
		<h4><?php echo lang('payment_information');?></h4>
        <div class="sidebarlist border_top paddingtop10">
		<?php if($payment['description']=='')
			echo lang('from deposit');
			else
			echo $payment['description']?>
	</div></div></div>
	
</div>
<div class="span9">
<div class="whiteconainer accountpage">
<div class="sidebarborder">
	<h4><?php echo lang('order_number');?>: <?php echo $order_id;?></h4>
<div class="sidebarlist border_top paddingtop10">
<div class="product15">
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th style="width:20%;"><?php echo lang('name');?></th>
			<th style="width:10%;"><?php echo lang('price');?></th>
			<th><?php echo lang('description');?></th>
			<th style="width:8%;"><?php echo lang('totals');?></th>
		</tr>
	</thead>
	
	<tfoot>
		<?php if($bse_tec['group_discount'] > 0)  : ?> 
		<tr>
			<td colspan="5"><strong><?php echo lang('group_discount');?></strong></td>
			<td><?php echo format_currency(0-$bse_tec['group_discount']); ?></td>
		</tr>
		<?php endif; ?>

		<tr>
			<td colspan="4"><strong><?php echo lang('subtotal');?></strong></td>
			<td><?php echo format_currency($bse_tec['subtotal']); ?></td>
		</tr>
		
		<?php if($bse_tec['coupon_discount'] > 0)  : ?> 
		<tr>
			<td colspan="5"><strong><?php echo lang('coupon_discount');?></strong></td>
			<td><?php echo format_currency(0-$bse_tec['coupon_discount']); ?></td>
		</tr>

		<?php if($bse_tec['order_tax'] != 0) : // Only show a discount subtotal if we still have taxes to add (to show what the tax is calculated from) ?> 
		<tr>
			<td colspan="5"><strong><?php echo lang('discounted_subtotal');?></strong></td>
			<td><?php echo format_currency($bse_tec['discounted_subtotal']); ?></td>
		</tr>
		<?php endif;

		endif; ?>
		<?php // Show shipping cost if added before taxes
		if($this->config->item('tax_shipping') && $bse_tec['shipping_cost']>0) : ?>
		<tr>
			<td colspan="5"><strong><?php echo lang('shipping');?></strong></td>
			<td><?php echo format_currency($bse_tec['shipping_cost']); ?></td>
		</tr>
		<?php endif ?>
		
		<?php if($bse_tec['order_tax'] != 0) : ?> 
		<tr>
			<td colspan="4"><strong><?php echo lang('taxes');?></strong></td>
			<td><?php echo format_currency($bse_tec['order_tax']); ?></td>
		</tr>
		<?php endif;?>
		
		<?php // Show shipping cost if added after taxes
		if(!$this->config->item('tax_shipping') && $bse_tec['shipping_cost']>0) : ?>
		<tr>
			<td colspan="4"><strong><?php echo lang('shipping');?></strong></td>
			<td><?php echo format_currency($bse_tec['shipping_cost']); ?></td>
		</tr>
		<?php endif;?>
		
		<?php if($bse_tec['gift_card_discount'] != 0) : ?> 
		<tr>
			<td colspan="4"><strong><?php echo lang('gift_card');?></strong></td>
			<td><?php echo format_currency(0-$bse_tec['gift_card_discount']); ?></td>
		</tr>
		<?php endif;?>
		<tr> 
			<td colspan="4"><strong><?php echo lang('grand_total');?></strong></td>
			<td><?php echo format_currency($bse_tec['total']); ?></td>
		</tr>
	</tfoot>

	<tbody>
	<?php
	$subtotal = 0;
	foreach ($bse_tec['contents'] as $cartkey=>$product):?>
		<tr>
			<td><?php echo $product['name']; ?></td>
			<td><?php echo format_currency($product['price']);   ?></td>
			<td><?php echo substr($product['description'],0,200);
				if(isset($product['options'])) {
					foreach ($product['options'] as $name=>$value)
					{
						if(is_array($value))
						{
							echo '<div><span class="gc_option_name">'.$name.':</span><br/>';
							foreach($value as $item)
								echo '- '.$item.'<br/>';
							echo '</div>';
						} 
						else 
						{
							echo '<div><span class="gc_option_name">'.$name.':</span> '.$value.'</div>';
						}
					}
				}
				?></td>
			
			<td><?php echo format_currency($product['price']); ?>				</td>
		</tr>
			
	<?php endforeach; ?>
	</tbody>
</table>
</div>
</div></div>
</div>
</div>
</div>
</article>