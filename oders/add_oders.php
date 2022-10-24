<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to decode jwt
include_once '../database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

	if($data){
		try{

			$firstName = $data->firstName;
            $lastName = $data->lastName;
            $contactNo1 = $data->contactNo1;
            $contactNo2 = $data->contactNo2;
            $email = $data->email;
            $addressLine1 = $data->addressLine1;
            $addressLine2 = $data->addressLine2;
            $homeTown = $data->homeTown;
            $zipCode = $data->zipCode;
            $item = json_encode($data->item);
            $itemPrice = $data->itemPrice;
            $taxPrice = $data->taxPrice;
            $ShippingPrice = $data->ShippingPrice;
            $totalPrice = $data->totalPrice;
            $paymentMethord = $data->paymentMethord;

            $name = $firstName.' '.$lastName;
            $requstDate = date("Y-m-d H:i:s");
            $deliveryDate = '';
            $address = $addressLine1.', '.$addressLine2.', '.$homeTown.'-'.$zipCode;
            $invoice_no = uniqid();

            $query = "INSERT INTO purchasing
                            SET INVOICE_NO  = :invoice_no,
                                NAME = :name,
                                ADDRESS = :address,
                                CONTACT_1 = :contact_1,
                                CONTACT_2 = :contact_2,
                                EMAIL = :email,
                                ITEM_LIST = :item_list,
                                ITEM_PRICE = :item_price,
                                DELIVERY_COST = :delivery_cost,
                                TAX = :tax,
                                TOTAL_PRICE = :total_price,
                                PAY_M = :pay_m,
                                REQUEST_DATE = :requst_date,
                                DELIVERY_DATE = :delivery_date ";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(':invoice_no', $invoice_no);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':contact_1', $contactNo1);
            $stmt->bindParam(':contact_2', $contactNo2);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':item_list', $item);	
            $stmt->bindParam(':item_price', $itemPrice);	
            $stmt->bindParam(':delivery_cost', $ShippingPrice);
            $stmt->bindParam(':tax', $taxPrice);
            $stmt->bindParam(':total_price', $totalPrice);
            $stmt->bindParam(':pay_m', $paymentMethord);
            $stmt->bindParam(':requst_date', $requstDate);
            $stmt->bindParam(':delivery_date', $deliveryDate);
            
            if($stmt->execute()){
                http_response_code(200);
                echo json_encode(array("status" => "Payment was successfully upload. INVOICE NO:".$invoice_no,
                                        "error" => false
                                        ));
            }
            else{
                //http_response_code(400);
                echo json_encode(array("status" => "Unable to Pay.",
                                        "error" => true
                ));
            }


			}
				catch (Exception $e){
					//http_response_code(401);
				 
					// show error message
					echo json_encode(array(
						"message" => "Try another time...",
						"register" => false,
						"error" => $e->getMessage()
					));
				}
	}else{
	 // set response code
    //http_response_code(401);

    echo json_encode(array("error" => "Access denied."));
	}

?>