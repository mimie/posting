<?php

function getMemberNonPosted($dbh){

  $sql = $dbh->prepare("SELECT bm.id as billing_id,membership_id, bm.contact_id,member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address,street,city,cm.status_id, cms.label AS status_type
                        FROM billing_membership bm, civicrm_membership cm, civicrm_membership_status cms
                        WHERE bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        AND bm.post_bill =  '0'");
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
}

function getTransactionsPerYear($dbh,$year){

  $sql = $dbh->prepare("SELECT bm.id as billing_id,membership_id, bm.contact_id,member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address,street,city,cm.status_id, cms.label AS status_type
                        FROM billing_membership bm, civicrm_membership cm, civicrm_membership_status cms
                        WHERE bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        AND bill_date LIKE '%$year%'

                        ");

  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

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
          . "<td>$memberName<td>"
          . "<td>$email</td>"   
          . "<td>$org</td>"
          . "<td>$amount</td>"
          . "<td>Print</td>"
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

?>
