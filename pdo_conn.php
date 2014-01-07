<?

  function civicrmConnect(){

    $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;

  }

  function weberpConnect(){

   $weberpConn = new PDO('mysql:host=localhost;dbname=IIAP_DEV','root','mysqladmin');
   $weberpConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   return $weberpConn;

  }  

?>
