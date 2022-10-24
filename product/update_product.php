<?php
include_once '../database.php';
include_once '../hearder_authorization.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
JWT::$leeway = 60;

$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$jwt = getBearerToken();
$key = getSecretKey();

if($jwt){
	try{
		$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
		//echo $jwt;
        $proCode = $data->productCode;
		$catCode = $data->category;
		$proName = $data->productName;
		$shortDisc = $data->shortDesc;
		$discription = $data->discription;
		$brand = $data->brand;
		$price = $data->price;
		$uploadAt = date("Y-m-d H:i:s");
		if($proCode){
			try{
                    $query = "UPDATE product
									SET 
										CATEGORY_CODE = :cat_code,
										PRODUCT_NAME = :pro_name,
										SHORT_DISCRIPTION = :short_disc,
										DISCRIPTION = :discription,
										BRAND = :brand,
										PRICE = :price,
										UPLOAD_AT = :upload_at
                                    WHERE PRDUCT_CODE  = :pro_code ";
	
					$stmt = $conn->prepare($query);
	
					$stmt->bindParam(':pro_code', $proCode);
					$stmt->bindParam(':cat_code', $catCode);
					$stmt->bindParam(':pro_name', $proName);
					$stmt->bindParam(':short_disc', $shortDisc);
					$stmt->bindParam(':discription', $discription);
					$stmt->bindParam(':brand', $brand);
					$stmt->bindParam(':price', $price);	
					$stmt->bindParam(':upload_at', $uploadAt);
				
				if($stmt->execute()){

					http_response_code(200);
					echo json_encode(array("status" => "Data was successfully update.",
											"error" => false
											));
				}
				else{
					//http_response_code(400);
					echo json_encode(array("status" => "Unable to update the Data.",
											"error" => true
					));
				}
			}catch (Exception $e){
					//http_response_code(401);
					echo json_encode(array(
						"status" => "Try another time.",
						"error" => true,
						"s_error" => $e->getMessage()
					));
				}
		}else{
            //http_response_code(400);
					echo json_encode(array("status" => "Emty data set.",
                    "error" => true
                ));
        }
		
	}catch(Exception $e){
		//http_response_code(401);
		echo json_encode(array("status" => "Access denied. please re-login",
								"error" => true,
								"s_error" => $e->getMessage()
						));
	}

}else{
	//http_response_code(401);
	echo json_encode(array("error" => "Access denied."));
}
?>