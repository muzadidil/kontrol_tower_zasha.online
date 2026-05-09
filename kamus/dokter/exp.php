<?php
/**
 * ZASHA DOCTOR - COLUMN EXPLORER (TERMINAL EDITION)
 * Modul untuk intip struktur tabel tanpa buka phpMyAdmin.
 */
session_start();
require 'koneksi_utama.php';

// 1. Daftar tabel yang ingin kamu SEMBUNYIKAN (Internal Systems)
$hidden_tables = ['admin', 'user', 'users', 'config', 'migrations', 'failed_jobs']; 

// 2. Ambil daftar semua tabel dari database utama
$tables_query = mysqli_query($conn, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_array($tables_query)) {
    if (!in_array($row[0], $hidden_tables)) {
        $tables[] = $row[0];
    }
}

// 3. Ambil tabel yang dipilih (Default ke tabel pertama jika kosong)
$selected_table = isset($_GET['table']) ? $_GET['table'] : ($tables[0] ?? '');

if (in_array($selected_table, $hidden_tables)) {
    die("[FATAL_ERROR] Access Denied: Restricted Sector.");
}

// 4. Ambil HANYA Struktur Kolom
$columns = [];
if ($selected_table) {
    // Gunakan backtick untuk keamanan nama tabel
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$selected_table`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZASHA | COLUMN EXPLORER</title>
    <style>
        body {
            background-color: #030303;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
            margin: 0;
            font-size: 14px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { border-bottom: 1px dashed #005500; padding-bottom: 15px; margin-bottom: 25px; }
        
        .system-msg { color: #888; font-size: 12px; margin-bottom: 20px; }
        
        .cmd-line { display: flex; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .prompt { color: #00ff00; font-weight: bold; }
        
        select {
            background: #000;
            border: 1px solid #005500;
            color: #00ffff; /* Warna Cyan untuk data */
            padding: 5px;
            font-family: 'Courier New';
            outline: none;
            cursor: pointer;
        }
        select:focus { border-color: #00ff00; }

        .btn-copy {
            background: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 5px 15px;
            font-family: 'Courier New';
            cursor: pointer;
            font-size: 12px;
        }
        .btn-copy:hover { background: #00ff00; color: #000; }

        /* Grid Kolom */
        .column-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .col-box {
            border: 1px solid #002200;
            padding: 8px 12px;
            background: #080808;
            display: flex;
            justify-content: space-between;
            color: #00ffff;
        }
        .col-box:hover { border-color: #00ff00; background: #001100; }
        .col-index { color: #005500; font-size: 10px; }

        .summary {
            background: #001100;
            padding: 10px;
            border-left: 3px solid #00ff00;
            margin-bottom: 20px;
            font-size: 12px;
        }

        a { color: #00ff00; text-decoration: none; font-size: 12px; border-bottom: 1px solid #00ff00; }
        a:hover { background: #00ff00; color: #000; }
        
        textarea#hiddenCopyText { position: absolute; left: -9999px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0;">[ MODULE ] COLUMN_EXPLORER_v1.0</h2>
        <a href="index.php">BACK_TO_DOCTOR</a>
    </div>

    <div class="system-msg">
        [SYSTEM] Database Connected: u607709216_zasha_services<br>
        [INFO] Terdeteksi <?= count($tables) ?> tabel aktif (Hiding restricted sectors).
    </div>

    <!-- PENGENDALI TERMINAL -->
    <div class="cmd-line">
        <span class="prompt">root@zasha/explorer:~$ mount_table --target</span>
        <form method="GET" id="tableForm">
            <select name="table" onchange="this.form.submit()">
                <?php foreach ($tables as $t): ?>
                    <option value="<?= $t ?>" <?= $selected_table == $t ? 'selected' : '' ?>>
                        <?= $t ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        
        <?php if (!empty($columns)): ?>
        <button onclick="copyToClipboard()" class="btn-copy" id="btnCopy">
            EXEC_COPY_ALL_COLUMNS
        </button>
        <?php endif; ?>
    </div>

    <?php if ($selected_table && !empty($columns)): ?>
        <div class="summary">
            CURRENT_LOCATION: <span style="color:#ffff00;">DATABASE/<?= $selected_table ?></span><br>
            TOTAL_COLUMNS: <?= count($columns) ?> fields found.
        </div>

        <div class="column-grid">
            <?php foreach ($columns as $index => $col): ?>
                <div class="col-box">
                    <span><?= $col ?></span>
                    <span class="col-index">#<?= sprintf("%02d", $index + 1) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Hidden data for copy functionality -->
        <textarea id="hiddenCopyText"><?= implode("\n", $columns) ?></textarea>

    <?php else: ?>
        <div style="color:#ff0000; padding:20px; border:1px solid #ff0000; background:rgba(255,0,0,0.1);">
            [!] ERROR: Sector is empty or corrupted.
        </div>
    <?php endif; ?>

</div>

<script>
    function copyToClipboard() {
        var copyText = document.getElementById("hiddenCopyText");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Untuk mobile

        navigator.clipboard.writeText(copyText.value).then(function() {
            var btn = document.getElementById("btnCopy");
            var originalText = btn.innerText;
            
            btn.innerText = "SUCCESS: COPIED_TO_CLIPBOARD";
            btn.style.background = "#00ff00";
            btn.style.color = "#000";
            
            setTimeout(function() {
                btn.innerText = originalText;
                btn.style.background = "#003300";
                btn.style.color = "#00ff00";
            }, 2000);
        }).catch(function(err) {
            alert('CRITICAL_ERROR: Clipboard access denied.');
        });
    }
</script>

</body>
</html>