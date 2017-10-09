<div class="templatelist">
<div class="maincontent_bg">
<div class="main_content_shadow">
<div class="container">
<div class="clsboxes">
<div class="tabslidercontainer">
	<div class="flexslider flextab">
    <ul class="table table-striped head slides">
			<li><h3><span class="border_gray"><span class="border_red"><?php echo lang('new items');?></span></span><div class="activeslidearrow"></div></h3></li>
            <li id = "popular_ajax"><h3><span class="border_gray"><span class="border_red"><?php echo lang('popular items');?></span></span><div class="activeslidearrow"></div></h3></li>           
	    </ul>
        </div>
</div>
<div class="tabsortingoptions border_gray">
<div class="border_red clearfix">
            <ul class="filter clearfix">
				<?php foreach($this->categories as $cat_menu):?>
                <li>
						<label class="place" for="checkbox<?php echo $cat_menu['category']->id;?>">
						<input type="checkbox" class="get_categories" value="<?php echo $cat_menu['category']->id;?>" id="checkbox<?php echo $cat_menu['category']->id;?>"/> 
                        <?php echo $cat_menu['category']->name;?>
						</label>
                </li>
				<?php endforeach;?>
</ul></div>
</div>
        <div class="flexslidertab">
			<ul class="table tab_content slides clearfix">
            <li>
            <div class="masonary-container">
            <ul id="new_products" class="clearfix pop_up_show">
					
					<?php foreach($new_products as $new):
			$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product	= json_decode($new->images);
					$primaryphoto = $logophoto = false;
				if($product){
					foreach($product as $photo)
					{
						
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($new->user_id);
							if(isset($username->user_firstname)){
								$userfirstname = $username->user_firstname;
							}
							else{$userfirstname = '';}
							$photo	= '<div class="items_div">
							<a href="'.site_url($new->slug).'">
							<img src="'.base_url('uploads/images/full/'.$logo->filename).'" alt="'.$new->seo_title.'"/>			
							</a><p>'.$new->name.'</p>
							<span> by '.$userfirstname.'</span>
							<p>$ '.format_currency($new->price).'</p>
							<p>'.$new->date_created.'</p></div>';
							
							$cat_show =  $this->Product_model->get_product_categorie($new->id)->name;
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$new->name.'#,'.$userfirstname.'#,'.format_currency($new->price).'#,'.$cat_show.'#,'.$new->date_created;
						}
						if(isset($photo->logo))
						{
							$logophoto = true;
							if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($new->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);
							$smallimagealt=$new->seo_title;
						}
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<li class="new_products items"><a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'" largeimage="'.$largeimageurl.'" alt="'.$smallimagealt.'"/>
							</a></li>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}}
					endforeach; ?>

					</ul>
             </div></li>
             <li><div class="masonary-container">
				<ul id="pop_products" class="clearfix pop_up_show" style="display:none">
										
	<?php foreach($popular_products as $popular):
				$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product	= json_decode($popular->images);
					$primaryphoto = $logophoto = false;
					foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($popular->user_id);
							if(isset($username->user_firstname)){
								$userfirstname = $username->user_firstname;
							}
							else{$userfirstname = '';}
							$photo	= '<div class="items_div">
							<a href="'.site_url($popular->slug).'">
							<img src="'.base_url('uploads/images/full/'.$logo->filename).'" alt="'.$popular->seo_title.'"/>			
							</a><p>'.$popular->name.'</p>
							<span> by '.$userfirstname.'</span>
							<p>$ '.$popular->price.'</p>
							<p>'.$popular->c.' Sales</p></div>';
							
							$cat_show =  $this->Product_model->get_product_categorie($popular->id)->name;
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$popular->name.'#,'.$userfirstname.'#,'.format_currency($popular->price).'#,'.$cat_show.'#,'.$popular->c.' Sales';
						}
						if(isset($photo->logo))
						{
							$logophoto = true;
							if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($popular->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);
							$smallimagealt=$popular->seo_title;
						}
						
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<li class="pop_products items"><a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'" largeimage="'.$largeimageurl.'" alt="'.$smallimagealt.'"/>
							</a></li>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}
					endforeach; ?>


					</ul>
                    </div></li>
            </ul>
            </div>
            </div>
