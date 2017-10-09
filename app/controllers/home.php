<?php

class Home extends Front_Controller {

	function __construct()
	{
		parent::__construct();
		$this->config->set_item('language',$this->auth->language());
		//make sure we're not always behind ssl
		$this->lang->load('common');
	}

	function index()
	{
		$this->load->helper('directory');
		$this->load->model(array('Banner_model','order_model','Blog_model','Category_model','Product_model'));
		$this->load->helper('directory');
		
		if($this->input->post('email')){
			$email = $this->input->post('email');
			$f_name = $this->input->post('firstname');
			$l_name = $this->input->post('lastname');
			
			$email_subs = array(
			'first_name' => $f_name,
			'last_name' => $l_name,
		   'email' => $email);
			$this->db->insert('newsletter', $email_subs); 
			$this->session->set_flashdata('newsletter', 'Thanks for subscription');
			redirect('home');
			}
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$data['banners']			= $this->Banner_model->get_homepage_banners(5);
		
		$data['top_products']	= $this->Product_model->get_top_products();
		$data['recent_top_products']	= $this->Product_model->get_recent_top_products();
		$data['customers']		= $this->Customer_model->get_customers(5,$offset=0, $order_by='recommend',$direction='DESC');
		$data['recent_customers']		= $this->Customer_model->get_recent_customers(5,$offset=0, $order_by='recommend',$direction='DESC');
		$data['new_products']			= $this->Product_model->new_products();
		$data['popular_products']		= $this->Product_model->popular_products();
		$data['new_authors']			= $this->Customer_model->new_authors(5);
		$data['product_position']		= $this->Product_model->get_product_position_count();
		$data['blogs']				= $this->Blog_model->get_homepage_blogs(); // blog updates on themes/views
		$data['homepage']			= true;
		$this->load->helper('directory');
		$data['body_content']			= 'homepage';
		$this->load->view('template', $data);
	}
	
