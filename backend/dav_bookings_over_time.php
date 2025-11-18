<?php
header('Content-Type: application/json; charset=UTF-8');
require_once 'db.php';

/* Daily bookings trend */
$sql = "
SELECT DATE(b.booking_date) AS day, COUNT(*) AS total
FROM bookings b
GROUP BY DATE(b.booking_date)
ORDER BY day
";
$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
echo json_encode($data);
$conn->close();
