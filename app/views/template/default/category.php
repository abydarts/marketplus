<article class="themelist-container">
<div class="row-fluid">
<div class="span9">
<div class="themelistmain">
<div class="head">
<?php if(count($products) > 0):?>
<ul class="clearfix toolbox">
<li class="toplevel span6">
		<div class="sortby pull-left"><label>sort by:</label>
        <?php $get = parse_str(substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1), $_GET);?>
        <div class="sort_by_selectbox" style="display:inline">
        <div class="search searchboxdisplay langselect clearfix">
<a class="language_flag english" href="<?php echo site_url(uri_string());?>/?by=name/asc;">
 <?php if(!empty($_GET['by'])){
	 if($_GET['by']=='name/asc'){echo lang('sort_by_name_asc');}
	 elseif($_GET['by']=='name/desc'){echo lang('sort_by_name_desc');}
	 elseif($_GET['by']=='price/asc'){echo lang('sort_by_price_asc');}
	 elseif($_GET['by']=='price/desc'){echo lang('sort_by_price_desc');}
 	 elseif($_GET['by']=='ratings/desc'){echo 'Rating';}
	 elseif($_GET['by']=='date_created/desc'){echo 'Date';}
	   }
else
{
	echo lang('default');
}?><b class="caret"></b>
</a>
</div>
<ul class="flag_dropdown" style="display:none;">
<li><a href="<?php echo site_url(uri_string());?>"><?php echo lang('default');?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=name/asc"><?php echo lang('sort_by_name_asc');?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=name/desc"><?php echo lang('sort_by_name_desc');?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=price/asc"><?php echo lang('sort_by_price_asc');?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=price/desc"><?php echo lang('sort_by_price_desc');?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=ratings/desc"><?php echo 'Rating';?></a></li>
<li><a href="<?php echo site_url(uri_string());?>/?by=date_created/desc"><?php echo 'Date';?></a></li>
</ul>
</div>
		<div class="order asc"></div>
		<div class="clear"></div>
		</div>
        <div class="show clearfix">
	<div class="list active" id="listview"><img src="<?php echo theme_img('icons/list.png');?>">
	</div>
	<div class="grid" id="gridview"><img src="<?php echo theme_img('icons/grid.png');?>">
	</div>
	<div class="clear"></div>
	</div>
	</li>
<li class="toplevel span6">
<div class="pagination-top pagination-top clearfix pull-right">
<?php echo $this->pagination->create_links();?>
</div>
</li>
<script type="text/javascript" >
$(document).ready(function(){
		if($.cookie("style") == 'grid'){
		$('.productlist').fadeOut('fast');
		$('.productgrid').fadeIn('slow');
		$('#listview').removeClass('active');
		$('#gridview').addClass('active');
		}

	});
$('#listview').click(function(){

	$.cookie("style", 'null');

	$.cookie("style", 'list');
$('#gridview').removeClass('active')
$(this).addClass('active');

$('.productgrid').fadeOut('fast');
$('.productlist').fadeIn('slow');

});

