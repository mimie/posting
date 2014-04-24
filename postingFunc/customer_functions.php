<?php

function getCustomerDetails(PDO $dbh){
  $sql = $dbh->prepare("SELECT em.id,cc.id as contact_id, cc.sort_name, em.email, em.is_primary
                        FROM civicrm_contact cc LEFT JOIN civicrm_email em ON cc.id = em.contact_id AND em.is_primary = '1'  
                        WHERE cc.is_deleted = '0' AND cc.contact_type ='Individual' ORDER by cc.sort_name");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);                                                         
                                                                                                      
  return $result;                       
}


function displayCustomerContacts(array $customer){

  $html = "<table id='info' width='100%'>"
        . "<thead>"
        . "<tr><td colspan='3'><input type='submit' name='insert' value='CREATE WEBERP RECORD'></td></tr>"
        . "<tr>"
        . "<th><input type='checkbox' id='check'>Select contact</th>"
        . "<th>Contact Name</th>"
        . "<th>Email</th>"
        . "</tr>"
        . "</thead>";

  $html = $html."<tbody>";

  foreach($customer as $key => $field){
    $contactId = $field["contact_id"];
    $name = $field["sort_name"];
    $name = mb_convert_encoding($name,"UTF-8");
    $email = $field["email"];
    $html = $html."<tr>"
          . "<td><input type='checkbox' name='contactIds[]' value='$contactId' class='checkbox'></td>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "</tr>";
         
  }
  $html =  $html."</tbody></table>";
  return $html;

}
                                                                 
function getCustomerByName(PDO $dbh,$name){
  $sql = $dbh->prepare("SELECT em.id,cc.id as contact_id, cc.sort_name, em.email, em.is_primary
                        FROM civicrm_contact cc LEFT JOIN civicrm_email em ON cc.id = em.contact_id AND em.is_primary = '1'  
                        WHERE cc.is_deleted = '0' AND cc.contact_type ='Individual' 
                        AND sort_name LIKE ?
                        ORDER by cc.sort_name");
  $sql->bindValue(1,"%".$name."%",PDO::PARAM_STR);
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);                                                         
  return $result;                       
}


function getCustomerById(PDO $dbh,$contactId){
  $sql = $dbh->prepare("SELECT em.id,cc.id as contact_id, cc.sort_name, em.email, em.is_primary
                        FROM civicrm_contact cc LEFT JOIN civicrm_email em ON cc.id = em.contact_id AND em.is_primary = '1'  
                        WHERE cc.is_deleted = '0' AND cc.contact_type ='Individual' 
                        AND cc.id = ?
                        ORDER by cc.sort_name");
  $sql->bindValue(1,$contactId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);                                                         
  return $result;                       
}
?>

