<html>
<head>
<title>Participant Listing</title>
 <link rel="stylesheet" type="text/css" href="billingStyle.css">
 <link rel="stylesheet" type="text/css" href="menu.css">
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css" />
<script type="text/javascript">
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $("#billings").jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
</script>
</head>
<body>
<?php

  include 'dbcon.php';
  include 'pdo_conn.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include 'login_functions.php';

  $dbh = civicrmConnect();

  /**session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  $userId = $_GET["user"];**/
  
  $logout = logoutDiv($dbh);
  echo $logout;
  echo "<br>";

  $eventId = $_GET["eventId"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);
   //navigation
   echo "<div id = 'navigation'>";
   echo "<a href='events2.php'><b>Event List</b></a>";
   echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
   echo "<i>$eventName</i>";
   echo "</div>";
   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td><a href='participantListing.php?eventId=$eventId'>ALL PARTICIPANTS</a></td>";
   echo "<td bgcolor='#084B8A'><a href='EventIndividualPostingByEvent.php?eventId=$eventId'>INDIVIDUAL EVENT POSTING</a></td>";
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
   echo "</table>";
   echo "</div>";


/*  $displayEventHeader = displayEventHeader($eventId);
  echo $displayEventHeader;*/

  $filterParticipantForm = filterParticipantForm();
  echo $filterParticipantForm;
  $searchParticipantByName = "";


  if(isset($_POST["searchNameEmail"])){
    
    $searchNameEmail = $_POST["nameEmailTb"];
    /**$contactIds = getContactIdSearchName($eventId,$searchNameEmail);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;*/
    $participants = findParticipantByCategory($dbh,$eventId,"name",$searchNameEmail);
    echo $participants;

  }

  elseif(isset($_POST["searchOrg"])){

    $searchOrg = $_POST["orgTb"];
    /*$contactIds = getContactIdSearchOrg($eventId,$searchOrg);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;**/
    $participants = findParticipantByCategory($dbh,$eventId,"org",$searchOrg);
    echo $participants;
  }

  elseif(isset($_POST["searchStatus"])){
    $statusId = $_POST["statusTypeSelect"];
    /**$contactIds = getContactIdSearchStatusId($eventId,$statusId);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;**/
    $participants = findParticipantByCategory($dbh,$eventId,"status",$statusId);
    echo $participants;
  }

  elseif(isset($_POST["updateStatus"])){
    $contactIds = $_POST["contactIds"];
    $statusId = $_POST["statusType"];
    
      foreach($contactIds as $contactId){
        updateParticipantStatus($dbh,$contactId,$eventId,$statusId);
      }

      if($searchParticipantByName){
        echo $searchParticipantByName;
      }

      else{
      
        $displayParticipants = getParticipantByEvent($dbh,$eventId);
        echo $displayParticipants;
       }
  }

  else{
  
    $displayParticipants = getParticipantByEvent($dbh,$eventId);
    echo $displayParticipants;
  }
  
?>
</body>
</html>
