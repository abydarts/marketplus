$(document).ready(function(){


	//global vars
	var form = $("#customForm");
	var phone = $("#phone");
	var phoneInfo = $("#phoneInfo");
	
	var fname = $("#fname");
	var fnameInfo = $("#fnameInfo");
	var lname = $("#lname");
	var lnameInfo = $("#lnameInfo");
	
	var email = $("#email");
	var emailInfo = $("#emailInfo");
	var pass1 = $("#pass1");
	var pass1Info = $("#pass1Info");
	var pass2 = $("#pass2");
	var pass2Info = $("#pass2Info");

	// Add Product form fields..

	var item_name =$("#name");
	var item_nameInfo =$("#nameInfo");

	var category_list =$("#category_list");
	var category_listInfo =$("#category_listInfo");

	var item_description =$("#description");
	var item_descriptionInfo =$("#descriptionInfo");

	var demourl =$("#demo_url");
	var demourlInfo =$("#demo_urlInfo");

	var item_price =$("#item_price");
	var item_priceInfo =$("#item_priceInfo");

	var standard_price =$("#standard_price");
	var standard_priceInfo =$("#standard_priceInfo");

	var comments =$("#comments");
	var commentsInfo =$("#commentsInfo");

	var brws_cmpt =$(".change_select");
	var brws_cmptInfo =$("#brws_cmptInfo");
	
	var file_include =$(".change_select");
	var file_includeInfo =$("#file_includeInfo");

	var meta =$("#meta");
	var metaInfo =$("#metaInfo");
	
	var download =$("#downloadss");
	var downloadInfo =$("#file_errorInfo");
	
	var imagename =$("#imagename");
	var imagesInfo =$("#imagesInfo");
	
	
	var view_image =$("#view_image");
	
	var selectall =$(".selectall_brws");
		
	//On blur
	phone.blur(validateName);
	fname.blur(validateFname);
	lname.blur(validateLname);
	email.blur(validateEmail);
	pass1.blur(validatePass1);
	pass2.blur(validatePass2);
	//On key press
	phone.keyup(validateName);
	fname.keyup(validateFname);
	lname.keyup(validateLname);
	pass1.keyup(validatePass1);
	pass2.keyup(validatePass2);

	
	item_name.blur(validateitemName1);

	item_description.blur(validateDescription);

	category_list.change(validateCategorylist);
	demourl.blur(validateDemourl);
	item_price.keyup(validateitemPrice);
	standard_price.keyup(validatestandardPrice);
	comments.blur(validateComments);
	
	//selectall.change(validateBrowsers);

	brws_cmpt.change(validateBrowsers);
	
	file_include.change(validatefileInclude);
	//view_image.change(validateImages);
		
	download.blur(validateFile);


	meta.change(validateMeta);
	
	//On Submitting
	$('#registor').submit(function(){

		if(validateName() & validateFname() & validateLname() & validateEmail() & validatePass1() & validatePass2())
			return true;
		else
			return false;
	});
	
	//validation functions
	function validateEmail(){
		//testing regular expression
		var a = $("#email").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		//if it's valid email
		if(filter.test(a)){
			email.removeClass("error");
			emailInfo.text("");
			emailInfo.removeClass("errors");
			return true;
		}
		//if it's NOT valid
		else{
			email.addClass("error");
			emailInfo.text("Stop ! Type a valid e-mail please");
			emailInfo.addClass("errors");

			return false;
		}
	}
	function validateName(){
		//if it's NOT valid
		if(phone.val().length < 10){
			phone.addClass("error");
			phoneInfo.text("Please enter the valid phone number");
			phoneInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			phone.removeClass("error");
			phoneInfo.text("");
			phoneInfo.removeClass("errors");
			return true;
		}
	}
	function validateFname(){
		//if it's NOT valid
		if(fname.val().length < 4){
			fname.addClass("error");
			fnameInfo.text("We want first names with more than 3 letters!");
			fnameInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			fname.removeClass("error");
			fnameInfo.text("");
			fnameInfo.removeClass("errors");
			return true;
		}
	}
	function validateLname(){
		//if it's NOT valid
		if(lname.val().length < 4){
			lname.addClass("error");
			lnameInfo.text("We want last names with more than 3 letters!");
			lnameInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			lname.removeClass("error");
			lnameInfo.text("");
			lnameInfo.removeClass("errors");
			return true;
		}
	}
	function validatePass1(){
		var a = $("#password1");
		var b = $("#password2");

		//it's NOT valid
		if(pass1.val().length <5){
			pass1.addClass("error");
			pass1Info.text("At least 5 characters: letters, numbers and '_'");
			pass1Info.addClass("errors");
			return false;
		}
		//it's valid
		else{			
			pass1.removeClass("error");
			pass1Info.text("");
			pass1Info.removeClass("errors");
			//validatePass2();
			return true;
		}
	}
	function validatePass2(){
		var a = $("#password1");
		var b = $("#password2");
		//are NOT valid
		if( pass1.val() != pass2.val() ){
			pass2.addClass("error");
			pass2Info.text("Passwords doesn't match!");
			pass2Info.addClass("errors");
			return false;
		}
		//are valid
		else{
			pass2.removeClass("error");
			pass2Info.text("");
			pass2Info.removeClass("errors");
			return true;
		}
	}
// validate contact form on keyup and submit


	function validateitemName1(){
		//if it's NOT valid


		if(item_name.val()=="")
		{
			item_name.addClass("error");
			item_nameInfo.text("Please enter Item Name");
			item_nameInfo.addClass("errors");
			return false;
		}

		else if(item_name.val().length < 5)
		{
			item_name.addClass("error");
			item_nameInfo.text("Please enter atleast 5 characters..");
			item_nameInfo.addClass("errors");
			return false;
		}
	
		else if(item_name.val().length > 32)
		{
			item_name.addClass("error");
			item_nameInfo.text("Please enter Maximum 32 characters..");
			item_nameInfo.addClass("errors");
			return false;
		}
		
		//if it's valid
		else{
			item_name.removeClass("error");
			item_nameInfo.text("");
			item_nameInfo.removeClass("errors");
			return true;
		}
	}

	function validateDescription(){
		//if it's NOT valid

		//alert('Description');
		//	alert($('.redactor').redactor('get'));
		if(item_description.val()=="")
		// if(item_description.val().length){
		{
			

			item_description.addClass("error");
			item_descriptionInfo.text("Please enter Item Description");
			item_descriptionInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			item_description.removeClass("error");
			item_descriptionInfo.text("");
			item_descriptionInfo.removeClass("errors");
			return true;
		}
	}


	function validateMeta(){
		//if it's NOT valid


		if(meta.val()=="")
		// if(item_description.val().length){
		{
			//alert('Description');

			meta.addClass("error");
			metaInfo.text("Please enter Meta");
			metaInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			meta.removeClass("error");
			metaInfo.text("");
			metaInfo.removeClass("errors");
			return true;
		}
	}


	function validateCategorylist()
	{
		//if it's NOT valid
		if(category_list.val()==0)
		{
		category_list.addClass("error");
		category_listInfo.text("Please select category");
		category_listInfo.addClass("errors");
		return false;
		}
		//if it's valid
		else{
		category_list.removeClass("error");
		category_listInfo.text("");
		category_listInfo.removeClass("errors");
		return true;
		}
	}

function validateBrowsers()
	{
		// alert('Browsers');
		//alert($('[name="brws_cmpt[]"]:checked').length);
			
		if($('[name="brws_cmpt[]"]:checked').length == 0)
		{
			brws_cmpt.addClass("error");
			brws_cmptInfo.text("Please select atleast 1 browser..");
			brws_cmptInfo.addClass("errors");
			return false;
		} 
		else
		{
			brws_cmpt.removeClass("error");
			brws_cmptInfo.text("");
			brws_cmptInfo.removeClass("errors");
			return true;
		}
	}


function validatefileInclude()
	{
		//alert('File');
		//alert($('[name="file_include[]"]:checked').length);
			
		if($('[name="file_include[]"]:checked').length == 0)
		{
			//alert('working');
			file_include.addClass("error");
			file_includeInfo.text("Please select atleast 1 file include..");
			file_includeInfo.addClass("errors");
			return false;
		} 
		else
		{
			file_include.removeClass("error");
			file_includeInfo.text("");
			file_includeInfo.removeClass("errors");
			return true;
		}
		
	}

	function validateDemourl(){
		//if it's NOT valid


		if(demourl.val()=="")
		{

			demourl.addClass("error");
			demourlInfo.text("Please enter Demo Url");
			demourlInfo.addClass("errors");
			return false;
		}

		else if(demourl.val()!="")
		{
			//var urlregex = new RegExp("^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");
			//var val = "/(http(s)?:\\)?([\w-]+\.)+[\w-]+[.com|.in|.org]+(\[\?%&=]*)?/";
			//var val = "^(http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$";
			var u = demourl.val();
			//alert(u);
			if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(u)) // returns true or false
			{
			demourl.removeClass("error");
			demourlInfo.text("");
			demourlInfo.removeClass("errors");
			return true;
			}
			else
			{
  			demourl.addClass("error");
			demourlInfo.text("Please enter Valid Demo Url");
			demourlInfo.addClass("errors");
			return false
			}
		}
		
	}

	function validateitemPrice(){
		//if it's NOT valid


		if(item_price.val()=="")
		{
			item_price.addClass("error");
			item_priceInfo.text("Please enter Item Price");
			item_priceInfo.addClass("errors");
			return false;
		}

		else if(item_price.val()!="")
		{
			var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
			if(numberRegex.test(item_price.val())==false) 
			{
			item_price.addClass("error");
			item_priceInfo.text("Please enter Valid Item Price");
			item_priceInfo.addClass("errors");
			return false;
			}
			//if it's valid
			else
			{
			item_price.removeClass("error");
			item_priceInfo.text("");
			item_priceInfo.removeClass("errors");
			return true;
			}
		}

		
		
	}


	function validatestandardPrice(){
		//if it's NOT valid


		if(standard_price.val()=="")
		{
			standard_price.addClass("error");
			standard_priceInfo.text("Please enter Standard Price");
			standard_priceInfo.addClass("errors");
			return false;
		}

		else if(standard_price.val()!="")
		{
			var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
			if(numberRegex.test(standard_price.val())==false) 
			{
			standard_price.addClass("error");
			standard_priceInfo.text("Please enter Valid Standard Price");
			standard_priceInfo.addClass("errors");
			return false;
			}
			//if it's valid
			else
			{
			standard_price.removeClass("error");
			standard_priceInfo.text("");
			standard_priceInfo.removeClass("errors");
			return true;
			}
		}
	
	}

	function validateComments(){
		//if it's NOT valid


		if(comments.val()=="")
		{

			comments.addClass("error");
			commentsInfo.text("Please enter Comments..");
			commentsInfo.addClass("errors");
			return false;
		}
		//if it's valid
		else{
			comments.removeClass("error");
			commentsInfo.text("");
			commentsInfo.removeClass("errors");
			return true;
		}
	}

		function validateImages()
	{
		//if it's NOT valid
		
		if($('input[name="imagename[]"]').length==0)
		{
			imagename.addClass("error");
			imagesInfo.text("Please Upload atleast 1 Image..");
			imagesInfo.addClass("errors");
			return false;
			
		}
		else 
		{
				//alert($('[name="view_image"]:checked').length)
				if ($('[name="view_image"]:checked').length==0) 
				{
					view_image.addClass("error");
					imagesInfo.text("Please select your View Image..");
					imagesInfo.addClass("errors");
					return false;
				}
			
				else if ($('[name="logo_image"]:checked').length==0) 
				{
					view_image.addClass("error");
					imagesInfo.text("Please select your Logo Image..");
					imagesInfo.addClass("errors");
					return false;
				}
			
				else if ($('[name="primary_image"]:checked').length==0) 
				{
					view_image.addClass("error");
					imagesInfo.text("Please select your Main Image..");
					imagesInfo.addClass("errors");
					return false;
				}
			
				else 
				{
				view_image.removeClass("error");
				imagesInfo.text("");
				imagesInfo.removeClass("errors");
				return true;
				}
		
		}
		//if it's valid
		
	}

	function validateFile()
	{
		//alert($('[name="downloadss[]"]:checked').length);
		if($('[name="downloadss[]"]:checked').length==0)
		{
			download.addClass("error");
			downloadInfo.text("Please upload your product file..");
			downloadInfo.addClass("errors");
			return false;
		} 
		else
		{
			download.removeClass("error");
			downloadInfo.text("");
			downloadInfo.removeClass("errors");
			return true;
		}
		
	}

	$('#form_val').submit(function(){

		if(validateitemName1() & validateDescription() & validateCategorylist() & validateDemourl() & validateitemPrice() & validatestandardPrice() & validateComments() & validateBrowsers() & validatefileInclude() & validateImages() & validateFile())  
		{
		
		 return true;
		}
		else
		{
		
			return false;
		}
			
	});		

});