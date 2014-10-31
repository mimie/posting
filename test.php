<?php

  include 'pdo_conn.php';
  include 'weberp_functions.php';
  include 'postingFunc/eventCompanyPost_functions.php';

  $dbh = civicrmConnect();
  $weberp = weberpConnect();

  
?>
<html>
<head>
<title>This is for testing only.</title>
</head>
<body>
<?php
	$result = checkBillTotallyCancelled($dbh,"SPR-14-01799");
        echo $result;




?>
</body>
</html>
