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

function displayCompanyPostedBillings($weberp,array $billingDetails){

  $html = "<table id='billingInfo' style='width:100%;'>"
        . "<thead>"
        . "<tr>"
        . "<td bgcolor='#084B8A' colspan='11'>"
        . "<input type='text' id='postDate' name='postdate' placeholder='Select post date..'>"
        . "<input type='submit' name='update' value='UPDATE POST DATE'>"
        . "</td></tr>"
        . "<tr>"
        . "<th>Select bill</th>"
        . "<th>Event Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Billing No</th>"
        . "<th>Total Amoun</th>"
        . "<th>Subtotal</th>"
        . "<th>VAT</th>"
        . "<th>Billing Date</th>"
        . "<th>Post Date</th>"
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

    $sql = $weberp->prepare("SELECT trandate FROM gltrans WHERE voucherno = ?");
    $sql->bindValue(1,$billingNo,PDO::PARAM_STR);
    $sql->execute();
    $postDate = $sql->fetchColumn();

    $participantsLink = "<a href='../webapp/pire/billedParticipants.php?eventId=$eventId&billingNo=$billingNo&orgId=$orgId' target='_blank'>"
                        . "<img src='../webapp/pire/participants.png' height='50' width='50'></a>";

    $printBill = "<a href='../webapp/pire/companyBillingReference.php?companyBillingRef=$billingNo&eventId=$eventId&orgId=$orgId' target='_blank'>"
            . "<img src='images/printer-icon.png' width='30' height='30'></a>";
    
    $html = $html."<tr>"
          . "<td><input type='checkbox' name='billingNos[]' value='$billingNo'></td>"
          . "<td>$eventName</td>"
          . "<td>$orgName</td>"
          . "<td>$billingNo</td>"
          . "<td>$totalAmount</td>"
          . "<td>$subtotal</td>"
          . "<td>$vat</td>"
          . "<td>$billingDate</td>"
          . "<td>$postDate</td>"
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

function searchCompanyPostedBillingsByDate($dbh,$startDate,$endDate){

  $startDate = date("Y-m-d",strtotime($startDate));
  $endDate = date("Y-m-d",strtotime($endDate));

  $startDate = $startDate." 00:00:00";
  $endDate = $endDate." 23:59:59";
  $sql = $dbh->prepare("SELECT bc.event_id, ce.title as event_name, bc.org_contact_id, 
                        cc.display_name as organization_name, bc.billing_no, 
                        bc.total_amount, bc.subtotal,bc.vat, bc.bill_date
                        FROM billing_company bc, civicrm_event ce, civicrm_contact cc
                        WHERE bc.event_id = ce.id
                        AND bc.org_contact_id = cc.id
                        AND post_bill = '1'
                        AND total_amount != '0'
                        AND bc.bill_date BETWEEN ? AND ?");
 $sql->bindValue(1,$startDate,PDO::PARAM_STR);
 $sql->bindValue(2,$endDate,PDO::PARAM_STR);

 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);

 return $result;
}
?>
