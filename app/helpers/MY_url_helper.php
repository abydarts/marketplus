<?php

function ssl_support()
{
	$CI =& get_instance();
    return $CI->config->item('ssl_support');
}

if ( ! function_exists('force_ssl'))
{
	function force_ssl()
	{
		if (ssl_support() &&  (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off'))
		{
			$CI =& get_instance();
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
			redirect($CI->uri->uri_string());
		}
	}
}

//thanks C4iO [PyroDEV]
if ( ! function_exists('remove_ssl'))
{
	function remove_ssl()
	{	
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		{
			$CI =& get_instance();
			$CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
			
			redirect($CI->uri->uri_string());
		}
	}
}

//function theme_url($uri)
//{
//	$CI =& get_instance();
//	return $CI->config->base_url('app/themes/'.$CI->config->item('theme').'/'.$uri);
//}

//Dynamic theme
	function theme_url($uri) {
		$CI =& get_instance(); 	 	
			$row = $CI->db->get_where('templates', array('status' => "1"))->row(); 								// getting theme records in helper 
				if(count($row)!=0){
					$template=$row->theme_name; //template name  	
					} else {		
					$template=$CI->config->item('theme'); //default template
				} 
		return $CI->config->base_url('app/themes/'.$template.'/'.$uri);	
} 
//Dynamic theme

//to generate an image tag, set tag to true. you can also put a string in tag to generate the alt tag
function theme_img($uri, $tag=false)
{
	if($tag)
	{
		return '<img src="'.theme_url('assets/img/'.$uri).'" alt="'.$tag.'">';
	}
	else
	{
		return theme_url('assets/img/'.$uri);
	}
	
}

function theme_images($uri, $tag=false)
{
	if($tag)
	{
		return '<img src="'.theme_url('assets/images/'.$uri).'" alt="'.$tag.'">';
	}
	else
	{
		return theme_url('assets/images/'.$uri);
	}
	
}

function theme_js($uri, $tag=false)
{
	if($tag)
	{
		return '<script type="text/javascript" src="'.theme_url('assets/js/'.$uri).'"></script>';
	}
	else
	{
		return theme_url('assets/js/'.$uri);
	}
}

//you can fill the tag field in to spit out a link tag, setting tag to a string will fill in the media attribute
function theme_css($uri, $tag=false)
{
	if($tag)
	{
		$media=false;
		if(is_string($tag))
		{
			$media = 'media="'.$tag.'"';
		}
		return '<link href="'.theme_url('assets/css/'.$uri).'" type="text/css" rel="stylesheet" '.$media.'/>';
	}
	
	return theme_url('assets/css/'.$uri);
}