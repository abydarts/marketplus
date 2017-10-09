<div class="themedetail-container">
<div class="row-fluid">
<div class="span9">
<h2><?php echo $product->name." - ".format_currency($product->price);?></h2>
<div class="themepanel">
<div class="themepanel-white clearfix">

<div class="themepanel-img">
<div class="">
<div class="flexslider flexdescslider">
          <ul class="slides"> 
            <!-- Carousel items -->  
				<?php
				foreach($category as $cat)
				{
					$product_category = $cat->name;
               $product_category_slug = $cat->slug;		
				}
				$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product->images	= array_values($product->images);
				if(!empty($product->images))
				{	$screen_count=0;
					foreach($product->images as $screen_shot){
						if($screen_count==0){
							$class = 'active item';}
						else {$class = 'item';}
						if(isset($screen_shot->logo))
						{$logo = 1;}
						elseif(isset($screen_shot->view))
						{$logo = 1;}
						else {$logo=0;}	
						if($logo==0){
						echo '<li class="'.$class.'">
						<img class="responsiveImage" src="'.base_url('uploads/images/full/'.$screen_shot->filename).'" alt="'.$product->seo_title.'"/>
						</li>';
						}
						$screen_count++;
						}					
					}
				?>

  </ul>  
  <!-- Carousel nav -->  
</div>
</div>
<div class="verticalthumbnav">
<div class="flexslider flexverticalthumbslider">
          <ul class="slides"> 
            <!-- Carousel items -->  
				<?php
				$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product->images	= array_values($product->images);
				if(!empty($product->images))
				{	$screen_count=0;
					foreach($product->images as $screen_shot){
						if($screen_count==0){
							$class = 'item';}
						else {$class = 'item flex-active-slide';}
						if(isset($screen_shot->logo))
						{$logo = 1;}
						elseif(isset($screen_shot->view))
						{$logo = 1;}
						else {$logo=0;}	
						if($logo==0){
						echo '<li class="'.$class.'">
						<img class="responsiveImage" src="'.base_url('uploads/images/full/'.$screen_shot->filename).'" alt="'.$product->seo_title.'"/>
						</li>';
						}
						$screen_count++;
						}					
					}
				?>

  </ul>  
  <!-- Carousel nav -->  
</div>
</div>
</div>
<div class="clear"></div>
		</div>
		</div>