</div>
</div>
</div>

<script type="text/javascript">
$(".place").live("click",function () {
   $(this).toggleClass("green");
});
</script>
<script type="text/javascript">
$('.get_categories').change(function(){
	var myarray = {};
	$('input:checkbox.get_categories').each(function (category_id) {
       myarray[category_id] = (this.checked ? $(this).val(): "");
  	 });
	 	 
 
 	$.ajax({
	url:"<?php echo site_url('home/products'); ?>",
	type:'POST',
	data:{myarray:myarray},
	success:function(data){
		
	$("#new_products").fadeOut('fast',function(){
	    $(this).html(data);
$('.items a img').tooltip({ 
    delay: 0, 
    showURL: false, 
    bodyHandler: function() { 
	largeimage = $(this).attr('largeimage');
	allvalues = largeimage.split("#,");
	finalimage = '<img src="'+allvalues[0]+'" alt="Image" width="470" height="240" />';
	if(typeof allvalues[1] != "undefined" || typeof allvalues[2] != "undefined" || typeof allvalues[3] != "undefined" || typeof allvalues[4] != "undefined"){
	finalimage += '<div class="largeimagecontainer row-fluid"><h4>'+allvalues[1]+'</h4>';
	finalimage += '<div class="largeimageleftcon span6"><p>'+allvalues[2]+'</p><p>'+allvalues[4]+'</p></div><div class="largeimagerightcon span6"><p class="price"><span class="currency_symbol"><span class="cur_sym">$</span><span class="price_int">'+allvalues[3]+'</span></span></p></div></div>';
		}
        return finalimage; 
    } 
});
	  
	}).fadeIn("slow");
	}
	});

	$.ajax({
	url:"<?php echo site_url('home/pop_products'); ?>",
	type:'POST',
	data:{myarray:myarray},
	success:function(data){
		$("#pop_products").fadeOut('fast',function(){
	    $(this).html(data);
$('.items a img').tooltip({ 
    delay: 0, 
    showURL: false, 
    bodyHandler: function() { 
	largeimage = $(this).attr('largeimage');
	allvalues = largeimage.split("#,");
	finalimage = '<img src="'+allvalues[0]+'" alt="Image" width="470" height="240" />';
	if(typeof allvalues[1] != "undefined" || typeof allvalues[2] != "undefined" || typeof allvalues[3] != "undefined" || typeof allvalues[4] != "undefined"){
		finalimage += '<div class="largeimagecontainer row-fluid"><h4>'+allvalues[1]+'</h4>';
	finalimage += '<div class="largeimageleftcon span6"><p>'+allvalues[2]+'</p><p>'+allvalues[4]+'</p></div><div class="largeimagerightcon span6"><p class="price"><span class="currency_symbol"><span class="cur_sym">$</span><span class="price_int">'+allvalues[3]+'</span></span></p></div></div>';
		}
        return finalimage; 
    } 
});

	}).fadeIn("slow");
	}
	});
	});
</script>

