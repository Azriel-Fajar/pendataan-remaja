<?php
function saveAttendance($name, $date)
{
    $dataFile = __DIR__ . '/../data/attendance.json';

    // Buat folder data jika belum ada
    if (!file_exists(dirname($dataFile))) {
        mkdir(dirname($dataFile), 0777, true);
    }

    // Baca data yang ada
    $attendances = [];
    if (file_exists($dataFile)) {
        $attendances = json_decode(file_get_contents($dataFile), true) ?: [];
    }

    // Tambahkan data baru
    $attendances[] = [
        'id' => uniqid(),
        'name' => $name,
        'attendance_date' => $date,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Simpan ke file
    file_put_contents($dataFile, json_encode($attendances, JSON_PRETTY_PRINT));

    return true;
}

function getAttendancesByDate($date)
{
    $dataFile = __DIR__ . '/../data/attendance.json';

    if (!file_exists($dataFile)) {
        return [];
    }

    $allAttendances = json_decode(file_get_contents($dataFile), true) ?: [];

    return array_filter($allAttendances, function ($att) use ($date) {
        return $att['attendance_date'] === $date;
    });
}

function getAllDates()
{
    $dataFile = __DIR__ . '/../data/attendance.json';

    if (!file_exists($dataFile)) {
        return [];
    }

    $allAttendances = json_decode(file_get_contents($dataFile), true) ?: [];

    $dates = array_unique(array_column($allAttendances, 'attendance_date'));
    rsort($dates);

    return $dates;
}

function deleteAttendance($id)
{
    $dataFile = __DIR__ . '/../data/attendance.json';

    if (!file_exists($dataFile)) {
        return false;
    }

    $allAttendances = json_decode(file_get_contents($dataFile), true) ?: [];
    $originalCount = count($allAttendances);

    $allAttendances = array_filter($allAttendances, function ($att) use ($id) {
        return $att['id'] !== $id;
    });

    if (count($allAttendances) < $originalCount) {
        file_put_contents($dataFile, json_encode(array_values($allAttendances), JSON_PRETTY_PRINT));
        return true;
    }

    return false;
}