	function products(){
		$cat = $this->input->post('myarray');
		foreach($cat as $catss){
		$categories['category_id'] = $catss;
		}
		$cats = $this->Product_model->new_products($cat);
		foreach($cats as $new):
				$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product	= json_decode($new->images);
					$primaryphoto = $logophoto = false;
					foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($new->user_id);
							$photo	= '<div class="items_div">
							<a href="'.site_url($new->slug).'">
							<img src="'.base_url('uploads/images/medium/'.$logo->filename).'" alt="'.$new->seo_title.'"/>			
							</a><p>'.$new->name.'</p>
							<span> by '.$username->user_firstname.'</span>
							<p>$ '.$new->price.'</p>
							<p>'.$new->date_created.'</p></div>';
							$cat_show =  $this->Product_model->get_product_categorie($new->id)->name;
							$largeimageurl = base_url('uploads/images/medium/'.$logo->filename).'#,'.$new->name.'#,'.$username->user_firstname.'#,'.$new->price.'#,'.$cat_show.'#,'.$new->date_created;
						}
						if(isset($photo->logo))
						{
							$logophoto = true;
							if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($new->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);
							$smallimagealt=$new->seo_title;				
						}
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<li class="new_products items"><a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'" largeimage="'.$largeimageurl.'" alt="'.$smallimagealt.'"/>
							</a></li>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}	
					endforeach;	
		}
		
		
	function pop_products(){
		$cat = $this->input->post('myarray');
		foreach($cat as $catss){
		$categories['category_id'] = $catss;
		}
		$catg = $this->Product_model->popular_products($cat);
		foreach($catg as $popular):
				$photo	= theme_img('no_picture.png', lang('no_image_available'));
				$product	= json_decode($popular->images);
					$primaryphoto = $logophoto = false;
					foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($popular->user_id);
							$photo	= '<div class="items_div">
							<a href="'.site_url($popular->slug).'">
							<img src="'.base_url('uploads/images/medium/'.$logo->filename).'" alt="'.$popular->seo_title.'"/>			
							</a><p>'.$popular->name.'</p>
							<span> by '.$username->user_firstname.'</span>
							<p>$ '.$popular->price.'</p>
							<p>'.$popular->c.' Sales</p></div>';
							$cat_show =  $this->Product_model->get_product_categorie($popular->id)->name;
							$largeimageurl = base_url('uploads/images/medium/'.$logo->filename).'#,'.$popular->name.'#,'.$username->user_firstname.'#,'.$popular->price.'#,'.$cat_show.'#,'.$popular->c;
						}

						if(isset($photo->logo))
						{
							$logophoto = true;
							if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($popular->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);
							$smallimagealt=$popular->seo_title;				
						}
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<li class="pop_products items"><a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'" largeimage="'.$largeimageurl.'" alt="'.$smallimagealt.'"/>
							</a></li>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}
					endforeach;
		}
	
	function product_position() {
		$array = $_POST['product_position'];
		$get_val= explode(',',$array); 
		$i=1;
		foreach($get_val as $get){
		$data['prod_id'] = $get;
		$data['position'] = $i;
		if($i!=(sizeof($get_val))){
		$this->Product_model->product_postion($data);} 
		$i++;		
	  	}	
	}
	  	
	function redactor_upload(){
		
		if(!empty($_FILES['file']['name']))
                {
                        $config['upload_path'] = 'uploads/wysiwyg';
                        $config['allowed_types'] = 'gif|jpg|png';

                        $this->load->library('upload', $config);

                        // Attempt upload
                        if($this->upload->do_upload('file'))
                        {
                                $image_data = $this->upload->data();
                                $json = array(
                                        'filelink' => base_url("uploads/wysiwyg/{$image_data['file_name']}")
                                );

                                echo stripslashes(json_encode($json));
                        }
             }
	
	}

	function language(){
			$code = 'language';
			$language 	= $this->input->post('language');
			$url = $this->input->post('url_rect');			
			$this->session->set_userdata('language', $language);
			redirect($url);
		}

	//subcribe
	function subscribe(){
		$this->load->library('form_validation');
		$this->load->model('Updates_model');
		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|max_length[128]|callback_check_email');
		if($this->form_validation->run() == FALSE){ 
		echo validation_errors();
		}
		else{
		echo 'success';			
		$data['email']		= $this->input->post('email');
		$data['joining_date']		= date("Y-m-d H:m:s");
		
		$this->Customer_model->subscribe($data);	//insert into table	
		//Subscribe email
		$res = $this->db->where('id', '15')->get('canned_messages'); // get the email template
		$row = $res->row_array();
		$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
		$row['content'] = str_replace('{site_name}', '<a href="'.site_url().'">'.$this->config->item('company_name').'</a>', $row['content']); 
	   
		$updates	= $this->Updates_model->get_updates(); 
		$update_news='';
		$i=0;
		foreach($updates as $news){
			if($i<2){
				$update='
				<div background-color:#f1f1f1;border-bottom:1px dashed #ccc;margin:5px 0;padding:5px;>
				<h5><a href="'.site_url('/updates/'.$news->id).'">'.$news->title.'</a></h5>
				<span>'.$news->date.'</span>
				<p>'.substr($news->content,0,200).'...</p>
				</div>'; 
				$update_news.= $update;		
			}//if
		}  //for
		$row['subject'] = str_replace('{updates}',$update_news, $row['subject']);
		$row['content'] = str_replace('{updates}',$update_news, $row['content']);		
		// {unsubscribe}		
		$sub_id=$this->Customer_model->get_subid(trim($this->input->post('email')));	  //get subscribe id
		$unsubscribe='<a href="'.site_url('/unsubscribe/'.$sub_id).'">unsubscribe</a>';
		$row['subject'] = str_replace('{unsubscribe}',$unsubscribe, $row['subject']);
		$row['content'] = str_replace('{unsubscribe}',$unsubscribe, $row['content']); 		
		if($data['email']!=''){  	
			$this->load->library('email'); //loading email library 
			$config['mailtype'] = 'html'; 
			$this->email->initialize($config);
			// Mail to user
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($data['email']);
			$this->email->subject($row['subject']); 
			$this->email->message(html_entity_decode($row['content']));			
			$this->email->send();  
		} 
		
	    }
		//Subscribe email 
	} 
	//subcribe

	function check_email($str)
	{
		if(!empty($this->customer['id']))
		{
			$email = $this->Customer_model->check_email_subscribe($str, $this->customer['id']);
		}
		else
		{
			$email = $this->Customer_model->check_email_subscribe($str);
		}
		
        if ($email)
       	{
			$this->form_validation->set_message('check_email', lang('error_email'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	//unsubscribe
	function unsubscribe($id){   
		$action=$this->Customer_model->unsubscribe($id); 	
		$this->session->set_flashdata('message','You has been unsubscribed from this site');  
		redirect('home');	
	}
	//unsubscribe

	function recenttop(){
	$recent_top_products	= $this->Product_model->get_recent_top_products(); 
	$product_position		= $this->Product_model->get_product_position_count();
	?>

	<h3><?php echo 'Recent Top Products';?><div class="activegrayarrow"></div></h3>
	<ul class="slistmain clearfix">
	<?php
	$i=1;
	$position_count = count($product_position);
	foreach($recent_top_products as $prod){
		 ?>
		<li class="products_lists">
		<div class="slist clearfix">
		<div class="slist-img pop_up_show clsFloatLeft items">
	
		<?php 		
			$photo	= theme_img('no_picture.png', lang('no_image_available'));
			$product	= json_decode($prod->images);
			@$items .= $prod->id.',';
			$primaryphoto = $logophoto = false;
			if($product){
			foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($prod->user_id);
							$photo	= '<div class="items_div">
							<img src="'.base_url('uploads/images/full/'.$logo->filename).'"/>			
							<p>'.$prod->name.'</p>
							<span> by '.$username->user_firstname.'</span>
							<p>$ '.$prod->price.'</p></div>';
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$prod->name.'#,'.$username->user_firstname.'#,'.$prod->price.'#,'.intval(($prod->c)/$position_count).' Sales';
						}
						if(isset($photo->logo))
						{
						$logophoto = true;
						if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($prod->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);				
						}
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'"  width=60 height=60 largeimage="'.$largeimageurl.'" />
							</a>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}		
			}
		 ?>
		</div>
		<div class="slist-content clsFloatLeft">
		<h5><?php echo $prod->name; ?></h5>
		<p class="count"><?php echo lang('no of sales');?>: <span><?php echo intval(($prod->c)/$position_count); ?></span></p>
		<p class="viewlink"><a href="<?php echo site_url($prod->slug); ?>"><?php echo lang('view work');?></a></p>
		</div>
		<div class="slist-rating clsFloatRight"><div class="count"><?php echo intval(($prod->c)/$position_count); ?></div>
		<div class="like">
		<?php if($i <= $prod->position){?>
		<img src="<?php echo theme_img('icons/like.png');?>" />
		<?php }else { ?>
			<img src="<?php echo theme_img('icons/dislike.png');?>" />
		<?php }?>
		</div></div>
		<div class="clear"></div>
		</div>
		</li>
	<?php $i++;  } ?> 
	<input type="hidden" class="product_lists" value="<?php echo @$items; ?>" />
	
	</ul>
	<p class="more"><a href="<?php echo site_url('TopProducts') ?>"><?php echo 'See More Recent Top Products';?> »</a> </p>
	<?php }

	function top_prod(){
	$top_products	  = $this->Product_model->get_top_products(); 
	$product_position = $this->Product_model->get_product_position_count();?>

	<h3><?php echo 'Top Products';?><div class="activegrayarrow"></div></h3>
	<ul class="slistmain clearfix">
	<?php
	$i=1;
	$position_count = count($product_position);
	foreach($top_products as $prod){
		?>
		<li class="products_lists">
		<div class="slist clearfix">
		<div class="slist-img pop_up_show clsFloatLeft items">
			<?php 		
			$photo	= theme_img('no_picture.png', lang('no_image_available'));
			$product	= json_decode($prod->images);
			@$items .= $prod->id.',';
			$primaryphoto = $logophoto = false;
			if($product){
			foreach($product as $photo)
					{
						if(isset($photo->primary))
						{ $logo	= $photo;
						$primaryphoto = true;
							$username = $this->Customer_model->get_customer($prod->user_id);
							$photo	= '<div class="items_div">
							<img src="'.base_url('uploads/images/full/'.$logo->filename).'"/>			
							<p>'.$prod->name.'</p>
							<span> by '.$username->user_firstname.'</span>
							<p>$ '.$prod->price.'</p></div>';
							$largeimageurl = base_url('uploads/images/full/'.$logo->filename).'#,'.$prod->name.'#,'.$username->user_firstname.'#,'.$prod->price.'#,'.intval(($prod->c)/$position_count).' Sales';					
						}
						if(isset($photo->logo))
						{
						$logophoto = true;
						if(!isset($largeimageurl)){$largeimageurl='';}
							$logo	= $photo;
							$smallimageurl=site_url($prod->slug);
							$smallimagename=base_url('uploads/images/thumbnails/'.$logo->filename);				
						}
						if($primaryphoto == true && $logophoto == true)
						{
							$finalphoto	= '<a href="'.$smallimageurl.'">
							<img src="'.$smallimagename.'"  width=60 height=60 largeimage="'.$largeimageurl.'" />
							</a>';	
							echo $finalphoto;	
							$primaryphoto = $logophoto = false;
						}
					}		
			}
		 ?>
		</div>
		<div class="slist-content clsFloatLeft">
		<h5><?php echo $prod->name; ?></h5>
		<p class="count"><?php echo lang('no of sales');?>: <span><?php echo intval(($prod->c)/$position_count); ?></span></p>
		<p class="viewlink"><a href="<?php echo site_url($prod->slug); ?>"><?php echo lang('view work');?></a></p>
		</div>
		<div class="slist-rating clsFloatRight"><div class="count"><?php echo intval(($prod->c)/$position_count); ?></div>
		<div class="like">
		<?php if($i <= $prod->position){?>
		<img src="<?php echo theme_img('icons/like.png');?>" />
		<?php }else { ?>
			<img src="<?php echo theme_img('icons/dislike.png');?>" />
		<?php }?>
		</div></div>
		<div class="clear"></div>
		</div>
		</li>
	<?php $i++;  } ?> 
	<input type="hidden" class="product_lists" value="<?php echo @$items; ?>" />
	</ul>
	<p class="more"><a href="<?php echo site_url('TopProducts') ?>"><?php echo 'See More Recent Top Products';?> »</a> </p>

	<?php }

	function recent_topauth(){ 
	$recent_customers		= $this->Customer_model->get_recent_customers(5,$offset=0, $order_by='recommend',$direction='DESC');
?>
	<h3><?php echo 'Recent TOP AUTHORS';?><div class="activegrayarrow"></div></h3>
			<ul class="slistmain clearfix">
				
			<?php
			$array_customer = $recent_customers;
			$previousValue = null;
			$count =0;
			foreach($array_customer as $customers){				
			$count++;
			if($count > 5){ break; }
			$id = $customers->user_id;
			if($previousValue != $id) {
			?>
			<li>
			<div class="slist clearfix">
			<div class="slist-img clsFloatLeft"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>">
			<?php if(!empty($customers->avatar)) { ?>
			<img width="60" height="60" alt="<?php echo $customers->user_firstname?>" id="profileImage" src="<?php echo base_url('uploads/profile/'.$customers->avatar) ?>" >
			   <?php } else { ?>
			   		<img width="60" height="60" alt="" id="profileImage" src="<?php echo theme_img('avatar.png') ?>" >			
				<?php } ?>
				</a></div>
				<div class="slist-content clsFloatLeft">
				<h5><?php
				echo $customers->user_firstname.' '.$customers->user_lastname; ?></h5>
				<p class="count"><?php echo lang('No of Items');?>: <span><?php
				$useritem = $this->Customer_model->get_user_items($id);
	 			if(count($useritem) != 0)
				{ echo count($useritem);  }
				else
				{ echo '0'; } ?></span></p>
				<p class="viewlink"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>"><?php echo lang('View Portfolio');?></a></p>
				</div>
				<div class="slist-rating clsFloatRight"><div class="count"><?php
				$usersale = $this->Customer_model->get_salecount($id);
				$sale_count =  count($usersale);
 			if($sale_count == 0){?>
			<p><?php echo '0'; ?></p>
			<?php }
			else{?>
			<p><?php echo $sale_count; ?></p>			
			<?php }
			?>		</div><div class="like"><a href="#"><img src="<?php echo theme_img('icons/like.png');?>" /></a></div></div>
				<div class="clear"></div>
				</div>
				</li>
				<?php $previousValue = $customers->user_id; } }?>
			</ul>
			<p class="more"><a href="<?php echo site_url('TopAuthors'); ?>"><?php echo 'See More Recent Top Authors';?> »</a> </p>
		</div>

<?php }

function topauth(){ 
	$customers		= $this->Customer_model->get_customers(5,$offset=0, $order_by='recommend',$direction='DESC');
?>
	<h3><?php echo 'TOP AUTHORS';?><div class="activegrayarrow"></div></h3>
			<ul class="slistmain clearfix">
			<?php
			$array_customer = $customers;
			$previousValue = null;
			$count =0;
			foreach($array_customer as $customers){				
			$count++;
			if($count > 5){ break; }
			$id = $customers->user_id;
			if($previousValue != $id) {
			?>
			<li>
				<div class="slist clearfix">
				<div class="slist-img clsFloatLeft"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>">
				<?php if(!empty($customers->avatar)) { ?>
						<img width="60" height="60" alt="<?php echo $customers->user_firstname?>" id="profileImage" src="<?php echo base_url('uploads/profile/'.$customers->avatar) ?>" >
			   <?php } else { ?>
			   		<img width="60" height="60" alt="" id="profileImage" src="<?php echo theme_img('avatar.png') ?>" >			
				<?php } ?>
				</a></div>
				<div class="slist-content clsFloatLeft">
				<h5><?php
				echo $customers->user_firstname.' '.$customers->user_lastname; ?></h5>
				<p class="count"><?php echo lang('No of Items');?>: <span><?php
				$useritem = $this->Customer_model->get_user_items($id);
	 			if(count($useritem) != 0)
				{ echo count($useritem);  }
				else
				{ echo '0'; } ?></span></p>
				<p class="viewlink"><a href="<?php echo site_url('profile/'.$customers->user_id); ?>"><?php echo lang('View Portfolio');?></a></p>
				</div>
				<div class="slist-rating clsFloatRight"><div class="count"><?php
				$usersale = $this->Customer_model->get_salecount($id);
				$sale_count =  count($usersale);
 			if($sale_count == 0){?>
			<p><?php echo '0'; ?></p>
			<?php }
			else{?>
			<p><?php echo $sale_count; ?></p>			
			<?php }
			?>		</div><div class="like"><a href="#"><img src="<?php echo theme_img('icons/like.png');?>" /></a></div></div>
				<div class="clear"></div>
				</div>
				</li>
				<?php $previousValue = $customers->user_id; } }?>
			</ul>
			<p class="more"><a href="<?php echo site_url('TopAuthors'); ?>"><?php echo lang('See more Top Authors');?> »</a> </p>

<?php }
	function sitemap(){
	 $this->load->library('breadcrumb');
	 $this->load->model(array('Blog_model', 'Forum_model', 'Bsepanel_model'));
    $this->breadcrumb->append_crumb('Home', base_url());
 	 $this->breadcrumb->append_crumb('Sitemap', base_url());
	 $breadcrumb = $this->breadcrumb->output();
	 $data['breadcrumb']	= $breadcrumb;
	 
	 $data['blogcategories']		= $this->Blog_model->get_blogs_cat();
	 $data['forumcategories']		= $this->Forum_model->get_forum_cats(); 
	 
	 $data['facebook']  = $this->Bsepanel_model->get_other('facebook');
	 $data['twitter']  = $this->Bsepanel_model->get_other('twitter');
	  $data['google']  = $this->Bsepanel_model->get_other('google');
	 $data['linked']  = $this->Bsepanel_model->get_other('linked');
	 $data['rss']  = $this->Bsepanel_model->get_other('rss');
	 $data['skype']  = $this->Bsepanel_model->get_other('skype');
	   
	 $data['categories']	= $this->Category_model->get_categories();
	 $data['pages'] = $this->Page_model->get_pages();
	 $data['body_content']			= 'sitemap';
	 $this->load->view('template', $data);
	}
}