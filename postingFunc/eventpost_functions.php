<?php

function getIndividualNonPostedBillings(PDO $dbh){

   $sql = $dbh->prepare("SELECT contact_id, participant_id, event_type, event_name, participant_name,
                         organization_name, org_contact_id, fee_amount, billing_no, bill_date
                         FROM billing_details
                         WHERE billing_type = 'Individual' AND post_bill='0'");
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

   $sql = $dbh->prepare("SELECT contact_id, participant_id, event_type, event_name, participant_name,
                         organization_name, org_contact_id, fee_amount, billing_no, bill_date
                         FROM billing_details
                         WHERE billing_type = 'Individual' 
                         AND post_bill='0'
                         $searchQuery");
   $sql->bindValue(1,"%".$value."%",PDO::PARAM_STR);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;

}



function displayIndividualEventBillings(array $eventBillings){

    $html = "<table id='info' width='100%'>"
          . "<thead>"
          . "<tr>"
          . "<th>Select Contact</th>"
          . "<th>Participant Id</th>"
          . "<th>Event Type</th>"
          . "<th>Event Name</th>"
          . "<th>Participant Name</th>"
          . "<th>Organization Name</th>"
          . "<th>Fee Amount</th>"
          . "<th>Billing Number</th>"
          . "<th>Billing Date</th>"
          . "</tr>"
          . "</thead>";

    $html = $html."<tbody>";

    foreach($eventBillings as $key => $field){

       $contactId = $field["contact_id"];
       $participantId = $field["participant_id"];
       $eventType = $field["event_type"];
       $eventName = $field["event_name"];
       $name = $field["participant_name"];
       $orgName = $field["organization_name"];
       $feeAmount = $field["fee_amount"];
       $billingNo = $field["billing_no"];
       $date = $field["bill_date"];

       $html = $html."<tr>"
             . "<td><input type='checkbox' name='contactIds[]' value='$contactId'></td>"
             . "<td>$participantId</td>"
             . "<td>$eventType</td>"
             . "<td>$eventName</td>"
             . "<td>$name</td>"
             . "<td>$orgName</td>"
             . "<td>$feeAmount</td>"
             . "<td>$billingNo</td>"
             . "<td>$date</td>"
             . "</tr>";
    }
  
    $html = $html."</tbody></table>";
    return $html;
}

function getCompanyNonPostedBillings($dbh){

   $sql = $dbh->prepare("SELECT event_name, org_contact_id,organization_name, billing_no,total_amount, subtotal, vat, bill_date
                         FROM billing_company
                         WHERE post_bill = '0'");
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

   $sql = $dbh->prepare("SELECT event_name, org_contact_id,organization_name, billing_no,total_amount, subtotal, vat, bill_date
                         FROM billing_company
                         WHERE post_bill = '0'
                         $searchQuery");
   $sql->bindValue(1,"%".$value."%",PDO::PARAM_STR);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;

}

function displayCompanyEventBillings(array $companyBillings){

   $html = "<table id='info' width='100%'>"
         . "<thead>"
         . "<tr>"
         . "<th>Select organization</th>"
         . "<th>Event Name</th>"
         . "<th>Organization Name</th>"
         . "<th>Billing No</th>"
         . "<th>Total Amount</th>"
         . "<th>Subtotal</th>"
         . "<th>VAT</th>"
         . "<th>Billing Date</th>"
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

      $html = $html."<tr>"
            . "<td><input type='checkbox' name='orgIds[]' value='$orgId'></td>"
            . "<td>$eventName</td>"
            . "<td>$orgName</td>"
            . "<td>$billingNo</td>"
            . "<td>$totalAmount</td>"
            . "<td>$subtotal</td>"
            . "<td>$vat</td>"
            . "<td>$billDate</td>"
            . "</tr>";
    }

   $html = $html."</tbody></table>";

   return $html;
}

?>
