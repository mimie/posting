<?php

function getMemberNonPosted($dbh){

  $sql = $dbh->prepare("SELECT membership_id, member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address, cm.status_id, cms.label AS status_type
                        FROM billing_membership bm, civicrm_membership cm, civicrm_membership_status cms
                        WHERE bm.membership_id = cm.id
                        AND cm.status_id = cms.id
                        AND bm.post_bill =  '0'");
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
}

function getTransactionsPerYear($dbh,$year){

  $sql = $dbh->prepare("SELECT membership_id, member_name, email, organization_name, fee_amount, paid_bill, post_bill, billing_no, bill_date, bill_address, cm.status_id, cms.label AS status_type
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

    $membershipId = $details["membership_id"];
    $memberName = $details["member_name"];
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
          . "<td><input type='checkbox' value='$membershipId' name='membershipIds[]' $checkbox $disabled></td>"
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

?>
