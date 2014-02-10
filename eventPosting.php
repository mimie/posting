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

   $dbh = civicrmConnect();
   $menu = logoutDiv($dbh);

   echo $menu;
?>
</body>
</html>
