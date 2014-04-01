<?php

function getEventByDate($startDate,$endDate){

  $sql = "SELECT id FROM civicrm_event\n"
       . "WHERE start_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' ORDER BY start_date DESC";
  $result = mysql_query($sql) or die(mysql_error());

  $eventIds = array();

  while($row = mysql_fetch_assoc($result)){
    $eventIds[] = $row['id'];
  }

  return $eventIds;
}

function displayEvents($eventIds){

  $allEvents = getAllEvents();
  $html = "<table id='eventInfo' align='center' border='1'>"
        . "<thead>"
        . "<tr>"
        . "<th>Event Title</th>"
        . "<th>Event Date</th>"
        //. "<th>Participant List</th>"
       // . "<th>Billing</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."</tbody>";

  foreach($eventIds as $id){

    $eventInfo = $allEvents["$id"];
    $title = $eventInfo["title"];
    $date = $eventInfo["start_date"];

    $html = $html."<tr>"
          ."<td><a href='participantListing.php?eventId=".$id."' style='text-decoration:none;' title='Click event name to view participants'>$title</a></td>"
          ."<td>".formatDate($date)."</td>"
          //."<td align='center'><a href='participantListing.php?eventId=".$id."'><img src='participants.png' height='50' width='50'></a></td>"
         // ."<td align='center'><a href='individualBilling.php?eventId=".$id."&billingType=individual'><img src='billing_icon.png' height='50' width='70'></a></td>"
          ."</tr>";

  }

  $html = $html."</tbody></table>";
  return $html;
}

function getStatusId($eventId,$contactId){

  $sql = "SELECT status_id FROM civicrm_participant WHERE event_id = '$eventId' AND contact_id='$contactId'";
  $result = mysql_query($sql) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $statusId = $row["status_id"];

  return $statusId;
}


