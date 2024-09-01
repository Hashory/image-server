<?php
$dsn = sprintf('mysql:host=%s;dbname=%s', getenv('MYSQL_HOST'), getenv('MYSQL_DATABASE'));
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if (isset($_GET['id'])) {
		$id = intval($_GET['id']);

		// Retrieve image data from the database
		$sql = "SELECT mime_type, image_data FROM images WHERE id = ?";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$id]);
		$stmt->bindColumn(1, $mime_type);
		$stmt->bindColumn(2, $data, PDO::PARAM_LOB);

		if ($stmt->fetch(PDO::FETCH_BOUND)) {
			$imageData = stream_get_contents($data);
			$dataLength = strlen($imageData);

			http_response_code(200);
			header("Content-Type: " . $mime_type); // Set appropriate MIME type
			header("Content-Length: " . $dataLength); // Set the content length
			echo $imageData;
		} else {
			http_response_code(404);
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'message' => 'Image not found.']);
			exit;
		}
	} else {
		http_response_code(400);
		header('Content-Type: application/json');
		echo json_encode(['success' => false, 'message' => 'Invalid request.']);
		exit;
	}
} catch (PDOException $e) {
	error_log($e->getMessage()); // Log error

	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode(['success' => false, 'message' => 'A server error occurred.']);
	exit;
}
