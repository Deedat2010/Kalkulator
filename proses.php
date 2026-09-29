<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a  = $_POST['angka1']   ?? '';
    $b  = $_POST['angka2']   ?? '';
    $op = $_POST['operator'] ?? '';

    if (!is_numeric($a) || !is_numeric($b)) {
        $_SESSION['error'] = "Kedua input harus berupa angka!";
    } elseif ($op === '/' && $b == 0) {
        $_SESSION['error'] = "Tidak bisa membagi dengan nol!";
    } else {
        $hasil = match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            '*' => $a * $b,
            '/' => $a / $b,
            default => null,
        };

        if ($hasil === null) {
            $_SESSION['error'] = "Operator tidak valid!";
        } else {
            // round() merapikan desimal panjang, "+ 0" mencegah hasil "-0"
            $_SESSION['hasil'] = (string) (round($hasil, 10) + 0);
        }
    }
}

header('Location: index.php');
exit;