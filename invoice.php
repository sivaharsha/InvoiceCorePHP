<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'dompdf/autoload.inc.php';
use Dompdf\Adapter\CPDF;
use Dompdf\Dompdf;
use Dompdf\Exception;



$servername = "localhost";
$username = "root";
$password = "roo";
$db = "Invoice";

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$data = $_REQUEST;
$items_length = count($data['name']);

$items=[];
$sub_wot=0;
$sub_wt=0;
$items=[];

for ($i = 0; $i <$items_length; $i++) {
    $line_total_wot=$data['quantity'][$i]*$data['price'][$i];
    $line_total = $line_total_wot+($line_total_wot*$data['tax'][$i]/100);
    $sub_wot += $line_total_wot;
    $sub_wt += $line_total;
   
    array_push($items, ['name' => $data['name'][$i], 'quantity' => $data['quantity'][$i],'price'=>$data['price'][$i],'tax'=>$data['tax'][$i],
    'total'=>$line_total,'total_wot'=>$line_total_wot
]); 
}

$ids=[];
foreach( $items as $row=>$row_val ) {
    echo '<pre>';
    print_r($row_val);
    echo '</pre>';
    $query ='INSERT INTO InvoiceDetails (name,quantity,unit_price,tax,total,total_wot) VALUES ("'.$row_val['name'].'",'.$row_val['quantity'].','.$row_val['price'].','.$row_val['tax'].','.$row_val['total'].','.$row_val['total_wot'].');';
    echo '<pre>';
    print_r($query);
    echo '</pre>';
    $conn->query($query);
    if($conn->error) die("Error in query");
    array_push($ids,$conn->insert_id);
}


$invoiceSql='INSERT INTO Invoices (sub_total,sub_total_wot) VALUES ('.$sub_wt.','.$sub_wot.');';
$conn->query($invoiceSql);
if($conn->error) die("Error in insertion of invoice");
echo $invoiceSql;
$invoiceId=$conn->insert_id;



foreach( $ids as $itemId) {
    $updateItem="UPDATE InvoiceDetails SET invoice_id = '".$invoiceId."' WHERE id = ".$itemId.";";
    $conn->query($updateItem);
    if($conn->error) die("Error in insertion of invoice");
    echo $updateItem;
}

$rows='';
foreach ($items as &$value) {
    $row='<tr>';
    foreach ($value as  $key => $item) {
        if($key!=='total_wot')
            $row.='<td>'.$item.'</td>';
    }   
    $row.='</tr>';

    $rows.=$row;
}

if(file_exists('./invoice.html')===TRUE)
{
    $contents = file_get_contents("./invoice.html");
}
else
{
    die("Unable to stream pdf");
}

$dynamicText = ['BODY','SUB_TOT_WOT','SUB_TOT_WT'];
$dynamicData   = [$rows,$sub_wot,$sub_wt];

$newContent=str_replace($dynamicText,$dynamicData,$contents);

$dompdf = new Dompdf(); 
$dompdf->loadHtml($newContent);
$dompdf->render();
ob_end_clean();
$dompdf->stream("invoice.pdf");
exit(0);




// INSERT INTO table (a,b) VALUES (1,2), (2,3), (3,4);


?>