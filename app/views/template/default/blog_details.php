<div class="clstheme category_title">
<div class="container">
	<div class="title_inner">
	<h2><?php echo lang('blog');?></h2> 
    </div>
 </div>
	</div>  
    <div class="cross_border"></div>
		<div class="container">	
            <div class="row-fluid news_update_list" style="margin:50px 0;">
<div class="span9">
<!-- All Updates News  -->
		<ul> 	
				<li>
				<div class="blog_list_wrap">
                <div class="whiteconainer1">
<div class="sidebarborder1">
				<h4><?php echo $blog->title; ?></h4>
                <div class="sidebarlist border_top1 paddingtop10">
                <span class="updatedate"><?php echo $blog->date; ?></span>
            <?php
            // Content short codes	  
				preg_match_all('/{([^}]*)}/', $blog->content, $matches);
				foreach($matches[1] as $match){
					foreach($shortcodes as $codes){
						if($match==$codes->name){
						$blog->content = str_replace('{'.$match.'}',$codes->description,$blog->content); 
						}
					}
				}  
				//Content short codes
				?>
			 	<div class="blog_list_con"><?php echo $blog->content; ?></div>	
                </div></div></div> 
				</div> 
				</li>  
		</ul>
        <div class="whiteconainer">
<div class="sidebarborder">
<h4 id="showcomment">Comments</h4>
<div class="sidebarlist border_top paddingtop10">
        <input type="hidden" id="product_ids" value="<?php echo $blog->id;?>" />
						<div id="ajax_loader_page">
						<div id="loading_img"><img src="<?php echo theme_img('loading.gif');?>" alt="" ></div>
						</div>
                        </div></div></div>
		</div>
<!-- All Updates News  -->
<div class="span3 sidebar rightside">
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
<!-- Blog Categories -->
<h4><?php echo lang('categories');?> </h4>
<div class="sidebarlist border_top paddingtop10">
<div class="content">
<ul>
<?php foreach ($categories as $cat){ ?>
<li><a href="<?php echo site_url('blogscat/'.trim($cat->id)); ?>"><?php echo $cat->name;?></a></li>			
<?php	} ?>
</ul>
<!-- Blog Categories --></div></div></div></div></div>
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
<!-- Blog Recent Post -->
<h4><?php echo lang('recent_post');?> </h4>
<div class="sidebarlist border_top paddingtop10">
                <div class="content">
	<ul>
		<?php $j=1; ?>
		<?php foreach ($recent as $recent_blogs){ ?>
		<?php if($j<=5){ ?>
			<li><a href="<?php echo site_url('blogsdetail/'.trim($recent_blogs->id)); ?>"><?php echo $recent_blogs->title;?></a></li>			
		<?php	} 
		$j++;
		}?> 
	</ul>
<!-- Blog Categories --></div></div></div></div></div>
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
<!-- Blog Tags -->
<h4><?php echo lang('tags');?> </h4>
<div class="sidebarlist border_top paddingtop10">
                <div class="content">
	<ul> 
		<?php foreach ($tags as $tag){ ?>
			<li><a href="<?php echo site_url('blogsdetail/'.$tag->id); ?>"><?php echo $tag->tags;?></a></li>			
		<?php	}  ?>
	</ul>
<!-- Blog Tags --></div></div></div></div></div>
</div>
</div>
</div>
						
<script type="text/javascript" >
var prod_id = $('#product_ids').val();
$.ajax({
url:"<?php echo site_url('blogs/blog_ajax'); ?>",
type:'POST',
data:{prod_id:prod_id},
success:function(data){
	$('#ajax_loader_page').html(data);
	$("#popcomm").click(function(){

var pathname = window.location.pathname;
register = pathname.substring(pathname.lastIndexOf("/") + 1);

if(register != 'login' && register != 'register'){
$('#overlay_form').load("<?php echo site_url('user/login_popup/');?>",function(){
$("#close").click(function(){
$("#overlay_forms").fadeOut(500);
});	
});
$('#overlay_forms').fadeIn(1000);

}

});
	}
	});
</script>