<div class="clsboxes">

	<div class="flexslider flextab1">
	<div class="direction_nav_outside flex-direction-nav">
    <a href="javascript:void(0)" onClick="jQuery('.flextab1').flexslider('prev');" class="previous flex-prev">Previous</a>
    <a href="javascript:void(0)" onClick="jQuery('.flextab1').flexslider('next');"  class="Next flex-next">Next</a>
    </div>

    <ul class="table table-striped head slides">
			<li><p class="tab_head">Description</p></li>
            <li class="related_products"><p class="tab_head"><?php echo lang('related_products_title');?></p></li>
            <li><p class="tab_head"><?php echo lang('change log');?></p></li>
            <li><p class="tab_head"><?php echo lang('faq');?></p></li>
            <li><p class="tab_head" id="comment_counts"><?php echo lang('Comments');?></p></li>
            </ul>
            </div>
            <div class="flexslidertab1">
			<ul class="table tab_content slides clearfix">
            <li><?php echo $product->description; ?>
            <div class="masonary-container123">
            <?php if($product->features=='')
				 echo lang('There is no Features content');
				 else 
				 echo $product->features; ?>

                </div></li>
                <li><div class="masonary-container_no">
	<?php if(!empty($product->related_products)):?>
	    <div class="related_products">
				<h3 style="margin-top:20px;"></h3>
				<ul class="thumbnails row-fluid">	
				<?php $i=0; foreach($product->related_products as $relate): ?>
					<li class="product span3 <?php if($i%4 == 0){ echo 'first'; }$i++; ?>">
						<?php
						$photo	= theme_img('no_picture.png', lang('no_image_available'));
						$relate->images	= array_values((array)json_decode($relate->images));					
						if(!empty($relate->images[0]))
						{
							$primary	= $relate->images[0];
							foreach($relate->images as $photo)
							{
								if(isset($photo->view))
								{
									$primary	= $photo;
								}
							}
							$photo	= '<img src="'.base_url('uploads/images/full/'.$primary->filename).'" alt="'.$relate->seo_title.'"/>';
						}
						?>
						<a class="thumbnailno" href="<?php echo site_url($relate->slug); ?>">
							<?php echo $photo; ?>
						</a>
						<div  style="margin-top:5px;">
                        <a href="<?php echo site_url($relate->slug); ?>"><?php echo $relate->name;?></a>
							<?php if($relate->saleprice > 0):?>
								<span class="price-slash"><?php echo lang('product_reg');?> <?php echo format_currency($relate->price); ?></span>
								<span class="price-sale"><?php echo lang('product_sale');?> <?php echo format_currency($relate->saleprice); ?></span>
							<?php else: ?>
								<span class="price-reg  pull-right"><?php echo lang('product_price');?> <?php echo format_currency($relate->price); ?></span>
							<?php endif; ?>
						</div>
	                    <?php if((bool)$relate->track_stock && $relate->quantity < 1 && config_item('inventory_enabled')) { ?>
							<div class="stock_msg"><?php echo lang('out_of_stock');?></div>
						<?php } ?>
					</li>
				<?php endforeach;?>
				</ul>
	</div>
    <?php else: echo lang('There is no Related Products');
     endif;?>	
    </div></li>
    <li><div class="masonary-container">
				 <?php if($product->changelog=='')
				 echo lang('There is no Change Log content');
				 else 
				 echo $product->changelog; ?>
                </div></li>
                <li><div class="masonary-container">
				 <?php if($product->faq=='')
				 echo lang('There is no FAQ');
				 else 
				 echo $product->faq; ?>
                </div></li>
                <li><div class="masonary-container productviewcommentcon">
					<div class="cm_box"  style="clear: both;">
						<input type="hidden" id="product_ids" value="<?php echo $product->id;?>" />
						<div id="ajax_loader_page">
						<div id="loading_img"><img src="<?php echo theme_img('loading.gif');?>" alt="" ></div>
						</div>
						</div>
				</div></li>
            </ul>
             </div>
            </div>
</div>

<div class="span3">
	<div class="demolink-container">
		<div class="demolink marginbottom20">
		<a href="<?php echo site_url('Demo/'.$product->id);?>" target="_blank" class="demo">
		<span><?php echo lang('live demo');?></span></a>
		
