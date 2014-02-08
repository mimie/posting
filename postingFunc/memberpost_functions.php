<?php

function getMemberNonPosted($dbh){

  $sql = $dbh->prepare("SELECT bm.id as billing_id,membership_id, bm.contact_id,member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address,street,city,cm.status_id, cms.label AS status_type
                        FROM billing_membership bm
                        LEFT JOIN civicrm_membership cm ON bm.contact_id = cm.contact_id
                        LEFT JOIN civicrm_membership_status cms ON bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        WHERE bm.post_bill =  '0'
                        ORDER BY member_name");
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
}

function getTransactionsPerYear($dbh,$year){

  $sql = $dbh->prepare("SELECT bm.id as billing_id,membership_id, bm.contact_id,member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address,street,city,cm.status_id, cms.label AS status_type
                        FROM billing_membership bm
                        LEFT JOIN civicrm_membership cm ON bm.contact_id = cm.contact_id
                        LEFT JOIN civicrm_membership_status cms ON bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        WHERE bill_date LIKE '%$year%'

                        ");

  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
  
}

function searchMemberNonPostedByName($dbh,$name){

  $sql = $dbh->prepare("SELECT bm.id as billing_id,membership_id, bm.contact_id,member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address,street,city,cm.status_id, cms.label AS status_type
                        FROM billing_membership bm
                        LEFT JOIN civicrm_membership cm ON bm.contact_id = cm.contact_id
                        LEFT JOIN civicrm_membership_status cms ON bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        WHERE bm.post_bill =  '0'
                        AND member_name LIKE '%$name%'
                        ORDER BY member_name");
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
}

function getBillingInfoById($dbh,$billingId){

  $sql = $dbh->prepare("SELECT contact_id,member_name,street,city,email,year,fee_amount
                        FROM billing_membership
                        WHERE id = ?
                       ");
  $sql->bindParam(1,$billingId,PDO::PARAM_INT);
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);

  return $details;
  
}

function displayBillings(array $members){

  $html = "<table><thead>"
        . "<tr>"
        . "<th>Select Bill</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Organization Name</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Print Bill</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference No.</th>"
        . "<th>Billing Date</th>"
        . "<th>Billing Address</th>"
        . "</tr></thead>";

  $html = $html."<tbody>";

  foreach($members as $details){

    $billingId = $details["billing_id"];
    $membershipId = $details["membership_id"];
    $memberName = $details["member_name"];
    $memberName = mb_convert_encoding($memberName, "UTF-8");
    $email = $details["email"];
    $org = $details["organization_name"];
    $amount = $details["fee_amount"];
    $amount = number_format($amount,2);
    $paymentStatus = $details["paid_bill"];
    $postBill = $details["post_bill"];
    $billingNo = $details["billing_no"];
    $billDate = $details["bill_date"];
    $billAddress = $details["bill_address"];
    $status = $details["status_type"];

    $disabled = $postBill == 1 ? 'disabled' : '';
    $checkbox = $postBill == 1 ? '' : 'class=checkbox';

    $html = $html."<tr>"
          . "<td><input type='checkbox' value='$billingId' name='billingIds[]' $checkbox $disabled></td>"
          . "<td>$memberName</td>"
          . "<td>$email</td>"  
          . "<td>$status</td>" 
          . "<td>$org</td>"
          . "<td>$amount</td>"
          . "<td><a href='../memberBillingReference.php?billingId=$billingId' target='_blank' title='Click to print membership bill' style='text-decoration: none;'>"
          . "<img src='../images/printer-icon.png' width='40' height='40'><br>Print"
          . "</a></td>"
          . "<td>Payment Status</td>"
          . "<td>$billingNo</td>"
          . "<td>$billDate</td>"
          . "<td>$billAddress</td>"
          . "</tr>";

 }

  $html = $html."</tbody>";
  $html = $html."</table>";

  return $html;
}

function updateMembershipPost($dbh,array $billingIds){

  foreach($billingIds as $id){
    $sql = $dbh->prepare("UPDATE billing_membership SET post_bill='1' WHERE id=?");
    $sql->bindParam(1,$id,PDO::PARAM_INT);
    $sql->execute();
  }
}

function checkMemberRecordExist($weberp,$contactId){

  $debtorno = "IIAP".$contactId;
  $sql = $weberp->prepare("SELECT COUNT(*) as count FROM debtorsmaster WHERE debtorno = '$debtorno'");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  $count = $result["count"];
  $count = intval($count);
  

  return $count;
}

?>
