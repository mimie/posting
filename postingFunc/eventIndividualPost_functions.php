<?php

function getIndividualBillingsByEvent($dbh,$eventId){

  $sql = $dbh->prepare("SELECT bd.id as billing_id, bd.participant_id, bd.participant_name, bd.organization_name,
                        bd.fee_amount, bd.subtotal,bd.vat,bd.billing_no,bd.bill_date, cps.name as status
                        FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cps
                        WHERE cp.id = bd.participant_id
                        AND cp.status_id = cps.id
                        AND bd.billing_type = 'Individual'
                        AND bd.event_id = ?
                       ");
  $sql->bindValue(1,$eventId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

?>