$('#gridview').click(function(){
	$.cookie("style", 'null');
	$.cookie("style", 'grid');
$('#listview').removeClass('active');
$(this).addClass('active');
$('.productlist').fadeOut('fast');
$('.productgrid').fadeIn('slow');
});
</script>
</ul>
<?php endif; ?>
</div>
	<?php if((!isset($subcategories) || count($subcategories)==0) && (count($products) == 0)):?>
		<div class="alert alert-info">
			<a class="close" data-dismiss="alert">×</a>
			<?php echo lang('no_products');?>
		</div>
	<?php endif;?>
  			<?php if(count($products) > 0):?>        
            <ul class="themelist productlist clearfix">
            <?php $i=1; $calfirst=0;
            foreach($products as $product):?>
					<li class="product items themeview <?php if($calfirst%3==0){ echo 'first';} $calfirst++; ?>">
						<?php
						$photo	= theme_img('no_picture.png', lang('no_image_available'));
						$product->images	= array_values($product->images);
						if(!empty($product->images[0]))
						{
							$primary	= $product->images[0];
							foreach($product->images as $photo)
							{
								if(isset($photo->view))
								{
									$primary	= $photo;
								}
								if(isset($photo->primary))
								{ 
									$logo	= $photo;
                           $username = $this->Customer_model->get_customer($product->user_id);
								}	
							}
						$price = format_currency($product->price);
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$product->name.'#,'.$username->user_firstname.'#,'.$price.'#,'.$product->date_created;
						$photo	= '<img src="'.base_url('uploads/images/full/'.$primary->filename).'"  largeimage="'.$largeimageurl.'" alt="'.$product->seo_title.'"/>';

						$photos	= '<div class="items_div"><img src="'.base_url('uploads/images/full/'.$logo->filename).'" alt="'.$product->seo_title.'"/>			

									<p>'.$product->name.'</p><span> by '.$username->user_firstname.'</span>

									<p>$ '.$product->price.'</p></div>';
						}
						?>
                  <div class="img span3"><a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>">
                  	<?php echo $photo; ?><?php // echo $photos; ?><span class="cms-wordpress"></span></a></div>
						<div class="desc span7">
						<a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?> "><?php echo $product->name;?></a>
						<?php $customer= $this->Customer_model->get_customer($product->user_id); ?>
						<p class="seller"><?php echo lang('Seller');?>: <a href="<?php echo site_url('profile').'/'.$customer->user_id; ?>"><?php echo $customer->user_firstname.' '.$customer->user_lastname; ?> </a></p>
                        <ul class="clearfix"><li class="likes"><a href="#"><?php echo $product->ratings.' '.lang('Likes');?></a></li>
						<li id="whistlist" class="whistlist whislist" style="cursor:pointer">
						<?php $user_id = $this->bse_tec->customer();
						if($user_id != ''){?>
						<input type="hidden" id="product_ids" class="product_ids" value="<?php echo $product->id;?>" />
						<input type="hidden" name="user_id" id="user_id" class="user_id" value="<?php echo $user_id['user_id'];?>"/>
							<?php $userid = $this->bse_tec->customer();
							$product_id = $product	->id;		
							$wishlist	= $this->Customer_model->check_wishlist($userid['user_id'],$product_id);
							if(count($wishlist)==1){
?>
								<a href="<?php echo  site_url('user/remove_wishlist/'.$product->id.'/'.$user_id['user_id']);?>"><?php echo 'Remove from Whislist';?></a>
							<?php }else{?>
								<a class="whistlistlink"><?php echo lang('Add to Whislist');?></a>	
								<?php }?>
						<p class="success_wishlist"></p>
						<?php } 

						?></li>

						</ul>
						</div>
                  <div class="amt span2"><p class="price">
						<span class=""><?php echo format_currency($product->price); ?></span>
						</p><div class="buy">
						<a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>"><?php echo lang('Buy Now');?></a>
						</div></div>
					<div class="clear"></div>			
					</li>
				<?php
				endforeach;?>
				</ul>
					<?php endif;?>	
                    <?php if(count($products) > 0):?>        

            <ul class="themelist productgrid clearfix">

            <?php $i=1; $calfirst=0;

            foreach($products as $product):?>

					<li class="product themeview items border_top_none span3 <?php if($calfirst%4==0){ echo 'first';} $calfirst++; ?>">
						<?php
						$photo	= theme_img('no_picture.png', lang('no_image_available'));
						$product->images	= array_values($product->images);
						if(!empty($product->images[0]))
						{
							$primary	= $product->images[0];

							foreach($product->images as $photo)
							{
								if(isset($photo->view))
								{
									$primary	= $photo;
								}
								if(isset($photo->primary))
								{ 
									$logo	= $photo;
                           $username = $this->Customer_model->get_customer($product->user_id);
								}	
							}
						$price = format_currency($product->price);
$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$product->name.'#,'.$username->user_firstname.'#,'.$price.'#,'.$product->date_created;
						$photo	= '<img src="'.base_url('uploads/images/full/'.$primary->filename).'" largeimage="'.$largeimageurl.'"  alt="'.$product->seo_title.'"/>';

						$photos	= '<div class="items_div"><img src="'.base_url('uploads/images/full/'.$logo->filename).'" alt="'.$product->seo_title.'"/>			
									<p>'.$product->name.'</p><span> by '.$username->user_firstname.'</span>
									<p>$ '.$product->price.'</p></div>';
						}
						?>
                  <div class="img span12"><a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>">
                  	<?php echo $photo; ?><?php //echo $photos; ?><span class="cms-wordpress"></span></a></div>
						<div class="desc span12">
						<a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?> "><?php echo $product->name;?></a>
						<?php $customer= $this->Customer_model->get_customer($product->user_id); ?>
						</div>
                  <div class="amt span12">
                  <div class="pull_left_large">
                        <div class="buy">
						<a href="<?php echo site_url(implode('/', $base_url).'/'.$product->slug); ?>"><?php echo 'View details';?></a>
						</div>
                        </div>
                        <div class="pull_right_large">
                  <p class="price">
                  	<span class="currency_symbol"><?php echo format_currency($product->price); ?></span>
						</p></div></div>		
					<div class="clear"></div>			
					</li>
				<?php
				endforeach;?>
				</ul>
					<?php endif;?>		
<div class="head">
<?php if(count($products) > 0):?>
<ul class="clearfix toolbox">
<li class="toplevel span6">
		<div class="sortby pull-left"><label>sort by:</label>
