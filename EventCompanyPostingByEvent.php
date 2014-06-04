<html>
<head>
 <title>Company Billing</title>
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
        $('#billings').jPaginate({
                'max': 10,
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
   include 'postingFunc/eventCompanyPost_functions.php';
   include 'billing_functions.php';
   include "postingFunc/eventpost_functions.php";
   include "../webapp/pire/company_functions.php";
   include "../weberp/postFunction.php";

   $dbh = civicrmConnect();
   $weberp = weberpConnect();
   $menu = logoutDiv($dbh);
   echo $menu;
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
   echo "<td bgcolor='#084B8A'><a href='EventIndividualPostingByEvent.php?eventId=$eventId'>INDIVIDUAL EVENT POSTING</a></td>";
   echo "<td><a href='EventCompanyPostingByEvent.php?eventId=$eventId'>COMPANY EVENT POSTING</a></td>";
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
   echo "</table>";
   echo "</div>";

   echo "<form action='' method='POST'>";
?>
    <div style='width:50%;padding:8px;top:0;bottom: 0;left: 0;right:0;margin: auto;'>
     <fieldset>
      <legend>Search Participant</legend>
       <table align='center'>
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
     $postDate = $_POST["postdate"];

      foreach($ids as $billingId){
        
        updateCompanyEventPost($dbh,$billingId);
        $details = getCompanyInfoBilling($dbh,$billingId);
        $orgId = $details["org_contact_id"];
        $custId = "IIAP".$orgId;
        $orgName = $details["organization_name"];
        $totalAmount = $details["total_amount"];
        $eventName = $details["event_name"];
        $eventId = $details["event_id"];
        $eventDescription = $eventId."/".$eventName;
        $billingNo = $details["billing_no"];
        $email = $details["email"];
        $billingDate = $details["bill_date"];
      
   
        $eventType = substr($billingNo,0,3);
        $address = getCompanyAddress($dbh,$orgId);
        $city = $address["city"];
        $street = $address["street_address"];

        $customer = array();
        $customer["contact_id"] = $orgId;
        $customer["participant_name"] = $orgName;
        $customer["street"] = $street;
        $customer["city"] = $city;
        $customer["email"] = $email;
        $customer["member_id"] = "NONE";


        $exist = checkContactRecordExist($weberp,$orgId);

        if($exist == 0){
          insertCustomer($weberp,$customer);
          myPost($eventType,$eventDescription,$totalAmount,$orgName,$custId,$billingNo,$billingDate,$postDate);
        }   
        
        else{
          myPost($eventType,$eventDescription,$totalAmount,$orgName,$custId,$billingNo,$billingDate,$postDate);
        }
      }
          echo'<div id="confirmation" title="Confirmation">';
          echo "<img src='../webapp/pire/images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>";
          echo'<p>Billing is already posted.</p>';
          echo'</div>';

     $billings = getCompanyBillingByEvent($dbh,$eventId);
     $display = displayCompanyBillingsByEvent($weberp,$billings,$eventTypeName);
     echo $display;

   }

   elseif(isset($_POST["search"])){
     $searchParameters = array();
     $searchParameters["billing_no"] = $_POST["billing_no"];
     $searchParameters["org"] = $_POST["org"];
     $billings =  searchCompanyBillingsByEvent($dbh,$eventId,$searchParameters);
     $display = displayCompanyBillingsByEvent($weberp,$billings,$eventTypeName);
     echo $display;
   }

   else{
     $billings = getCompanyBillingByEvent($dbh,$eventId);
     $display = displayCompanyBillingsByEvent($weberp,$billings,$eventTypeName);
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
