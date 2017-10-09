<?php
function timeBetween($start,$end){
    	$time = $end - $start;
    
    	if($time <= 60){
    		return 'one moment ago';
    	}
    	if(60 < $time && $time <= 3600){
    		return round($time/60,0).' minutes ago';
    	}
    	if(3600 < $time && $time <= 86400){
    		return round($time/3600,0).' hours ago';
    	}
    	if(86400 < $time && $time <= 604800){
    		return round($time/86400,0).' days ago';
    	}
    	if(604800 < $time && $time <= 2592000){
    		return round($time/604800,0).' weeks ago';
    	}
    	if(2592000 < $time && $time <= 29030400){
    		return round($time/2592000,0).' months ago';
    	}
    	if($time > 29030400){
    		return date('M d y at h:i A',$start);
    	}
    }   
    
    $page_id = 1;
    
?>
<?php echo theme_js('comm/tipsy.js', true);?>
<?php echo theme_js('comm/comments.js', true);?>
<?php echo theme_js('comm/count_down.js', true);?>

<?php echo theme_css('com/page.css', true);?>
<?php echo theme_css('com/tipsy.css', true);?>
    <script type="text/javascript">
        $(function() {
        	$('.tip').tipsy({ gravity: 'e' }); 
        });     
    </script>    

<div id="page" class="clearfix">

<div class="comments clearfix">
    <?php
    	foreach ($query as $comment) {
    ?>
        <div class="post_comment_con marbot15">
            <div class="left tip  posted_avator" title="<?php echo $comment->firstname.' '.$comment->lastname;?> Said">
                <img width="60" height="60" class="avatar" src="<?php echo base_url('uploads/profile/'.$comment->image) ?>" />
            </div>
            <div class="left posted_comment">
            <?php echo nl2br($comment->comment);?>
            <div class="details small">
            <span class="comment_name"><?php echo $comment->firstname;?></span>
                <span class="comment_time"><?php echo timeBetween($comment->time,time());?></span> 
            </div>
            <div class="comment_arrow"></div>
            </div>
            <div class="clear"></div>
        </div>
    <?php
    }
    ?>
</div>
<div class="add_comment">
    <div class="write shadow comment">
        <p class="left avatarcomment">
<?php $user_image = $this->bse_tec->customer();
$image = $user_image['avatar']; ?>
            <?php if($image==''){ ?>
				<img src="<?php echo base_url('uploads/profile').'/users.png';?>" width="60" height="60" style="border:1px solid #333; margin-right:5px; padding:2px;"/>
				<?php } else { 
				$avatar=base_url('uploads/profile').'/'.$image; ?>
				<img src="<?php echo $avatar;?>" width="60" height="60" style="border:1px solid #333; margin-right:5px; padding:2px;"/>
				<?php } ?>	
        </p>
        <p class="textarea">
            <textarea class=""></textarea>
            <input class="login_btn" value="<?php echo lang('SEND');?>" type="submit" />

				<input id="path_val" value="<?php echo site_url('blogs/ajax'); ?>" type="hidden" />
        </p>
    </div>
    <?php if($this->bse_tec->customer()){ ?>
    <a  class="btn" onclick="$(this).add_comment(<?php echo $page_id;?>);return false;" href="#"><?php echo lang('Add Comment');?></a>
    <?php } else { ?>
    <a class="btn" id="pop" href="<?php echo site_url('register/1'); ?>"><?php echo lang('login');?></a>
    <?php  }?>
</div>
</div>