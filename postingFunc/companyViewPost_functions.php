<?php

function viewAllCompanyPostedBillings($dbh){
  $sql = $dbh->prepare("SELECT bc.event_id, ce.title as event_name, bc.org_contact_id, 
                        cc.display_name as organization_name, bc.billing_no, 
                        bc.total_amount, bc.subtotal,bc.vat, bc.bill_date
                        FROM billing_company bc, civicrm_event ce, civicrm_contact cc
                        WHERE bc.event_id = ce.id
                        AND bc.org_contact_id = cc.id
                        AND post_bill = '1'
                        AND total_amount != '0'");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

function displayCompanyPostedBillings(array $billingDetails){

  $html = "<table id='billingInfo' style='width:100%;'>"
        . "<thead>"
        . "<tr>"
        . "<th>Event Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Billing No</th>"
        . "<th>Total Amoun</th>"
        . "<th>Subtotal</th>"
        . "<th>VAT</th>"
        . "<th>Billing Date</th>"
        . "<th>Billed Participants</th>"
        . "<th>Print Bill</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($billingDetails as $key => $field){
    $eventId = $field["event_id"];
    $orgId = $field["org_contact_id"];
    $eventName = $field["event_name"];
    $orgName = $field["organization_name"];
    $billingNo = $field["billing_no"];
    $totalAmount = $field["total_amount"];
    $subtotal = $field["subtotal"];
    $vat = $field["vat"];
    $billingDate = $field["bill_date"];

    $participantsLink = "<a href='../webapp/pire/billedParticipants.php?eventId=$eventId&billingNo=$billingNo&orgId=$orgId' target='_blank'>"
                        . "<img src='../webapp/pire/participants.png' height='50' width='50'></a>";

    $printBill = "<a href='../webapp/pire/companyBillingReference.php?companyBillingRef=$billingNo&eventId=$eventId&orgId=$orgId' target='_blank'>"
            . "<img src='images/printer-icon.png' width='30' height='30'></a>";
    
    $html = $html."<tr>"
          . "<td>$eventName</td>"
          . "<td>$orgName</td>"
          . "<td>$billingNo</td>"
          . "<td>$totalAmount</td>"
          . "<td>$subtotal</td>"
          . "<td>$vat</td>"
          . "<td>$billingDate</td>"
          . "<td>$participantsLink</td>"
          . "<td>$printBill</td>"
          . "</tr>";
  }
  $html = $html."</tbody></table>";

  return $html;
}

function searchCompanyPostedBillings($dbh,$searchType,$searchValue){

  $searchQuery = '';
  
  switch($searchType){

    case 'org_name':
         $searchQuery = "AND cc.display_name LIKE ? AND cc.contact_type = 'Organization'";
         break;
    case 'event_name':
         $searchQuery = "AND ce.title LIKE ?";
         break;
    case 'billing_no':
         $searchQuery = "AND bc.billing_no LIKE ?";
         break;
  }
  $sql = $dbh->prepare("SELECT bc.event_id, ce.title as event_name, bc.org_contact_id, 
                        cc.display_name as organization_name, bc.billing_no, 
                        bc.total_amount, bc.subtotal,bc.vat, bc.bill_date
                        FROM billing_company bc, civicrm_event ce, civicrm_contact cc
                        WHERE bc.event_id = ce.id
                        AND bc.org_contact_id = cc.id
                        AND post_bill = '1'
                        AND total_amount != '0'
                        $searchQuery");
  $sql->bindValue(1,"%".$searchValue."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
?>
