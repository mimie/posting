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
  include 'billing_functions.php';

  $dbh = civicrmConnect();
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

   $bills = getIndividualBillingsByEvent($dbh,$eventId);
   $display = displayIndividualBillingsByEvent($bills);
   echo $display;
  

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