</div>
<?php $user_id = $this->bse_tec->customer();
if($user_id){
if($user_id['user_id'] !=  $product->user_id){  ?>
<?php
foreach($orders as $order)
{
	if($product->id == $order->product_id) 
	{
		$item_detail = unserialize($order->contents);
	}
}
?>
<div class="pricedetails whiteconainer clearfix">
<div class="sidebarborder">
<div id="flip_rate"><?php echo lang('regular license');?> ( <?php echo format_currency($product->price); ?> ) </div>
<div id="panel_rate">
<div class="demolink">
			<?php echo form_open('cart/add_to_cart', 'class="form-horizontal"');?>
					<input type="hidden" name="cartkey" value="<?php echo $this->session->flashdata('cartkey');?>" />
					<input type="hidden" name="id" value="<?php echo $product->id?>"/>
					<div class="control-group-new">
							<div>
							<?php if(!config_item('inventory_enabled') || config_item('allow_os_purchase') || !(bool)$product->track_stock || $product->quantity > 0) : ?>
								<?php 
								
								//print_r($orders);
								if($user_id['username']!=$_GET['ref']):	
										
								if(!empty($item_detail))
								{	
									if($item_detail['standard']==0) { ?><a href="<?php echo site_url('downloads'); ?>" class="productbought">You Bought this product with Regular license</a> <?php }
									else {
									?>
								<button class="buy" type="submit" value="submit"><span> <?php echo lang('form_add_to_cart');?></span></button>
								<?php
									}
								}
								else{
														
								?>
								<button class="buy" type="submit" value="submit"><span> <?php echo lang('form_add_to_cart');?></span></button>
								<?php
															
								}
								
								
								endif;
															
							endif;?>
						</div>
					</div>
<input name="redirect_previous" type="hidden" value="<?= $this->uri->uri_string() ?>" />
</form>
</div>
</div>
 
<div id="flips_rate" class="hideprice"><?php echo lang('standard license');?> ( <?php echo format_currency($product->standard_price); ?> ) </div>
<div id="panels_rate">
<div class="demolink">
			<?php echo form_open('cart/add_to_cart', 'class="form-horizontal"');?>
					<input type="hidden" name="cartkey" value="<?php echo $this->session->flashdata('cartkey');?>" />
					<input type="hidden" name="id" value="<?php echo $product->id?>"/>
					<input type="hidden" name="ids" value="standard"/>
					<div class="control-group-new">
						<!--<label class="control-label"><?php echo lang('quantity') ?></label>-->
						<div>
							<?php if(!config_item('inventory_enabled') || config_item('allow_os_purchase') || !(bool)$product->track_stock || $product->quantity > 0) : ?>
								
								<?php 
								
								if($user_id['username']!=$_GET['ref']):	
															
										
								if(!empty($item_detail))
								{	
									if($item_detail['standard']==1) 
									{ ?><a href="<?php echo site_url('downloads'); ?>" class="productbought">You Bought this product with Standard license</a> <?php }
								else {
									?>
								<button class="buy" type="submit" value="submit"><span> <?php echo lang('form_add_to_cart');?></span></button>
								<?php
									}
								}
								else{
														
								?>
								<button class="buy" type="submit" value="submit"><span> <?php echo lang('form_add_to_cart');?></span></button>
								<?php
															
								}
							
							endif;							
							endif;?>
						</div>
					</div>
<input name="redirect_previous" type="hidden" value="<?= $this->uri->uri_string() ?>" />
</form>
</div>
</div>
<div class="common_padd clearfix">
			<div class="add_comment1">
				<?php $userid = $this->bse_tec->customer();
				$follow	= $this->Product_model->check_Recommend($product->id,$userid['user_id']);
				if(count($follow)==1){$val = lang('Recommended');}
				else{$val =lang('Recommend');}
				if($userid!=''){?>
				<input type="button" class="btn btn-primary" id="recommend" value="<?php echo $val; ?>" />
				<?php }?>
				<input type="hidden" id="follower_id" value="<?php echo $product->id;?>" />
				<input type="hidden" id="user_id" value="<?php echo $userid['user_id'];?>" />
			</div>
            <div id="whislist" class="whislist" style="cursor:pointer">
<?php $user_id = $this->bse_tec->customer();
if($user_id != ''){?>
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id['user_id'];?>"/>
		<?php $userid = $this->bse_tec->customer();
		$product_id = $product->id;
		$wishlist	= $this->Customer_model->check_wishlist($userid['user_id'],$product_id);
		?>
		<input type="hidden" id="wishlist_count" name="wishlist_count" value="<?php echo count($wishlist);?>" />
		<?php
		if(count($wishlist)==1){?>
			<a class="removewhistlistlink"><?php echo lang('Remove from Wishlist');?></a>
			<?php }else{?>
			<a class="whistlistlink"><?php echo lang('Add to Wishlist');?></a>	
			<?php }?>
<p id="success_wishlist"></p>
<?php } ?>
</div>
</div>
<div class="clear"></div></div>
</div>
<?php }
 }
else{
echo '<div class="demolink marginbottom20"><a class="demo logingreenbtn" id="pop_buy" href="javascript:void(0)"><span>login to Buy</span></a></div>';
}?>
<div class="whiteconainer ratingsmain sidebar">
<div class="sidebarborder">
<h4><?php echo lang('Buyer rating');?></h4>
<div class="border_top padd_top10">
<?php 

if($product->ratings>3){ 
$product_count = $this->Order_model->get_ordercount_by_product($product->id);
$count = $product_count->c;
$ratingcount = $product->ratings/$count;

?>
<div class="ratingtop">
		<div class="exemple">
		<div class="basic jDisabled" data-average="<?php echo $ratingcount; ?>" data-id="1"></div>
		</div>
				<input type="hidden" class="small_path" value="<?php echo theme_img('rating/small.png');?>" />
				<input type="hidden" class="big_path" value="<?php echo theme_img('rating/stars.png');?>" />
				<input class="rating_limit" value="5" type="hidden" />

<script type="text/javascript">
		$(document).ready(function(){
			$('.basic').jRating({readOnly: true});
		});
</script>
<p class="ratingdesc">(<?php echo round($ratingcount,2)." average based on ".$product->ratings.' '.lang('ratings');?>)</p><p>
</p></div>
<?php }
else {
	echo 'Minimum of 3 votes required';} ?>
