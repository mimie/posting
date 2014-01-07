<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Membership Posting</title>
  <link rel="stylesheet" type="text/css" href="../billingStyle.css">
  <link rel="stylesheet" type="text/css" href="../menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="../js/jquery-jPaginate.js"></script>
  <script src="../js/jquery.tablesorter.js"></script>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('table').jPaginate({
                'max': 50,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

</script>
</head>
<body>
<?php
  include '../login_functions.php';
  include '../pdo_conn.php';
  include '../postingFunc/memberpost_functions.php';
  
  $dbh = civicrmConnect();
  $logout = logoutDiv($dbh);
  echo $logout;

?>
   <br>
   <table width='100%'>
    <tr>
     <td align='center' bgcolor="#084B8A"><a href='../membershipIndividualBilling.php?&user=<?=$userId?>'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='../membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
     <td align='center' bgcolor='white'><a href='memberPosting/membershipIndividualPosting.php'>INDIVIDUAL POSTING</td>
    </tr>
   </table><br>
</body>
</html>
