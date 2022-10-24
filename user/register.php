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

				$email = $data->email;
				$name = $data->name;
				$mobile = $data->mobile;
				$password = $data->password;
				$userProfile = 0; // 0-User profile not create
				$status = 1; //1-Active user; 0-inactive
				$userRole = 1; // type-0 addmin, type -1 user

				$query = "INSERT INTO user
								SET EMAIL = :email,
									NAME = :name,
									MOBILE = :mobile,
									USER_ROLE = :user_role,
									PASSWORD = :password,
									PROFILE = :profile,
									STATUS = :status";

				$stmt = $conn->prepare($query);

				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':mobile', $mobile);
				$stmt->bindParam(':user_role', $userRole);
				$stmt->bindParam(':password', $password);
				$stmt->bindParam(':profile', $userProfile);
				$stmt->bindParam(':status', $status);

				$password_hash = password_hash($password, PASSWORD_BCRYPT);

				$stmt->bindParam(':password', $password_hash);

				if($stmt->execute()){

					http_response_code(200);
					echo json_encode(array("message" => "User was successfully registered.",
											"register" => true
											));
				}
				else{
					//http_response_code(400);

					echo json_encode(array("message" => "Unable to register the user.",
											"register" => false
					));
				}

			}
				catch (Exception $e){
					//http_response_code(401);
				 
					// show error message
					echo json_encode(array(
						"message" => "This username is already taken, Try another.",
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