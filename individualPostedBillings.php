<html>
<head>
   <title>Individual Posted Billings</title>
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
  include "login_functions.php";
  include "billing_functions.php";
  include "pdo_conn.php";
  include "postingFunc/eventpost_functions.php";
  include "postingFunc/individualViewPost_functions.php";

  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";
  echo "<form action='' method='POST'>";

  echo "<table width='100%'>"
       . "<tr>"
       . "<td><a href='individualPostedBillings.php'>INDIVIDUAL POSTED BILLINGS</a></td>"
       . "<td bgcolor='#084B8A'><a href='companyPostedBillings.php'>COMPANY POSTED BILLINGS</a></td>"
       . "</tr>"
       . "</table><br><br>";

   echo "<div style='padding:9px;width:50%;margin:0 auto;'>";
   echo "<fieldset>";
   echo "<legend>Search Individual Posted Billing</legend>";
   echo "Search category:";
   echo "<select name='category'>"
        . "<option value='participant_name'>Participant Name</option>"
        . "<option value='event_name'>Event Name</option>"
        . "<option value='org_name'>Organization Name</option>"
        . "<option value='billing_no'>Registration No</option>"
        . "</select>";
   echo "&nbsp;<input type='text' name='searchText' placeholder='Enter search text here.....'>";
   echo "<input type='submit' name='search' value='SEARCH'>";
   echo "<br><br>";
   echo "Search bill date:&nbsp;";
   echo "<input type='text' name='startDate' id='datepickerStart' placeholder='From'>";
   echo "<input type='text'  name='endDate' id='datepickerEnd' placeholder='To'>";
   echo "<input type='submit' value='SEARCH BILL DATE' name='searchDate'>";
   echo "<br><br>";
   echo "</fieldset>";
   echo "</div>";


   if(isset($_POST["search"])){
     $searchType = $_POST["category"];
     $searchValue = $_POST["searchText"];

     $postedBillings = searchPostedBillings($dbh,$searchType,$searchValue);
     $displayBillings = displayIndividualPostedBilings($weberp,$postedBillings);
     echo $displayBillings;

   }

   elseif(isset($_POST["searchDate"])){
     $startDate = $_POST["startDate"];
     $endDate = $_POST["endDate"];
     $postedBillings = searchPostedBillingsByDate($dbh,$startDate,$endDate);
     $displayBillings = displayIndividualPostedBilings($weberp,$postedBillings);
     echo $displayBillings;
    
   }

   elseif(isset($_POST["update"])){
     $vouchers = $_POST["billingNos"];
     $date = $_POST["postdate"];
     foreach($vouchers as $key => $voucherNo){
        updatePostDate($weberp,$voucherNo,$date);
     }

     $allIndividualPostedBillings = viewAllIndividualPostedBillings($dbh);
     $displayBillings = displayIndividualPostedBilings($weberp,$allIndividualPostedBillings);
     echo $displayBillings;
     
   }

   else{
     $allIndividualPostedBillings = viewAllIndividualPostedBillings($dbh);
     $displayBillings = displayIndividualPostedBilings($weberp,$allIndividualPostedBillings);
     echo $displayBillings;
   }

   echo "</form>";

?>
</body>
</html>
