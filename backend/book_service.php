<?php
// backend/book_service.php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php'; // provides $conn (mysqli)

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST allowed');
    }

    // 1) Collect inputs
    $name       = trim($_POST['name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $service_id = trim($_POST['service_id'] ?? '');
    $car_model  = trim($_POST['car_model'] ?? '');

    // ✅ New fields (from map)
    $latitude   = trim($_POST['latitude'] ?? '');
    $longitude  = trim($_POST['longitude'] ?? '');

    if ($name === '' || $phone === '' || $service_id === '' || $car_model === '') {
        throw new Exception('Missing required fields');
    }

    // 2) Verify service exists
    $stmt = $conn->prepare("SELECT service_id FROM services WHERE service_id = ?");
    $stmt->bind_param('i', $service_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        throw new Exception('Invalid service_id');
    }
    $stmt->close();

    // 3) Find or create user
    $user_id = null;
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone = ? LIMIT 1");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $stmt->bind_result($user_id_found);
    if ($stmt->fetch()) {
        $user_id = $user_id_found;
    }
    $stmt->close();

    if ($user_id === null) {
        $email = 'guest+' . preg_replace('/\D+/', '', $phone) . '@example.local';
        $password_hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
        $role = 'user';

        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss', $name, $email, $phone, $password_hash, $role);
        if (!$stmt->execute()) {
            throw new Exception('Failed to create guest user: ' . $conn->error);
        }
        $user_id = $stmt->insert_id;
        $stmt->close();
    }

    if (!$user_id) throw new Exception('Could not resolve user_id');

    // ✅ 4) Insert booking (Now including latitude, longitude)
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, car_model, service_id, contact_phone, latitude, longitude)
        VALUES (?,?,?,?,?,?)
    ");

    $stmt->bind_param('isisss', $user_id, $car_model, $service_id, $phone, $latitude, $longitude);

    if (!$stmt->execute()) {
        throw new Exception('Failed to insert booking: ' . $stmt->error);
    }

    $response = [
        'status' => 'success',
        'booking_id' => $stmt->insert_id,
        'message' => 'Booking saved successfully ✅'
    ];

    $stmt->close();

} catch (Throwable $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
} finally {
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
    echo json_encode($response);
}
