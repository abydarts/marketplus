<?php libxml_use_internal_errors(true);
$doc=new DOMDocument;
?>
<div class="clstheme category_title">
<div class="container">
 </div>
	</div>  
    <div class="cross_border"></div>
    <div class="container">
<div class="row-fluid news_update_list" style="margin:50px 0;">
<div class="span9">
<!-- All Updates News  -->
		<ul class="row-fluid">
		<?php if(count($blog)==0){
echo lang('no_blogs');
}?>
			<?php  $i=0; foreach ($blog as $blogs): $html='';?>			
				<li class="spa_n6  <?php if($i%2==0){echo 'first';} $i++;?>">
				<div class="blog_list_wrap">
                <h4>
				<a href="<?php echo site_url('/blogsdetail/'.trim($blogs->id)); ?>">
				<?php echo $blogs->title; ?></a>
				</h4>
                <div class="blog_list_img"><a href="<?php echo site_url('/blogsdetail/'.trim($blogs->id)); ?>"><?php 
				$html=$doc->loadHTML($blogs->content);
$path=new DOMXPath($doc);
foreach ($path->query('//img') as $found){
    $img_tag = $doc->saveXML($found); echo $img_tag;  break;}?></a></div>
				<span class="updatedate"><?php echo $blogs->date; ?></span>
				<div class="blog_list_con"><?php echo substr(strip_tags($blogs->content),0,350); ?><a class="more" href="<?php echo site_url('/blogsdetail/'.trim($blogs->id)); ?>">Read more...</a></div>		
				</div> 
				</li>
			<?php endforeach; ?> 
		</ul>	 
<!-- All Updates News  -->
</div>
<div class="span3 sidebar rightside">
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
<!-- Blog Categories -->
<h4><?php echo lang('categories');?> </h4>
<div class="sidebarlist border_top paddingtop10">
<div class="content">
	<ul>
	<?php if(count($categories)==0){
	echo lang('no_categories');
	}?>
		<?php foreach ($categories as $cat){ ?>
			<li><a href="<?php echo site_url('blogscat/'.trim($cat->id)); ?>"><?php echo $cat->name;?></a></li>			
		<?php	} ?>
	</ul></div></div></div></div></div>
<!-- Blog Categories -->

<!-- Blog Recent Post -->
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
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
	</ul></div></div></div></div></div>
<!-- Blog Categories -->
<!-- Blog Tags -->
<div class="blog-categories">
<div class="whiteconainer">
<div class="sidebarborder">
<h4><?php echo lang('tags');?> </h4>
<div class="sidebarlist border_top paddingtop10">
                <div class="content">
	<ul> 
		<?php foreach ($tags as $tag){ ?>
			<li><a href="<?php echo site_url('blogsdetail/'.$tag->id); ?>"><?php echo $tag->tags;?></a></li>			
		<?php	}  ?>
	</ul>
<!-- Blog Tags -->
	</div></div></div></div></div>
    </div>
</div>