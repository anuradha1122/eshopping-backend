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
		$productCode = $data->productCode;
				$query = "DELETE FROM product WHERE PRDUCT_CODE ='" . $productCode . "'";
				$stmt = $conn->prepare($query);
	
				if($stmt->execute()){

					http_response_code(200);
					echo json_encode(array("status" => "Data was successfully Deleted.",
											"error" => false
											));
				}
				else{
					//http_response_code(400);

					echo json_encode(array("status" => "Unable to upload the Deleted.",
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