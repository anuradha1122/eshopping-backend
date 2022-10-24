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
                    $brandName = $data->brandName;
                    $imgUrl = $data->imgUrl;
                    
                    $brandCode = uniqid();
                    $uploadAt = date("Y-m-d H:i:s");
    
                    $query = "INSERT INTO brands
                                    SET BRAND_CODE = :brand_code,
                                        BRAND_NAME = :brand_name,
                                        IMG_URL = :img_url,
                                        UPLOAD_AT = :upload_at";
    
                    $stmt = $conn->prepare($query);
    
                    $stmt->bindParam(':brand_code', $brandCode);
                    $stmt->bindParam(':brand_name', $brandName);
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