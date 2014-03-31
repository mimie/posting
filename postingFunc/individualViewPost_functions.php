<?php

function viewAllIndividualPostedBillings($dbh){

  $sql = $dbh->prepare("SELECT bd.participant_id, bd.event_type, ce.title AS event_name, bd.event_id,
                        cc.sort_name AS participant_name, cc.organization_name, cs.name AS participant_status, 
                        bd.fee_amount, bd.billing_no, bd.bill_date
                        FROM billing_details bd, civicrm_event ce, civicrm_contact cc, civicrm_participant cp, civicrm_participant_status_type cs
                        WHERE bd.event_id = ce.id
                        AND bd.contact_id = cc.id
                        AND bd.participant_id = cp.id
                        AND cp.status_id = cs.id
                        AND bd.post_bill =  '1'
                        AND bd.billing_type = 'Individual'");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function displayIndividualPostedBilings($weberp,array $billingDetails){

  $html = "<table id='billingInfo' style='width:100%;'>"
        . "<thead>"
        . "<tr>"
        . "<td bgcolor='#084B8A' colspan='12'>"
        . "<input type='text' name='postdate' id='postDate' placeholder='Select post date..'>"
        . "<input type='submit' name='update' value='UPDATE POST DATE'>"
        . "</td>"
        . "</tr>"
        . "<tr>"
        . "<th>Select bill</th>"
        . "<th>Participant Id</th>"
        . "<th>Event Type</th>"
        . "<th>Event Name</th>"
        . "<th>Participant Name</th>"
        . "<th>Organization Name</th>"
        . "<th>Participant Status</th>"
        . "<th>Fee Amount</th>"
        . "<th>Billing Number</th>"
        . "<th>Billing Date</th>"
        . "<th>Post Date</th>"
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
    $eventId = $field["event_id"];

    $sql = $weberp->prepare("SELECT trandate FROM gltrans WHERE voucherno = ?");
    $sql->bindValue(1,$billingNo,PDO::PARAM_STR);
    $sql->execute();
    $postDate = $sql->fetchColumn();

    $html = $html."<tr>"
          . "<td><input type='checkbox' name='billingNos[]' value='$billingNo'></td>"
          . "<td>$participantId</td>"
          . "<td>$eventType</td>"
          . "<td>$eventName</td>"
          . "<td>$participantName</td>"
          . "<td>$orgName</td>"
          . "<td>$participantStatus</td>"
          . "<td>$feeAmount</td>"
          . "<td>$billingNo</td>"
          . "<td>$billingDate</td>"
          . "<td>$postDate</td>"
          . "<td><a href='../webapp/pire/individualBillingReference.php?billingRef=$billingNo&eventId=$eventId' target='_blank'>"
          . "<img src='../webapp/pire/images/printer-icon.png' width='40' height='40'>"
          . "</a></td>"
          . "</tr>";

  }

  $html = $html."</tbody></table>";

  return $html;
}

function updatePostDate($weberp,$voucherNo,$postDate){

  $postDate = date("Y-m-d",strtotime($postDate));
  $sql = $weberp->prepare("UPDATE gltrans SET trandate = ? WHERE voucherno = ?");
  $sql->bindValue(1,$postDate,PDO::PARAM_STR);
  $sql->bindValue(2,$voucherNo,PDO::PARAM_STR);
  $sql->execute();

}

function searchPostedBillings($dbh,$searchType,$searchValue){

  $searchQuery = '';

  switch($searchType){

    case 'participant_name':
         $searchQuery = "AND cc.sort_name LIKE ?";
         break;
    case 'event_name':
         $searchQuery = "AND ce.title LIKE ?";
         break;
    case 'org_name':
         $searchQuery = "AND cc.organization_name LIKE ?";
         break;
    case 'billing_no':
         $searchQuery = "AND bd.billing_no LIKE ?";
         break;
    
  }


  $sql = $dbh->prepare("SELECT bd.participant_id, bd.event_type, ce.title AS event_name, bd.event_id,
                        cc.sort_name AS participant_name, cc.organization_name, cs.name AS participant_status, 
                        bd.fee_amount, bd.billing_no, bd.bill_date
                        FROM billing_details bd, civicrm_event ce, civicrm_contact cc, civicrm_participant cp, civicrm_participant_status_type cs
                        WHERE bd.event_id = ce.id
                        AND bd.contact_id = cc.id
                        AND bd.participant_id = cp.id
                        AND cp.status_id = cs.id
                        AND bd.post_bill =  '1'
                        AND bd.billing_type = 'Individual'
                        $searchQuery");

  $sql->bindValue(1,"%".$searchValue."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function searchPostedBillingsByDate($dbh,$startDate,$endDate){

  $startDate = date("Y-m-d",strtotime($startDate));
  $endDate = date("Y-m-d",strtotime($endDate));

  $startDate = $startDate." 00:00:00";
  $endDate = $endDate." 23:59:59";


  $sql = $dbh->prepare("SELECT bd.participant_id, bd.event_type, ce.title AS event_name, bd.event_id,
                        cc.sort_name AS participant_name, cc.organization_name, cs.name AS participant_status, 
                        bd.fee_amount, bd.billing_no, bd.bill_date
                        FROM billing_details bd, civicrm_event ce, civicrm_contact cc, civicrm_participant cp, civicrm_participant_status_type cs
                        WHERE bd.event_id = ce.id
                        AND bd.contact_id = cc.id
                        AND bd.participant_id = cp.id
                        AND cp.status_id = cs.id
                        AND bd.post_bill =  '1'
                        AND bd.billing_type = 'Individual'
                        AND bd.bill_date BETWEEN ? AND ?");

  $sql->bindValue(1,$startDate,PDO::PARAM_STR);
  $sql->bindValue(2,$endDate,PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

?>
