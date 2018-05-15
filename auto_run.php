<?php
ini_set('max_execution_time', 600);
require_once('service_database.php');
//  $base_url = "https://ats.fuse5live.com/f5api/";
//  $apikey = "RfiqLWuptjQ5VdkNJpgKBJETAl9YHqqA";
$mins = (int) date("i");
$hours = (int) date("H");

// it'll be called once, as only once in day hours and mins are 0
if($hours == 0 && $mins == 0){
	echo "Update Location";
	updateLocations();
}
//It'll be called once an hour as once in hour minutes become 0
if($mins == 0){
	echo "Updating Accounts";
	updateAccounts();
}
updateDeliveryLists();

function updateAccounts(){
	$offset = getOffset("customer");
	if($offset == -1){
		$offset = getOffset("customer");
		if($offset == -1){echo "DB connection not available."; return false;}
	}
	$postdata = array();
	$postdata['data'] = '{
	    "authenticate":{
	        "apikey":"RfiqLWuptjQ5VdkNJpgKBJETAl9YHqqA" 
	    },
	    "services":[{
	        "call":"account/all",
	        "params": null,
	         "identifier":{
	              "offset":"'.$offset.'",
	               "limit":"10000",
	               "sort_column":"account_id",
	               "sort_order":"asc"
	           }
	   }]
	}';
	$result = do_curl($postdata);
	if($result){
		$result = json_decode($result , 1);
		$result = $result["services"][0]["response"];
		$count = 0;
		$insertQuery = "INSERT INTO `customer` (`id`, `account_id`, `account_name`, `account_number`, `primary_email`, `default_location`) VALUES ";
		if($result["status"]){
			foreach($result["data"]["rows"] as $row){
				$count++;
				$insertQuery .= "(NULL, '".$row['account_id']."', '".$row['account_name']."', '".$row['account_number']."', '".$row['primary_email']."', '".$row['default_location']."') ,";
			}
			if($count > 0){
				$insertQuery = substr($insertQuery, 0, -2);
				$result = multiQueryDatabase($insertQuery);
					var_dump($result);
				if($result){
					echo "Updation successful";
				}
				else{
					echo "Error occured";
				}
			}	
		}
	}
	else{
		echo "Something went wrong";
	}
}

function updateLocations(){
	$offset = getOffset("locations");
	if($offset == -1){
		$offset = getOffset("locations");
		if($offset == -1){echo "DB connection not available."; return false;}
	}
	$postdata = array();
	$postdata['data'] = '{
		"authenticate": {
			"apikey": "RfiqLWuptjQ5VdkNJpgKBJETAl9YHqqA"
		},
		"services": [
			{
				"call": "location/all"
			}
		]
	}';
	$result = do_curl($postdata);
	if($result){
		$result = json_decode($result , 1);
		$result = $result["services"][0]["response"];
		$count = 0;
		$insertQuery = "INSERT IGNORE INTO `locations`(`id`, `location_name`) VALUES ";
		if($result["status"]){
			foreach($result["data"] as $row){
				$count++;
				$insertQuery .= "( '".$row['id']."', '".$row['location_name']."' ) ,";
			}
			if($count > 0){
				$insertQuery = substr($insertQuery, 0, -2);
				$result = multiQueryDatabase($insertQuery);
					var_dump($result);
				if($result){
					echo "Updation successful";
				}
				else{
					echo "Error occured";
				}
			}	
		}
	}
	else{
		echo "Something went wrong";
	}
}

