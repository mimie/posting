<?php

function getIndividualBillingsByEvent($dbh,$eventId){

  $sql = $dbh->prepare("SELECT bd.id as billing_id, bd.participant_id,cc.sort_name,cc.display_name,bd.organization_name,
                        bd.fee_amount, bd.subtotal,bd.vat,bd.billing_no,bd.bill_date,bd.post_bill, cps.name as status
                        FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cps,civicrm_contact cc
                        WHERE cp.id = bd.participant_id
                        AND cp.status_id = cps.id
                        AND bd.billing_type = 'Individual'
                        AND cp.contact_id = cc.id
                        AND bd.event_id = ?
                       ");
  $sql->bindValue(1,$eventId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayIndividualBillingsByEvent(array $bills){

  $html = "<table id='billInfo' style='width:100%;'>"
        . "<thead>"
        . "<tr>"
        . "<th>Post Bill</th>"
        . "<th>Participant Id</th>"
        . "<th>Prefix</th>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Participant Status</th>"
        . "<th>Fee Amount</th>"
        . "<th>Subtotal</th>"
        . "<th>VAT</th>"
        . "<th>Billing Number</th>"
        . "<th>Billing Date</th>"
        . "<th>Print Bill</th>"
        . "<tr>"
        . "</thead>";

 $html = $html."<tbody>";

  foreach($bills as $key => $field){
    $billingId = $field["billing_id"];
    $participantId = $field["participant_id"];
    $name = $field["sort_name"];
    $displayName = $field["display_name"];
    $firstWord = strtok($displayName, " ");
    $prefix = in_array($firstWord,$prefixes) ? $firstWord : '';
    $orgName = $field["organization_name"];
    $status = $field["status"];
    $feeAmount = number_format($field["fee_amount"], 2, '.',',');
    $subtotal = number_format($field["subtotal"], 2, '.',',');
    $vat = number_format($field["vat"], 2, '.',',');
    $billingNo = $field["billing_no"];
    $billDate = date("F j, Y",strtotime($field["bill_date"]));
    $postBill = $field["post_bill"];

    
    $enabled = $postBill == '0' || $status == 'Attended' ? "class='checkbox'" : "disabled";

    $html = $html."<tr>"
          . "<td><input type='checkbox' name='billingIds[]' value='$billingId' $enabled></td>"
          . "<td>$participantId</td>"
          . "<td>$prefix</td>"
          . "<td>$name</td>"
          . "<td>$orgName</td>"
          . "<td>$status</td>"
          . "<td>$feeAmount</td>"
          . "<td>$subtotal</td>"
          . "<td>$vat</td>"
          . "<td>$billingNo</td>"
          . "<td>$billDate</td>"
          . "<td></td>";
  }

  $html = $html."</tbody></table>";

  return $html;
}

?>
