<?php
session_start();

// Ambil data "flash" dari proses.php (jika ada), lalu hapus agar tidak muncul lagi saat refresh
$hasil = $_SESSION['hasil'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['hasil'], $_SESSION['error'], $_SESSION['angka1'], $_SESSION['angka2'], $_SESSION['operator']);

// Rapikan tampilan angka desimal (contoh: 0.30000000000000004 -> 0.3)
if ($hasil !== null) {
    $hasil = rtrim(rtrim(number_format((float)$hasil, 10, '.', ''), '0'), '.');
    if ($hasil === '' || $hasil === '-0') {
        $hasil = '0';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Kalkulator Aritmatika</title>
</head>
<body>
    <div class="kalkulator">
        <div class="display" id="display">0</div>

        <div class="tombol">
            <button type="button" data-type="num" data-value="7">7</button>
            <button type="button" data-type="num" data-value="8">8</button>
            <button type="button" data-type="num" data-value="9">9</button>
            <button type="button" data-type="clear" class="merah">C</button>
            <button type="button" data-type="back" class="merah">⌫</button>

            <button type="button" data-type="num" data-value="4">4</button>
            <button type="button" data-type="num" data-value="5">5</button>
            <button type="button" data-type="num" data-value="6">6</button>
            <button type="button" data-type="op" data-value="+" class="merah">+</button>
            <button type="button" data-type="op" data-value="*" class="merah">x</button>

            <button type="button" data-type="num" data-value="1">1</button>
            <button type="button" data-type="num" data-value="2">2</button>
            <button type="button" data-type="num" data-value="3">3</button>
            <button type="button" data-type="op" data-value="-" class="merah">-</button>
            <button type="button" data-type="op" data-value="/" class="merah">/</button>

            <button type="button" data-type="num" data-value=".">.</button>
            <button type="button" data-type="num" data-value="0">0</button>
            <button type="button" data-type="equals" class="lebar">=</button>
        </div>
    </div>

    <!-- Form tersembunyi: dikirim ke proses.php saat tombol "=" ditekan -->
    <form id="formHitung" method="POST" action="proses.php">
        <input type="hidden" name="angka1" id="angka1">
        <input type="hidden" name="operator" id="operator">
        <input type="hidden" name="angka2" id="angka2">
    </form>

    <script>
        // Data dari PHP (hasil / pesan error setelah proses.php)
        const hasilAwal = <?= json_encode($hasil) ?>;
        const errorAwal = <?= json_encode($error) ?>;

        const display = document.getElementById('display');
        const simbol = { '+': '+', '-': '-', '*': 'x', '/': '/' };

        let a = '';        // angka pertama
        let op = '';       // operator
        let b = '';        // angka kedua
        let fresh = false; // true jika a berisi hasil hitungan sebelumnya

        if (hasilAwal !== null) {
            a = hasilAwal;
            fresh = true;
        }

        function tampil() {
            const teks = a + (op ? ' ' + simbol[op] + ' ' + b : '');
            display.textContent = teks === '' ? '0' : teks;
            display.classList.remove('error');
        }

        if (errorAwal) {
            display.textContent = errorAwal;
            display.classList.add('error');
        } else {
            tampil();
        }

        function tambah(s, d) {
            if (d === '.') {
                if (s.includes('.')) return s;
                if (s === '' || s === '-') return s + '0.';
                return s + '.';
            }
            if (s === '0') return d;
            return s + d;
        }

        function tekanAngka(d) {
            if (op === '') {
                if (fresh) { a = ''; fresh = false; }
                a = tambah(a, d);
            } else {
                b = tambah(b, d);
            }
            tampil();
        }

        function tekanOperator(o) {
            if (a === '') a = '0';
            if (b !== '') return; // hanya 2 angka per perhitungan
            op = o;
            fresh = false;
            tampil();
        }

        function hapusSatu() {
            if (b !== '') b = b.slice(0, -1);
            else if (op !== '') op = '';
            else a = a.slice(0, -1);
            fresh = false;
            tampil();
        }

        function bersihkan() {
            a = ''; op = ''; b = ''; fresh = false;
            tampil();
        }

        function hitung() {
            if (a === '' || op === '' || b === '') return;
            document.getElementById('angka1').value = a;
            document.getElementById('operator').value = op;
            document.getElementById('angka2').value = b;
            document.getElementById('formHitung').submit();
        }

        // Klik tombol
        document.querySelector('.tombol').addEventListener('click', function (e) {
            const btn = e.target.closest('button');
            if (!btn) return;
            const tipe = btn.dataset.type;
            const nilai = btn.dataset.value;

            if (tipe === 'num') tekanAngka(nilai);
            else if (tipe === 'op') tekanOperator(nilai);
            else if (tipe === 'clear') bersihkan();
            else if (tipe === 'back') hapusSatu();
            else if (tipe === 'equals') hitung();
        });

        // Dukungan keyboard
        document.addEventListener('keydown', function (e) {
            if ((e.key >= '0' && e.key <= '9') || e.key === '.') tekanAngka(e.key);
            else if (['+', '-', '*', '/'].includes(e.key)) tekanOperator(e.key);
            else if (e.key === 'Enter' || e.key === '=') { e.preventDefault(); hitung(); }
            else if (e.key === 'Backspace') hapusSatu();
            else if (e.key === 'Escape') bersihkan();
        });
    </script>
</body>
</html>