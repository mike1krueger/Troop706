/*
 * Java Script code to call server side php script passing in contact form variables
 * 
 * good intro into ajax with examples (Traversy Media):  https://www.youtube.com/watch?v=82hnvUYY6QA
 */

    //JQUERY register listener on form submit button and register function sendContactEmail
    //i.e. when user presses submit, the sendContactEmail() method is called and executed
    document.getElementById('contact-form').addEventListener('submit', sendContactEmail);
    
    // send email
    function sendContactEmail(e)
    {
	    //web server will send user to php file by default, prevent this from occurring when submit is pressed    	
    	e.preventDefault(); 
    	
    	//var fn = document.getElementById("form_firstname").value;
		//console.log( "sendContactEmail invoked for name:" + fn); //send to browser console log
		
	
		//var email_formdata = new FormData();
		//email_formdata.set( "firstname", 			document.getElementById("form_firstname").value );
		//email_formdata.set( "surname", 				document.getElementById("form_lastname").value );
		//email_formdata.set( "email", 				document.getElementById("form_email").value );
		//email_formdata.set( "message", 				document.getElementById("form_message").value );
		//email_formdata.set( "need", 				document.getElementById("form_need").value );
	
		//having challenges with FormData structure, lets try json object instead
		var emailObj = {firstname: 		document.getElementById("form_firstname").value	, 
						surname: 		document.getElementById("form_lastname").value	, 
						email: 			document.getElementById("form_email").value		,
						need: 			document.getElementById("form_need").value		,
						message: 		document.getElementById("form_message").value }	;
		var emailJSON = JSON.stringify(emailObj);
		console.log("emailJSON:"+emailJSON);
		
	    //create http request object
		var xhr = new XMLHttpRequest();
	
		//initialize request with POST, php script
		xhr.open( "POST", "./php/troop706contact.php", true ); //php email script asynchronous
		xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

		//"onload" - callback that gets executed after php file is executed
        xhr.onload = function() 
        {
		  //HTTP Statuses
		  //200: Ok
		  //403: forbidden
		  //404: not found
          if(this.status == 200) 
          {
			var rc = this.responseText;
			var jsonReturnObj = JSON.parse(rc); //Javascript function JSON.parse to parse JSON data
			console.log("json parse type"+jsonReturnObj.type);
			console.log("json parse type"+jsonReturnObj.message);
					
			// compose Bootstrap alert box HTML
			var messageAlert = 'alert-' + jsonReturnObj.type;
			var messageText = 'Msg ' + jsonReturnObj.message;
			var alertBox = '<div class="alert ' + messageAlert + 
				  ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + messageText + 
				  '</div>';

			if (jsonReturnObj.type == "success") 
			{
				console.log( "success logic" ); 
						
				// inject the alert to .messages div in our form
				$('#contact-form').find('.messages').html(alertBox);
				
				// empty the contact form
				$('#contact-form')[0].reset();
				     
				//disable the submit button - so user can not send multiple emails
				document.getElementById("emailButton").setAttribute("disabled","disabled");

				     
				} else if (jsonReturnObj.type == "danger")
				{
				  console.log( "danger logic" + messageText ); 
				  //("status").innerHTML = "danger - come back later.";
				  document.getElementById('status').innerHTML = "<br></br> <h2>danger come back later - messageText</h2>" + messageText;
						
				  //disable the submit button
				  $(this).prop("disabled",true);
						
				  } else 
				  {
					 console.log( "error/other return code logic:" + messageText ); 
					 
					 //error status
					 
					 document.getElementById('status').innerHTML = "<br></br> <h2> error come back later - </h2>" + messageText;	
					} //end if condition
        } //status 200
      } //end of onload()
      xhr.error = function() //callback that gets executed if something goes wrong with xhr 
      {
		
		console.log("email request call failed");
		document.getElementById('messages').innerHTML = "xhr error - failed to send request ";
    	  
      }
	  
	  //Optional - used for loaders
	  //xhr.onprogress = function() {console.log('ReadyState', xhr.readyState);


      xhr.send( emailJSON ); //send the request to PHP now
      
    }
