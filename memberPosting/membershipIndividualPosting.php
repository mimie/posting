<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <!--<meta http-equiv="refresh" content="5">-->
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
                'max': 10,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

function postConfirmation(){

  alert("Membership bill/s are already posted.");
}

</script>
</head>
<body>
<?php
  include '../login_functions.php';
  include '../pdo_conn.php';
  include '../billing_functions.php';
  include '../postingFunc/memberpost_functions.php';
  include '../weberp_functions.php';
  include '../../weberp/postFunction.php';
  include '../menu_functions.php';
  
  $dbh = civicrmConnect();
  $weberp = weberpConnect();
  $menu = menu();
  echo $menu;

?>
   <br>
   <table width='100%'>
    <tr>
     <td align='center' bgcolor="#084B8A"><a href='../membershipIndividualBilling.php?&user=<?=$userId?>'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='../membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
     <td align='center' bgcolor='white'><a href='membershipIndividualPosting.php'>INDIVIDUAL POSTING</td>
    </tr>
   </table><br>
<?php

?>
  <form method="POST" action="">
   Search name:&nbsp;<input type='text' name='contactName'><input type="submit" name="searchContact" value="SEARCH"><br>
<!--   <select name="year">-->
<?php 
   /**$year = 2014;
   echo "<option>- Select transaction year -</option>";
   echo "<option></option>";
 
   for($i=0;$i<=30;$i++){
     echo "<option>$year</option>";
     $year++;
   }**/
?>
<!--  </select>-->
  <!--<input type="submit" value="Show Billings">--><br>
 </form>
 

 <form method="POST" action="membershipIndividualPosting.php?year=<?=$_POST['year']?>">
 <input type="checkbox" id="check">Check All
 <input type="submit" value="Post to Weberp" name="post"><br><br>

<?php
/**
  if(isset($_POST["year"])){
    $yearSelected = $_POST["year"];
    $members = getTransactionsPerYear($dbh,$yearSelected);

    if($members){
      echo "<div style='padding:6px;' align='center'><b><font color='003366'>Generated Membership Billings For Year $yearSelected</font></b></div>";
      $displayBillings = displayBillings($members);
      echo $displayBillings;
    }

    else{
      echo "<div style='border-style:solid;width:30%;'><font color='red'><i><b>No available transactions for the selected year.</i></b></font></div>";
    }

  }

  elseif(isset($_GET["year"])){
     $yearSelected = $_GET["year"];
     $members = getTransactionsPerYear($dbh,$yearSelected);
     echo "<div style='padding:6px;' align='center'><b><font color='003366'>Generated Membership Billings For Year $yearSelected</font></b></div>";
     $displayBillings = displayBillings($members);
     echo $displayBillings;
  }
**/
  if(isset($_POST["searchContact"])){
    $name = $_POST["contactName"];
    $members = searchMemberNonPostedByName($dbh,$name);
    $displayBillings = displayBillings($members);
    echo $displayBillings;

  }

  else{
    $members = getMemberNonPosted($dbh);
    echo "<div style='padding:6px;' align='center'><b><font color='003366'>All Non-Posted Membership Billings</font></b></div>";
    $displayBillings = displayBillings($members);
    echo $displayBillings;
  }
?>
<!--end form for posting the bill -->
</form>
<?php

  if(isset($_POST["post"])){
    $billingIds = $_POST["billingIds"];
    updateMembershipPost($dbh,$billingIds);

    foreach($billingIds as $id){
      $billingDetails = getBillingInfoById($dbh,$id);
      $contactId = $billingDetails['contact_id'];
      $memberId = getMemberId($dbh,$contactId);

      $customerDetails = array();
      $customerDetails["contact_id"] = $billingDetails["contact_id"];
      $customerDetails["participant_name"] = $billingDetails["member_name"];
      $customerDetails["street"] = $billingDetails["street"];
      $customerDetails["city"] = $billingDetails["city"];
      $customerDetails["member_id"] = $memberId;
      $customerDetails["email"] = $billingDetails["email"];

      $membershipYear = $billingDetails["year"];
      $amount = $billingDetails["fee_amount"];
      $description = "Membership $membershipYear";

      $exist = checkMemberRecordExist($weberp,$contactId);
    
      if($exist == 0){
        insertCustomer($weberp,$customerDetails);
        $name = $billingDetails["member_name"];
        myPost("MEM",$description,$amount,$name);
      }

      else{
        $name = $billingDetails["member_name"];
        myPost("MEM",$description,$amount,$name);
      }
    }

    echo'<div id="dialog" title="Confirmation">';
    echo'<p>Billing is already posted.</p>';
    echo'</div>';

  }

?>


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
