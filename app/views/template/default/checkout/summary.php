<div class="margintop10">	
<table class="table table-striped table-bordered">
		<thead>
			<thead>
				<tr>
					<th style="width:20%;"><?php echo lang('name');?></th>
					<th style="width:10%;"><?php echo 'Code';?></th>
					<th style="width:10%;"><?php echo lang('price');?></th>
					<th class="hidden-phone"><?php echo lang('description');?></th>
					<th style="width:20%;"><?php echo lang('totals');?></th>
				</tr>
			</thead>
		</thead>
		
		<tfoot>
			<?php
			/**************************************************************
			Subtotal Calculations
			**************************************************************/
			?>
			
			<tr>
				<td colspan="3"><strong><?php echo lang('grand_total');?></strong></td>
                <td class="hidden-phone"></td>
				<td><?php echo format_currency($this->bse_tec->total()); ?></td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
			$subtotal = 0;

			foreach ($this->bse_tec->contents() as $cartkey=>$product):?>
				<tr>
						<td><a class="additemimg" href="<?php echo site_url($product['slug']); ?>"><?php echo $product['name']; ?><?php 
						$photo	= theme_img('no_picture.png', lang('no_image_available'));
						$products	= json_decode($product['images']);

						if(!empty($products))
						{
							$primary	= $products;
							foreach($products as $photo)
							{
								if(isset($photo->logo))
								{
									$primary	= $photo;
								}
	
							}
						$photo	= '<img src="'.base_url('uploads/images/thumbnails/'.$primary->filename).'" />';
						}
						echo $photo;
		//endforeach; 
?></a></td>
					<td><?php echo $product['prod_code'];?></td>
					<td><?php echo format_currency($product['price']);?></td>
					<td class="hidden-phone">
						<?php echo substr($product['description'], 0, 200).'....';
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
							?>

					</td>
		
					<td><?php echo format_currency($product['price']); ?>

	<button class="btn btn-danger" type="button" onclick="if(confirm('<?php echo lang('remove_item');?>')){window.location='<?php echo site_url('cart/remove_item/'.$cartkey);?>';}"><i class="icon-remove icon-white"></i></button>

				</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>