function searchEventName($dbh,$eventName){

  /*$allEvents = getAllEvents();
  $eventIdMatches = array();

  $patternEvent = "/\b\w*".$eventName."\w*\b/";

  foreach($allEvents as $eventId => $details){
    $title = $details["title"];
    $result = preg_match($patternEvent,$title);

    if($result == 1){
      $eventIdMatches[] = $eventId;

    }
  }*/

  $sql = $dbh->prepare("SELECT id FROM civicrm_event WHERE title LIKE ?");
  $sql->bindValue(1,"%".$eventName."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  $eventIdMatches = array();

  foreach($result as $key => $field){
    $eventIdMatches[] = $field["id"];
  }
  
  return $eventIdMatches;
}

function getParticipantByEvent($dbh,$eventId){

 $allEmails = getAllEmails();
 $status = getParticipantStatusType();
 $statusSelector = participantStatusSelector();
 //$filterParticipantForm = filterParticipantForm();

 //$html = $filterParticipantForm;
 $prefixes = array("Mr.","Mrs.","Ms.","Dr.","Sr.","Jr.");

 $sql = $dbh->prepare("SELECT cp.id as participant_id,cp.contact_id, cp.status_id,cp.event_id,cc.sort_name,cc.display_name,cc.organization_name, cs.name as status, cp.fee_amount
                       FROM civicrm_participant cp,civicrm_contact cc, civicrm_participant_status_type cs
                       WHERE cp.contact_id = cc.id
                       AND cp.status_id = cs.id 
                       AND cp.event_id = ?
                       AND cp.fee_amount != '0'
                       ORDER BY cc.sort_name");

 $sql->bindParam(1,$eventId,PDO::PARAM_INT);
 $sql->execute();

 $participants = $sql->fetchAll(PDO::FETCH_ASSOC);
 
 $html = "<form id='participants' method='post'>"
       . "<div align='center' style='padding:6px;'>$statusSelector</div>";
 
 $html = $html."<table border='1' align='center' id='billings'>"
       . "<thead>"
       . "<tr>"
       . "<th>Prefix</th>"
       . "<th>Participant Name</th>"
       . "<th>Organization Name</th>"
       . "<th>Email Address</th>"
       . "<th>Participant Status</th>"
       . "<th>Change Participant Status</th>"
       . "<th>Fee Amount</th>"
       . "<th>Billing Type</th>"
       . "<th>Billing No.</th>"
       . "<tr>"
       . "</thead>";

 $html = $html."<tbody>";

 foreach($participants as $key => $field){

  $name = $field["sort_name"];
  $name = mb_convert_encoding($name,"UTF-8");
  $displayName = $field["display_name"];
  $org = $field["organization_name"];
  $statusName = $field["status"];
  $contactId = $field["contact_id"];
  $email = $allEmails[$contactId];
  $feeAmount = $field["fee_amount"];
  $feeAmount = number_format($feeAmount, 2, '.',','); 
  $firstWord = strtok($displayName, " ");
  $prefix = in_array($firstWord,$prefixes) ? $firstWord : '';
  $eventId = $field["event_id"];

  $sql = $dbh->prepare("SELECT billing_type,billing_no FROM billing_details WHERE contact_id = ? AND event_id = ?");
  $sql->bindValue(1,$contactId,PDO::PARAM_INT);
  $sql->bindValue(2,$eventId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $billingType = $result["billing_type"];
  $billingNo = $result["billing_no"];

  $html = $html."<tr>"
        . "<td>$prefix</td>"
        . "<td>$name</td>"
        . "<td>$org</td>"
        . "<td>$email</td>"
        . "<td align='center'>$statusName</td>"
        . "<td align='center' style='width:3%;'><input type='checkbox' name='contactIds[]' value='$contactId' class='checkbox'></td>"
        . "<td align='center'>$feeAmount</td>"
        . "<td>$billingType</td>"
        . "<td>$billingNo</td>"
        . "</tr>";
  }

  $html = $html."</tbody></table></form>";

  return $html;
}


function findParticipantByCategory($dbh,$eventId,$searchCategory,$searchValue){
 
  $allEmails = getAllEmails();
  $status = getParticipantStatusType();
  $statusSelector = participantStatusSelector();
  //$filterParticipantForm = filterParticipantForm();
 
  //$html = $filterParticipantForm;
 
  switch($searchCategory){
    case "name":
         $query = "AND cc.sort_name LIKE ?";
         break;
 
    case "org":
         $query = "AND cc.organization_name LIKE ?";
         break;
 
    case "status":
         $query = "AND cp.status_id = ?";
         break;
 
  }

  echo $searchCategory;

  $prefixes = array("Mr.","Mrs.","Ms.","Dr.","Sr.","Jr.");
 
  $sql = $dbh->prepare("SELECT cp.id as participant_id,cp.contact_id, cp.status_id,cp.event_id,cc.sort_name,cc.display_name,cc.organization_name, cs.name as status, cp.fee_amount
                        FROM civicrm_participant cp
                        INNER JOIN civicrm_contact cc ON cp.contact_id = cc.id
                        INNER JOIN civicrm_participant_status_type cs ON cp.status_id = cs.id 
                        WHERE cp.event_id = ?
                        $query
                        ORDER BY cc.sort_name");
  $sql->bindValue(1,$eventId,PDO::PARAM_INT);
  if($searchCategory == 'status'){
      $sql->bindValue(2,$searchValue,PDO::PARAM_INT);
  }
  else{
      $sql->bindValue(2,"%".$searchValue."%",PDO::PARAM_STR);
  }
  $sql->execute();
 
  $participants = $sql->fetchAll(PDO::FETCH_ASSOC);
 
 
  $html = "<form id='participants' method='post'>"
        . "<div align='center' style='padding:6px;'>$statusSelector</div>";
 
  $html = $html."<table border='1' align='center'>"
        . "<tr>"
        . "<th>Prefix</th>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Email Address</th>"
        . "<th>Participant Status</th>"
        . "<th>Change Participant Status</th>"
        . "<th>Fee Amount</th>"
        . "<th>Billing Type</th>"
        . "<th>Billing No.</th>"
        . "<tr>";
 
  foreach($participants as $key => $field){
 
   $name = $field["sort_name"];
   $displayName = $field["display_name"];
   $org = $field["organization_name"];
   $statusName = $field["status"];
   $contactId = $field["contact_id"];
   $email = $allEmails[$contactId];
   $feeAmount = $field["fee_amount"];
   $feeAmount = number_format($feeAmount, 2, '.',',');
   $firstWord = strtok($displayName, " ");
   $prefix = in_array($firstWord,$prefixes) ? $firstWord : '';
 
   $sql = $dbh->prepare("SELECT billing_type,billing_no FROM billing_details WHERE contact_id = ?");
   $sql->bindParam(1,$contactId,PDO::PARAM_INT);
   $sql->execute();
   $result = $sql->fetch(PDO::FETCH_ASSOC);
   $billingType = $result["billing_type"];
   $billingNo = $result["billing_no"];
 
   $html = $html."<tr>"
         . "<td>$prefix</td>"
         . "<td>$name</td>"
         . "<td>$org</td>"
         . "<td>$email</td>"
         . "<td align='center'>$statusName</td>"
         . "<td align='center' style='width:3%;'><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
         . "<td align='center'>$feeAmount</td>"
         . "<td>$billingType</td>"
         . "<td>$billingNo</td>"
         . "</tr>";
   }
 
   $html = $html."</form></table>";
 
   return $html;
 }


/*
 *display searched participant by name or email
 */
function searchedParticipantListByName(array $contactIds,$eventId){

 $allContacts = getAllContacts();
 $allEmails = getAllEmails();
 $status = getParticipantStatusType();
 $statusSelector = participantStatusSelector();

 $html = "<form id='participants' method='post'>"
       . "<div align='center' style='padding:6px;'>$statusSelector</div>";
 
 $html = $html."<table border='1' align='center'>"
       . "<tr>"
       . "<th>Participant Name</th>"
       . "<th>Organization Name</th>"
       . "<th>Email Address</th>"
       . "<th>Participant Status</th>"
       . "<th>Change Participant Status</th>"
       . "<th>Fee Amount</th>"
//       . "<th>Post</th>"
       . "<tr>";

 foreach($contactIds as $id){

  $details = $allContacts[$id];
  $name = $details["name"];
  $org = $details["org"];
  $email = $allEmails[$id];

  $feeAmount = getParticipantFeeAmount($id,$eventId);
  
  $statusId = getParticipantStatusId($id,$eventId);
  $statusName = $status[$statusId];
  //$statusTypeSelectForm = statusTypeSelectForm($statusId);

  $html = $html."<tr>"
        . "<td>$name</td>"
        . "<td>$org</td>"
        . "<td>$email</td>"
        . "<td align='center'>$statusName</td>"
        . "<td align='center'><input type='checkbox' name='contactIds[]' value='$id'></td>"
        . "<td align='center'>$feeAmount</td>"
  //      . "<td align='center'><input type='submit' value='Post'></td>"
        . "</tr>";
  }

  $html = $html."</form></table>";

  return $html;

}

function getContactIdSearchName($eventId,$searchCriteria){

  $searchCriteria = mysql_real_escape_string($searchCriteria);

  $sql = "SELECT cc.id as contactId FROM civicrm_participant cp, civicrm_email cem, civicrm_contact cc\n"
      . "WHERE cp.event_id='$eventId'\n"
      . "AND cp.contact_id = cc.id\n"
      . "AND cem.contact_id = cc.id\n"
      . "AND (cc.display_name LIKE '%{$searchCriteria}%' OR cem.email LIKE '%{$searchCriteria}%')";

  $result = mysql_query($sql) or die(mysql_error());

  $contactIds = array();

  while($row = mysql_fetch_assoc($result)){

    $contactIds[] = $row["contactId"];
  }

  return $contactIds;
}

function getContactIdSearchOrg($eventId,$orgName){

  $orgName = mysql_real_escape_string($orgName);
  $sql = "SELECT organization_name, cc.id AS contactId\n"
       . "FROM civicrm_participant cp, civicrm_contact cc\n"
       . "WHERE cp.event_id = '$eventId'\n"
       . "AND cp.contact_id = cc.id\n"
       . "AND cc.organization_name LIKE '%{$orgName}%'";
  $result = mysql_query($sql) or die(mysql_error());

  $contactIds = array();

  while($row = mysql_fetch_assoc($result)){

    $contactIds[] = $row["contactId"];
  }

  return $contactIds;

}

function getContactIdSearchStatusId($eventId,$statusId){

   $sql = "SELECT contact_id,status_id FROM civicrm_participant\n"                                       
        . "WHERE status_id = '$statusId'\n"
        . "AND event_id ='$eventId'";

   $result = mysql_query($sql) or die(mysql_error());

   $contactIds = array();

   while($row = mysql_fetch_assoc($result)){
     $contactIds[] = $row["contact_id"];
   }

  return $contactIds;
}

function getParticipantStatusId($contactId,$eventId){

 $contactId = mysql_real_escape_string($contactId);
 $eventId = mysql_real_escape_string($eventId);
 $sql = "SELECT status_id FROM civicrm_participant\n" 
      . "WHERE contact_id = '{$contactId}'"
      . "AND event_id ='{$eventId}'";
 $result = mysql_query($sql) or die(mysql_error());

 $row = mysql_fetch_assoc($result);
 $statusId = $row["status_id"];

 return $statusId;

}

/*
 *On waitlist, Awaiting Approval, Pending from waitlist,
 *Pending from Approval, Rejected are disabled status type
 */
function getParticipantStatusType(){
 
  $status = array();
  
  $sql = "SELECT id,label FROM civicrm_participant_status_type\n"
       . "WHERE id NOT IN(5,6,7,8,9,10,11,12,13)";
  $result = mysql_query($sql) or die(mysql_error());
 
  while($row = mysql_fetch_assoc($result)){
     
     $statusId = $row["id"];
     $statusName = $row["label"];
  
     $status[$statusId] = $statusName;
  }
  
  return $status;
 }

function statusTypeSelectForm($statusId){

  $status = getParticipantStatusType();

  $html = "<SELECT name='statusType'>";

  foreach($status as $id => $statusName){

    $selected = $statusId == $id ? 'selected' : '';
    var_dump($selected);
    $html = $html."<option value='$id' $selected>$statusName</option>";
  }

  $html = $html."</SELECT>";

  return $html;
}

function displayEventHeader($eventId){

  $eventName = getEventName($eventId);
  $html = "<div align='center'>"
        . "<h3>List of Participants for $eventName</h3>"
        . "</div>";

  return $html;
}

function getParticipantFeeAmount($contactId,$eventId){

  $contactId = mysql_real_escape_string($contactId);
  $eventId = mysql_real_escape_string($eventId);

  $sql = "SELECT fee_amount FROM civicrm_participant\n"
       . "WHERE contact_id ='{$contactId}' AND event_id = '{$eventId}'";
  $result = mysql_query($sql) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $feeAmount = $row["fee_amount"];

  return $feeAmount;
}

function participantStatusSelector(){

  $status = getParticipantStatusType();

  $html = "Change Participant Status: "
        . "<SELECT name='statusType'>"
        . "<option value='select'>Select status</option>"
        . "<option disabled>-------------</option>";
  foreach($status as $id => $statusName){
   $html = $html."<option value='$id'>$statusName</option>";
  }
  $html = $html."</SELECT>";
  $html = $html."Check All<input type='checkbox' id='check'>";
  $html = $html."<input type='submit' value='UPDATE' name='updateStatus'>";

  return $html;
}

/*
 *form for searching the participant
 */
function filterParticipantForm(){

 $status = getParticipantStatusType();

 $html = "<center>"
       . "<div align='center' style='width:50%;'>"
       . "<fieldset>"
       . "<legend><b>Search Participant</b></legend>"
       . "<form method='post' name='searchFilter'>"
       . "<table>"
       . "<tr>"
       . "<td><b>Name or Email:</b></td><td><input type='textbox' name='nameEmailTb'><input type='submit' value='SEARCH' name='searchNameEmail'></td>"
       . "</tr>"
       . "<tr>"
       . "<td><b>Organization Name:</b></td><td><input type='textbox' name='orgTb'><input type='submit' value='SEARCH' name='searchOrg'></td>"
       . "</tr>";

 $html = $html."<tr><td><b>Participant Status:</b></td>"
       . "<td><SELECT name='statusTypeSelect'>"
       . "<option value='select'>Select status</option>"
       . "<option disabled>-------------</option>";

  foreach($status as $id => $statusName){
    $html = $html."<option value='$id'>$statusName</option>";
  }

  $html = $html."</SELECT>"
        . "<input type='submit' value='SEARCH' name='searchStatus'></td>"
        . "</tr>"
        . "</table>"
        . "</form>"
        . "</fieldset>"
        . "</div>"
        . "</center>";

  return $html;
}

/*
 *option_group_id = 14 FROM civicrm_option_group table
 */
function getAllEventTypes(){

 $sql = "SELECT label,value FROM civicrm_option_value\n"
      . "WHERE option_group_id='14'";
 $result = mysql_query($sql) or die(mysql_error());

 $eventTypes = array();

 while($row = mysql_fetch_assoc($result)){
    $eventTypeId = $row["value"];
    $eventTypeName = $row["label"];

    $eventTypes[$eventTypeId] = $eventTypeName;
 }

 return $eventTypes;
}

function eventTypesFilterForm(){

  $allEventTypes = getAllEventTypes();

  $html = "<form name='eventTypes' method='post'>"
        . "<SELECT name='eventType'>"
        . "<option value='select'>Select event type</option>"
        . "<option value='' disabled>-----------------</option>";

  foreach($allEventTypes as $eventTypeId => $eventTypeName){

    $html = $html."<option value='$eventTypeId'>$eventTypeName</option>";
  }

  $html = $html."</SELECT>";
  $html = $html."<input type='submit' value='Search Event Type' name='searchEventType'>";
  $html = $html."</form>";

  return $html;

}

function searchEventType($eventTypeId){

  $sql = "SELECT id FROM civicrm_event\n"
       . "WHERE event_type_id='$eventTypeId'";
  $result = mysql_query($sql) or die(mysql_error());

  $eventIds = array();

  while($row = mysql_fetch_assoc($result)){
    $eventIds[] = $row["id"];
  }

  return $eventIds;
}

function updateParticipantStatus($dbh,$contactId,$eventId,$statusId){

  $contactId = mysql_real_escape_string($contactId);
  $eventId = mysql_real_escape_string($eventId);
  $statusId = mysql_real_escape_string($statusId);

  $sql = "UPDATE civicrm_participant\n"
       . "SET status_id = '{$statusId}'\n"
       . "WHERE event_id = '{$eventId}'\n"
       . "AND contact_id = '{$contactId}'";

  $result = mysql_query($sql) or die(mysql_error());
  $allStatus = getParticipantStatusType();
  $statusName = $allStatus[$statusId];

  //$dbh = new PDO('mysql:host=10.110.215.92;dbname=iiap_civicrm_dev', 'iiap', 'mysqladmin');
  $sqlUpdateBilling = $dbh->prepare("UPDATE billing_details
                                     SET participant_status = '$statusName'
                                     WHERE event_id = '$eventId'
                                     AND contact_id = '$contactId'
                                    ");
 $sqlUpdateBilling->execute();

}
?>

