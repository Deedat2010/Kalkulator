<?php
session_start();

$hasil = $_SESSION['hasil'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['hasil'], $_SESSION['error']);
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
            <button data-type="num" data-value="7">7</button>
            <button data-type="num" data-value="8">8</button>
            <button data-type="num" data-value="9">9</button>
            <button data-type="clear" class="merah">C</button>
            <button data-type="back" class="merah">⌫</button>

            <button data-type="num" data-value="4">4</button>
            <button data-type="num" data-value="5">5</button>
            <button data-type="num" data-value="6">6</button>
            <button data-type="op" data-value="+" class="merah">+</button>
            <button data-type="op" data-value="*" class="merah">x</button>

            <button data-type="num" data-value="1">1</button>
            <button data-type="num" data-value="2">2</button>
            <button data-type="num" data-value="3">3</button>
            <button data-type="op" data-value="-" class="merah">-</button>
            <button data-type="op" data-value="/" class="merah">/</button>

            <button data-type="num" data-value=".">.</button>
            <button data-type="num" data-value="0">0</button>
            <button data-type="equals" class="lebar">=</button>
        </div>
    </div>

    <form id="formHitung" method="POST" action="proses.php">
        <input type="hidden" name="angka1"   id="angka1">
        <input type="hidden" name="operator" id="operator">
        <input type="hidden" name="angka2"   id="angka2">
    </form>

    <script>
        const error   = <?= json_encode($error) ?>;
        const display = document.getElementById('display');
        const simbol  = { '+': '+', '-': '-', '*': 'x', '/': '/' };

        let a = <?= json_encode($hasil) ?>;  // angka pertama (atau hasil sebelumnya)
        let op = '';                         // operator
        let b = '';                          // angka kedua
        let fresh = a !== '';                // true = angka berikutnya menimpa hasil

        function tampil() {
            display.textContent = (a + (op ? ` ${simbol[op]} ${b}` : '')) || '0';
            display.classList.remove('error');
        }

        function tambah(s, d) {
            if (d === '.') {
                if (s.includes('.')) return s;
                return (s === '' || s === '-' ? s + '0' : s) + '.';
            }
            return s === '0' ? d : s + d;
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
            if (b !== '') return;
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
            a = op = b = '';
            fresh = false;
            tampil();
        }

        function hitung() {
            if (a === '' || op === '' || b === '') return;
            document.getElementById('angka1').value   = a;
            document.getElementById('operator').value = op;
            document.getElementById('angka2').value   = b;
            document.getElementById('formHitung').submit();
        }

        // Klik tombol
        document.querySelector('.tombol').addEventListener('click', e => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const { type, value } = btn.dataset;

            if (type === 'num')         tekanAngka(value);
            else if (type === 'op')     tekanOperator(value);
            else if (type === 'clear')  bersihkan();
            else if (type === 'back')   hapusSatu();
            else if (type === 'equals') hitung();
        });

        // Keyboard
        document.addEventListener('keydown', e => {
            if ((e.key >= '0' && e.key <= '9') || e.key === '.') tekanAngka(e.key);
            else if ('+-*/'.includes(e.key) && e.key.length === 1) tekanOperator(e.key);
            else if (e.key === 'Enter' || e.key === '=') { e.preventDefault(); hitung(); }
            else if (e.key === 'Backspace') hapusSatu();
            else if (e.key === 'Escape') bersihkan();
        });

        // Tampilkan error jika ada
        if (error) {
            display.textContent = error;
            display.classList.add('error');
        } else {
            tampil();
        }
    </script>
</body>
</html>