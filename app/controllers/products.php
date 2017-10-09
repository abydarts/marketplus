<?php

class Products extends Front_Controller {	
	
	private $use_inventory = false;
	
	function __construct()
	{		
		parent::__construct();
		
		$this->config->set_item('language',$this->auth->language());	
		$this->load->model('Product_model');
		$this->load->model('Customer_model');
		$this->load->helper('form');
		$this->lang->load('admin_common');			
		$this->lang->load('product');
		$this->load->helper('date');
		$this->load->language('category');
		$this->load->language('quiz');
		$this->customer = $this->bse_tec->customer();
	}

	function index($order_by="name", $sort_order="ASC", $code=0, $page=0, $rows=10)
	{
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('My Products', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb'] = $breadcrumb;
		$data['activePage']		= 'myproducts';

		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if (!$redirect)
		{			
			redirect('user/login');
		}

		$data['page_title']	= lang('item management');
		
		$data['code']		= $code;
		$term				= false;
		$category_id		= false;
		
		//get the category list for the drop menu
		$data['categories']	= $this->Category_model->get_categories_tierd();	
		$post				= $this->input->post(null, false);
		$this->load->model('Search_model');
		if($post)
		{
			$term			= json_encode($post);
			$code			= $this->Search_model->record_term($term);
			$data['code']	= $code;
		}
		elseif ($code)
		{
			$term			= $this->Search_model->get_term($code);
		}	
		//store the search term
		$data['term']		= $term;
		$data['order_by']	= $order_by;
		$data['sort_order']	= $sort_order;
		$customer_id = $this->customer['user_id'];
		$data['products']	= $this->Product_model->user_products(array('term'=>$term, 'order_by'=>$order_by, 'sort_order'=>$sort_order, 'rows'=>$rows, 'page'=>$page,'user_id'=>$customer_id));

		//total number of products
		$data['total']		= $this->Product_model->user_products(array('term'=>$term, 'order_by'=>$order_by, 'sort_order'=>$sort_order,'user_id'=>$customer_id), true);

		$this->load->library('pagination');
		
		$config['base_url']			= site_url('/products/index/'.$order_by.'/'.$sort_order.'/'.$code);
		$config['total_rows']		= count($data['total']);
		$config['per_page']			= $rows;
		$config['uri_segment']		= 6;
		$config['first_link']		= 'First';
		$config['first_tag_open']	= '<li>';
		$config['first_tag_close']	= '</li>';
		$config['last_link']		= 'Last';
		$config['last_tag_open']	= '<li>';
		$config['last_tag_close']	= '</li>';

		$config['full_tag_open']	= '<div class="pagination"><ul>';
		$config['full_tag_close']	= '</ul></div>';
		$config['cur_tag_open']		= '<li class="active"><a href="#">';
		$config['cur_tag_close']	= '</a></li>';
		
		$config['num_tag_open']		= '<li>';
		$config['num_tag_close']	= '</li>';
		
		$config['prev_link']		= '&laquo;';
		$config['prev_tag_open']	= '<li>';
		$config['prev_tag_close']	= '</li>';

		$config['next_link']		= '&raquo;';
		$config['next_tag_open']	= '<li>';
		$config['next_tag_close']	= '</li>';
		
		$this->pagination->initialize($config);
		
		$data['product_pagination']=$this->pagination->create_links();		

		$data['body_content']			= 'item/products';
	
		$this->load->view('template', $data);
	}

	//basic category search
	function product_autocomplete()
	{
		$name	= trim($this->input->post('name'));
		$limit	= $this->input->post('limit');
		
		if(empty($name))
		{
			echo json_encode(array());
		}
		else
		{
			$results	= $this->Product_model->product_autocomplete($name, $limit);
			$return		= array();
			foreach($results as $r)
			{
				$return[$r->id]	= $r->name;
			}
			echo json_encode($return);
		}
	}
	
	function bulk_save()
	{
		$products	= $this->input->post('product');
		if(!$products)
		{
			$this->session->set_flashdata('error',  lang('error_bulk_no_products'));
			redirect('/products');
		}
		foreach($products as $id=>$product)
		{
			$product['id']	= $id;
			$this->Product_model->save($product);
		}
		$this->session->set_flashdata('message', lang('message_bulk_update'));
		redirect('/products');
	}
	
	function quiz_stop(){
		 $data['body_content']			= 'quiz_stop';
		 $this->load->view('template', $data);
		}

	function subcategory()
	{
		$id =  $this->input->post('categoryid');
		$res='';
		if($id!=0)
		{
			$val = $this->Category_model->get_subcategory($id);
		
			if(count($val)>0)
			{
				$res .= '<select style="margin:0px;" size="1" id="subcategory_list" name="subcategory_list">
		<option value="">Select</option>';
		
				foreach($val as $cat )
				{
				$res .= '<option id="category_item_'.$cat->id.'" value="'.$cat->id.'">'.$cat->name.'</option>';
				}
		
				$res .='</select>';
			}
		}										
		echo $res;
	}

	function form($id = false, $duplicate = false)
	{
		$data['activePage']		= 'myproducts';
			$cart_val = $this->session->userdata('cart_contents');
			foreach($cart_val as $user)
			{
			if(isset($user['user_id'])) {
	 		$user_id = $user['user_id'];	
			}	
			}

		$redirect	= $this->Customer_model->is_logged_in(false, false);
		//if they are logged in, we send them back to the my_account by default
		if (!$redirect)
		{			
			redirect('user/login');
		}
			
		$author	= $this->Customer_model->is_logged_in_author($user_id);
		//Marketplus version 1.5
		$data['product_count'] = $this->Customer_model->get_user_items($user_id);
		$quiz_status = $this->Settings_model->get_settings(quiz);
		$data['quiz_status'] = $quiz_status['enable'];
		$data['author_type'] = $author->user_type;
						
		if($author->user_type == 0 && $quiz_status['enable']==1)
		{
			 redirect('products/quiz_stop');
		}
		
		//Marketplus version 1.5
		
		$this->product_id	= $id;
		$this->load->library('form_validation');
		$this->load->model(array('Category_model', 'Digital_Product_model'));
		$this->lang->load('digital_product');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$userid = $this->customer['user_id'];
		$data['file_list']		= $this->Digital_Product_model->get_list_user($userid);

		$data['page_title']		= lang('add item');

		//default values are empty if the product is new
		$data['id']					= '';
		$data['user_id']			= '';
		$data['name']				= '';
		$data['slug']				= '';
		$data['description']		= '';
		$data['comments']			= '';
		$data['features']			= '';
		$data['changelog']			= '';
		$data['faq']			= '';
		$data['item_price']				= '';
		$data['standard_price']				= '';
		$data['demo_url']				= '';
		$data['seo_title']			= '';
		$data['meta']				= '';
		$data['price']				= '';
		$data['item_sale']			= '';
		$data['enabled']			= '';
		$data['prod_code']			= strtotime(date("Y-m-d H:i:s")).''.$this->customer['user_id'];
		$data['related_products']	= array();
		$data['product_categories']	= array();
		$data['images']				= array();
		$data['product_files']		= array();
		$data['files']		= array();
		$data['brws_cmpt']  	= $this->Product_model->get_brws();
		$data['file_include']  	= $this->Product_model->get_file_type();
		$data['admins']			= $this->admin_session->userdata('admin');
		//browses compatible		
		//create the photos array for later use
		$data['photos']		= array();

		$data['category_list']			= '';
		$data['subcategory_list']			= '';

		if ($id)
		{	
			// get the existing file associations and create a format we can read from the form to set the checkboxes;
			
			$pr_files 		= $this->Digital_Product_model->get_associations_by_file($id);
			foreach($pr_files as $f)
			{
				$data['product_files'][]  = $f->file_id;
			}
			
			$data['files'] = $this->Digital_Product_model->get_lists($id);

		   // get product & options data;
		   
			$product					= $this->Product_model->get_product($id);

			//if the product does not exist, redirect them to the product list with an error;
			
			if($user_id != $product->user_id){
			$this->session->set_flashdata('error', 'Sorry!! You have no rights to edit this product');
			//go back to the product list
			redirect('products');
			}
			
			if (!$product)
			{
				$this->session->set_flashdata('error', lang('error_not_found'));
				redirect('products');
			}
								
			//helps us with the slug generation;
			$this->product_name	= $this->input->post('slug', $product->slug);
			
			//set values to db values;
			$data['id']					= $id;
			$data['user_id']			= $product->user_id;
			$data['name']				= $product->name;
			$data['seo_title']		= $product->seo_title;
			$data['username']			= $product->username;
			$data['meta']				= $product->meta;
			$data['slug']				= $product->slug;
			$data['description']		= $product->description;
			$data['comments']			= $product->comments;
			$data['features']			= $product->features;
			$data['changelog']			= $product->changelog;
			$data['faq']				= $product->faq;
			$data['brws_cmpts']			= $product->brws_cmpt;
			$data['file_includes']    = $product->file_include;
			$data['price']				= $product->price;
			$data['standard_price']				= $product->standard_price;
			$data['demo_url']			= $product->demo_url;
			$data['item_sale']			= $product->item_sale;
			$data['enabled']			= $product->enabled;
			$data['prod_code']			= $product->prod_code;
			
			//make sure we haven't submitted the form yet before we pull in the images/related products from the database;
			if(!$this->input->post('submit'))
			{
				$data['product_categories']	= $product->categories;
				$data['related_products']	= $product->related_products;
				$data['images']			= (array)json_decode($product->images);
			}
		
		$cat_id = $data['product_categories'][0]->category_id;
		$data['subcat'] = $this->Category_model->get_subcategory($cat_id);
		}
				
		//if $data['related_products'] is not an array, make it one.
		if(!is_array($data['related_products']))
		{
			$data['related_products']	= array();
		}

		//no error checking on these
		
		$this->form_validation->set_rules('caption', 'Caption');
		$this->form_validation->set_rules('primary_photo', 'Primary');
		$this->form_validation->set_rules('seo_title', 'lang:seo_title', 'trim');
		$this->form_validation->set_rules('meta', 'lang:meta_data', 'trim');
		$this->form_validation->set_rules('name', 'lang:name', 'trim|required|max_length[64]');
		$this->form_validation->set_rules('slug', 'lang:slug', 'trim');
		$this->form_validation->set_rules('description', 'lang:description', 'trim|required');
		
		$this->form_validation->set_rules('comments', 'lang:comments', 'trim|required');
		$this->form_validation->set_rules('faq', 'lang:faq', 'trim');
		$this->form_validation->set_rules('features', 'lang:features', 'trim');
		$this->form_validation->set_rules('changelog', 'lang:changelog', 'trim');
		
		$this->form_validation->set_rules('item_price', 'lang:price', 'trim|numeric|floatval|required');
		$this->form_validation->set_rules('standard_price', 'lang:standard price', 'trim|numeric|floatval|required');
		$this->form_validation->set_rules('demo_url', 'lang:demo_url', 'trim|required');
		$this->form_validation->set_rules('item_sale', 'lang:quantity', 'trim|numeric');
		$this->form_validation->set_rules('enabled', 'lang:enabled', 'trim|numeric');
		$this->form_validation->set_rules('images', 'lang:images', 'required');
		

		/*
		if we've posted already, get the photo stuff and organize it
		if validation comes back negative, we feed this info back into the system
		if it comes back good, then we send it with the save item
		
		submit button has a value, so we can see when it's posted
		*/
		if($duplicate)
		{
			$data['id']	= false;
		}
		if($this->input->post('submit'))
		{
			//reset the product options that were submitted in the post
			$data['product_options']	= $this->input->post('option');
			$data['related_products']	= $this->input->post('related_products');
			$data['product_categories']	= $this->input->post('category_list');
			$data['images']				= $this->input->post('images');
			$data['product_files']		= $this->input->post('downloads');
		}
		
		$data['product_category']	= $this->Category_model->get_category1();
		
		if ($this->form_validation->run() == FALSE)
		{
		
		$data['error']  = validation_errors();		
		$data['body_content']			= 'item/product_form';
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('Product', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb'] = $breadcrumb;
		$this->load->view('template', $data);
		}
		else
		{
			$this->load->helper('text');
			
			//first check the slug field
			$slug = $this->input->post('slug');
			
			//if it's empty assign the name field
			if(empty($slug) || $slug=='')
			{
				$slug = $this->input->post('name');
			}
			
			$slug	= url_title(convert_accented_characters($slug), 'dash', TRUE);
			
			//validate the slug
			$this->load->model('Routes_model');

			if($id)
			{
				$slug		= $this->Routes_model->validate_slug($slug, $product->route_id);
				$route_id	= $product->route_id;
			}
			else
			{
				$slug	= $this->Routes_model->validate_slug($slug);
				$route['slug']	= $slug;	
				$route_id	= $this->Routes_model->save($route);
			}

			$save['id']					= $id;
			$save['user_id']			= $this->customer['user_id'];	
			$save['name']				= $this->input->post('name');
			$save['username']			= $this->input->post('username');
			$save['seo_title']			= $this->input->post('seo_title');
			$save['meta']				= $this->input->post('meta');
			$save['description']		= $this->input->post('description');
			$save['comments']			= $this->input->post('comments');
			$save['features']			= $this->input->post('features');
			$save['changelog']			= $this->input->post('changelog');
			$save['faq']			= $this->input->post('faq');
			$save['price']				= $this->input->post('item_price');
			$save['standard_price']				= $this->input->post('standard_price');
			$save['demo_url']				= $this->input->post('demo_url');
			$save['item_sale']			= $this->input->post('item_sale');
			
			/*version 1.3 */
			
			if($id)
			{
				$save['enabled'] 			= 3; // Product update status
			}
			else 
			{
				$save['enabled']			= $this->input->post('enabled');
			}
			/*version 1.3 close*/
			
			$save['date_created']	= date("Y:m:d");  
			$save['prod_code']   =$this->input->post('prod_code');
			$post_images				= $this->input->post('images');
			$save['slug']				= $slug;
			$save['route_id']			= $route_id;

			// Browser compatability 
			if($this->input->post('brws_cmpt'))
			{
				$save['brws_cmpt'] = json_encode($this->input->post('brws_cmpt'));
			}
			else
			{
				$save['brws_cmpt'] = '';
			}			

			// File included
			if($this->input->post('file_include'))
			{
				$save['file_include'] = json_encode($this->input->post('file_include'));
			}
			else
			{
				$save['file_include'] = '';
			}		
			
			$imagetype = $this->input->post('image_type');
				
			if($primary	= $this->input->post('images'))
			{
				if($post_images)
				{
					foreach($post_images as $key => &$pi)
					{
				         if($post_images[$key]['image_type'] == "logo") {
							$pi['logo']	= true;
							continue;
							}
						
						   if($post_images[$key]['image_type'] == "view") {
							$pi['view']	= true;
							continue;
							}
						
						   if($post_images[$key]['image_type'] == "primary") {
							$pi['primary']	= true;
							continue;
							}
						}	
				}
				
			}
			
			$save['images']				= json_encode($post_images);
			if($this->input->post('related_products'))
			{
				$save['related_products'] = json_encode($this->input->post('related_products'));
			}
			else
			{
				$save['related_products'] = '';
			}
			
			//save categories
			
			$cat['category']			= $this->input->post('category_list');
			$categories		=  $this->input->post('category_list');
			$subcategory		=  $this->input->post('subcategory_list');
						
			// format options
			
			$options	= array();
			if($this->input->post('option'))
			{
				foreach ($this->input->post('option') as $option)
				{
					$options[]	= $option;
				}

			}	
			// save product 
			$product_id	= $this->Product_model->save($save, $options, $categories,$subcategory);
						
			// add file associations

			$downloads = $this->input->post('downloadss');
						
			if(is_array($downloads))
			{
				foreach($downloads as $d)
				{
					$this->Digital_Product_model->associated($d, $product_id,$this->customer['user_id']);
				}
			}			

			//save the route
			$route['id']	= $route_id;
			$route['slug']	= $slug;
			$route['route']	= 'cart/product/'.$product_id;
			
			$this->Routes_model->save($route);
			
			//Send products Notification
			if(!$id){
			$res = $this->db->where('id', '13 ')->get('canned_messages');
			$row = $res->row_array();

			$cart_val = $this->session->userdata('cart_contents');
			foreach($cart_val as $user)
			{
			if(isset($user['user_id'])) {
	 		$user_name = $user['user_firstname'].' '.$user['user_lastname'];
			$user_email = $user['user_email'];	
			}	
			}
			// set replacement values for subject & body
			
			// {customer_name}
			$row['content'] = str_replace('{customer_name}', $user_name, $row['content']);		
			
			// {site_name}
			$row['subject'] = str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);
			$row['content'] = str_replace('{site_name}', $this->config->item('company_name'), $row['content']);

			$row['content'] = str_replace('{p_id}', $save['prod_code'], $row['content']);
			$row['content'] = str_replace('{p_name}', $save['name'], $row['content']);
			$row['content'] = str_replace('{p_description}', $save['description'], $row['content']);			
			$row['content'] = str_replace('{p_date}', date("Y-m-d H:i:s"), $row['content']);

			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
	
			$this->email->from($this->config->item('email'), $this->config->item('company_name'));
			$this->email->to($user_email);
			$this->email->bcc($this->config->item('email'));
			$this->email->subject($row['subject']);
			$this->email->message(html_entity_decode($row['content']));
			
			$this->email->send();
			}
			//Send products Notification

			$this->session->set_flashdata('message', lang('message_saved_product'));
			//go back to the product list
			redirect('products');
		}
	}
	
	function category_autocomplete()
	{
			//$results	= $this->Category_model->category_autocomplete($name, $limit);
			$results	= $this->Category_model->category_autocomplete_form();
			
			$return		= array();
			foreach($results as $r)
			{
				$return[$r->id]	= $r->name;
			}
			echo json_encode($return);	
	}	

	function product_image_form()
	{
		$data['file_name'] = false;
		$data['error']	= false;
		$data['body_content']			= 'item/product_image_uploader';
		$this->load->view('pre_load/product_image_uploader', $data);
	}
	
	function product_image_upload()
	{
		//Marketplus version 1.5
		$this->load->model('Bsepanel_model');
		$watermarker_image = $this->Bsepanel_model->get_value('watermarker');
		//close
		
		$data['file_name'] = false;
		$data['error']	= false;
		$config['image_library'] = 'gd2';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['upload_path'] = 'uploads/images/full';
		$config['encrypt_name'] = true;
		$config['remove_spaces'] = true;
	
		$this->load->library('upload', $config);
						
		if ( $this->upload->do_upload())
		{
			$upload_data	= $this->upload->data();
						
			if($_POST['image_type']=="logo")
			{
					
		   if($upload_data[image_width]==75 && $upload_data[image_height]==75) 
		   {
			$this->load->library('image_lib');		
			
			//this is the larger image
			$config['image_library'] = 'gd2';
			$config['source_image'] = 'uploads/images/full/'.$upload_data['file_name'];
			$config['new_image']	= 'uploads/images/thumbnails/'.$upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 75;
			$config['height'] = 75;
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();
					
			$data['file_name']	= $upload_data['file_name'];
			$data['image_type'] = $this->input->post('image_type'); 
			}
		   else 
		    {
				$file_full2 = $upload_data['full_path'];
				chmod( $upload_data['file_path'],0777);
  				@unlink($file_full2);
				$data['error']="Logo Image Size should be 75X75..";
		     }
		}
			if($_POST['image_type']=="view")
			{
							
		   if($upload_data[image_width]==190 && $upload_data[image_height]==100) 
		   {
			$this->load->library('image_lib');		
			
			//this is the larger image
			$config['image_library'] = 'gd2';
			$config['source_image'] = 'uploads/images/full/'.$upload_data['file_name'];
			$config['new_image']	= 'uploads/images/small/'.$upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 190;
			$config['height'] = 100;
			
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();
						
			$data['file_name']	= $upload_data['file_name'];
			$data['image_type'] = $this->input->post('image_type'); 
			}
		else 
		{
				$file_full2 = $upload_data['full_path'];
				chmod( $upload_data['file_path'],0777);
  				@unlink($file_full2);
				$data['error']="View Image Size should be 190X100 ..";
		     }
		}
		if($_POST['image_type']=="primary")
			{ 
			if($upload_data[image_width]>=850 && $upload_data[image_height]>=370) 
		   {
			$this->load->library('image_lib');		
			//this is the larger image
			$config['image_library'] = 'gd2';
			
			// Marketplus version 1.5
			$config['source_image'] = 'uploads/images/full/'.$upload_data['file_name'];
			$config['new_image'] = 'uploads/images/full/'.$upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			//$config['wm_text'] = 'Marketplus';
			
			//$config['wm_type'] = 'text';
			$config['wm_type'] = 'overlay';
			$config['wm_overlay_path'] = 'uploads/images/'.$watermarker_image;
			$config['quality'] = 50;
									
     	   $config['wm_opacity'] = 10;
			$config['wm_x_transp'] = 4;
		   $config['wm_y_transp'] = 4;
        // $config['wm_font_path'] = './system/fonts/texb.ttf';
        // $config['wm_font_size'] = '16';
        // $config['wm_font_color'] = 'ffffff';
        
         $config['wm_vrt_alignment'] = 'center';
         $config['wm_hor_alignment'] = 'center';
         $config['wm_padding'] = '20';
			
			$this->image_lib->initialize($config);
			$this->image_lib->watermark();
			$this->image_lib->clear();
			
			$config['source_image'] = 'uploads/images/full/'.$upload_data['file_name'];
			$config['new_image']	= 'uploads/images/medium/'.$upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 850;
			$config['height'] = 370;
			// Marketplus version 1.5 close
					
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();
						
			$data['file_name']	= $upload_data['file_name'];
			$data['image_type'] = $this->input->post('image_type'); 
			}
		  else 
		  {
				$file_full2 = $upload_data['full_path'];
				chmod( $upload_data['file_path'],0777);
  				@unlink($file_full2);
				$data['error']="Primary Image Size should be above 850X370 ..";
		  }
		}
		if($_POST['image_type']=="")
		{
			$data['error']="Please Choose type of your Image ..";
		}
		if($this->upload->display_errors() != '')
		{
			$data['error'] = $this->upload->display_errors();
		}
				
	}
	$this->load->view($this->config->item('admin_folder').'/iframe/product_image_uploader', $data);
}
	
	function product_image_upload_logo()
	{
		$data['file_name'] = false;
		$data['error']	= false;
		
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_width'] = 75;
		$config['max_height'] = 75; 
		$config['upload_path'] = 'uploads/images/thumbnails';
		$config['encrypt_name'] = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		
		if ( $this->upload->do_upload())
		{
			$upload_data	= $this->upload->data();
			$this->load->library('image_lib');		
			//this is the larger image
			$config['image_library'] = 'gd2';
			$config['source_image'] = 'uploads/images/thumbnails/'.$upload_data['file_name'];
			$config['new_image']	= 'uploads/images/full/'.$upload_data['file_name'];
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 600;
			$config['height'] = 500;
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();
			$data['file_name']	= $upload_data['file_name'];
		}
		if($this->upload->display_errors() != '')
		{
			$data['error'] = $this->upload->display_errors();
		}
		$this->load->view($this->config->item('admin_folder').'/iframe/product_image_uploader', $data);
	}
	

	function delete($id = false)
	{
		if ($id)
		{	
			$product	= $this->Product_model->get_product($id);
			//if the product does not exist, redirect them to the customer list with an error
			if (!$product)
			{
				$this->session->set_flashdata('error', lang('error_not_found'));
				redirect('products');
			}
			else
			{
				// version 1.3
				if($product->enabled!=1 && $product->enabled!=4 && $product->enabled!=0 && $product->enabled!=3)
				{
				// remove the slug
				$this->load->model('Routes_model');
				$this->Routes_model->remove('('.$product->slug.')');

				//if the product is legit, delete them
				$this->Product_model->delete_product($id);

				$this->session->set_flashdata('message', lang('message_deleted_product'));
				redirect('products');
				}
				else
				{
				 	$this->session->set_flashdata('error', lang('not_to_delete'));
					redirect('products');
				}
			// version 1.3 close
			}
		}
		else
		{
			//if they do not provide an id send them to the product list page with an error
			$this->session->set_flashdata('error', lang('error_not_found'));
			redirect('products');
		}
	}

	function live_demo($id){
		$data['product']	= $this->Product_model->get_product($id);
			
		$this->load->view('template/'.$this->config->item('theme').'/item/livedemo', $data);	
		}
	function wish_list(){
	$save['prod_id'] = $this->input->post('wish_list_prodid');
	$save['user_id'] = $this->input->post('wish_list_userid');
	$this->Customer_model->wish_list($save);	
	}
	
	function product_recommend(){
	$save['recommend_product_id'] = $this->input->post('follower_id');
	$save['user_id'] = $this->input->post('user_id');
	$btn = $this->input->post('r_btn');
   $this->Product_model->recommend_list($save,$btn);
   $result = $this->Product_model->check_recommend($save['user_id'],$save['recommend_product_id']);
   echo count($result);		
	}
	
	function top_products(){
	
		$this->load->library('breadcrumb');
		$this->breadcrumb->append_crumb('Home', base_url());
		$this->breadcrumb->append_crumb('Top Products', base_url());
		$breadcrumb = $this->breadcrumb->output();
		$data['breadcrumb'] = $breadcrumb;
		$data['product_columns']	= $this->config->item('product_columns');
		//grab the products using the pagination lib
		$data['top_products']	= $this->Product_model->get_top_products($id=false,20);
   	$data['body_content']			= 'item/top_products';
		$this->load->view('template', $data);	
	}

	function remove_image($id)
	{
		$file_full = "uploads/images/full/$id.gif";
  		@unlink($file_full);
		$file_full1 = "uploads/images/full/$id.png";
  		@unlink($file_full1);
		$file_full2 = "uploads/images/full/$id.jpg";
  		@unlink($file_full2);
  
  		$file_medium = "uploads/images/medium/$id.gif";
  		@unlink($file_medium);
		$file_medium1 = "uploads/images/medium/$id.png";
  		@unlink($file_medium1);
		$file_medium2 = "uploads/images/medium/$id.jpg";
  		@unlink($file_medium2);
  		
 		$file_small = "uploads/images/small/$id.gif";
  		@unlink($file_small);
		$file_small1 = "uploads/images/small/$id.png";
  		@unlink($file_small1);
		$file_small2 = "uploads/images/small/$id.jpg";
  		@unlink($file_small2);
  		
 		$file_thumbnails = "uploads/images/thumbnails/$id.gif";
  		@unlink($file_thumbnails);
		$file_thumbnails1 = "uploads/images/thumbnails/$id.png";
  		@unlink($file_thumbnails1);
		$file_thumbnails2 = "uploads/images/thumbnails/$id.jpg";
  		@unlink($file_thumbnails2);
	}

function file_upload () {
	header('Vary: Accept');
		if (isset($_SERVER['HTTP_ACCEPT']) &&
    (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    header('Content-type: application/json');
	} else {
    header('Content-type: text/plain');
	}
	$data['body_content']			= 'item/ajax_item';
	$data['id']			= $this->input->post('prod_id');
	$data['digitalfile_name']		= $this->input->post('digitalfile_name'); // version 1.3
	$data['digitalfile_id']			= $this->input->post('digitalfile_id'); // version 1.3
	$this->load->view('template/default/item/ajax_item', $data);
	}

function uploadfile() {
	
	$this->load->model('digital_product_model');
		// A list of permitted file extensions
	$allowed = array('zip');
	if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
	
			$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
		$_FILES['upl']['max_size'] = '20MB';
				
		$digitalfile = $this->input->post('digitalfile');
		$digitalfile_id = $this->input->post('digitalfileid');
		
		if($digitalfile=='') 
		{						
			$save['title']	= $_FILES['upl']['name'];
	 	   $_FILES['upl']['name'] = time().$_FILES['upl']['name'];
			$save['filename']	= $_FILES['upl']['name'];
			$save['user_id']	= $this->customer['user_id'];
			$save['size']		= $_FILES['upl']['size'];
			
			$this->digital_product_model->save($save);		
			if(!in_array(strtolower($extension), $allowed)){
			echo '{"status":"error"}';
			exit;
			}
			if(move_uploaded_file($_FILES['upl']['tmp_name'], 'uploads/digital_uploads/'.$_FILES['upl']['name'])){
				echo '{"status":"success"}';
			}
		}
		else 
		{
			if($this->digital_product_model->verify_content($digitalfile))
			{
				@chmod(FCPATH.'/uploads/digital_uploads/', 0777); 
		 		unlink('uploads/digital_uploads/'.$digitalfile);
		 		
		 		$save['id'] = $digitalfile_id ;
		 		$save['title']	= $_FILES['upl']['name'];
			   $save['size']		= $_FILES['upl']['size'];
		 		
		 		$this->digital_product_model->save($save);	
		 		if(move_uploaded_file($_FILES['upl']['tmp_name'], 'uploads/digital_uploads/'.$digitalfile))
		 		{
				echo '{"status":"success"}';
				}
			}
		}
	}
	echo '{"status":"error"}';
	exit;
	}

	function product_iframe($id) {
	$data['page_title']	= 'Products Iframe';
	$data['product']	= $this->Product_model->get_product($id);
	$this->load->view('template/default/item/iframe_product', $data);
	}

function delete_files($id,$p_id){
		$this->load->model('digital_product_model');
		//error_reporting(1);
		if ($id)
		{	
				//if the product is legit, delete them
				$this->digital_product_model->delete($id);
				$this->session->set_flashdata('message', lang('message_deleted_file'));
				redirect('products/form/'.$p_id);
		}
		else
		{
			//if they do not provide an id send them to the product list page with an error;
			$this->session->set_flashdata('error', lang('error_not_found'));
			redirect('productsorm/'.$p_id);
		}
		}
	function removewish_list(){
	$prod_id = $this->input->post('wish_list_prodid');
	$user_id = $this->input->post('wish_list_userid');
   $this->db->delete('user_wishlist', array('prod_id' => $prod_id,'user_id'=>$user_id));	
	}
}