function updateDeliveryLists(){
	$offset = getOffset("delivery_list");
	if($offset == -1){
		$offset = getOffset("delivery_list");
		if($offset == -1){echo "DB connection not available."; return false;}
	}
	$postdata = array();
	$postdata['data'] = '{
	"authenticate": {
		"apikey": "RfiqLWuptjQ5VdkNJpgKBJETAl9YHqqA"
	},
	"services": [
		{
			"call": "delivery/all",
			"identifier": {
				"offset": '.$offset.',
				"limit": 10000,
				"delivery_log_for": "salesorder",
				"delivery_log_type": "assigned",
	            "sort_column" : "target_delivery_time",
	            "sort_order":"asc"
			}
		}
	]
}';
	$result = do_curl($postdata);
	if($result){
		$result = json_decode($result , 1);
		$result = $result["services"][0]["response"];
		$count = 0;
		$insertQuery = "INSERT IGNORE INTO `delivery_list` ( `salesorder_delivery_id`, `target_delivery_time`, `driver_run`, `day_run`, `stop_on_run`, `salesorder_location`, `salesorder_number`, `vendor_name`, `line_code`, `product_number`, `counter_person`, `account_name`, `ship_street`, `ship_pobox`, `ship_city`, `ship_state`, `ship_county`, `ship_country`, `ship_code`, `deliveries_assigned_to`) VALUES ";
		if($result["status"]){
			foreach($result["data"]["rows"] as $row){
				$count++;
				$insertQuery .= "( '".$row['salesorder_delivery_id']."', '".$row['target_delivery_time']."', '".$row['driver_run']."', '".$row['day_run']."', '".$row['stop_on_run']."', '".$row['salesorder_location']."', '".$row['salesorder_number']."', '".$row['vendor_name']."', '".$row['line_code']."', '".$row['product_number']."', '".$row['counter_person']."', '".$row['account_name']."', '".$row['ship_street']."', '".$row['ship_pobox']."', '".$row['ship_city']."', '".$row['ship_state']."', '".$row['ship_county']."', '".$row['ship_code']."', '".$row['deliveries_assigned_to']."') ,";
			}
			if($count > 0){
				$insertQuery = substr($insertQuery, 0, -2);
				$result = multiQueryDatabase($insertQuery);
				if($result){
					echo "Updation successful";
				}
				else{
					echo "Error occured";
				}
			}
		}
	}
	else{
		echo "Something went wrong";
	}
}

function updateSalesOrders(){
	$offset = getOffset("sales_orders");
	if($offset == -1){
		$offset = getOffset("sales_orders");
		if($offset == -1){echo "DB connection not available."; return false;}
	}
	$postdata = array();
	$postdata['data'] = '{
"authenticate": {
"apikey": "RfiqLWuptjQ5VdkNJpgKBJETAl9YHqqA"
},
"services": [
{
"call": "sales_order/search",
"identifier": {
					"offset": 0,
					"limit": 100,
"searchkeyword": "S",
	               "sort_column":"sales_order_number",
	               "sort_order":"asc"
}
}
]
}';
	$result = do_curl($postdata);
	if($result){
		$result = json_decode($result , 1);
		$result = $result["services"][0]["response"];
		$count = 0;
		$insertQuery = "INSERT INTO `sales_orders` (`id`, `sales_order_number`, `sales_order_location`, `sales_order_invoice_number`, `sales_order_total_products`, `sales_order_created_date`, `sales_order_invoice_date`, `sales_order_shipped_via`, `sales_order_grand_total`, `sales_order_id`, `account_balance_terms`, `sales_order_status`) VALUES ";
		if($result["status"]){
			foreach($result["data"] as $row){
				echo $row['sales_order_number'] . "<br><br>";
				$count++;
				continue;
				$insertQuery .= "(NULL, '".$row['sales_order_number']."', '".$row['sales_order_location']."', '".$row['sales_order_invoice_number']."', '".$row['sales_order_total_products']."', '".$row['sales_order_created_date']."', '".$row['sales_order_invoice_date']."', '".$row['sales_order_shipped_via']."', '".$row['sales_order_grand_total']."', '".$row['sales_order_id']."', '".$row['account_balance_terms']."', '".$row['sales_order_status']."' ) ,";
			}
			echo $count;
			return;
			if($count > 0){
				$insertQuery = substr($insertQuery, 0, -2);
				$result = multiQueryDatabase($insertQuery);
				if($result){
					echo "Updation successful";
				}
				else{
					echo "Error occured";
				}
			}
		}
	}
	else{
		echo "Something went wrong";
	}
}


function do_curl( array $post = NULL, array $options = array()) {
	$url = "https://ats.fuse5live.com/f5api/";
    $field_string = http_build_query($post);
    $defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_POSTFIELDS => $field_string,
        CURLOPT_HEADER => 0, 
        CURLOPT_URL => $url, 
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        
    );

    $ch = curl_init(); 
    curl_setopt_array($ch, ($options + $defaults)); 
    if( ! $result = curl_exec($ch)) 
    { 
        //trigger_error(curl_error($ch)); 
    }
    if(!$result) {
	    curl_close($ch);
		return NULL;
	}
    curl_close($ch); 
    return $result; 
}
function getOffset($table){
	$query = "SELECT COUNT(*) FROM `$table`";
	$result = queryDatabase($query);
	if($result && $data = mysqli_fetch_array($result)){
		return (int)$data[0];
	}
	else{
		return -1;
	}
}
?>
