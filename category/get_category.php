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

$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

			$query = "SELECT ID, CATEGORY_CODE, CATOGARY_NAME, IMG_URL FROM category WHERE 1";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$rows = array();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$rows[] = $row;
				}
			http_response_code(200);
			echo json_encode($rows);
?>