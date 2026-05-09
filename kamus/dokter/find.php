<?php
/**
 * ZASHA DOCTOR - GLOBAL TEXT SCANNER & REPLACER
 * SECURITY LEVEL: CRITICAL
 */
set_time_limit(0); 

$search_keyword = $_POST['keyword'] ?? '';
$replace_keyword = $_POST['replace_word'] ?? '';
$target_dir = $_POST['dir'] ?? '../../'; // Default mundur ke root zasha.online
$action = $_POST['action'] ?? '';
$results = [];
$scan_done = false;
$total_replaced = 0;

$allowed_extensions = ['php', 'html', 'css', 'js', 'txt', 'sql', 'env', 'json'];
$excluded_folders = ['.git', 'vendor', 'node_modules', 'images', 'img', 'assets', 'storage'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($search_keyword) && !empty($target_dir)) {
    if (is_dir($target_dir)) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target_dir, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($rii as $file) {
            if ($file->isDir()) continue;
            $path = $file->getPathname();

            // Filter folder terlarang
            $skip = false;
            foreach ($excluded_folders as $folder) {
                if (stripos($path, DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR) !== false) {
                    $skip = true; break;
                }
            }
            if ($skip) continue;

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowed_extensions)) continue;

            $content = file_get_contents($path);
            
            if (strpos($content, $search_keyword) !== false) {
                // JIKA AKSI ADALAH REPLACE
                if ($action == 'replace' && isset($_POST['confirm_replace']) && !empty($replace_keyword)) {
                    $new_content = str_replace($search_keyword, $replace_keyword, $content);
                    
                    if(file_put_contents($path, $new_content) !== false) {
                        $total_replaced++;
                        $results[] = [
                            'file' => str_replace('\\', '/', $path),
                            'status' => 'REPLACED',
                            'snippet' => "Changed to: <span style='color:#ffff00'>" . htmlspecialchars($replace_keyword) . "</span>"
                        ];
                    } else {
                        $results[] = [
                            'file' => str_replace('\\', '/', $path),
                            'status' => 'FAILED',
                            'snippet' => "Permission Denied."
                        ];
                    }
                } 
                // JIKA AKSI HANYA PENCARIAN
                else {
                    $lines = file($path);
                    foreach ($lines as $line_num => $line) {
                        if (strpos($line, $search_keyword) !== false) {
                            $clean_line = htmlspecialchars(trim($line));
                            $highlighted = str_replace(htmlspecialchars($search_keyword), "<span style='background:#ff0000; color:#fff; font-weight:bold;'>".htmlspecialchars($search_keyword)."</span>", $clean_line);
                            $results[] = [
                                'file' => str_replace('\\', '/', $path),
                                'status' => 'FOUND',
                                'snippet' => "Line " . ($line_num + 1) . ": " . $highlighted
                            ];
                        }
                    }
                }
            }
        }
        $scan_done = true;
    } else {
        $error = "DIREKTORI TIDAK DITEMUKAN.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ZASHA | GLOBAL FIND & REPLACE</title>
    <style>
        body {
            background-color: #030303;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
            margin: 0;
            font-size: 14px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { border-bottom: 1px dashed #005500; padding-bottom: 20px; margin-bottom: 20px; }
        .danger-zone { border: 1px solid #ff0000; padding: 15px; background: rgba(255,0,0,0.1); margin-bottom: 20px; }
        
        input[type="text"] {
            background: #000;
            border: 1px solid #005500;
            color: #00ff00;
            padding: 8px;
            font-family: 'Courier New';
            width: 100%;
            box-sizing: border-box;
            outline: none;
        }
        input[type="text"]:focus { border-color: #00ff00; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        
        .btn {
            padding: 10px 20px;
            font-family: 'Courier New';
            cursor: pointer;
            font-weight: bold;
            border: 1px solid;
            width: 100%;
        }
        .btn-search { background: #002200; color: #00ff00; border-color: #00ff00; }
        .btn-replace { background: #330000; color: #ff0000; border-color: #ff0000; }
        .btn:hover { filter: brightness(1.5); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #002200; padding: 10px; border: 1px solid #005500; color: #00ffff; }
        td { padding: 8px; border: 1px solid #002200; font-size: 12px; }
        
        .status-found { color: #00ffff; font-weight: bold; }
        .status-replaced { color: #ffff00; font-weight: bold; }
        .status-failed { color: #ff0000; font-weight: bold; }
        
        .path { color: #888; font-size: 11px; }
        pre { margin: 0; background: #080808; padding: 5px; color: #00ffff; overflow-x: auto; }
        
        .prompt { color: #00ff00; margin-bottom: 5px; display: block; }
        .warning-text { color: #ff0000; font-weight: bold; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="margin:0;">[ SYSTEM_UTILITY ] FIND_AND_REPLACE_GLOBAL</h2>
        <a href="index.php" style="color:#00ffff; font-size:12px; text-decoration:none;">&lt; BACK_TO_DOCTOR</a>
    </div>

    <?php if (isset($error)): ?>
        <div style="color:#ff0000; margin-bottom:20px;">[ERROR] <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" id="mainForm">
        <div class="grid">
            <div>
                <span class="prompt">root@zasha/doctor:~$ search_string --input</span>
                <input type="text" name="keyword" placeholder="Ketik kata yang dicari..." value="<?= htmlspecialchars($search_keyword) ?>" required>
            </div>
            <div>
                <span class="prompt">root@zasha/doctor:~$ target_directory --path</span>
                <input type="text" name="dir" placeholder="../../" value="<?= htmlspecialchars($target_dir) ?>" required>
            </div>
        </div>

        <div class="danger-zone">
            <span class="warning-text">[!] WARNING: DANGER ZONE [!]</span><br>
            <span class="prompt" style="margin-top:10px;">root@zasha/doctor:~$ replace_with --new-value</span>
            <input type="text" name="replace_word" placeholder="Ketik kata pengganti..." value="<?= htmlspecialchars($replace_keyword) ?>">
            
            <div style="margin-top:10px;">
                <input type="checkbox" name="confirm_replace" id="confirmCheck">
                <label for="confirmCheck" style="color:#ff0000; font-size:12px; cursor:pointer;">SAYA BERTANGGUNG JAWAB ATAS SEGALA KERUSAKAN SISTEM.</label>
            </div>
        </div>

        <div class="grid">
            <button type="submit" name="action" value="search" class="btn btn-search" id="btnS">START_SEARCH_ONLY</button>
            <button type="submit" name="action" value="replace" class="btn btn-replace" id="btnR">START_GLOBAL_REPLACE</button>
        </div>
    </form>

    <?php if ($scan_done): ?>
        <div style="margin-top:20px; border-top:1px solid #005500; padding-top:10px;">
            <?php if ($action == 'replace'): ?>
                <span style="color:#ffff00;">[SUCCESS] Total File Dimodifikasi: <?= $total_replaced ?></span>
            <?php else: ?>
                <span style="color:#00ffff;">[INFO] Total Baris Ditemukan: <?= count($results) ?></span>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th width="30%">FILE_LOCATION</th>
                        <th width="10%">STATUS</th>
                        <th>OUTPUT_DETAILS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr><td colspan="3" style="text-align:center; padding:50px;">NO_MATCHING_STRINGS_FOUND</td></tr>
                    <?php else: ?>
                        <?php foreach ($results as $res): ?>
                        <tr>
                            <td class="path"><?= $res['file'] ?></td>
                            <td class="status-<?= strtolower($res['status']) ?>"><?= $res['status'] ?></td>
                            <td><pre><?= $res['snippet'] ?></pre></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    const form = document.getElementById('mainForm');
    form.addEventListener('submit', function(e) {
        const action = e.submitter.value;
        const replaceInput = document.querySelector('input[name="replace_word"]').value;
        const isChecked = document.getElementById('confirmCheck').checked;

        if (action === 'replace') {
            if (replaceInput.trim() === '') {
                e.preventDefault();
                alert('FATAL: Kata pengganti kosong!');
                return;
            }
            if (!isChecked) {
                e.preventDefault();
                alert('ERROR: Konfirmasi tanggung jawab belum dicentang!');
                return;
            }
            if(!confirm('SISTEM AKAN MENGUBAH FILE ASLI. LANJUTKAN?')) {
                e.preventDefault();
                return;
            }
        }
        
        document.getElementById('btnS').disabled = true;
        document.getElementById('btnR').disabled = true;
        e.submitter.innerText = 'PROCESSING_REQUEST...';
    });
</script>
</body>
</html>