<?php $get = parse_str(substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1), $_GET);?>
							<select id="sort_products" onchange="window.location='<?php echo site_url(uri_string());?>/?'+$(this).val();">

								<option value=''><?php echo lang('default');?></option>

								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/asc')?' selected="selected"':'';?> value="by=name/asc"><?php echo lang('sort_by_name_asc');?></option>

								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='name/desc')?' selected="selected"':'';?>  value="by=name/desc"><?php echo lang('sort_by_name_desc');?></option>

								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/asc')?' selected="selected"':'';?>  value="by=price/asc"><?php echo lang('sort_by_price_asc');?></option>

								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='price/desc')?' selected="selected"':'';?>  value="by=price/desc"><?php echo lang('sort_by_price_desc');?></option>
								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='ratings/desc')?' selected="selected"':'';?>  value="by=ratings/desc"><?php echo 'Rating';?></option>
								<option<?php echo(!empty($_GET['by']) && $_GET['by']=='date_created/desc')?' selected="selected"':'';?>  value="by=date_created/desc"><?php echo 'Date';?></option>
							</select>
		<div class="order asc"></div>
		<div class="clear"></div>
		</div>
	</li>
<li class="toplevel span6">
<div class="pagination-top pagination-top clearfix pull-right">
<?php echo $this->pagination->create_links();?>
</div>
</li>
<script type="text/javascript" >
$(document).ready(function(){
		if($.cookie("style") == 'grid'){
		$('.productlist').fadeOut('fast');
		$('.productgrid').fadeIn('slow');
		$('#listview').removeClass('active');
		$('#gridview').addClass('active');
		}
	});
$('#listview').click(function(){
	$.cookie("style", 'null');
	$.cookie("style", 'list');
$('#gridview').removeClass('active')
$(this).addClass('active');
$('.productgrid').fadeOut('fast');
$('.productlist').fadeIn('slow');
});

$('#gridview').click(function(){
	$.cookie("style", 'null');
	$.cookie("style", 'grid');
$('#listview').removeClass('active');
$(this).addClass('active');
$('.productlist').fadeOut('fast');
$('.productgrid').fadeIn('slow');
});

</script>
</ul>
<div class="drop_down_toolbox hidden-desktop"></div>
<?php endif; ?>
</div>
</div>
</div>
<div class="span3">
		<?php if(isset($subcategories) && count($subcategories) > 0): ?>
        <div class="whiteconainer compatability">
<div class="sidebarborder">
        <h4><?php echo lang('Subcategories');?></h4>

        <div class="sidebarlist border_top">

			<ul class="clearfix">

				<?php foreach($subcategories as $subcategory):?>

					<li><a href="<?php echo site_url(implode('/', $base_url).'/'.$subcategory->slug); ?>"><?php echo $subcategory->name;?></a></li>

				<?php endforeach;?>

			</ul>

			</div>
            </div>
<div class="side_border_bot"></div>
            </div>

		<?php endif;?>

<div class="whiteconainer compatability">
<div class="sidebarborder">
<h4><?php echo lang('Other Categories');?></h4>
<?php $current_cat = $this->uri->segment(1);?>
<div class="sidebarlist border_top">

<ul class="clearfix">

<?php foreach($this->categories as $cat_menu):
if($current_cat != $cat_menu['category']->slug){?>
<li><a href="<?php echo site_url($cat_menu['category']->slug);?>"><?php echo $cat_menu['category']->name;?></a></li>
<?php } endforeach;?>
</ul>
</div>
</div>
<div class="side_border_bot"></div>
</div>

<div class="whiteconainer compatability marginnone">
<div class="sidebarborder">
<h4><?php echo lang('Populairty');?></h4>

<div class="sidebarlist border_top">

<ul class="clearfix">

<li><a href="<?php echo @site_url('LastAdded');?>"><?php echo lang('Last Added');?></a></li>

<li><a href="<?php echo @site_url('ZeroDownloads');?>"><?php echo lang('Zero Downloads');?></a></li>

</ul>

</div>
</div>
<div class="side_border_bot"></div>
</div>
</div>
</div>
<article>

<script type="text/javascript">

	window.onload = function(){

		$('.product').equalHeights();

	}

$('.whislist').click(function(){

	var wish_list_prodid =$(this).children('.product_ids').val();

	var wish_list_userid =$(this).children('.user_id').val();

	$(this).children('.success_wishlist').html("<?php echo lang('Successfull Added to your Wishlist');?>").fadeIn(6000, 'linear');

	$(this).children('.whistlistlink').hide();

	$.ajax({

	url:"<?php echo site_url('products/wish_list'); ?>",

	type:'POST',

	data:{wish_list_prodid:wish_list_prodid,wish_list_userid:wish_list_userid},

	success:function(data){

	}	

		});

	});	
</script>