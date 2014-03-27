
<html>
<head>
<title>Participant Listing</title>
 <link rel="stylesheet" type="text/css" href="billingStyle.css">
 <link rel="stylesheet" type="text/css" href="menu.css">
<script>
</script>
</head>
<body>
<?php

  include 'pdo_conn.php';
  include 'login_functions.php';

  $dbh = civicrmConnect();
  include 'billing_functions.php';
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
   echo "<td><a href='EventIndividualPostingByEvent.php'>INDIVIDUAL EVENT POSTING</a></td>";
   echo "<td bgcolor='#084B8A'><a href='EventCompanyPostingByEvent.php'>COMPANY EVENT POSTING</a></td>";
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
  

?>
</body>
</html>

