<html>
<head>
<title>Individual Billing</title>
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

$(function() {
    $( "#confirmation" ).dialog({
      resizable: false,
      width:500,
      modal: true,
      buttons: {
        "OK": function() {
          $( this ).dialog( "close" );
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
  include 'postingFunc/eventIndividualPost_functions.php';
  include 'postingFunc/eventCompanyPost_functions.php';
  include "postingFunc/eventpost_functions.php";
  include "billing_functions.php";
  include "../weberp/postFunction.php";

  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";

  @$eventId = $_GET["eventId"];
   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);
   echo "<div id = 'navigation'>";
   echo "<a href='events2.php'><b>Event List</b></a>";
   echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
   echo "<i>$eventName</i>";
   echo "</div>";
   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td bgcolor='#084B8A'><a href='participantListing.php?eventId=$eventId'>ALL PARTICIPANTS</a></td>";
   echo "<td><a href='EventIndividualPostingByEvent.php?eventId=$eventId'>INDIVIDUAL EVENT POSTING</a></td>";
   echo "<td bgcolor='#084B8A'><a href='EventCompanyPostingByEvent.php?eventId=$eventId'>COMPANY EVENT POSTING</a></td>";
   echo "</tr>";
   echo "</table>";

   echo "<div id='eventDetails'>";
   echo "<table border = '1' align='center'>";
   echo "<tr>";
   echo "<th>Event Name</th><td><b><i>$eventName</i></b></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Start Date</th><td><i>$eventStartDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>End Date</th><td><i>$eventEndDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Type</th><td><i>$eventTypeName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Location</th><td><i>$eventLocation</i></td>";
   echo "</tr>";
   echo "</table><br>";
   echo "<form action='' method='POST'>";

?>
   
    <div style='width:50%;padding:8px;top:0;bottom: 0;left: 0;right:0;margin: auto;'>
     <fieldset>
      <legend>Search Participant</legend>
       <table align='center'>
        <tr> 
         <td><b>Name:</b>&nbsp;</td>
         <td><input type='text' name='name' placeholder='Type search name here...'></td>
        </tr>
        <tr>
         <td><b>Organization:</b>&nbsp;</td>
         <td><input type='text' name='org' placeholder='Type search organization here...'></td>
        </tr>
        <tr>
         <td><b>Billing Number:</b>&nbsp;<br></td>
         <td><input type='text' name='billing_no'></td>
        </tr>
        <tr>
         <td colspan='2' align='right'><input type='submit' value='SEARCH' name='search'></td>
        </tr>
      </table>
     </fieldset>
    </div>
   

<?php


   if(isset($_POST["post"])){
     $ids = $_POST["billingIds"];
     $acct = $_POST["acct_code"];
      foreach($ids as $billingId){
        updateIndividualEventPost($dbh,$billingId);
        //you can get name, contactId, & email
        $details = getParticipantInfoBilling($dbh,$billingId);
        $contactId = $details["contact_id"];
        $custId = "IIAP".$contactId;
        $name = $details["participant_name"];
        $email = $details["email"];
        $eventType = $details["event_type"];
        $eventName = $details["event_name"];
        $eventId = $details["event_id"];
        $eventDescription = $eventId."/".$eventName;
        $feeAmount = $details["fee_amount"];
        $billingNo = $details["billing_no"];
        $billDate = $details["bill_date"];
        $withVat = $details["vat"] == 0 ? 0 : 1;

        $address = getAddressDetails($dbh,$contactId);
        $street = $address["street"];
        $city = $address["city"];

        $memberId = getMemberId($dbh,$contactId);

        $customer = array();
        $customer["contact_id"] = $contactId;
        $customer["participant_name"] = $name;
        $customer["street"] = $street;
        $customer["city"] = $city;
        $customer["email"] = $email;
        $customer["member_id"] = $memberId;

        $postDate = $_POST["postdate"];
        $exist = checkContactRecordExist($weberp,$contactId);

        if($exist == 0){
          insertCustomer($weberp,$customer);
          $eventTypeName == 'OTH' ? postOTH($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate,$withVat,$acct) : myPost($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate);

        }

        else{
          $eventTypeName == 'OTH' ? postOTH($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate,$withVat,$acct) : myPost($eventType,$eventDescription,$feeAmount,$name,$custId,$billingNo,$billDate,$postDate);

        }
    }

          echo'<div id="confirmation" title="Confirmation">';
          echo "<img src='../webapp/pire/images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>";
          echo'<p>Billing is already posted.</p>';
          echo'</div>';

     $bills = getIndividualBillingsByEvent($dbh,$eventId);
     $display = displayIndividualBillingsByEvent($weberp,$bills,$eventTypeName);
     echo $display;

   }

   elseif(isset($_POST["search"])){
     $searchParameters = array();
     $searchParameters["name"] = $_POST["name"];  
     $searchParameters["org"] =  $_POST["org"];
     $searchParameters["billing_no"] = $_POST["billing_no"];

     $bills = searchIndividualBillingsByEvent($dbh,$eventId,$searchParameters);
     $display = displayIndividualBillingsByEvent($weberp,$bills,$eventTypeName);
     echo $display;

   }

   else{

   $bills = getIndividualBillingsByEvent($dbh,$eventId);
   $display = displayIndividualBillingsByEvent($weberp,$bills,$eventTypeName);
   echo $display;
  }
  
  echo "</form>";

?>
</body>
<script type="text/javascript">                                                                
  $("#check").click(function(){                                                                
          
    if($(this).is(":checked")){                                                                
      $("body input[type=checkbox][class=checkbox]").prop("checked",true);                     
    }else{
      $("body input[type=checkbox][class=checkbox]").prop("checked",false);                    
    }
                                                                                               
  });                                                                                          
</script> 
</html>

