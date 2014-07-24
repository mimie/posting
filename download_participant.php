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

 $sql = $dbh->prepare("SELECT cp.id as participant_id,cp.contact_id, cc.sort_name,cc.organization_name, cs.name as status, cp.fee_amount
                       FROM civicrm_participant cp,civicrm_contact cc, civicrm_participant_status_type cs
                       WHERE cp.contact_id = cc.id
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

  // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($data as $row) {
    if(!$flag) {
      // display field/column names as first row
      //fputcsv($out, array_keys($row), ',', '"');
      $labels = array("Participant No.","CIVICRM ID","Participant Name","Organization","Status","Fee Amount");
      fputcsv($out,$labels, ',', '"');
      $flag = true;
    }
    array_walk($row, 'cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;
?>
</body>
</html>
