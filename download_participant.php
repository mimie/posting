<?php

  include 'dbcon.php';
  include 'pdo_conn.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';

  $dbh = civicrmConnect();
?>
<?php
 // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.
 $eventId = $_GET['eventId'];
 $eventDetails = getEventDetails($dbh,$eventId);
 $eventName = $eventDetails["event_name"];
 $eventStartDate = $eventDetails["start_date"];
 $eventEndDate = $eventDetails["end_date"];
 $eventTypeName = getEventTypeName($dbh,$eventId);
 $locationDetails = getEventLocation($dbh,$eventId);
 $eventLocation = formatEventLocation($locationDetails);

 echo "Event Name: \t$eventName\n";
 echo "Start Date: \t$eventStartDate\n";
 echo "End Date: \t$eventEndDate\n";
 echo "Event Type: \t$eventTypeName\n";
 echo "Location: \t$eventLocation\n\n";

 $sql = $dbh->prepare("SELECT cp.id as participant_id,cp.contact_id, cc.sort_name,cc.organization_name, cs.name as status, cp.fee_amount,billtype.billing_45 as billing_type
                       FROM civicrm_participant cp,civicrm_contact cc, civicrm_participant_status_type cs, civicrm_value_billing_17 billtype
                       
                       WHERE cp.contact_id = cc.id
                       AND billtype.entity_id = cp.id
                       AND cp.status_id = cs.id 
                       AND cp.event_id = ?
                       AND cp.fee_amount != '0'
                       AND cc.is_deleted = '0'
                       ORDER BY cc.sort_name");

 $sql->bindParam(1,$eventId,PDO::PARAM_INT);
 $sql->execute();

 $data = $sql->fetchAll(PDO::FETCH_ASSOC);
  function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  

  $file_event = implode('_', explode(' ', $eventName));
  // filename for download
  $filename = $file_event."_". date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($data as $row) {
    if(!$flag) {
      // display field/column names as first row
      //fputcsv($out, array_keys($row), ',', '"');
      $labels = array("Participant No.","CIVICRM ID","Participant Name","Organization","Status","Fee Amount","Billing Type","Billing Number");
      fputcsv($out,$labels, ',', '"');
      $flag = true;
    }

    $participant_id = $row['participant_id'];
    $sql_billing = $dbh->prepare("SELECT billing_no FROM billing_details bd WHERE bd.participant_id = ? AND is_cancelled='0'");
    $sql_billing->bindValue(1,$participant_id,PDO::PARAM_INT);
    $sql_billing->execute();
    $result = $sql_billing->fetch(PDO::FETCH_ASSOC);
    $billing_no = $result['billing_no'];
    $row[] = $billing_no;
    array_walk($row, 'cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;
?>
</body>
</html>
