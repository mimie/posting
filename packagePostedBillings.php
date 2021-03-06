<html>
<head>
	<title>Package Posted Billings</title>
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
        $('#billingInfo').jPaginate({
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
  include 'postingFunc/packageViewPost_functions.php';
  include 'login_functions.php';

  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";

  $posted_billings = getAllPostedBillings();
  $display = displayPackagePostedBillings($posted_billings);
  echo $display; 

?>
</body>
</html>
