<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Membership Billing</title>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
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
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';
  include 'billing_functions.php';

  $dbh = civicrmConnect();

  session_start();
  //if the user has not logged in
  /**if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  @$userId = $_GET["user"];**/

  $logout = logoutDiv($dbh);
  echo $logout;
  /**$header = headerDiv();
  echo $header;**/

  $lastYear = date('Y',strtotime('-1 year'));
  $currentYear = date('Y');
  $nextYear = date('Y',strtotime('+1 year'));

  //$expiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));
  $currentExpiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));
  $lastExpiredDate = date('Y-m-d',strtotime($lastYear.'-12-31'));
  $nextExpiredDate = date('Y-m-d',strtotime($nextYear.'-12-31'));

  $formatCurrent = date('F j Y',strtotime($currentExpiredDate));
  $formatLast = date('F j Y',strtotime($lastExpiredDate));
  $formatNext = date('F j Y',strtotime($nextExpiredDate));
?>

    <br>
    <div style="background-color:#A9E2F3;">
  
   <table width='100%'>
    <tr>
     <td align='center' bgcolor="white"><a href='membershipIndividualBilling.php?&user=<?=$userId?>'>INDIVIDUAL BILLING</a></td>
     <td align='center' bgcolor='#084B8A'><a href='membershipCompanyBilling.php?&user=<?=$userId?>'>COMPANY BILLING</td>
    </tr>
   </table><br>

    <form method="POST" action="">
      <div style="float:left;">
        <select name="expiredDate" id="expiredDate">
         <option value="select">- Select date of expiration -</option>
         <option value="<?=$lastExpiredDate?>"><?=$formatLast?></option>
         <option value="<?=$currentExpiredDate?>"><?=$formatCurrent?></option>
         <option value="<?=$nextExpiredDate?>"><?=$formatNext?></option>
        </select>&nbsp;
        <select name="searchOptions" id="searchOptions">
         <option value="select">- Select search option -</option>
         <option value="Name">Name</option>
         <option value="Status">Status</option>
         <option value="Membership Type">Membership Type</option>
         <option value="statusAndType">Status and Membership Type</option>
        </select>
      </div>
      <!--This textbox will appear only when name is selected in the search options-->
      <div id="searchName" style="display:none;float:left;">
        &nbsp;&nbsp;Member Name:&nbsp;<input type="textbox" name="name">
      </div>
      <!-- This textbox will appear only when status is selected in the search options-->
      <div id="searchStatus" style="display:none;float:left;">
        &nbsp;&nbsp;Status:&nbsp;
        <select name="status">
        <option value="select">- Select membership status -</option>
<?php
        $status = getAllMembershipStatus($dbh);
        foreach($status as $statusId => $statusName){
          echo "<option value=$statusId>$statusName</option>";
        }
?>
        </select>
      </div>
      <div id="searchType" style="display:none;float:left">
        &nbsp;&nbsp;Membership Type:&nbsp;
        <select name="type">
          <option value="select">- Select membership type -</option>
<?php
         $type = getAllMembershipType($dbh);

         foreach($type as $typeId => $membershipType){
             echo "<option value='$typeId'>$membershipType</option>";
         }
?>
        </select>
      </div>
      
      <div style="float:left;">
        <input type="submit" name="dates" value="View Members" onclick="defaultSelect(document.getElementById('expiredDate'),'Please select an expired date to view members.')">
      </div>
    </form><br>    
<?php

  if(isset($_POST["expiredDate"])){

    $expiredDate = $_POST["expiredDate"];
    $membersToExpire = getMembersToExpire($dbh,$expiredDate);
  }

  elseif(isset($_POST["expiredDate"]) && isset($_POST["name"])){

    $name = $_POST["name"];
    $expiredDate = $_POST["expiredDate"];
    $membersToExpire = getMembersByName($dbh,$expiredDate,$name);
  }

  else{

    if(isset($_GET["endDate"])){
      $expiredDate = $_GET["endDate"];
      $membersToExpire = getMembersToExpire($dbh,$expiredDate);
    }
   
    else{    
      $expiredDate = $currentExpiredDate;
      $membersToExpire = getMembersToExpire($dbh,$expiredDate);
     }
  }

  $members = array();
  $memberInfo = array();

  foreach($membersToExpire as $key => $details){ 
    $membershipId = $details["id"];
    $contactId = $details["contact_id"];
    $statusId = $details["status_id"];
    $typeId = $details["membership_type_id"];
    $joinDate = $details["join_date"];
    $startDate = $details["start_date"];
    $endDate = $details["end_date"];
    $status = getMembershipStatus($dbh,$statusId);
    $memberType = getMemberType($dbh,$typeId);
    $memberId = getMemberId($dbh,$contactId);

    $contactDetails = getContactDetails($dbh,$contactId);
    $name = $contactDetails["name"];
    $orgname = $contactDetails["companyName"];
    $orgId = getOrgId($dbh,$orgname);
    $email = getContactEmail($dbh,$contactId);
    $feeAmount = getMemberFeeAmount($dbh,$typeId);
    $addressDetails = getAddressDetails($dbh,$contactId);
    $street = $addressDetails["street"];
    $city = $addressDetails["city"];
    $address = $street." ".$city;
    //echo mb_convert_encoding($name, "UTF-8");

    $memberInfo["contact_id"] = $contactId;
    $memberInfo["status"] = $status;
    $memberInfo["name"] = $name;
    $memberInfo["company"] = $orgname;
    $memberInfo["email"] = $email;
    $memberInfo["fee_amount"] = $feeAmount;
    $memberInfo["address"] = $address;
    $memberInfo["join_date"] = $joinDate;
    $memberInfo["start_date"] = $startDate;
    $memberInfo["end_date"] = $endDate;
    $memberInfo["member_type"] = $memberType;
    $memberInfo["street"] = $street;
    $memberInfo["city"] = $city;
    $memberInfo["org_contact_id"] = $orgId;
    $memberInfo["membership_id"] = $membershipId;
    $memberInfo["member_id"] = $memberId;

    $members[$membershipId] = $memberInfo;
    unset($memberInfo);
    $memberInfo = array();
  }

    $currentYear = date("Y");
    $nextYear = date("Y", strtotime('+1 years'));