<div class="container">
<div class="staticlist">
<div class="row-fluid">
<div class="span4">
<div class="top-static">
<a class="sidebarlinks blog_tit" id="recent_top" href="javascript:void(0);">Latest</a>
<a id="top" class="sidebarlinks blog_tit" href="javascript:void(0);" style="display:none;">Featured</a>
<div class="whiteconainer top_products">
<h3><?php echo 'Top Products';?><div class="activegrayarrow"></div></h3>
	<ul class="slistmain clearfix">
	<?php
	$i=1;
	$position_count = count($product_position);

	foreach($top_products as $prod){
	?>
		<li class="products_lists">
		<div class="slist clearfix">
		<div class="slist-img pop_up_show clsFloatLeft items">
		<?php 		
			$photo	= theme_img('no_picture.png', lang('no_image_available'));
			$product	= json_decode($prod->images);
			@$items .= $prod->id.',';
			$primaryphoto = $logophoto = false;
			if($product){
			foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($prod->user_id);
							$photo	= '<div class="items_div">
							<img src="'.base_url('uploads/images/full/'.$logo->filename).'" alt="'.$new->seo_title.'"/>			
							<p>'.$prod->name.'</p>
							<span> by '.$username->user_firstname.'</span>
							<p>$ '.format_currency($prod->price).'</p></div>';
														
							$cat_show =  $this->Product_model->get_product_categorie($prod->id)->name;
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$prod->name.'#,'.$username->user_firstname.'#,'.$prod->price.'#,'.$cat_show.'#,'.intval(($prod->c)/$position_count).' Sales';
							}
						if(isset($photo->logo))
						{
						$logophoto = true;
						if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($new->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);
							$smallimagealt=$new->seo_title;
							
						}
						if($primaryphoto == true && $logophoto == true)
						{	
							$smallimageurl=site_url($prod->slug);
							$finalphoto	= '<a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'"  width=60 height=60 largeimage="'.$largeimageurl.'" alt="'.$smallimagealt.'"/>
							</a>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}		
			}
		 ?>
		
		</div>
		<div class="slist-content clsFloatLeft">
		<h5><?php echo $prod->name; ?></h5>
		<p class="count"><?php echo lang('no of sales');?>: <span><?php echo intval(($prod->c)/$position_count); ?></span></p>
		<p class="viewlink"><a href="<?php echo site_url($prod->slug); ?>"><?php echo lang('view work');?></a></p>
		</div>
		<div class="slist-rating clsFloatRight"><div class="count"><?php echo intval(($prod->c)/$position_count); ?></div>
		<div class="like">
		<?php if($i % 2 == 1){?>
		<img src="<?php echo theme_img('icons/like.png');?>" />
		<?php }else { ?>
			<img src="<?php echo theme_img('icons/dislike.png');?>" />
		<?php }?>
		</div></div>
		<div class="clear"></div>
		</div>
		</li>
	<?php $i++;  } ?> 
	<input type="hidden" class="product_lists" value="<?php echo @$items; ?>" />
	
	</ul>
<p class="more"><a href="<?php echo site_url('TopProducts') ?>"><?php echo lang('see more top products');?> »</a> </p>
</div>
</div>
</div>

<script type="text/javascript">
$("#top").click(function(){
$('.top_products').fadeOut().fadeIn('slow').load('<?php echo site_url('home/top_prod') ?>');
$('#top').hide();
$('#recent_top').show();
});
$("#recent_top").click(function(){
$('.top_products').fadeOut().fadeIn('slow').load('<?php echo site_url('home/recenttop') ?>');
$('#recent_top').hide();
$('#top').show();
});
</script>

<div class="span4">
	<div class="top-static">
	<a class="sidebarlinks blog_tit" href="javascript:void(0);" id="recent_top_author">Latest</a>
    <a class="sidebarlinks blog_tit" href="javascript:void(0);" style="display: none;" id="top_author">Featured</a>
		<div class="whiteconainer top_authors"><h3><?php echo 'TOP AUTHORS';?><div class="activegrayarrow"></div></h3>
			<ul class="slistmain clearfix">
				
			<?php
			$array_customer = $customers;
					
			$previousValue = null;
			$count =0;
			foreach($array_customer as $customers){				
			$count++;
			if($count > 5){ break; }
			$id = $customers->user_id;
			if($previousValue != $id) {
			?>
			<li>
				<div class="slist clearfix">
				<div class="slist-img clsFloatLeft"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>">
				<?php if(!empty($customers->avatar)) { ?>
						<img width="60" height="60" alt="<?php echo $customers->user_firstname?>" id="profileImage" src="<?php echo base_url('uploads/profile/'.$customers->avatar) ?>" >
			   <?php } else { ?>
			   		<img width="60" height="60" alt="" id="profileImage" src="<?php echo theme_img('avatar.png') ?>" >			
				<?php } ?>
				</a></div>
				<div class="slist-content clsFloatLeft">
				<h5><?php
				echo $customers->user_firstname.' '.$customers->user_lastname; ?></h5>
				<p class="count"><?php echo lang('No of Items');?>: <span><?php
				$useritem = $this->Customer_model->get_user_items($id);
	 			if(count($useritem) != 0)
				{ echo count($useritem);  }
				else
				{ echo '0'; } ?></span></p>
				<p class="viewlink"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>"><?php echo lang('View Portfolio');?></a></p>
				</div>
				<div class="slist-rating clsFloatRight"><div class="count"><?php
				$usersale = $this->Customer_model->get_salecount($id);
				$sale_count =  count($usersale);
 			if($sale_count == 0){?>
			<p><?php echo '0'; ?></p>
			<?php }
			else{?>
			<p><?php echo $sale_count; ?></p>			
			<?php }
			?>		</div><div class="like"><a href="#"><img src="<?php echo theme_img('icons/like.png');?>" /></a></div></div>
				<div class="clear"></div>
				</div>
				</li>
				<?php $previousValue = $customers->user_id; } }?>
			</ul>
			<p class="more"><a href="<?php echo site_url('TopAuthors'); ?>"><?php echo lang('See more Top Authors');?> »</a> </p>
		</div>
	</div>
