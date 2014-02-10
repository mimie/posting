<html>
<head>
  <title>Company Event Posting</title>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#info').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
</script>
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
        . "<td  bgcolor='#084B8A'><a href='eventIndividualPosting.php'>INDIVIDUAL EVENT POSTING</a></td>"
        . "<td><a href='eventCompanyPosting.php'>COMPANY EVENT POSTING</a></td>"
        . "</tr>"
        . "</table><br><br>";

  $companyBillings = getCompanyNonPostedBillings($dbh);
  $display = displayCompanyEventBillings($companyBillings);
  echo $display;
?>
</body>
</html>
