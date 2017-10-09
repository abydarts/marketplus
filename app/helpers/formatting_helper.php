<?php 
function format_address($fields, $br=false)
{
	if(empty($fields))
	{
		return ;
	}
	
	// Default format
	$default = "{firstname} {lastname}\n{company}\n{address_1}\n{address_2}\n{city}, {zone} {postcode}\n{country}";
	
	// Fetch country record to determine which format to use
	$CI = &get_instance();
	$CI->load->model('location_model');
	$c_data = $CI->location_model->get_country($fields['country_id']);
	
	if(empty($c_data->address_format))
	{
		$formatted	= $default;
	} else {
		$formatted	= $c_data->address_format;
	}

	$formatted		= str_replace('{firstname}', $fields['firstname'], $formatted);
	$formatted		= str_replace('{lastname}',  $fields['lastname'], $formatted);
	$formatted		= str_replace('{company}',  $fields['company'], $formatted);
	
	$formatted		= str_replace('{address_1}', $fields['address1'], $formatted);
	$formatted		= str_replace('{address_2}', $fields['address2'], $formatted);
	$formatted		= str_replace('{city}', $fields['city'], $formatted);
	$formatted		= str_replace('{zone}', $fields['zone'], $formatted);
	$formatted		= str_replace('{postcode}', $fields['zip'], $formatted);
	$formatted		= str_replace('{country}', $fields['country'], $formatted);
	
	// remove any extra new lines resulting from blank company or address line
	$formatted		= preg_replace('`[\r\n]+`',"\n",$formatted);
	if($br)
	{
		$formatted	= nl2br($formatted);
	}
	return $formatted;
	
}

function format_currency($value, $symbol=true)
{
	if(!is_numeric($value))
	{
		return;
	}
	
	$CI = &get_instance();
	
	if($value < 0 )
	{
		$neg = '- ';
	} else {
		$neg = '';
	}
	
	$symbol= $CI->auth->symbol($CI->auth->currency_select('currency'));
			   
		   if($symbol)	{
				
			$convert_value= currency_convert($CI->auth->currency_select('currency'),$CI->auth->currency_select('currency'),$value); 
			
			$formatted	= number_format(abs($convert_value), 2, $CI->config->item('currency_decimal'), $CI->config->item('currency_thousands_separator'));	
			
			if($CI->config->item('currency_symbol_side') == 'left') {
				$formatted	= $neg.$symbol.$formatted;
				} else {
				$formatted	= $neg.$symbol;
				}
			} else {		
				$formatted	= number_format(abs($convert_value), 2, '.', ','); 	
		}		
		return $formatted.' '.$CI->auth->currency_select('currency');;
}
function currency_convert($from,$to,$amount) {
		
		if($from!=$to){
	  $amounts = urlencode(1);
  $from_Currency = urlencode($from);
  $to_Currency = urlencode($to);
  $get = file_get_contents("https://www.google.com/finance/converter?a=$amounts&from=$from_Currency&to=$to_Currency");
  $get = explode("<span class=bld>",$get);
  $get = explode("</span>",$get[1]);  
  $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
 
  return $converted_amount*$amount;
		}
		else{
			return $amount;
			}
}