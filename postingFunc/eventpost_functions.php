<?php

function getGeneratedEventBillings(PDO $dbh){

   $sql = $dbh->prepare("SELECT contact_id, participant_id, event_type, event_name, participant_name,
                         organization_name, org_contact_id, fee_amount, billing_no, bill_date
                         FROM billing_details
                         WHERE billing_type = 'Individual' AND post_bill='0'");
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
   
}

function displayEventBillings(array $eventBillings){

    $html = "<table width='100%'>"
          . "<thead>"
          . "<tr>"
          . "<th>Select Contact</th>"
          . "<th>Participant Id</th>"
          . "<th>Event Type</th>"
          . "<th>Event Name</th>"
          . "<th>Participant Name</th>"
          . "<th>Organization Name</th>"
          . "<th>Fee Amount</th>"
          . "<th>Billing Number</th>"
          . "<th>Billing Date</th>"
          . "</tr>"
          . "</thead>";

    $html = $html."<tbody>";

    foreach($eventBillings as $key => $field){

       $contactId = $field["contact_id"];
       $participantId = $field["participant_id"];
       $eventType = $field["event_type"];
       $eventName = $field["event_name"];
       $name = $field["participant_name"];
       $orgName = $field["organization_name"];
       $feeAmount = $field["fee_amount"];
       $billingNo = $field["billing_no"];
       $date = $field["bill_date"];

       $html = $html."<tr>"
             . "<td><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
             . "<td>$participantId</td>"
             . "<td>$eventType</td>"
             . "<td>$eventName</td>"
             . "<td>$name</td>"
             . "<td>$orgName</td>"
             . "<td>$feeAmount</td>"
             . "<td>$billingNo</td>"
             . "<td>$date</td>"
             . "</tr>";
    }
  
    $html = $html."</tbody></table>";
    return $html;
}

function getGeneratedCompanyBillings($dbh){

   $sql = $dbh->prepare("SELECT event_name, org_contact_id,organization_name, total_amount, subtotal, vat, bill_date
                         FROM billing_company
                         WHERE post_bill = '0'");
   $sql->execute;
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

?>