<div class="viewcount">
<ul class="clearfix">
<li class="purchasecount"><span id="down_count">0</span><p><?php echo lang('Purchases');?></p></li>
<li class="commentcount"><span id="count_comm"></span><p><?php echo lang('comments');?></p></li>
</ul>
</div>
<div class="sociallike">
<ul class="clearfix">
<li><div class="flike"><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a></div></li>
<li><div class="tlike"><a class="addthis_button_tweet"></a></div></li>
<li class="marginbottomnone"><div class="glike"><a class="addthis_button_pinterest_pinit"></a></div></li>
<li class="marginbottomnone"><div class="slike"><a class="addthis_counter addthis_pill_style"></a></div></li>
</ul>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51504e734aa8785b"></script>
</div>
</div>
</div>
<div class="side_border_bot"></div>
</div>
<div class="greycontainer sidebar">
<div class="sidebarborder">
<ul class="themedetails-side clearfix">
<li><p class="sub_tit"><?php echo lang('CATEGORY');?></p><p><?php echo '<a class="" href="'.site_url($product_category_slug).'">'.$product_category.'</a>'; ?></p></li>
<li><p class="sub_tit"><?php echo lang('CREATED');?></p><p><?php echo format_date($product->date_created); ?></p></li>
<li><p class="sub_tit"><?php echo lang('COMPATIBLE BROWSERS');?></p><p>
									<?php $brw = json_decode($product->brws_cmpt);
									$i=0;
									foreach($brws_cmpt as $brws_cmpt):
									 if(in_array($brws_cmpt->id,$brw)){
										 $i++;
										 if($i==1)
										 {
											 echo '<a class="" href="'.site_url('TagSearch').'/'.$brws_cmpt->id.'/0/'.$brws_cmpt->brws_name.'">'.$brws_cmpt->brws_name.'</a> ';
										 }
										 else
										 {
									     echo ', <a class="" href="'.site_url('TagSearch').'/'.$brws_cmpt->id.'/0/'.$brws_cmpt->brws_name.'">'.$brws_cmpt->brws_name.'</a> ';
										 }																									 
									 }
									endforeach; ?></p></li>
<li><p class="sub_tit"><?php echo lang('FILES INCLUDED');?></p><p><?php $brws = json_decode($product->file_include);
$i=0;
									foreach($file_include as $file_include):
									 if(in_array($file_include->id,$brws)){
										 $i++;
										 if($i==1)
										 {
											 echo '<a class="" href="'.site_url('TagSearch').'/'.$file_include->id.'/0/'.$file_include->file_name.'">'.$file_include->file_name.'</a> ';
										 }
										 else
										 {
									     echo ', <a class="" href="'.site_url('TagSearch').'/'.$file_include->id.'/0/'.$file_include->file_name.'">'.$file_include->file_name.'</a> ';
										 } 
									 }
									endforeach; ?></p></li>
</ul>
</div>
<div class="side_border_bot"></div>
</div>
<div class="whiteconainer compatability">
<div class="sidebarborder">
<h4>Tags</h4>
<div class="taglist border_top">
<ul class="clearfix">
<?php $tags = explode(',',$product->seo_title);
foreach($tags as $tags):
echo ' <li><a class="" href="'.site_url('TagSearch').'/'.$tags.'/0/'.$tags.'">'.$tags.'</a></li> ';																							 
endforeach; ?>

<li><a href="#"><?php echo lang('+13 more');?></a></li>
</ul>
</div>
</div>
<div class="side_border_bot"></div>
</div>
<div class="whiteconainer compatability">
<div class="sidebarborder">
<h4><?php echo lang('About the Author');?></h4>
<div class="about-author border_top clearfix">

