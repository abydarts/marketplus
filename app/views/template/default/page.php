<article class="themedetail-container">
<div class="row-fluid">
<div class="span12">
<div class="whiteconainer accountpage static-page">
<?php echo html_entity_decode($page->content); ?>

<div class="page_content container clearfix">
<div class="row-fluid">
<?php 
$count = 0;
foreach($contents as $contents):
?>
<?php if($contents->column == 1){$count++; if($count==1){echo '<div class="onecolumn clearfix">';} echo '<div class="span12">';}
elseif($contents->column == 2){$count++; if($count==1){echo '<div class="twocolumn clearfix">';} echo '<div class="span6">';}
else {$count++; if($count==1){echo '<div class="threecolumn clearfix">';}echo '<div class="span4">';} 
?>

<h4><?php echo $contents->conten_title;?></h4>
 <?php echo $contents->contents;?> 
</div>
<?php if($contents->column == $count){ echo '</div>';$count=0;}?>

<?php endforeach; ?>
 </div>
 </div>
</div>
</div>
</div>
</article>