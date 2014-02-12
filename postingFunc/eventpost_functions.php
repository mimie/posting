<?php

function getIndividualNonPostedBillings(PDO $dbh){

   $sql = $dbh->prepare("SELECT bd.id,bd.contact_id, bd.participant_id, bd.event_id,bd.event_type, bd.event_name, bd.participant_name,
                         bd.organization_name, bd.org_contact_id, bd.fee_amount, bd.billing_no, bd.bill_date, cs.name as status
                         FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cs
                         WHERE billing_type = 'Individual' AND post_bill='0'
                         AND cp.id = bd.participant_id
                         AND cp.status_id  = cs.id");
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
   
}

function searchNonPostedBilling($dbh,$category,$value){

   $searchQuery = "";

   switch($category){

     case "name":
       $searchQuery = "AND participant_name LIKE ?";
       break;
     case "event_type":
       $searchQuery = "AND event_type LIKE ?";
       break;
     case "event_name":
       $searchQuery = "AND event_name LIKE ?";
       break;
     case "org_name":
       $searchQuery = "AND organization_name LIKE ?";
       break;
     case "billing_no":
       $searchQuery = "AND billing_no LIKE ?";
       break;
   }

   $sql = $dbh->prepare("SELECT bd.id,bd.contact_id, bd.participant_id, bd.event_id,bd.event_type, bd.event_name, bd.participant_name,
                         bd.organization_name, bd.org_contact_id, bd.fee_amount, bd.billing_no, bd.bill_date, cs.name as status
                         FROM billing_details bd, civicrm_participant cp, civicrm_participant_status_type cs
                         WHERE billing_type = 'Individual' AND post_bill='0'
                         AND cp.id = bd.participant_id
                         AND cp.status_id  = cs.id
                         $searchQuery");
   $sql->bindValue(1,"%".$value."%",PDO::PARAM_STR);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;

}



function displayIndividualEventBillings(array $eventBillings){

    $html = "<table id='info' width='100%'>"
          . "<thead>"
          . "<tr><td colspan='11' bgcolor='#2c4f85'><input type='submit' value='Post to Weberp' name='post'></td></tr>"
          . "<tr>"
          . "<th><input type='checkbox' id='check'>Select Contact</th>"
          . "<th>Participant Id</th>"
          . "<th>Event Type</th>"
          . "<th>Event Name</th>"
          . "<th>Participant Name</th>"
          . "<th>Organization Name</th>"
          . "<th>Participant Status</th>"
          . "<th>Fee Amount</th>"
          . "<th>Billing Number</th>"
          . "<th>Billing Date</th>"
          . "<th>Print Bill</th>"
          . "</tr>"
          . "</thead>";

    $html = $html."<tbody>";

    foreach($eventBillings as $key => $field){

       //$contactId = $field["contact_id"];
       $participantId = $field["participant_id"];
       $eventType = $field["event_type"];
       $eventName = $field["event_name"];
       $name = $field["participant_name"];
       $orgName = $field["organization_name"];
       $feeAmount = $field["fee_amount"];
       $billingNo = $field["billing_no"];
       $date = $field["bill_date"];
       $status = $field["status"];
       $eventId = $field["event_id"];
       $billingId = $field["id"];

       $checkbox = $status == 'Attended' ? "class='checkbox'" : "";
       $disabled = $status != 'Attended' ? "disabled" : "";

       $html = $html."<tr>"
             . "<td><input type='checkbox' name='billingIds[]' value='$billingId' $checkbox $disabled></td>"
             . "<td>$participantId</td>"
             . "<td>$eventType</td>"
             . "<td><a href='participantListing.php?eventId=$eventId' title='Click this link to edit participant status'>$eventName</a></td>"
             . "<td>$name</td>"
             . "<td>$orgName</td>"
             . "<td>$status</td>"
             . "<td>$feeAmount</td>"
             . "<td>$billingNo</td>"
             . "<td>$date</td>"
             . "<td><a href='../webapp/pire/individualBillingReference.php?billingRef=$billingNo&eventId=$eventId' target='_blank'>"
             . "<img src='images/printer-icon.png' width='30' height='30'></a></td>"
             . "</tr>";
    }
  
    $html = $html."</tbody></table>";
    return $html;
}

function getCompanyNonPostedBillings($dbh){

   $sql = $dbh->prepare("SELECT cbid,event_name, org_contact_id,organization_name, billing_no,total_amount, subtotal, vat, bill_date,event_id
                         FROM billing_company
                         WHERE post_bill = '0'
                         AND total_amount != '0'");
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

function searchCompanyNonPostedBillings($dbh,$category,$value){

   $searchQuery = "";

   switch($category){
     case "org_name":
       $searchQuery = "AND organization_name LIKE ?";
       break;
     case "event_name":
       $searchQuery = "AND event_name LIKE ?";
       break;
     case "billing_no":
       $searchQUery = "AND billing_no LIKE ?";
       break;

   }

   $sql = $dbh->prepare("SELECT event_name, org_contact_id,organization_name, billing_no,total_amount, subtotal, vat, bill_date,event_id
                         FROM billing_company
                         WHERE post_bill = '0'
                         AND total_amount != '0'
                         $searchQuery");
   $sql->bindValue(1,"%".$value."%",PDO::PARAM_STR);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;

}

function displayCompanyEventBillings(array $companyBillings){

   $html = "<table id='info' width='100%'>"
         . "<thead>"
          . "<tr><td colspan='12' bgcolor='#2c4f85'><input type='submit' value='Post to Weberp' name='post'></td></tr>"
         . "<tr>"
         . "<th><input type='checkbox' id='check'>Select organization</th>"
         . "<th>Event Name</th>"
         . "<th>Organization Name</th>"
         . "<th>Billing No</th>"
         . "<th>Total Amount</th>"
         . "<th>Subtotal</th>"
         . "<th>VAT</th>"
         . "<th>Billing Date</th>"
         . "<th>Print Bill</th>"
         . "</tr>" 
         . "</thead>";
    $html = $html."<tbody>";

    foreach($companyBillings as $key => $field){

      $eventName = $field["event_name"];
      $orgId = $field["org_contact_id"];
      $orgName = $field["organization_name"];
      $billingNo = $field["billing_no"];
      $totalAmount = $field["total_amount"];
      $subtotal = $field["subtotal"];
      $vat = $field["vat"];
      $billDate = $field["bill_date"];
      $billingId = $field["cbid"];
      $eventId = $field["event_id"];

      $html = $html."<tr>"
            . "<td><input type='checkbox' name='billingIds[]' value='$billingId' class='checkbox'></td>"
            . "<td>$eventName</td>"
            . "<td>$orgName</td>"
            . "<td>$billingNo</td>"
            . "<td>$totalAmount</td>"
            . "<td>$subtotal</td>"
            . "<td>$vat</td>"
            . "<td>$billDate</td>"
            . "<td><a href='../webapp/pire/companyBillingReference.php?companyBillingRef=$billingNo&eventId=$eventId&orgId=$orgId' target='_blank'>"
            . "<img src='images/printer-icon.png' width='30' height='30'></a></td>"
            . "</tr>";
    }

   $html = $html."</tbody></table>";

   return $html;
}

function updateIndividualEventPost($dbh,$billingId){

    $sql = $dbh->prepare("UPDATE billing_details SET post_bill = '1'
                          WHERE id = ?
                         ");
    $sql->bindValue(1,$billingId,PDO::PARAM_INT);
    $sql->execute();
}

function getParticipantInfoBilling($dbh,$billingId){

   $sql = $dbh->prepare("SELECT participant_name,contact_id,email,event_type,event_name,fee_amount FROM billing_details
                         WHERE id = ? ");
   $sql->bindValue(1,$billingId,PDO::PARAM_INT);
   $sql->execute();
   $result = $sql->fetch(PDO::FETCH_ASSOC);

   return $result;
   
}

function checkContactRecordExist($weberp,$contactId){

  $debtorno = "IIAP".$contactId;
  $sql = $weberp->prepare("SELECT COUNT(*) as count FROM debtorsmaster WHERE debtorno = '$debtorno'");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  $count = $result["count"];
  $count = intval($count);


  return $count;
}

function updateCompanyEventPost($dbh,$billingId){

  $sql = $dbh->prepare("UPDATE billing_company SET post_bill = '1'
                        WHERE cbid = ?");
  $sql->bindValue(1,$billingId,PDO::PARAM_INT);
  $sql->execute();
}

function getCompanyInfoBilling($dbh,$billingId){

  $sql = $dbh->prepare("SELECT bc.cbid, bc.org_contact_id,bc.organization_name, bc.total_amount, bc.event_name,bc.billing_no,em.email
                        FROM billing_company bc
                        LEFT JOIN civicrm_email em ON em.contact_id = bc.org_contact_id
                        WHERE bc.cbid = ?");
  $sql->bindValue(1,$billingId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  return $result;
}
?>
