<?php echo theme_css('index.css', true);?>
<?php echo theme_js('js_quiz.js', true);?>
<?php echo theme_js('jQuiz.js', true);?>
<div class="themedetail-container">
<div class="row-fluid">
<div class=span9>
    <div class="whiteconainer accountpage">
    <div class="sidebarborder">
    <h4><?php echo lang('Quiz');?></h4>
    <div class="border_top paddingtop10">
    <div class="">
	<form accept-charset="utf-8" method="post" action="<?php echo site_url('quiz/check');?>">
    <h3><?php echo lang('Status Bar');?></h3>
    <div id="progressKeeper" class="radius">
        <div id="progress"></div>
    </div>
				<p style="color:red;font-weight:bold;"></p>
				<?php $i=1; $last = sizeof($quiz);
					echo '<input type="hidden" id="total_question" value="'.$last.'" />';				
				 foreach($quiz as $quiz){
					if($i ==1)
					echo '<div class="questionContainer radius"><p style="float:right;color:red;">'.$i.'/'.$last.'</p>';
					else
					echo '<div class="questionContainer hide radius"><p style="float:right;color:red">'.$i.'/'.$last.'</p>';
					
					echo '<div class="question">'.$i.') '.$quiz->question.'</div>';
					echo '<input type="hidden" value="'.$quiz->id.'" name="question_'.$i.'">
					<div class="answers">
	            	<ul> ';
						$quiz_ans = explode(',', $quiz->answer);					
						$j=1;					
							foreach($quiz_ans as $ans){
							echo '<li><label><input type="radio" value="'.$j.'" name="answer_'.$i.'"> ' .$ans;'</label></li>';
								$j++;
							}
						 echo '</ul>
            	</div>
            	
            	<div>
	                <div class="next">';
	                if($i == $last){
	                   echo '<input type="submit" class="productbtn" value="'.lang("Next").'" name="submit">';}
	                else{
	                     echo '<a class="btnNext productbtn">'.lang("Next").'</a>';}
	                echo '</div>
       			 </div>
       			 </div>';
					$i++;
					}?>
		</form>
</div>
</div>
    </div>
    <div class="side_border_bot"></div>
</div>

</div>
<?php include('user/user_sidebar.php'); ?>
</div>
</div>