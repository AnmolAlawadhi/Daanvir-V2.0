<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

// following files need to be included
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");
require_once("../main/db/init.php");

$paytmChecksum = "";
$paramList = array();
$isValidChecksum = "FALSE";

$paramList = $_POST;
$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; 
//Sent by Paytm pg

//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application�s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.
?>
<!DOCTYPE html>
<html>
	<head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <title>Daanvir - Payment Status</title>
         
        <meta name="author" content="Daanvir.org" />
<link rel="shortcut icon" href="../main/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="../css/style2.css" />
    <link rel="stylesheet" type="text/css" href="../css/style2_small.css" />  
</head>
<body>  

<?php
$ordr_id = $_SESSION['ordr_id'];
$total = $_SESSION['total_amount'];
$hitId = $_SESSION['block_id']; 
$checkbox=substr($needs[$i],18);
$session_arr = $_SESSION[$ordr_id];
$session_arr = explode("/", $session_arr); 
//print_r($session_arr);
$name = $session_arr[0];
$phone = $session_arr[1];
$email = $session_arr[2]; 
$u = $userData['userId']; 
$tag='I just helped someone on Daanvir.';
$desc='Daanvir.org is a platform that helps you reinstate your neighbourhood by promoting support in its favour throughout the country. Not only you can donate to support the locality you believe in , but also you get a free opportunity to shift the flow of global sympathy from virtual reality to a real development of localities.';

if($isValidChecksum == "TRUE") {
	if (isset($_POST) && count($_POST)>0 )
	{  
foreach($_POST as $paramName => $paramValue) {
	if(strcmp($paramName,"TXNAMOUNT") == 0) 
$total_received = $paramValue;
	if(strcmp($paramName,"ORDERID") == 0)  
$ordr_id_received = $paramValue;
		}	
	}else{ 
	$total_received = "NA";
	$ordr_id_received = "NA";
	}
	//echo "<b>Checksum matched and following are the transaction details:</b>" . "<br/>";
	if ($_POST["STATUS"] == "TXN_SUCCESS"){
		//echo "<b>Transaction status is success</b>" . "<br/>";
		//Process your transaction here as success transaction.
		//Verify amount & order id received from Payment gateway with your application's order id and amount.
		//Store in DB
	//echo $total.'='.$total_received.', '.$ordr_id.'='.$ordr_id_received;
if($total==(int)$total_received && $ordr_id==$ordr_id_received){
if(count($session_arr)>5){
$needs = explode(",", $session_arr[5]);  
$values = explode(",",$session_arr[6]); 
echo 'YES';
$number1=sizeof($needs);
$number2=sizeof($values);

for($i=0; $i<$number1;$i++){
$checkbox = (int)substr($needs[$i],18);
$checkbox_val= (int)$values[$i];
//pickup = 9 means transaction
$upd = mysqli_query($GLOBALS['conn2'],"INSERT INTO currfillmethod (userId,confirmed,dated,currentNeedId,currNeedFillNum,pickup) VALUES ('$u',1,now(),'$checkbox','$checkbox_val',9)");

 }
}else{
	echo 'NO';
	$upd = mysqli_query($GLOBALS['conn2'],"INSERT INTO currfillmethod (userId,confirmed,dated,pickup) VALUES ('$u',1,now(),9)"); 
}
}else{
	//echo 'Error' ;
}
if((int)$total_received > 0){
	$hit_id= substr($hitId,14); 
	$ord = substr($ordr_id_received, (4 + strlen($u)));
$upd_trans = mysqli_query($GLOBALS['conn2'],"INSERT INTO transac(ordr_id,cust_id,date,amount,hitId,gateway) VALUES ('$ord' , '$u' , now() , '$total' , '$hit_id', 1)");

include '../main/html/mailer.php';
sleep(1);
$mail->ClearAllRecipients();
$mail->setFrom('noreply@daanvir.org', 'daanvir.org');
$mail->addAddress( $email , 'Daanvir'); 
$mail->Subject = "DAANVIR PAYMENT-STATUS";   
$m="Dear ".$name.", \n\n
Your payment with Daanvir.org was successful. Payment details are:\n\n
ORDER ID: {$ordr_id}\n
TOTAL AMOUNT: {$total}
\n\nThankyou for helping people around you. Thankyou for being a daanvir. Regards,\nTeam Daanvir."; 
$mail->Body = $m; 
} 
 
 $printed='
<div id="fb-root"></div>
<script> 
 $(function(){
 window.fbAsyncInit = function() {
            FB.init({
              appId      : \'1807657979451474\',
              xfbml      : true, cookie: true,status: true,
              version    : \'v2.2\'
            });
        };

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));

});

</script>
<div id="cover-articles">
<section id="thankyou-f">
<h2><strong>Thankyou</strong>! '.$name.'<br>For being a Daanvir</h2>
</section>
<section id="thankyou-l">
<!-- Or save address if pickup not available -->  
<p>PAYMENT STATUS:</p> 
<b>SUCCESS!</b><hr>
<p>ORDER ID:</p> 
<b>'.$ordr_id_received.'</b><hr>
<p>TRANSACTION AMOUNT:</p>
<h4>INR <strong>'.$total_received.'</strong></h4> <hr>
<p>Would you like to share for a good thing:</p>
<!--Share button-->
<button onclick="fShare();">Share this moment on facebook</button> 
<p>We would love if you add us in your connections:</p>
<!--Like button-->
<button>Connect with us on facebook</button>
</section>
</div>
<script type="text/javascript">
function fShare(){ 
var obj = {method: \'feed\', link: \'http://www.daanvir.org\', picture: \'http://daanvir.org/images/5.jpg\',name: \'Daanvir - '.$tag.'\',description: \''.$desc.'\'};
function callback(response){}
FB.ui(obj, callback); 
}
</script>
';

 echo $printed;
}
else {
		//echo "<b>Transaction status is failure</b>" . "<br/>";
	 

	/*
	if (isset($_POST) && count($_POST)>0 )
	{ 
		foreach($_POST as $paramName => $paramValue) {
				echo "<br/>" . $paramName . " = " . $paramValue;
		}
	}
	*/
//PRINTING STUFF
$printed='
<div id="fb-root"></div>
<script> 
 $(function(){
 window.fbAsyncInit = function() {
            FB.init({
              appId      : \'1807657979451474\',
              xfbml      : true, cookie: true,status: true,
              version    : \'v2.2\'
            });
        };

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));

});

</script>
<div id="cover-articles">
<section id="thankyou-f">
<h2><strong>Sorry</strong>! '.$name.'<br>Payment Failed.</h2>
</section>
<section id="thankyou-l">
<!-- Or save address if pickup not available -->  
<p>ORDER ID:</p> 
<b>'.$ordr_id_received.'</b><hr>
<p>TRANSACTION AMOUNT:</p>
<h4>INR <strong>'.$total_received.'</strong></h4><hr> 
<!--Like button-->
<button>Connect with us on facebook</button>
</section>
</div>  
';
echo $printed;
	}
}
else {
	//echo "<b>Checksum mismatched.</b>";
	//Process transaction as suspicious.
	//PRINTING STUFF
$printed='
<div id="cover-articles">
<section id="thankyou-f">
<h2><strong>Alert</strong>! '.$name.'<br>Suspicious Activity Registered</h2>
</section>  
</div>
';
echo $printed;
}
  
?>

	</body>
</html>