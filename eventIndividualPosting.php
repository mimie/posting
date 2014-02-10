<html>
<head>
 <title>Event Posting</title>
 <link rel="stylesheet" type="text/css" href="billingStyle.css">
 <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php
   include "login_functions.php";
   include "pdo_conn.php";
   include "postingFunc/eventpost_functions.php";


   $dbh = civicrmConnect();
   $menu = logoutDiv($dbh);

   echo $menu;
   echo "<br>";

   echo "<table width='100%'>"
        . "<tr>"
        . "<td><a href='eventIndividualPosting.php'>INDIVIDUAL EVENT POSTING</a></td>"
        . "<td bgcolor='#084B8A'><a href='eventCompanyPosting.php'>COMPANY EVENT POSTING</a></td>"
        . "</tr>"
        . "</table><br><br>";

   $eventBillings = getGeneratedEventBillings($dbh);
   $display = displayEventBillings($eventBillings);
   echo $display;
?>
</body>
</html>
