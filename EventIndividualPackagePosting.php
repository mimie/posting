<html>
<head>
<title>Individual Package Posting</title>
 <link rel="stylesheet" type="text/css" href="billingStyle.css">
 <link rel="stylesheet" type="text/css" href="menu.css">
 <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billInfo').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
function reloadPage(){
    window.location=window.location;
}

$(function() {
    $( "#confirmation" ).dialog({
      resizable: false,
      width:500,
      modal: true,
      buttons: {
        "OK": function() {
          //$( this ).dialog( "close" );
          reloadPage();
        }
      }
    });
  });

$(function() {
    $( "#datepickerStart" ).datepicker();
    $( "#datepickerEnd" ).datepicker();
    $( "#postDate" ).datepicker();
});
</script>
</head>
<body>
<?php

  include 'pdo_conn.php';
  include 'login_functions.php';
  include '../webapp/pirev2/packages/package_functions.php';
  include '../webapp/pirev2/shared_functions.php';
  include '../webapp/pirev2/packages/packagebill_functions.php';
  include 'postingFunc/eventCompanyPost_functions.php';
  include "../weberp/postFunction.php";

  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";

   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td><a href='EventIndividualPackagePosting.php'>INDIVIDUAL PACKAGE POSTING</a></td>";
   echo "<td bgcolor='#084B8A'><a href='EventCompanyPackagePosting.php'>COMPANY PACKAGE POSTING</a></td>";
   echo "</tr>";
   echo "</table></br>";

    @$pid = $_GET['pid'];

    $events = getEventsPerPackage($pid);
    $package_name = getPackageName($pid);

    $display = "<table align='center'>"
           . "<tr><th colspan='4'>$package_name</th></tr>"
           . "<tr><th>Event Id</th><th>Event Name</th><th>Start Date</th><th>End Date</th></tr>";

  $eventIds = array();
  foreach($events as $key=>$field){
        $display = $display."<tr>"
                 . "<td>".$field['event_id']."</td>"
                 . "<td>".$field['event_name']."</td>"
                 . "<td>".date_standard($field['start_date'])."</td>"
                 . "<td>".date_standard($field['end_date'])."</td>"
                 . "</tr>";
        $eventIds[] = $field['event_id'];
  }

  $display = $display."</table></div><br><br>";
  echo $display;

  $accts = getOTHDebitAcct($weberp);

    $oth = "<select name='acct_code'>"
           . "<option value=''>-Select account code-</option>"
           . "<option value=''>---------------------------------------</option>";
    foreach($accts as $key=>$field){
       $oth = $oth."<option value='".$field['accountcode']."'>".$field['glacode']." - ".$field['accountname']."</option>";
    }

    $oth = $oth. "</select>";

  $bills = getBillByPackageId($pid,"Individual");
  echo "<form action='' method='POST'>";
  $display = "<table width='100%' align='center' id='packages'>"
           . "<thead>"
           . "<tr><td  bgcolor='#084B8A' colspan='12'>"
           . "<input type='text' name='postdate' id='postDate' placeholder='Select post date..'>"
           . "$oth<input type='submit' value='Post to Weberp' name='post'></td></tr>"
           . "<th>Name</th>"
           . "<th>Organization</th>"
           . "<th>Fee</th>"
           . "<th>Subtotal</th>"
           . "<th>12% VAT</th>"
           . "<th>Print Bill</th>"
           . "<th>Amount Paid</th>"
           . "<th>Registration No.</th>"
           . "<th>ATP</th>"
           . "<th>Billing Date</th>"
           . "<th>Notes</th>"
           . "<th>Edit</th>"
           . "</tr>"
           . "</thead><tbody>";

   $preview_img = "<img src='../webapp/pirev2/images/preview.png' height='30' width='30'>";
   $all_contacts = array();

    foreach($bills as $key=>$field){
         $all_contacts[$field['contact_id']] = $field;

         $post_bill = $field['post_bill'];
     
         if($post_bill == 0){

         $bir_no = $field['bir_no'];
         $billing_no = $field['billing_no'];
         $billing_id = $field['bid'];
         $print_img = $bir_no == NULL || $field['edit_bill'] == 0 ? '' : "<a href='../webapp/pirev2/BIRForm/print_package_individual.php?billing_no=".$billing_no."&uid=".$uid."' target='_blank'><img src='../webapp/pirev2/printer-icon.png' width='30' height='30'></a>";
        $img_link = "<a href='../webapp/pirev2/edit_individual_package.php?pid=$pid&billing_no=$billing_no&billing_id=$billing_id&bir_no=$bir_no&uid=$uid' onclick=\"window.open(this.href,'edit_individual.php?pid=$pid&billing_no=$billing_no&billing_id=$billing_id&bir_no=$bir_no&uid=$uid','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=900,height=900');return false;\"><img src='../webapp/pirev2/images/edit_bill.png'></a>";
         $display = $display."<tr>"
                  . "<td><input type='checkbox' name='contact_ids[]' value='".$field['contact_id']."'>".$field['sort_name']."</td>"
                  . "<td>".$field['organization_name']."</td>"
                  . "<td>".number_format($field['total_amount'],2)."</td>"
                  . "<td>".number_format($field['subtotal'],2)."</td>"
                  . "<td>".number_format($field['vat'],2)."</td>"
                  . "<td><a href='../webapp/pirev2/BIRForm/birform_package_individual.php?billing_no=".$billing_no."&uid=".$uid."' target='_blank'>$preview_img</a>"
                  . "$print_img"
                  . "</td>"
                  . "<td>".number_format($field['amount_paid'],2)."</td>"
                  . "<td>".$field['billing_no']."</td>"
                  . "<td>".$bir_no."</td>"
                  . "<td>".date("F j, Y",strtotime($field['bill_date']))."</td>"
                  . "<td>".$field['notes']."</td>"
                  . "<td>$img_link</td>"
                  . "</tr>";
         }
   }


  $display = $display."</tbody></table>";
  echo $display;
  echo "</form>";

  if($_POST['post']){
        $acct =  $_POST["acct_code"];
	$ids = $_POST['contact_ids'];
	foreach($ids as $contact_id){
		$info = $all_contacts[$contact_id];
                $custId = "IIAP".$contact_id;
                $name = $info["sort_name"];
                $eventType = substr($info['billing_no'],0,3);
                $eventName = $package_name;
                $eventDescription = $pid."/".$eventName;
                $feeAmount = $info["total_amount"];
                $billingNo = $info["billing_no"];
                $billDate = $info["bill_date"];
                $withVat = $info["vat"] == 0 ? 0 : 1;

                $address = getAddressDetails($dbh,$contactId); 
                $street = $address["street"];
                $city = $address["city"];

                $memberId = getMemberId($dbh,$contactId);

                $customer = array();
                $customer["contact_id"] = $contact_id;
                $customer["participant_name"] = $name;
                $customer["street"] = $street;
                $customer["city"] = $city;
                $customer["email"] = $email;
                $customer["member_id"] = $memberId;

                $postDate = $_POST["postdate"];
                $exist = checkContactRecordExist($weberp,$contact_id);

                if($exist == 0){
                	insertCustomer($weberp,$customer);
          		$eventType == 'OTH' ? postOTH($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate,$withVat,$acct) : myPost($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate);

                }

                else{
                       $eventType == 'OTH' ? postOTH($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate,$withVat,$acct) : myPost($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate);

              }

        }

        echo'<div id="confirmation" title="Confirmation">';
        echo "<img src='../webapp/pirev2/images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>";
        echo'<p>Billing is already posted.</p>';
        echo'</div>';

  }
?>
</body>
</html>
