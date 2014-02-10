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

?>
