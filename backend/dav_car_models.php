<?php
header('Content-Type: application/json; charset=UTF-8');
require_once 'db.php';

/* Count bookings per car_model (string stored in bookings) */
$sql = "
SELECT b.car_model, COUNT(*) AS total
FROM bookings b
GROUP BY b.car_model
ORDER BY total DESC
";
$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
echo json_encode($data);
$conn->close();
