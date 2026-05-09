<?php
// FILE: kamus.zasha.online/dokter/koneksi_utama.php

$host = "127.0.0.1";
$user = "u607709216_zasha";
$pass = "Q05a10z92!!!";
$db   = "u607709216_zasha_services";

// Koneksi ke Jantung Utama Zasha
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Gagal Terhubung ke Database Utama: " . mysqli_connect_error());
}
?>