<div class="author-desc">
<?php $username = $this->Customer_model->get_customer($product->user_id); ?>
<a class="pull-left" href="<?php echo site_url('profile/'.$username->user_id) ?>">
<?php if($username->avatar!='') {?>
<img src="<?php echo base_url('uploads/profile').'/'.$username->avatar;?>" title="<?php echo $username->user_id; ?>" />
<?php } else {
?>
<img height="60" width="60" src="<?php echo theme_img('avatar.png') ?>" title="<?php echo $username->user_id; ?>" />
<?php 
}?>
</a>
<div class="desc pull-left marleft10">
<a href="<?php echo site_url('profile/'.$username->user_id) ?>"><?php echo $username->user_firstname.' '.$username->user_lastname;?></a>
<label for="account_email"><?php echo lang('Member Since');?>:</label>
<span><?php echo format_date($username->date_created);?></span>
</div>
<div class="desc clear paddingtop10">
<p><?php //echo $username->aboutme;?></p>
</div>
</div>
<div class="clear"></div>

</div>
</div>
  <div class="side_border_bot"></div>
</div>
<div class="whiteconainer compatability">
<div class="sidebarborder">
<h4><?php echo lang('Support');?></h4>
<div class="socialshare border_top">
<ul class="clearfix">
<?php if($username->facebook != ''){ ?>
<li><a href="<?php echo $username->facebook;?>" target="_blank"><img src="<?php echo theme_images('button/fshare.png')?>" /></a></li>
<?php } if($username->twitter != ''){ ?>
<li><a href="<?php echo $username->twitter;?>" target="_blank"><img src="<?php echo theme_images('button/tshare.png')?>" /></a></li>
<?php } if($username->google != ''){ ?>
<li><a href="<?php echo $username->google;?>" target="_blank"><img src="<?php echo theme_images('button/gshare.png')?>" /></a></li>
<?php } if($username->rssfeed != ''){?>
<li><a href="<?php echo $username->rssfeed;?>" target="_blank"><img src="<?php echo theme_images('button/rshare.png')?>" /></a></li>
<?php } if($username->youtupe != ''){ ?>
<li><a href="<?php echo $username->youtupe;?>" target="_blank"><img src="<?php echo theme_images('button/hdshare.png')?>" /></a></li>
<?php }?>
</ul>
</div>
</div>
<div class="side_border_bot"></div>
</div>

<div class="whiteconainer">
<div class="sidebarborder">
<h4>Share Product</h4>
<div id="myRadioGroup" class="border_top padd_top10">
 <input type="radio" name="Option" checked="checked"  value="1" /> Link
 <input type="radio" name="Option" value="2"  /> Button
 <input type="radio" name="Option" value="3" /> Widget
		<div id="Option1" class="desc">
		<?php if($this->username) { ?>
		<input type="text" onclick="this.focus();this.select()" value="<?php echo site_url($product->slug).'?ref='.$this->username; ?>" />
		<?php } 
		else {?>
		<input type="text" onclick="this.focus();this.select()" value="<?php echo site_url($product->slug) ?>" />
		<?php } ?>
		</div>
		<div id="Option2" class="desc" style="display: none;">
		<textarea onclick="this.focus();this.select()">
		&lt;div id="buyonmp<?php site_url($product->id) ?>"&gt;&lt;/div&gt;
		<?php if($this->username) { ?>
		<script>
		var __marketplus__ = {url:'<?php echo site_url($product->slug).'?ref='.$this->username; ?>',text:'Buy on Market Plus',itemID:'<?php site_url($product->id) ?>'};
		</script>
		<?php echo theme_js('purchase_button.js', true);?>
		<?php } 
		else {?>
		<script>
		var __marketplus__ = {url:'<?php echo site_url($product->slug) ?>',text:'Buy on Market Plus',itemID:'<?php site_url($product->id) ?>'};
		</script>
		<?php echo theme_js('purchase_button.js', true);?>
		<?php } ?>
		</textarea> 
		<div id="buyonmp<?php site_url($product->id) ?>"></div>
		<?php if($this->username) { ?>
		<script>
		var __marketplus__ = {url:'<?php echo site_url($product->slug).'?ref='.$this->username; ?>',text:'Buy on Market Plus',itemID:'<?php site_url($product->id) ?>'};
		</script>
		<?php echo theme_js('purchase_button.js', true);?>
		<?php } 
		else {?>
		<script>
		var __marketplus__ = {url:'<?php echo site_url($product->slug) ?>',text:'Buy on Market Plus',itemID:'<?php site_url($product->id) ?>'};
		</script>
		<?php echo theme_js('purchase_button.js', true);?>
		<?php } ?>
		</div>
		<div id="Option3" class="desc" style="display: none;">
        <textarea onclick="this.focus();this.select()">
		&lt;div id="marketplus-product<?php echo $product->id?>"&gt;&lt;/div&gt;
		<script>
		var __marketplus__ = {width:220,url:'<?php echo site_url('products/product_iframe').'/'.$product->id ?>',height:150,itemID:'<?php echo $product->id?>'};
		</script>
		<?php echo theme_js('product_widget.js', true);?>
		</textarea>
		<div id="marketplus-product<?php echo $product->id?>"></div>
		<script>
		var __marketplus__ = {width:220,url:'<?php echo site_url('products/product_iframe').'/'.$product->id ?>',height:150,itemID:'<?php echo $product->id?>'};
		</script>
		<?php echo theme_js('product_widget.js', true);?>      
		</div>
