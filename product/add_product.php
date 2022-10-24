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
		if($data){
			try{
					$catCode = $data->category;
					$proName = $data->productName;
					$shortDisc = $data->shortDesc;
					$discription = $data->discription;
					$brand = $data->brand;
					$price = $data->price;
					$imgUrl = $data->imgUrl;
					$uploadAt = date("Y-m-d H:i:s");
					$discount = 0.00;
					
					$proCode = uniqid();
	
					$query = "INSERT INTO product
									SET PRDUCT_CODE  = :pro_code,
										CATEGORY_CODE = :cat_code,
										PRODUCT_NAME = :pro_name,
										SHORT_DISCRIPTION = :short_disc,
										DISCRIPTION = :discription,
										BRAND = :brand,
										PRICE = :price,
										DISCOUNT = :discount,
										IMG_URL = :img_url,
										UPLOAD_AT = :upload_at";
	
					$stmt = $conn->prepare($query);
	
					$stmt->bindParam(':pro_code', $proCode);
					$stmt->bindParam(':cat_code', $catCode);
					$stmt->bindParam(':pro_name', $proName);
					$stmt->bindParam(':short_disc', $shortDisc);
					$stmt->bindParam(':discription', $discription);
					$stmt->bindParam(':brand', $brand);
					$stmt->bindParam(':price', $price);	
					$stmt->bindParam(':discount', $discount);	
					$stmt->bindParam(':img_url', $imgUrl);
					$stmt->bindParam(':upload_at', $uploadAt);
					
					if($stmt->execute()){
						http_response_code(200);
						echo json_encode(array("status" => "Data was successfully upload.",
												"error" => false
												));
					}
					else{
						//http_response_code(400);
						echo json_encode(array("status" => "Unable to upload the Data.",
												"error" => true
						));
					}
	
				}
					catch (Exception $e){
						//http_response_code(401);
						// show error message
						echo json_encode(array(
							"status" => "This Cat-Code is already taken, Try another.",
							"error" => true,
							"s_error" => $e->getMessage()
						));
					}
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