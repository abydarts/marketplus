<article class="themedetail-container">
<div class="row-fluid">

<div class="span3">	
<?php include('order_details.php');?>
</div>
<div class="span9">
 <div class="whiteconainer accountpage">
 <div class="sidebarborder">
	<h4><?php echo lang('form_checkout');?></small></h4>
    <div class="border_top padd_top10">
<?php include('summary.php');?>

<div class="clearfix">
		<a class="btn btn-primary pull-right" href="<?php echo site_url('checkout/place_order');?>"><?php echo lang('submit_order');?></a>
</div>
<div class="clear"></div>
</div></div>
</div>
</div>
</div>
</article>