<?php

function viewAllIndividualPostedBillings($dbh){

  $sql = $dbh->prepare("SELECT bd.participant_id, bd.event_type, ce.title AS event_name, 
                        cc.sort_name AS participant_name, cc.organization_name, cs.name AS participant_status, 
                        bd.fee_amount, bd.billing_no, bd.bill_date
                        FROM billing_details bd, civicrm_event ce, civicrm_contact cc, civicrm_participant cp, civicrm_participant_status_type cs
                        WHERE bd.event_id = ce.id
                        AND bd.contact_id = cc.id
                        AND bd.participant_id = cp.id
                        AND cp.status_id = cs.id
                        AND bd.post_bill =  '1'");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function displayIndividualPostedBilings(array $billingDetails){

  $html = "<table id='billingInfo' style='width:100%;'>"
        . "<thead>"
        . "<tr>"
        . "<th>Participant Id</th>"
        . "<th>Event Type</th>"
        . "<th>Event Name</th>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Participant Status</th>"
        . "<th>Fee Amount</th>"
        . "<th>Billing Number</th>"
        . "<th>Billing Date</th>"
        . "<th>Print Bill</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($billingDetails as $key => $field){
    $participantId = $field["participant_id"];
    $eventType = $field["event_type"];
    $eventName = $field["event_name"];
    $participantName = $field["participant_name"];
    $orgName = $field["organization_name"];
    $participantStatus = $field["participant_status"];
    $feeAmount = $field["fee_amount"];
    $billingNo = $field["billing_no"];
    $billingDate = $field["bill_date"];

    $html = $html."<tr>"
          . "<td>$participantId</td>"
          . "<td>$eventType</td>"
          . "<td>$eventName</td>"
          . "<td>$participantName</td>"
          . "<td>$orgName</td>"
          . "<td>$participantStatus</td>"
          . "<td>$feeAmount</td>"
          . "<td>$billingNo</td>"
          . "<td>$billingDate</td>"
          . "<td></td>"
          . "</tr>";

  }

  $html = $html."</tbody></table>";

  return $html;
}

?>
