<html>
<head>
  <title>Company Event Posting</title>
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
        $('#info').jPaginate({
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
</script>
</head>
<body>
<?php

  include "login_functions.php";
  include "billing_functions.php";
  include "pdo_conn.php";
  include "postingFunc/eventpost_functions.php";
  include "../webapp/pire/company_functions.php";
  include "../weberp/postFunction.php";

  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $menu = logoutDiv($dbh);

  echo $menu;
  echo "<br>";
   echo "<table width='100%'>"
        . "<tr>"
        . "<td  bgcolor='#084B8A'><a href='eventIndividualPosting.php'>INDIVIDUAL EVENT POSTING</a></td>"
        . "<td><a href='eventCompanyPosting.php'>COMPANY EVENT POSTING</a></td>"
        . "</tr>"
        . "</table><br><br>";

   echo "<div style='padding:9px;width:50%;margin:0 auto;'>";
   echo "<form action='' method='POST'>";
   echo "<fieldset>";
   echo "<legend>Search Company Event Billing</legend>";
   echo "Search category:";
   echo "<select name='category'>"
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
     $companyBillings = searchCompanyNonPostedBillings($dbh,$category,$searchValue);
     $display = displayCompanyEventBillings($companyBillings);
     echo $display;
  }

  elseif(isset($_POST["post"])){
     $ids = $_POST["billingIds"];

      foreach($ids as $billingId){
        updateCompanyEventPost($dbh,$billingId);
        $details = getCompanyInfoBilling($dbh,$billingId);
        $orgId = $details["org_contact_id"];
        $orgName = $details["organization_name"];
        $totalAmount = $details["total_amount"];
        $eventName = $details["event_name"];
        $billingNo = $details["billing_no"];
        $email = $details["email"];
      
   
        $eventType = substr($billingNo,0,3);
        $address = getCompanyAddress($dbh,$orgId);
        $city = $address["city"];
        $street = $address["street_address"];

        $customer = array();
        $customer["contact_id"] = $orgId;
        $customer["participant_name"] = $orgName;
        $customer["street"] = $street;
        $customer["city"] = $city;
        $customer["email"] = $email;
        $customer["member_id"] = "NONE";


        $exist = checkContactRecordExist($weberp,$orgId);

        if($exist == 0){
          insertCustomer($weberp,$customer);
          myPost($eventType,$eventName,$totalAmount,$orgName);
        }   
        
        else{
          myPost($eventType,$eventName,$totalAmount,$orgName);
        }
      }
          echo'<div id="confirmation" title="Confirmation">';
          echo "<img src='../webapp/pire/images/confirm.png' alt='confirm' style='float:left;padding:5px;'i width='42' height='42'/>";
          echo'<p>Billing is already posted.</p>';
          echo'</div>';

          $companyBillings = getCompanyNonPostedBillings($dbh);
          $display = displayCompanyEventBillings($companyBillings);
          echo $display;
      
   }

  else{
    $companyBillings = getCompanyNonPostedBillings($dbh);
    $display = displayCompanyEventBillings($companyBillings);
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
