<?php

function getCompanyBillingByEvent($dbh,$eventId){

   $sql = $dbh->prepare("SELECT cbid as billing_id, organization_name, event_id,org_contact_id,billing_no,
                         total_amount,subtotal,vat,bill_date,post_bill
                         FROM billing_company
                         WHERE event_id = ?
                         AND EXISTS (SELECT * FROM billing_details WHERE billing_details.billing_no = billing_company.billing_no)
                         AND total_amount != '0'");
   $sql->bindValue(1,$eventId,PDO::PARAM_INT);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
}

function searchCompanyBillingsByEvent($dbh,$eventId,$searchParameters){
   $billingNo = $searchParameters["billing_no"];
   $org = $searchParameters["org"];

   $sql = $dbh->prepare("SELECT cbid as billing_id, organization_name, event_id,org_contact_id,billing_no,
                         total_amount,subtotal,vat,bill_date,post_bill
                         FROM billing_company
                         WHERE event_id = ?
                         AND billing_no LIKE ?
                         AND organization_name LIKE ?
                         AND EXISTS (SELECT * FROM billing_details WHERE billing_details.billing_no = billing_company.billing_no)
                         AND total_amount != '0'");
   $sql->bindValue(1,$eventId,PDO::PARAM_INT);
   $sql->bindValue(2,"%".$billingNo."%",PDO::PARAM_STR);
   $sql->bindValue(3,"%".$org."%",PDO::PARAM_STR);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);

   return $result;
   
}

function getOTHDebitAcct($weberp){
	$sql = $weberp->prepare("SELECT accountcode, glacode, accountname FROM chartmaster WHERE group_ = 'Receipts/Revenue'");
	$sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $result;
      
}
function displayCompanyBillingsByEvent($weberp,array $billings,$eventType){
   $accts = getOTHDebitAcct($weberp);

  if($eventType == 'OTH'){
    $oth = "<select name='acct_code'>"
           . "<option value='select'>-Select account code-</option>"
           . "<option>---------------------------------------</option>";
    foreach($accts as $key=>$field){
       $selected = $field['glacode'] == '4-850-PDC' ? 'selected' : '';
       $oth = $oth."<option value='".$field['accountcode']."' $selected>".$field['glacode']." - ".$field['accountname']."</option>";
    }

    $oth = $oth. "</select>";
  }

  else{
    $oth = '';
  }

  $html = "<table width='100%' id='billings'>"
        . "<thead>"
        . "<tr><td colspan='13' bgcolor='#2c4f85'>"
        . "<input type='text' name='postdate' id='postDate' placeholder='Select post date..'>"
        . "$oth";

  $html = $html. "<input type='submit' value='Post to Weberp' name='post'></td></tr>"
        . "<tr>"
        . "<th><input type='checkbox' id='check'>Select bill</th>"
        . "<th>Organization</th>"
        . "<th>Billing Number</th>"
        . "<th>Total Amount</th>"
        . "<th>Subtotal</th>"
        . "<th>VAT</th>"
        . "<th>Billing Date</th>"
        . "<th>Billed Participants</th>"
        . "<th>Print Bill</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($billings as $key => $field){

     $billingId = $field["billing_id"];
     $orgName = $field["organization_name"];
     $orgName = mb_convert_encoding($orgName,"UTF-8");
     $billingNo = $field["billing_no"];
     $totalAmount = number_format($field["total_amount"], 2, '.',',');
     $subtotal = number_format($field["subtotal"], 2, '.',',');
     $vat = number_format($field["vat"], 2, '.',',');
     $billDate = $field["bill_date"];
     $billDate = date("F j, Y",strtotime($billDate));
     $eventId = $field["event_id"];
     $orgId = $field["org_contact_id"];
     $postBill = $field["post_bill"];

     $disabled = $postBill == '1' ? "disabled" : "class='checkbox'";

     $participantsLink = "<a href='../webapp/pire/billedParticipants.php?eventId=$eventId&billingNo=$billingNo&orgId=$orgId' target='_blank'>"
                        . "<img src='../webapp/pire/participants.png' height='50' width='50'></a>";

     $html = $html."<tr>"
           . "<td><input type='checkbox' name='billingIds[]' value='$billingId' $disabled></td>"
           . "<td>$orgName</td>"
           . "<td>$billingNo</td>"
           . "<td>$totalAmount</td>"
           . "<td>$subtotal</td>"
           . "<td>$vat</td>"
           . "<td>$billDate</td>"
           . "<td>$participantsLink</td>"
            . "<td><a href='../webapp/pire/companyBillingReference.php?companyBillingRef=$billingNo&eventId=$eventId&orgId=$orgId' target='_blank'>"
            . "<img src='images/printer-icon.png' width='30' height='30'></a></td>"
           . "</tr>";
  }

  $html = $html."</tbody></table>";

  return $html;

}

?>
