<div class="clstheme category_title">
<div class="container">
	<div class="title_inner">
	<h2><?php echo lang('news_updates');?></h2> 
    </div>
 </div>
	</div>  
    <div class="cross_border"></div>
<div class="row1 news_update_list" style="margin:50px 0;">
	<div class="container">
<!-- All Updates News  -->
		<ul class="row-fluid">
			<?php  $i=0; foreach ($updates as $update):  $html='';?>			
				<li class="span6 <?php if($i%2==0){echo 'first';} $i++;?>">
				<div class="blog_list_wrap">
				<h4>
				<a href="<?php echo site_url('/updates/'.trim($update->id)); ?>">
				<?php echo  substr($update->title,0,38).'...'; ?></a>
				</h4> 
				<span class="updatedate"><?php echo $update->date; ?></span>
				<?php
				// Content short codes	  
				preg_match_all('/{([^}]*)}/', $update->content, $matches);
				foreach($matches[1] as $match){
					foreach($shortcodes as $codes){
						if($match==$codes->name){
						$update->content = str_replace('{'.$match.'}',$codes->description,$update->content); 
						}
					}
				}  
				//Content short codes
			?>
				<div class="blog_list_con"><?php echo substr(strip_tags($update->content),0,350); ?><a class="more" href="<?php echo site_url('/updates/'.trim($update->id)); ?>">Read more...</a></div>		
				</div> 
				</li>
			<?php endforeach; ?> 
		</ul>	 
<!-- All Updates News  -->
	</div>
</div>