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
<?php echo theme_css('redactor.css', true);?>
<?php echo theme_js('redactor.js', true);?>
<?php echo theme_js('redactor.min.js', true);?>

<?php echo theme_css('com/page.css', true);?>
<?php echo theme_css('com/tipsy.css', true);?>
    <script type="text/javascript">
        $(function() {
        	$('.tip').tipsy({ gravity: 'e' }); 
        });     
    </script>    
<script type="text/javascript">
$(document).ready(function(){

	$('.redactor').redactor({ 
            imageUpload: "<?php echo site_url('home/redactor_upload'); ?>"
        });

});
</script>

<div id="page" class="clearfix">

<div class="comments">
    <?php
    	foreach ($query as $comment) {
    ?>
        <div class="post_comment_con marbot16">
            <div class="left tip posted_avator" title="<?php echo $comment->user_firstname.' '.$comment->user_lastname;?> Said">
               <a href="<?php echo site_url('profile').'/'.$comment->user_id ?>"> <img class="avatar" src="<?php echo base_url('uploads/profile/'.$comment->avatar) ?>" width="60" height="60" />
            </div></a>
            <div class="left posted_comment">
            <?php echo nl2br($comment->comment);?>
            <div class="details small">
                <span class="comment_name"><?php echo $comment->user_firstname;?></span> 
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
    <?php $users = $this->bse_tec->customer() ?>
        <p class="left avatarcomment">
            <img class="avatar" src="<?php echo base_url('uploads/profile/'.$users['avatar']) ?>" width="60" height="60"  />
        </p>
        <p class="textarea left">
            <textarea class="left"></textarea>
            <input class="left" value="<?php echo 'SEND';?>" type="submit" />

				<input id="path_val" value="<?php echo site_url('item_comments/ajax'); ?>" type="hidden" />
        </p>
    </div>
        <?php if($this->bse_tec->customer()){ ?>
    <a onclick="$(this).add_comment(<?php echo $page_id;?>);return false;" class="btn" href="#"><?php echo lang('Add Comment');?></a>
    <?php } else { ?>
    <a class="btn" id="popcomm"><?php echo lang('login');?></a>
    <?php  }?>
    </div>
</div>