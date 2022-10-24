<?php
include_once '../database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();


$data = json_decode(file_get_contents("php://input"));

if($data){

$userID = $data->userName;
$password = $data->password;

$query = "SELECT ID, EMAIL, NAME, MOBILE, USER_ROLE, PASSWORD FROM user WHERE EMAIL = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $userID);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
	$DB_id = $row['ID'];
    $DB_emal = $row['EMAIL'];
    $DB_name = $row['NAME'];
	$DB_mobile = $row['MOBILE'];
	$DB_userRole = $row['USER_ROLE'];
	$DB_password = $row['PASSWORD'];

    if(password_verify($password, $DB_password))
    {
        $secret_key = "*$%43MVKJTKMN$#";
        $issuer_claim = "http://localhost.com/"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60*60*24; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
				"id" => $DB_id,
				"email" => $DB_emal,
                "name" => $DB_name,
				"mobile" => $DB_mobile,
				"userRole" => $DB_userRole
        ));

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key, 'HS256');
        echo json_encode(
            array(
                "status" => "Successful login.",
				"error" => false,
                "accessToken" => $jwt,
				"userRole" => $DB_userRole,
				"userData" => array (
					"id" => $DB_id,
					"email" => $DB_emal,
					"name" => $DB_name,
					"mobile" => $DB_mobile
				),
            ));
    }
    else{

        //http_response_code(401);
        echo json_encode(array(
                    "status" => "Incorrect password.",
					"error" => true,
								));
    }
}else{
		//http_response_code(401);
        echo json_encode(array("status" => "User not registed", 
                                "error" => true,
								"user" => $userID
								));
}
}else{
 
    // set response code
	//http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("status" => "Access denied."));
}
?>