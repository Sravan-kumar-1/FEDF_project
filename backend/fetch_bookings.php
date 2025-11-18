<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$sql = "
SELECT 
    b.booking_id,
    u.name AS customer_name,
    b.service_id,
    s.service_name,
    b.car_model,
    b.booking_date,
    b.latitude,
    b.longitude
FROM bookings b
JOIN users u ON b.user_id = u.user_id
JOIN services s ON b.service_id = s.service_id
ORDER BY b.booking_date DESC
";

$result = $conn->query($sql);

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
