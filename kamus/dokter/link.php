<?php
/**
 * ZASHA DOCTOR - LARAVEL LOG READER
 * Modul untuk membaca file laravel.log langsung dari folder storage.
 */
set_time_limit(0); 

$log_file_path = '../../storage/logs/laravel.log'; // Path standar Laravel (mundur 2 folder)
$action = $_POST['action'] ?? '';
$log_data = [];
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!file_exists($log_file_path)) {
        $error_msg = "CRITICAL: File log tidak ditemukan di " . $log_file_path;
    } else {
        if ($action === 'read') {
            // Baca 500 baris terakhir agar tidak berat
            $file = file($log_file_path);
            if ($file) {
                $file = array_slice($file, -500); // Ambil 500 baris dari bawah
                $file = array_reverse($file); // Balik agar yang terbaru di atas
                
                $current_log = null;
                foreach ($file as $line) {
                    // Deteksi awal pesan log (misal: [2023-10-27 10:00:00] local.ERROR: ...)
                    if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
                        if ($current_log) {
                            $log_data[] = $current_log;
                        }
                        $type = 'INFO';
                        if (strpos($line, '.ERROR:') !== false || strpos($line, 'stack trace:') !== false) $type = 'ERROR';
                        elseif (strpos($line, '.WARNING:') !== false) $type = 'WARNING';
                        
                        $current_log = [
                            'time' => substr($line, 1, 19),
                            'type' => $type,
                            'message' => trim(substr($line, 22))
                        ];
                    } else {
                        // Gabungkan baris lanjutan (stack trace)
                        if ($current_log) {
                            $current_log['message'] .= "\n" . trim($line);
                        }
                    }
                }
                if ($current_log) $log_data[] = $current_log;
            }
        } elseif ($action === 'clear') {
            // Kosongkan file log
            if (file_put_contents($log_file_path, '') !== false) {
                $error_msg = "[SUCCESS] File log berhasil dikosongkan.";
            } else {
                $error_msg = "FAILED: Tidak ada izin (Permission Denied) untuk menghapus log.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZASHA | ERROR LOG VIEWER</title>
    <style>
        body {
            background-color: #030303;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
            margin: 0;
            font-size: 13px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { border-bottom: 1px dashed #005500; padding-bottom: 15px; margin-bottom: 25px; }
        
        .cmd-line { display: flex; align-items: center; margin-bottom: 20px; gap: 10px; }
        .prompt { color: #00ff00; font-weight: bold; }
        
        button {
            background: #003300;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 8px 20px;
            cursor: pointer;
            font-family: 'Courier New';
            font-weight: bold;
        }
        button:hover { background: #00ff00; color: #000; }
        .btn-danger { background: #330000; color: #ff0000; border-color: #ff0000; }
        .btn-danger:hover { background: #ff0000; color: #000; }

        .log-container {
            border: 1px solid #003300;
            background: #080808;
            padding: 15px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .log-entry {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #002200;
        }
        .log-header {
            display: flex;
            gap: 15px;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .log-time { color: #ffff00; }
        .log-type-error { color: #ff0000; font-weight: bold; background: #330000; padding: 0 5px; }
        .log-type-warning { color: #ffaa00; font-weight: bold; background: #332200; padding: 0 5px; }
        .log-type-info { color: #00ffff; font-weight: bold; background: #003333; padding: 0 5px; }
        
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
            color: #ccc;
            font-size: 12px;
            padding-left: 10px;
            border-left: 2px solid #005500;
        }
        
        a.back-link { color: #00ff00; text-decoration: none; font-size: 12px; border-bottom: 1px solid #00ff00; }
        a.back-link:hover { background: #00ff00; color: #000; }
        .sys-msg { color: #00ffff; margin-bottom: 15px; }
        .err-msg { color: #ff0000; border: 1px solid #ff0000; padding: 10px; background: rgba(255,0,0,0.1); margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0;">[ MODULE ] LARAVEL_ERROR_LOGS_v1.0</h2>
        <a href="index.php" class="back-link">EXIT_TO_DOCTOR</a>
    </div>

    <?php if ($error_msg): ?>
        <div class="<?= strpos($error_msg, '[SUCCESS]') !== false ? 'sys-msg' : 'err-msg' ?>">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div class="sys-msg">
        [TARGET_FILE] storage/logs/laravel.log<br>
        [INFO] Menampilkan 500 baris terbaru. Format: LIFO (Last In First Out).
    </div>

    <form method="POST" class="cmd-line">
        <span class="prompt">root@zasha/doctor:~$ execute --action</span>
        <button type="submit" name="action" value="read">READ_LOGS</button>
        <button type="submit" name="action" value="clear" class="btn-danger" onclick="return confirm('Yakin ingin menghapus semua isi file log?')">CLEAR_LOGS</button>
    </form>

    <?php if ($action === 'read' && !empty($log_data)): ?>
        <div class="log-container">
            <?php foreach ($log_data as $log): ?>
                <div class="log-entry">
                    <div class="log-header">
                        <span class="log-time">[<?= htmlspecialchars($log['time']) ?>]</span>
                        <span class="log-type-<?= strtolower($log['type']) ?>"><?= $log['type'] ?></span>
                    </div>
                    <pre><?= htmlspecialchars($log['message']) ?></pre>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($action === 'read'): ?>
        <div class="log-container" style="text-align: center; color: #888; padding: 40px;">
            FILE LOG KOSONG (TIDAK ADA ERROR).
        </div>
    <?php endif; ?>
</div>

</body>
</html>