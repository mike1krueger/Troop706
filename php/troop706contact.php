<?php
/*
 *  Contact Form - Accepts data entered from Troop706 web site (html-form) and sends email(s)
 *  
 * prerequisites:
 * PHP script will only execute on an application server. 
 * XAMPP application server can be executed on Windows 10 for testing this script.
 * 
 */
 
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//assert that PHPMailer libraries are defined and located
require './phpMailLibrary/Exception.php';
require './phpMailLibrary/PHPMailer.php';
require './phpMailLibrary/SMTP.php';

 
 
//phpinfo(); //spits out php version information and php server configuration
//exit();

	//https://github.com/PHPMailer/PHPMailer
	//Server settings
	//	COMMENT OUT mail VARIABLES WHEN TESTING LOCALLY
	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer(true);
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                  // Enable verbose debug output
    //$mail->isSMTP();                                        // Send using SMTP
    //$mail->Host       = 'smtpout.secureserver.net';         // Set the SMTP server to send through
    //$mail->SMTPAuth   = true;                               // Enable SMTP authentication
    //$mail->Username   = 'admin@bsatroop706.org';           // SMTP username
    //$mail->Password   = 'SHGtroop706!';                    // SMTP password
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    //$mail->Port       = 587;                                // TCP port to connect to

	//GoDaddy restrictions - per github post https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting
	//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->Host = 'localhost';
	$mail->SMTPAuth = false;
	$mail->SMTPAutoTLS = false; 
	$mail->Port = 25; 



    //Recipients
    $mail->setFrom('admin@bsatroop706.org', 'Mailer');
    //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML


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



//okay, after wasting a great deal time, when using _POST with data structures you need to use a more specialized call to extract the data structure - in this case, ajax invoked php script with data structure containing email object.  file_get_contents does the trick.
$emailData = json_decode(file_get_contents("php://input"));


//The decoded data will be of type object. To turn it into an Array, you can cast it to type array
$emailDataArray = (array) $emailData; // cast (convert) the object to an array


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
	
	//email interested parties
	$mail->addAddress('T706treasurer@gmail.com', 'Troop706 Treasurer ');     
	$mail->addAddress('jedgeld@gmail.com', 'Troop706 Assistant Scout Master ');     
	$mail->addAddress('t706committeechairman@gmail.com', 'Troop706 Committee Chair ');     
	$mail->addAddress('william.hand@ngc.com', 'Troop706 Scout Master '); 
	$mail->addAddress('mjkrueger@yahoo.com', 'Troop706 Committee Member '); 
	//$mail->addAddress('troop706dvancement@gmail.com', 'Troop706 Advancement Chair '); 
	
	
  	// Send email
    $mail->Subject = 'Troop706 - ' . $emailDataArray[need];
    $mail->Body    = 'From:' . $emailDataArray[firstname] . ' ' . $emailDataArray[surname] . '<br>' . 'Request:' . $emailDataArray[message];
	

	$mail->AddCC($emailDataArray[email], 'Contact Request');
    
    if ($mail->send())
	{
			//array variable returned to AJAX 
			$responseArray = array('type' => 'success', 'message' => $okMessage);  
	} else
	{
		//array variable returned to AJAX 
		$errorDetail = "ErrorDetail: " . $mail->ErrorInfo ;
		$fullErrorResonse = "$errorMessage . $errorDetail";
	  	$responseArray = array(	"type" => "danger", "message" => $fullErrorResonse ); 
	}
	
    
    
}
catch (phpmailerException $e) {
  //Pretty error messages from PHPMailer
  $responseArray = array(	"type" => "error","message" => $e->getMessage() ); //array variable indicating error, returned to AJAX caller
}  
catch (Exception $e)
{    
	//echo "Exception: $e" ;
    $responseArray = array(	"type" => "error","message" => $e->getMessage() ); //array variable indicating error, returned to AJAX caller   
}

//return json structure to caller (java script/jquery)
$encoded = json_encode($responseArray);
header('Content-Type: application/json');
echo $encoded; //this returns results to caller, make sure you dont have other echo commands enabled in this script or it will fail    


//close php file
?>
