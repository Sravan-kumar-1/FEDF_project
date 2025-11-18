<?php
header('Content-Type: application/json; charset=UTF-8');
require_once 'db.php';

/* Count bookings per service */
$sql = "
SELECT s.service_name, COUNT(*) AS total
FROM bookings b
JOIN services s ON b.service_id = s.service_id
GROUP BY s.service_name
ORDER BY total DESC
";
$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
echo json_encode($data);
$conn->close();
