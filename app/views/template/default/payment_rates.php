<article class="themedetail-container">
<div class="row-fluid">
<div class="span12">
<div class="whiteconainer accountpage">
<div class="sidebarborder">
<h4><?php echo $plan->title; ?></h4>
    <div class="sidebarlist border_top paddingtop10">
    <div class="product151">
    <p class="marginbottom20">	As an Author on the Envato Marketplaces you are eligible to receive between <?php echo $plan->percentage_from.'%'; ?> and <?php echo $plan->percentage_to.'%'; ?> of every sale should you choose to sell only items that are not sold elsewhere. 
    <br>
    Being an exclusive author simply means that any items you sell on an Envato Marketplace are unique to our libraries.
    <br>
    You are entitled to sell other items elsewhere, but not the same ones. 
    <br>
    This helps keep the marketplaces healthy and unique, and ensures buyers aren’t just browsing through items they could get anywhere. So to sum up:</p>
    
    <h3>Rates Schedule</h3>
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th><?php echo lang('id');?></th>
            <th><?php echo lang('percentage')?></th>
            <th><?php echo lang('price')?></th>
        </tr>
    </thead>
    <tbody>
    
        <?php	
            foreach($payment_rates as $rates):?>
            <tr>
                <td><?php echo  $rates->id; ?></td>
                <td><?php echo  $rates->percentage; ?> % </td>
                <td> $ <?php echo $rates->from;?></td>
            </tr>
    <?php	endforeach; ?>
    </tbody>
    </table>
    </div>
    </div>
</div>
</div>
</div>
</article>