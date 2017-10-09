<div class="contactus_page_con">
	<div class="contactus_page">
    <div class="container">
    <div class="row-fluid sitemap_page">
    <div class="span4">
    <div class="sitemap_padd">
    <a href="<?php echo site_url(); ?>" class="sitemap_home"><?php echo lang('home');?></a>
    <h3><?php echo lang('our_category');?></h3>
    <ul>
    <?php
     foreach($categories as $category){?>
    <li><a href="<?php echo site_url($category->slug); ?>"><?php echo $category->name;?></a></li>
    <?php }?>
   </ul>
    </div>
    </div>
    <div class="span4">
    <div class="sitemap_padd">
    <div class="group_con">
    <h3><?php echo lang('pages');?></h3>
    <ul>
    <?php foreach($pages as $page){?>
   	<?php if($page->title == 'Blog'){?>
     <li><a href="<?php echo site_url($page->url); ?>"><?php echo $page->title;?></a></li>
     <?php } elseif($page->title == 'Updates'){?>
     <li><a href="<?php echo site_url($page->url); ?>"><?php echo $page->title;?></a></li>
    <?php } else { ?>
    	<li><a href="<?php echo site_url($page->slug); ?>"><?php echo $page->title;?></a></li>
  <?php  }
  }?>
  	 </ul>
    </div>
    <div class="group_con">
    <h3><?php echo lang('forum_category');?></h3>
    <ul>
     <?php 
     foreach($forumcategories as $forumcat){?>
    <li><a href="<?php echo site_url('/forum/forum_cat/'.trim($forumcat->id)); ?>"><?php echo $forumcat->name;?></a></li>
       <?php }?>
    </ul>
    </div>
    </div>
    </div>
    <div class="span4">
    <div class="sitemap_padd">
    <div class="group_con">
    <h3><?php echo lang('blog_category');?></h3>
   <ul>
    <?php foreach($blogcategories as $blogcat){?>
    <li><a href="<?php echo site_url('blogscat/'.trim($blogcat->id)); ?>"><?php echo $blogcat->name;?></a></li>
       <?php }?>
    </ul>
    </div>
    <div class="group_con">
    <h3><?php echo lang('follow');?></h3>
     <ul>
    <li><a href="<?php echo $facebook[0]->value; ?>"><?php echo lang('facebook');?></a></li>
    <li><a href="<?php echo $twitter[0]->value; ?>"><?php echo lang('twitter');?></a></li>
    <li><a href="<?php echo $google[0]->value; ?>"><?php echo lang('google');?></a></li>
    <li><a href="<?php echo $linked[0]->value; ?>"><?php echo lang('linkedin');?></a></li>
  	 <li><a href="<?php echo $rss[0]->value; ?>"><?php echo lang('rss');?></a></li>
  	<li><a href="<?php echo $skype[0]->value; ?>"><?php echo lang('skype');?></a></li>
    </ul>
    </div>
    </div>
    </div>
    </div></div></div></div>