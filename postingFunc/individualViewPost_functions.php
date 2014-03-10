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

?>
