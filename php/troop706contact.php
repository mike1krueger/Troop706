<?php
/*
 *  Contact Form - Accepts data entered from Troop706 web site (html-form) and sends email(s)
 *  
 * prerequisites:
 * PHP scripts only run on an application server. 
 * XAMPP application server can be executed on Windows 10 for testing this script.
 * 
 * The application server must be configured to execute PHP scripts.
 * The application server must be configured to send e-mails.
 * . email service can be commented out for testing
 */
 
//phpinfo(); //spits out php version information and php server configuration
//exit();

$php_static_from = "Troop706 Contact Form <mjkrueger@yahoo.com>";

// an email address that will receive the email with the output of the form
$php_static_sendTo = "mjkrueger@yahoo.com"; 

// message that will be displayed on website when everything is OK
$okMessage = "Contact form successfully submitted. Troop706 will get back to you soon!"; 

// message that will be displayed on website if something goes wrong
$errorMessage = "There was an error while submitting the contact form. Please try again later or contact Website administrator"; 

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
//report runtime errors, compile errors are not reported here, this can be set in php.ini file also
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", "On");
// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(0); //detailed SMTP mail() service errors are posted if debug is enabled, comment out this line to enable

/*
 *  SENDING EMAIL
*/

//you can uncomment echo commands if you want to see variables assignment in web browser
//echo returns these fields back to calling program - so they need to be commented out when not debugging code

//var_dump($_POST) ;



//okay, after wasting a great deal time, It seems like when using _POST with data structures you need to use a more specialize call to extract the data structure - in this case, ajax invoked php script with data structure containing email object.  file_get_contents does the trick.
$emailData = json_decode(file_get_contents("php://input"));

//echo "<BR> emailData";
//print_r($emailData);
//echo "<BR>";

//The decoded data will be of type object. To turn it into an Array, you can cast it to type array
$emailDataArray = (array) $emailData; // cast (convert) the object to an array

//echo "<BR> emailDataArray";
//print_r($emailDataArray);
//$lfirstName = $emailDataArray->firstname;
//echo "lfirstName: $lfirstName";

//echo "<BR> emailDataArray[firstname] $emailDataArray[firstname]";
//echo "<BR> emailDataArray[surname] $emailDataArray[surname]";
//echo "<BR> emailDataArray[email] $emailDataArray[email]";
//echo "<BR> emailDataArray[need] $emailDataArray[need]";
//echo "<BR> emailDataArray[message] $emailDataArray[message]";





try //wrap this in a try block to catch any Exceptions
{
	
	//check mandatory fields are passed from contact form
	if (!isset($emailDataArray[firstname])  )
	{
		throw new Exception('Form is is missing mandatory field  "name"');
	}
		if (!isset($emailDataArray[surname])  )
	{
		throw new Exception('Form is is missing mandatory field "surname"');
	}
		if (!isset($emailDataArray[email])  )
	{
		throw new Exception('Form is is missing mandatory field "email"');
	}
		if (!isset($emailDataArray[need])  )
	{
		throw new Exception('Form is is missing mandatory field "need"');
	}
			if (!isset($emailDataArray[message])  )
	{
		throw new Exception('Form is is missing mandatory field "message"');
	}
	

  // headers for the email.
	$headers = "From: $php_fromEmail\n";
	$headers .= "MIME-Version: 1.0\n"; //notice the period concatenates this field
	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
    
  // Send email
    $emailSubject = "Troop706 - " . $emailDataArray[need];
	$success = mail($php_static_sendTo, $emailSubject, $emailDataArray[message], $headers);

	//$success=true; //note, you cant call mail() method when service is not running, it clogs up the works returning unexpected error info to caller when service is in debug mode see error_reporting(0)
	if ($success) 
	{
		$responseArray = array(	"type" => "success", "message" => $okMessage ); //array variable returned to AJAX caller
		$rc = "success";
	}
	else {
  	    $responseArray = array(	"type" => "danger", "message" => $errorMessage ); //array variable returned to AJAX caller
  	    $rc = "danger";
	}
}
catch (Exception $e)
{    
//echo "Exception" $e
    $responseArray = array(	"type" => "error","message" => $e ); //array variable indicating error, returned to AJAX caller   
    $rc = "error";
}

//PROBLEMS parsing json response, lets return success and error for now
//return json structure to caller (java script/jquery)
//$encoded = json_encode($responseArray);
//header('Content-Type: application/json');
//echo $encoded; //this returns results to caller, make sure you dont have other echo commands enabled in this script or it will fail    



echo $rc;


//============================================
//this function wasnt helpful for me but leaving it here in case its use becomes apparent later
function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

//close php file
?>