?>

    <form method="POST" action="membershipIndividualBilling.php?endDate=<?=$expiredDate?>">
     <select name="actionType" id="action">
      <option value="select">- Select actions -</option>
      <option value="" disabled>------------------------</option>
      <option value="Generate Bill">Generate Bill</option>
      <option value="Send Bill">Send Bill</option>
    </select>&nbsp;
    <input type="submit" name="process" value="Process Action" onclick="defaultSelect(document.getElementById('action'),'Please select an action.')"><br>
       <div id="memberyear" style="display:none;">
         <b>Select year of membership:</b>&nbsp;
         <input type="radio" name="year" value="<?=$currentYear?>"><?=$currentYear?>&nbsp;
         <input type="radio" name="year" value="<?=$nextYear?>"><?=$nextYear?><br>
       </div>
    <input type="checkbox" id="check">Check All

<?php

  $displayBilling = displayMemberBilling($dbh,$members,$expiredDate);
  echo $displayBilling;
  echo "</form>";
  echo "</div>";


  if(isset($_POST["actionType"]) == 'Generate Bill'){

    $membershipIds = $_POST["membershipIds"];
    $membershipYear = $_POST["year"];
    $prevYear = $membershipYear - 1;
    $expirationDate = $prevYear."-12-31";
    
   

    foreach($membershipIds as $id){

        $sql = $dbh->prepare("SELECT id,contact_id,end_date,status_id,membership_type_id,join_date,start_date,end_date
                              FROM civicrm_membership
                              WHERE membership_type_id = '1'
                              AND end_date = '$expirationDate'
                              AND id = '$id'
                             ");
        $sql->execute();
        $details = $sql->fetch(PDO::FETCH_ASSOC);
    
        $membershipId = $details["id"];
        $contactId = $details["contact_id"];
        $statusId = $details["status_id"];
        $typeId = $details["membership_type_id"];
        $joinDate = $details["join_date"];
        $startDate = $details["start_date"];
        $endDate = $details["end_date"];
        $status = getMembershipStatus($dbh,$statusId);
        $memberType = getMemberType($dbh,$typeId);
        $memberId = getMemberId($dbh,$contactId);

        $contactDetails = getContactDetails($dbh,$contactId);
        $name = $contactDetails["name"];
        $orgname = $contactDetails["companyName"];
        $orgId = getOrgId($dbh,$orgname);
        $email = getContactEmail($dbh,$contactId);
        $feeAmount = getMemberFeeAmount($dbh,$typeId);
        $addressDetails = getAddressDetails($dbh,$contactId);
        $street = $addressDetails["street"];
        $city = $addressDetails["city"];
        $address = $street." ".$city;
        //echo mb_convert_encoding($name, "UTF-8");
       
        $memberInfo = array();

        $memberInfo["contact_id"] = $contactId;
        $memberInfo["status"] = $status;
        $memberInfo["name"] = $name;
        $memberInfo["company"] = $orgname;
        $memberInfo["email"] = $email;
        $memberInfo["fee_amount"] = $feeAmount;
        $memberInfo["address"] = $address;
        $memberInfo["join_date"] = $joinDate;
        $memberInfo["start_date"] = $startDate;
        $memberInfo["end_date"] = $endDate;
        $memberInfo["member_type"] = $memberType;
        $memberInfo["street"] = $street;
        $memberInfo["city"] = $city;
        $memberInfo["org_contact_id"] = $orgId;
        $memberInfo["membership_id"] = $membershipId;
        $memberInfo["member_id"] = $memberId;

        insertMemberBilling($dbh,$memberInfo,$membershipYear);
    }
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

  $("#action").change(function(){

    var selectedAction = this.value;    
    if(selectedAction == "Generate Bill"){
      $("#memberyear").show();
    } 

    if(selectedAction == "Send Bill" || selectedAction == "select"){
      $("#memberyear").hide();
    }


  });

  $("#searchOptions").change(function(){

    var selectedOption = this.value;
    if(selectedOption == "Name"){
      $("#searchName").show();
      $("#searchStatus").hide();
      $("#searchType").hide();
    }

    if(selectedOption == "Status"){
      $("#searchName").hide();
      $("#searchType").hide();
      $("#searchStatus").show();
    }

    if(selectedOption == "select"){
      $("#searchName").hide();
      $("#searchStatus").hide();
      $("#searchType").hide();
    }

    if(selectedOption == "Membership Type"){
      $("#searchName").hide();
      $("#searchStatus").hide();
      $("#searchType").show();
    }

    if(selectedOption == "statusAndType"){
      $("#searchName").hide();
      $("#searchStatus").show();
      $("#searchType").show();
    }
    
    //alert("something");
  });

  function defaultSelect(elem,helperMessage){
    if(elem.value == 'select'){
       alert(helperMessage);
       elem.focus;
       return false;
    }

    return true;

  }
</script>
</html>
