<?php
session_start();
require 'koneksi_utama.php';

// --- A. LOGIKA UNTUK AJAX (MENGAMBIL KOLOM) ---
if (isset($_GET['get_columns'])) {
    $tabel = mysqli_real_escape_string($conn, $_GET['get_columns']);
    $query = mysqli_query($conn, "SHOW COLUMNS FROM $tabel");
    $columns = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $columns[] = $row['Field'];
    }
    echo json_encode($columns);
    exit;
}

// --- B. LOGIKA PEMINDAIAN FILE ---
$hasil_pencarian = [];
if (isset($_POST['cari'])) {
    $kolom_target = $_POST['kolom'];
    $direktori_zasha = '../../'; 

    $iterasi = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($direktori_zasha));
    foreach ($iterasi as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'php' && strpos($file->getPathname(), 'vendor') === false) {
            $content = file($file->getPathname());
            foreach ($content as $line_num => $line_content) {
                if (strpos($line_content, $kolom_target) !== false) {
                    $hasil_pencarian[] = [
                        'file' => basename($file->getPathname()),
                        'baris' => $line_num + 1,
                        'teks' => htmlspecialchars(trim($line_content))
                    ];
                }
            }
        }
    }
}

// --- C. LOGIKA LACAK GLOBAL ---
$hasil_global = [];
if (isset($_POST['lacak_global'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword_global']);
    $sql_lacak = "SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE 
                  FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE COLUMN_NAME LIKE '%$keyword%' 
                  AND TABLE_SCHEMA = DATABASE()";
    $q_lacak = mysqli_query($conn, $sql_lacak);
    while ($row = mysqli_fetch_assoc($q_lacak)) {
        $hasil_global[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZASHA | DOCTOR SYSTEM</title>
    <style>
        body {
            background-color: #030303;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .terminal-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .ascii-art {
            color: #00ff00;
            white-space: pre;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .system-msg {
            border-top: 1px dashed #005500;
            border-bottom: 1px dashed #005500;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .cmd-line {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }

        .prompt { color: #00ff00; font-weight: bold; margin-right: 10px; }

        select, input[type="text"] {
            background: #000;
            border: 1px solid #005500;
            color: #00ff00;
            padding: 5px;
            font-family: 'Courier New';
            outline: none;
        }

        select:focus, input[type="text"]:focus {
            border-color: #00ff00;
            box-shadow: 0 0 5px #00ff00;
        }

        button {
            background: #005500;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 5px 15px;
            cursor: pointer;
            font-family: 'Courier New';
            margin-left: 10px;
        }

        button:hover {
            background: #00ff00;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }

        th {
            text-align: left;
            border-bottom: 2px solid #005500;
            color: #00ffff;
            padding: 8px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #002200;
            vertical-align: top;
        }

        .text-cyan { color: #00ffff; }
        .text-yellow { color: #ffff00; }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
            color: #00ffff;
            background: #080808;
            padding: 5px;
        }

        a { color: #00ff00; text-decoration: none; border-bottom: 1px solid #00ff00; font-size: 12px; }
        a:hover { background: #00ff00; color: #000; }
        
        .section-title {
            color: #00ffff;
            margin-top: 30px;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="terminal-container">
    <div class="ascii-art">
  ______         _             ___        _ _            
 |___  /        | |           / _ \      | (_)           
    / / __ _ ___| |__   __ _ | | | |_ __ | |_ _ __   ___ 
   / / / _` / __| '_ \ / _` || | | | '_ \| | | '_ \ / _ \
  / /_| (_| \__ \ | | | (_| || |_| | | | | | | | | |  __/
 /_____\__,_|___/_| |_|\__,_| \___/|_| |_|_|_|_| |_|\___|
    </div>

    <div class="system-msg">
        [SYSTEM] DOCTOR MODULE ACTIVE v1.0<br>
        [TARGET] DATABASE: u607709216_zasha_services<br>
        [STATUS] READY. <a href="../">EXIT_TO_TERMINAL</a>
    </div>

    <!-- FITUR 1: SCAN FILE -->
    <div class="section-title">PHASE_01: SOURCE_CODE_SCANNER</div>
    <form method="POST">
        <div class="cmd-line">
            <span class="prompt">root@zasha/doctor:~$ select_target --table</span>
            <select name="tabel" id="pilihTabel" onchange="loadColumns(this.value)">
                <option value="">-- TABLE --</option>
                <?php
                $tables = mysqli_query($conn, "SHOW TABLES");
                while ($t = mysqli_fetch_array($tables)) {
                    echo "<option value='$t[0]'>$t[0]</option>";
                }
                ?>
            </select>
            
            <span class="prompt" style="margin-left:15px;">--column</span>
            <select name="kolom" id="pilihKolom" required>
                <option value="">-- COLUMN --</option>
            </select>
            <button type="submit" name="cari">EXECUTE_SCAN</button>
        </div>
    </form>

    <?php if (!empty($hasil_pencarian)): ?>
    <table>
        <thead>
            <tr>
                <th width="20%">FILE_PATH</th>
                <th width="10%">LINE</th>
                <th>CODE_SNIPPET</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hasil_pencarian as $h): ?>
            <tr>
                <td class="text-yellow"><?= $h['file'] ?></td>
                <td class="text-cyan">#<?= $h['baris'] ?></td>
                <td><pre><?= $h['teks'] ?></pre></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- FITUR 2: GLOBAL SCAN -->
    <div class="section-title">PHASE_02: GLOBAL_DATABASE_LOCATOR</div>
    <form method="POST">
        <div class="cmd-line">
            <span class="prompt">root@zasha/doctor:~$ locate_column --name</span>
            <input type="text" name="keyword_global" placeholder="Enter column name..." required>
            <button type="submit" name="lacak_global">SCAN_ALL_TABLES</button>
        </div>
    </form>

    <?php if (!empty($hasil_global)): ?>
    <table>
        <thead>
            <tr>
                <th>TABLE_NAME</th>
                <th>COLUMN_NAME</th>
                <th>DATA_TYPE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hasil_global as $g): ?>
            <tr>
                <td class="text-yellow"><?= $g['TABLE_NAME'] ?></td>
                <td class="text-cyan">`<?= $g['COLUMN_NAME'] ?>`</td>
                <td><?= $g['COLUMN_TYPE'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>

<script>
function loadColumns(tableName) {
    if (!tableName) return;
    fetch('?get_columns=' + tableName)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('pilihKolom');
            select.innerHTML = '<option value="">-- COLUMN --</option>';
            data.forEach(col => {
                const opt = document.createElement('option');
                opt.value = col;
                opt.textContent = col;
                select.appendChild(opt);
            });
        });
}
</script>
</body>
</html>