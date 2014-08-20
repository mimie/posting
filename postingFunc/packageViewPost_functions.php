<?php

function getAllPostedBillings(){
	$sql = civicrmDB("SELECT cc.sort_name, bdp.pid, bp.package_name, bdp.billing_no, bdp.bir_no,bdp.total_amount,bdp.subtotal,bdp.vat,bdp.bill_date,bdp.billing_type
                          FROM civicrm_contact cc, billing_details_package bdp, billing_package bp
                          WHERE bdp.contact_id = cc.id
                          AND bdp.pid = bp.pid
                          ORDER BY cc.sort_name
                        ");
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $result;
}

function displayPackagePostedBillings($posted_billings){
	$html = "<table id='billingInfo' width='100%'>"
	      . "<thead>"
	      . "<tr>"
	      . "<th>Contact Name</th>"
	      . "<th>Package Name</th>"
	      . "<th>Registration No.</th>"
	      . "<th>ATP</th>"
	      . "<th>Total Amount</th>"
	      . "<th>Subtotal</th>"
	      . "<th>VAT</th>"
	      . "<th>Billing Date</th>"
	      . "<th>View Bill</th>"
	      . "<th>Billing Type</th>"
	      . "</tr>"
	      . "</thead>"
	      . "<tbody>";

	foreach($posted_billings as $key=>$info){

		$html = $html."<tr>"
                      . "<td>".$info['sort_name']."</td>"
                      . "<td>".$info['package_name']."</td>"
                      . "<td>".$info['billing_no']."</td>"
                      . "<td>".$info['bir_no']."</td>"
                      . "<td>".number_format($info['total_amount'],2)."</td>"
                      . "<td>".number_format($info['subtotal'],2)."</td>"
                      . "<td>".number_format($info['vat'],2)."</td>"
                      . "<td>".date("F j, Y",strtotime($info['bill_date']))."</td>"
                      . "<td></td>"
                      . "<td>".$info['billing_type']."</td>"
                      . "</tr>";

        }

        $html = $html."</tbody></table>";

        return $html;

}


?>
