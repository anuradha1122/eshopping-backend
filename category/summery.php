<?php
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

			$query = "SELECT A.ID, A.CATEGORY_CODE, A.CATOGARY_NAME, A.IMG_URL, A.UPLOAD_AT, COUNT(B.PRDUCT_CODE) AS PRODUCT_COUNT
						FROM category AS A
						INNER JOIN product AS B ON A.CATEGORY_CODE = B.CATEGORY_CODE
						GROUP BY B.CATEGORY_CODE";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$rows = array();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$rows[] = $row;
				}
			http_response_code(200);
			echo json_encode($rows);
?>