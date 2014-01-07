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
                        AND bill_date BETWEEN '".$year."-01-01 00:00:00 AND '".$year."12-31 59:59:59'
                        ");
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details; 
  
}

?>