</div>

<script type="text/javascript">
$("#top_author").click(function(){
$('.top_authors').fadeOut().fadeIn('slow').load('<?php echo site_url('home/topauth') ?>');
$('#top_author').hide();
$('#recent_top_author').show();
});
$("#recent_top_author").click(function(){
$('.top_authors').fadeOut().fadeIn('slow').load('<?php echo site_url('home/recent_topauth') ?>');
$('#recent_top_author').hide();
$('#top_author').show();
});
</script>

<!-- Blogs Block  -->
		<div class="span4 bloglistspan">
        <div class="top-static blog_home_page_list">
<div class="whiteconainer">
			<h3><?php echo 'From Our Blog';?><div class="activegrayarrow"></div></h3>
			<ul>
				<?php  
			$i=0;
			foreach ($blogs as $blog): ?>				 
			<?php if($i<5){?>
				<li>
                <div class="slist clearfix">
                <div class="slist-content">
                <a href="<?php echo site_url('/blogsdetail/'.$blog->id); ?>"><span class="blog_tit"><?php echo substr($blog->title,0,50); ?></span></a>
					<p><?php echo substr(strip_tags($blog->content),0,100); ?>... <span class="updatedate"><?php echo $blog->date; ?></span></p>
</div></div></li> 
			<?php } ?>
			<?php 
			$i++;
			endforeach; ?> 
			</ul>
            <p class="more">
<a href="<?php echo site_url('/blogs'); ?>">See more Blogs »</a>
</p>
            </div></div>
		</div>
		<!-- Blogs Block  -->
      
</div>
</div>
</div>
<div class="maincontent_bg bor_bot_none">
<div class="main_content_shadow">
<div class="container">
<div class="newsletterinner">
<form method="POST" action="<?php echo site_url('home') ?>">
<div class="formrow clearfix">
<label>Join our newsletter for <span>New Projects, Updates or Releases.</span></label>
<div class="span6">
<input type="email" name="email" id="email" class="formrowleft" placeholder="Enter your email address" onBlur="if(this.value==''){this.value=this.defaultValue;}" onFocus="if(this.value==this.defaultValue){ this.value='';}"/>
<div class="submit_btn_bor">
<input type="button" id="submit" name="subscribe" class="formrowright" value="<?php echo lang('subscribe');?>"/>
</div>
 <p id="msg"></p>
</div>
<div class="clear"></div>
</div>
</form>

<script type="text/javascript" >
$(document).ready(function(){
	$('#submit').click(function(){
	var semail = $('#email').val();	
			$.ajax({ 
			url:'<?php echo site_url('/home/subscribe');?>',
			type:'POST',
			data:{email:semail},
			success:function(data){	 
			var datas = $.trim(data);
			if(datas=='success'){
				$('#msg').html('Sucessfully subscribed').css('color','green').delay(5000).fadeOut('slow');}
			else{
				$('#msg').fadeIn('fast').html(data).css('color','red').delay(2000).fadeOut('slow');}
		}		
		});

	}); 
});
</script>
</div>
</div></div></div>

<script type="text/javascript" >
var d = new Date();
var day = d.getDay();

if(day==1){
var product_position = $('.product_lists').val();

$.ajax({
url:"<?php echo site_url('home/product_position'); ?>",
	data:{product_position:product_position},
	type:'POST',
	success:function(data){
			}		
	});
}
</script>
<script>
$('#popular_ajax').click(function(){
$('#pop_products').show();
return false;
});
</script>
</div>