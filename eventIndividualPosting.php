<html>
<head>
 <title>Event Posting</title>
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
   include "billing_functions.php";


   $dbh = civicrmConnect();
   $weberp = weberpConnect();
   $menu = logoutDiv($dbh);


   echo $menu;
   echo "<br>";

   echo "<table width='100%'>"
        . "<tr>"
        . "<td><a href='eventIndividualPosting.php'>INDIVIDUAL EVENT POSTING</a></td>"
        . "<td bgcolor='#084B8A'><a href='eventCompanyPosting.php'>COMPANY EVENT POSTING</a></td>"
        . "</tr>"
        . "</table><br><br>";

   echo "<div style='padding:9px;width:50%;margin:0 auto;'>";
   echo "<form action='' method='POST'>";
   echo "<fieldset>";
   echo "<legend>Search Individual Event Billing</legend>";
   echo "Search category:";
   echo "<select name='category'>"
        . "<option value='name'>Name</option>"
        . "<option value='event_type'>Event Type</option>"
        . "<option value='event_name'>Event Name</option>"
        . "<option value='org_name'>Organization Name</option>"
        . "<option value='billing_no'>Billing No</option>"
        . "</select>";
   echo "&nbsp;<input type='text' name='searchText' placeholder='Enter search text here.....'>";
   echo "<input type='submit' name='search' value='SEARCH'>";
   echo "</fieldset>";
   echo "</div>";

   if(isset($_POST["search"])){
      $category = $_POST["category"];
      $searchValue = $_POST["searchText"];
      $eventBillings = searchNonPostedBilling($dbh,$category,$searchValue);
      $display = displayIndividualEventBillings($eventBillings);
      echo $display;
   }

   elseif(isset($_POST["post"])){
      $ids = $_POST["billingIds"];

      foreach($ids as $billingId){
        //updateIndividualEventPost($dbh,$billingId);
        //you can get name, contactId, & email
        $details = getParticipantInfoBilling($dbh,$billingId);
        $contactId = $details["contact_id"];
        $name = $details["participant_name"];
        $email = $details["email"];

        $address = getAddressDetails($dbh,$contactId);
        $street = $address["street"];
        $city = $address["city"];

        $memberId = getMemberId($dbh,$contactId);

        $customer = array();
        $customer["contact_id"] = $contactId;
        $customer["participant_name"] = $name;
        $customer["street"] = $street;
        $customer["city"] = $city;
        $customer["email"] = $email;
        $customer["member_id"] = $memberId;

        echo $contactId."<br>";

        $exist = checkParticipantRecordExist($weberp,$contactId);
        echo $exist."<br>";

        if($exist == 0){
          insertCustomer($weberp,$customer);
          //echo $contactId;
        }

        else{
         echo "existing";
        }
    }

   }

   else{
      $eventBillings = getIndividualNonPostedBillings($dbh);
      $display = displayIndividualEventBillings($eventBillings);
      echo $display;
   }

?>
   </form>
</body>
<script type="text/javascript">                                                                
  $("#check").click(function(){                                                                
          
    if($(this).is(":checked")){                                                                
      $("body input[type=checkbox][class=checkbox]").prop("checked",true);                     
    }else{
      $("body input[type=checkbox][class=checkbox]").prop("checked",false);                    
    }
                                                                                               
  });                                                                                          
</script> 
</html>
