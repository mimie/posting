<?php

function menu(){
     $html = "<div width='100%' style='background-color:black; padding:1px;'>"
           . "<ul>"
           . "<li><a href='../events2.php'>Event</a><ul><li><a href='../eventIndividualPosting.php'>Event Posting</a></li></ul></li>"
           . "<li><a href='#'>Membership</a>"
           . "<ul><li><a href='../membershipIndividualBilling.php'>Membership Billing</a></li></ul>"
           . "</li>"
           . "<li><a href='../addCustomer.php'>Civicrm Contacts</></li>"
           . "<li><a href='../logout.php'>Logout</a></li>"
           . "</ul><br><br>"
           . "</div>";


  return $html;
}

?>
