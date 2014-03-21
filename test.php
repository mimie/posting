<?php

  include 'pdo_conn.php';
  include 'weberp_functions.php';

  $dbh = civicrmConnect();
?>
<html>
<head>
<title>This is for testing only.</title>
</head>
<body>
<?php

  $result = searchEventName($dbh,"tools & tech");
  var_dump($result);
?>
</body>
</html>