</div>
</div>
<div class="side_border_bot"></div>
</div>
</div>
</div>
</div></div>
<script type="text/javascript" >
$(document).ready(function() {
    $("input[name$='Option']").click(function() {
        var test = $(this).val();
        $("div.desc").hide();
        $("#Option" + test).show();
    });
});
</script>
<script type="text/javascript">
var prod_id  = $('#product_ids').val();
$.ajax({
url:"<?php echo site_url('item_comments'); ?>",
type:'POST',
data:{prod_id:prod_id},
success:function(data){
	$('#ajax_loader_page').html(data);
	}
	});
	
$.ajax({
url:"<?php echo site_url('item_comments/counts'); ?>",
type:'POST',
data:{prod_id:prod_id},
success:function(data){
	if(data){
	var splits = data.split(',');
	$('#comment_counts').html('Comments('+splits[1]+')');
	$('#count_comm').html(splits[1]);
	if(splits[0]==0)
	$('#down_count').html('0');
	else
	$('#down_count').html(splits[0]);
}
else{
	$('#comment_counts').html('Comments(0)');
	$('#count_comm').html('0');
	$('#down_count').html('0');
}}
});

$('#whislist').click(function(){
	var wish_list_prodid =$('#product_ids').val();
	var wish_list_userid =$('#user_id').val();
	var wish_list_count = $('#wishlist_count').val();
	if(wish_list_count==0)
	{
	$.ajax({
	url:"<?php echo site_url('products/wish_list'); ?>",
	type:'POST',
	data:{wish_list_prodid:wish_list_prodid,wish_list_userid:wish_list_userid},
	success:function(data){
	$('#success_wishlist').html('Successfull Added to your Wishlist').fadeIn(6000, 'linear');
	$('.whistlistlink').hide();
      }	
		});
	}
	else {
		$.ajax({
	url:"<?php echo site_url('products/removewish_list'); ?>",
	type:'POST',
	data:{wish_list_prodid:wish_list_prodid,wish_list_userid:wish_list_userid},
	success:function(data){
	$('#success_wishlist').html('Removed from your Wishlist').fadeIn(6000, 'linear');
	$('.removewhistlistlink').hide();
      }	
		});
}
	});
	
var r_btn = $('#recommend').val();
$('#recommend').click(function(){
var user_id = $('#user_id').val();
var follower_id = $('#follower_id').val();
$.ajax({
	url:"<?php echo site_url('products/product_recommend'); ?>",
	data:{user_id:user_id,r_btn:r_btn,follower_id:follower_id},
	type:'POST',
	success:function(data){
		if(data==1){
		$('#recommend').val('<?php echo lang('Recommended');?>');}
		else{
			$('#recommend').val('<?php echo lang('Recommend');?>');}
	}	
	});
});
</script>
<script> 
$(document).ready(function(){
  $("#flip_rate").click(function(){
    $("#panel_rate").slideToggle("medium");
	 $("#panels_rate").slideToggle("medium");
	 $("#flip_rate").toggleClass('hideprice');
	 $("#flips_rate").toggleClass('hideprice');
  });
  $('#flips_rate').click(function(){
	 $("#panel_rate").slideToggle("medium");
  	 $("#panels_rate").slideToggle("medium");
	 $("#flip_rate").toggleClass('hideprice');
	 $("#flips_rate").toggleClass('hideprice');
  	});
});
</script>