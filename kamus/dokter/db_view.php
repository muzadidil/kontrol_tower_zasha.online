<?php
/**
 * ZASHA DOCTOR - DATABASE DATA EXPLORER
 * Modul untuk memantau isi tabel (Data) secara real-time.
 */
session_start();
require 'koneksi_utama.php';

// 1. Daftar tabel yang ingin kamu SEMBUNYIKAN (Internal Systems & Auth)
$hidden_tables = ['admin', 'user', 'users', 'config', 'migrations', 'personal_access_tokens', 'password_reset_tokens']; 

// 2. Ambil daftar semua tabel
$tables_query = mysqli_query($conn, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_array($tables_query)) {
    if (!in_array($row[0], $hidden_tables)) {
        $tables[] = $row[0];
    }
}

// 3. Ambil tabel yang dipilih
$selected_table = isset($_GET['table']) ? $_GET['table'] : ($tables[0] ?? '');

if (in_array($selected_table, $hidden_tables)) {
    die("[FATAL_ERROR] Access Denied: Security Policy Violation.");
}

// 4. Ambil isi tabel (Maksimal 100 baris terbaru agar terminal tidak nge-lag)
$data = null;
$columns = [];
if ($selected_table) {
    // Pastikan order by ID atau kolom pertama agar data terbaru di atas
    $data = mysqli_query($conn, "SELECT * FROM `$selected_table` ORDER BY 1 DESC LIMIT 100");
    if ($data) {
        while ($finfo = mysqli_fetch_field($data)) {
            $columns[] = $finfo->name;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZASHA | DATA EXPLORER</title>
    <style>
        body {
            background-color: #030303;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
            margin: 0;
            font-size: 13px;
        }
        .container { max-width: 100%; overflow-x: hidden; }
        .header { border-bottom: 1px dashed #005500; padding-bottom: 15px; margin-bottom: 20px; }
        
        .cmd-line { display: flex; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px; }
        .prompt { color: #00ff00; font-weight: bold; }
        
        select {
            background: #000;
            border: 1px solid #005500;
            color: #00ffff;
            padding: 5px;
            font-family: 'Courier New';
            outline: none;
            cursor: pointer;
        }
        select:focus { border-color: #00ff00; }

        .btn {
            background: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 5px 15px;
            font-family: 'Courier New';
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
        }
        .btn:hover { background: #00ff00; color: #000; }

        .sys-info {
            background: #080808;
            border: 1px solid #333;
            padding: 10px;
            color: #888;
            margin-bottom: 15px;
        }
        .sys-highlight { color: #ffff00; font-weight: bold; }

        /* Tabel Data Ala Terminal */
        .table-container {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #003300;
            max-height: 70vh; /* Agar bisa scroll ke bawah jika data banyak */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }
        thead th {
            background: #001a00;
            color: #00ffff;
            border-bottom: 2px solid #00ff00;
            padding: 10px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #002200;
            border-right: 1px dashed #002200;
            color: #ccc;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        tbody tr:hover { background: #001100; }
        tbody tr:hover td { color: #fff; }

        a.back-link { color: #00ff00; text-decoration: none; border-bottom: 1px solid #00ff00; font-size: 12px; }
        a.back-link:hover { background: #00ff00; color: #000; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0;">[ MODULE ] RAW_DATA_VIEWER_v1.0</h2>
        <a href="index.php" class="back-link">EXIT_TO_DOCTOR</a> | 
        <a href="explorer.php" class="back-link">GOTO_COLUMN_EXPLORER</a>
    </div>

    <!-- PENGENDALI TERMINAL -->
    <div class="cmd-line">
        <span class="prompt">root@zasha/viewer:~$ fetch_data --table</span>
        <form method="GET" id="tableForm">
            <select name="table" onchange="this.form.submit()">
                <?php foreach ($tables as $t): ?>
                    <option value="<?= $t ?>" <?= $selected_table == $t ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <button onclick="document.getElementById('tableForm').submit()" class="btn">RELOAD_DATA</button>
    </div>

    <?php if ($selected_table && $data): ?>
        <?php $row_count = mysqli_num_rows($data); ?>
        <div class="sys-info">
            [CONNECTION] <span class="sys-highlight">u607709216_zasha_services</span><br>
            [QUERY] SELECT * FROM `<span class="sys-highlight"><?= $selected_table ?></span>` ORDER BY 1 DESC LIMIT 100<br>
            [RESULT] Fetched <span class="sys-highlight"><?= $row_count ?></span> rows.
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($row_count > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <td title="<?= htmlspecialchars($row[$col] ?? '') ?>">
                                        <?= htmlspecialchars($row[$col] ?? 'NULL') ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($columns) ?>" style="text-align:center; padding:30px; color:#ff0000;">
                                TABLE_IS_EMPTY
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="color:#ff0000; padding:20px; border:1px solid #ff0000; background:rgba(255,0,0,0.1);">
            [!] ERROR: No table selected or database connection failed.
        </div>
    <?php endif; ?>

</div>

</body>
</html>