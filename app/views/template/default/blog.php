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
		<ul>
        
        <!-- All Updates News  -->
			<?php  $i=0; foreach ($blog as $blogs): $html='';?>			
				<li class="  <?php if($i%2==0){echo 'first';} $i++;?>">
				<div class="blog_list_wrap">
                <h4>
				<a href="<?php echo site_url('blogsdetail/'.trim($blogs->id)); ?>">
				<?php echo $blogs->title; ?></a>
				</h4>
                <div class="blog_list_img"><a href="<?php echo site_url('blogsdetail/'.trim($blogs->id)); ?>"><?php 
						$html=$doc->loadHTML($blogs->content);
						$path=new DOMXPath($doc);
						foreach ($path->query('//img') as $found){$img_tag = $doc->saveXML($found); echo $img_tag;  break;}?></a>
						<?php foreach ($path->query('//iframe') as $found){$img_tag = $doc->saveXML($found); echo $img_tag;  break;} ?>
						 <div class="blog_date_com"><span class="updatedate"><?php echo $blogs->date; ?></span>
						<a href="<?php echo site_url('blogsdetail/'.trim($blogs->id)).'#showcomment'; ?>"<span><?php echo count($this->Blog_model->get_comment_count($blogs->id))?><?php echo lang('comment');?></span></a></div>
						 </div>
						 <?php
				// Content short codes	  
				preg_match_all('/{([^}]*)}/', $blogs->content, $matches);
				foreach($matches[1] as $match){
					foreach($shortcodes as $codes){
						if($match==$codes->name){
						$blogs->content = str_replace('{'.$match.'}',$codes->description,$blogs->content); 
						}
					}
				}  
				//Content short codes
			?>
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
<h4><?php echo lang('categories');?> </h4>
<div class="sidebarlist border_top paddingtop10">

                <div class="content">
	<ul>
		<?php foreach ($categories as $cat){ ?>
			<li><a href="<?php echo site_url('blogscat/'.trim($cat->id)); ?>"><?php echo $cat->name;?></a></li>			
		<?php	} ?>
	</ul></div>
    </div>
            </div>
            <div class="side_border_bot"></div>
    </div>
</div>
<!-- Blog Categories -->
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
<!-- Blog Categories -->
</div>
</div>
            </div>
            <div class="side_border_bot"></div>
</div>
</div>
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
<!-- Blog Tags --></div>
</div>
            </div>
            <div class="side_border_bot"></div>
</div>
</div>
	</div>
</div>