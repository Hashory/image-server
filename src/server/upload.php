<?php
$dsn = sprintf('mysql:host=%s;dbname=%s', getenv('MYSQL_HOST'), getenv('MYSQL_DATABASE'));
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

header('Content-Type: application/json');

try {
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$imageData = file_get_contents('php://input');

		// File size check (up to 5MB)
		if (strlen($imageData) > 5000000) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'File size is too large.']);
			exit;
		}

		// MIME type check
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime_type = $finfo->buffer($imageData);
		if ($mime_type !== 'image/jpeg' && $mime_type !== 'image/png' && $mime_type !== 'image/gif' && $mime_type !== 'image/webp' && $mime_type !== 'image/avif') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Only JPEG, PNG, GIF, WebP or AVIF images are allowed.']);
			exit;
		}

		// Save to database
		$sql = "INSERT INTO images (mime_type, image_data) VALUES (?, ?)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $mime_type);
		$stmt->bindParam(2, $imageData, PDO::PARAM_LOB);

		$stmt->execute();

		$id = $pdo->lastInsertId(); // Get the last inserted ID

		http_response_code(200);
		echo json_encode(['success' => true, 'id' => $id]);
	} else {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Invalid request.']);
		exit;
	}
} catch (PDOException $e) {
	error_log($e->getMessage()); // Log error

	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'A server error occurred.']);
	exit;
}
