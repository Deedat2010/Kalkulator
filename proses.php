<?php
session_start();

// Hanya boleh diakses lewat form (method POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$angka1   = $_POST['angka1']   ?? '';
$angka2   = $_POST['angka2']   ?? '';
$operator = $_POST['operator'] ?? '';

// Simpan input agar form tetap terisi saat kembali ke index.php
$_SESSION['angka1']   = $angka1;
$_SESSION['angka2']   = $angka2;
$_SESSION['operator'] = $operator;

// Validasi: harus angka
if (!is_numeric($angka1) || !is_numeric($angka2)) {
    $_SESSION['error'] = "Kedua input harus berupa angka!";
} else {
    $a = floatval($angka1);
    $b = floatval($angka2);

    switch ($operator) {
        case '+':
            $_SESSION['hasil'] = $a + $b;
            break;
        case '-':
            $_SESSION['hasil'] = $a - $b;
            break;
        case '*':
            $_SESSION['hasil'] = $a * $b;
            break;
        case '/':
            // Validasi pembagian dengan nol
            if ($b == 0) {
                $_SESSION['error'] = "Tidak bisa membagi dengan nol!";
            } else {
                $_SESSION['hasil'] = $a / $b;
            }
            break;
        default:
            $_SESSION['error'] = "Operator tidak valid!";
    }
}

// Kembali ke halaman form
header('Location: index.php');
exit;