<?php
// admin.php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();

// --- CONFIGURATION ---

// Set your database credentials here
$dbHost = 'localhost';
$dbName = 'u952857351_sizerdb';
$dbUser = 'u952857351_sizerdbadmin';
$dbPass = 'dKwIvuTO6';

// Dell internal hardware guide URL
$dellExcelUrl = 'https://www.delltechnologies.com/asset/en-us/products/converged-infrastructure/technical-support/dell-ax-solutions-for-microsoft-hardware-configurations-guide.xlsx';

// Directory for storing uploaded/downloaded Excel files
$storageDir = __DIR__ . '/uploads/';
if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);

// --- AUTHENTICATION (simple version) ---

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    if (isset($_POST['admin_login'])) {
        // For demo: hard-coded password check (use SSO or DB in production!)
        if ($_POST['password'] === 'MyDellSecurePass123') {
            $_SESSION['is_admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $loginError = "Invalid password.";
        }
    }
    // Login Form
    ?>
    <form method="post">
      <h2>Admin Login</h2>
      <?php if (!empty($loginError)) echo "<div style='color:red;'>$loginError</div>"; ?>
      <input type="password" name="password" required placeholder="Admin password"><br><br>
      <button type="submit" name="admin_login">Login</button>
    </form>
    <?php exit;
}

// --- DATABASE CONNECTION ---
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbStatus = "<span style='color: green;'>Connected to the database.</span>";
} catch (PDOException $e) {
    die("<span style='color: red;'>DB connection error: " . htmlspecialchars($e->getMessage()) . "</span>");
}

// --- FILE DOWNLOAD: Dell Internal Guide ---
$downloadStatus = '';
if (isset($_POST['download_file'])) {
    $localFile = $storageDir . 'hardware-guide-latest.xlsx';
    // Use cURL for secure file download
    $ch = curl_init($dellExcelUrl);
    // If basic auth is needed, set:
    // curl_setopt($ch, CURLOPT_USERPWD, "username:password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($data && !$err) {
        file_put_contents($localFile, $data);
        $downloadStatus = "<span style='color: green;'>File downloaded to $localFile</span>";
    } else {
        $downloadStatus = "<span style='color: red;'>Failed to download: $err</span>";
    }
}

// --- FILE UPLOAD (for externally-downloaded Excel) ---
$uploadStatus = '';
if (isset($_POST['upload_file']) && isset($_FILES['excel_file'])) {
    $f = $_FILES['excel_file'];
    if ($f['error'] == UPLOAD_ERR_OK && preg_match('/\.xlsx$/i', $f['name'])) {
        $dest = $storageDir . basename($f['name']);
        move_uploaded_file($f['tmp_name'], $dest);
        $uploadStatus = "<span style='color: green;'>Uploaded file: $dest</span>";
        $fileForImport = $dest;
    } else {
        $uploadStatus = "<span style='color: red;'>Upload failed or wrong file type (must be .xlsx)</span>";
    }
}

// Use last downloaded or uploaded file if available
$fileForImport = $storageDir . 'hardware-guide-latest.xlsx';
if (!file_exists($fileForImport)) {
    $files = glob($storageDir . '*.xlsx');
    if ($files) $fileForImport = $files[0];
}

// --- PARSE AND IMPORT EXCEL to MySQL ---
$importStatus = '';
if (isset($_POST['import_file'])) {
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    try {
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($fileForImport);
        $sheet = $spreadsheet->getActiveSheet();

        // Column mapping example—adapt to match actual Excel structure:
        // Assume headers at row 1
        $rows = $sheet->toArray(null, true, true, true);
        $importedModels = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            if ($i == 1) continue; // skip header

            // Map columns to expected schema fields
            $model_name         = trim($row['A'] ?? '');
            $rack_units         = trim($row['B'] ?? '1U');
            $cpu_slots          = intval($row['C'] ?? 2);
            $dimm_slots         = intval($row['D'] ?? 16);
            $chassis_type       = trim($row['E'] ?? 'Hybrid SSD+HDD');
            $cache_disk_slots   = intval($row['F'] ?? 2);
            $capacity_disk_slots= intval($row['G'] ?? 10);
            $pcie_slots         = intval($row['H'] ?? 2);
            $ocp_slots          = intval($row['I'] ?? 1);

            // --- MODELS table: Upsert by model_name ---
            $stmt = $pdo->prepare(
                "INSERT INTO models (model_name, rack_units, cpu_slots, dimm_slots, chassis_type, cache_disk_slots, capacity_disk_slots, pcie_slots, ocp_slots)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE rack_units=?, cpu_slots=?, dimm_slots=?, chassis_type=?, cache_disk_slots=?, capacity_disk_slots=?, pcie_slots=?, ocp_slots=?"
            );
            $stmt->execute([
                $model_name, $rack_units, $cpu_slots, $dimm_slots, $chassis_type, $cache_disk_slots, $capacity_disk_slots, $pcie_slots, $ocp_slots,
                $rack_units, $cpu_slots, $dimm_slots, $chassis_type, $cache_disk_slots, $capacity_disk_slots, $pcie_slots, $ocp_slots
            ]);

            // --- Get model_id for reference ---
            $model_id = $pdo->query("SELECT model_id FROM models WHERE model_name=?", [$model_name])->fetchColumn();

            // --- CPU Table (sometimes multiple CPUs per model) ---
            if (!empty($row['J'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO cpu_specs (model_id, manufacturer, family, cpu_model, core_count, speed_ghz)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $model_id,
                    trim($row['J'] ?? 'Intel'),         // Manufacturer
                    trim($row['K'] ?? 'Xeon'),          // Family
                    trim($row['L'] ?? ''),              // CPU model
                    intval($row['M'] ?? 0),             // Core count
                    floatval($row['N'] ?? 2.1)          // Speed (GHz)
                ]);
            }

            // --- RAM Table ---
            if (!empty($row['O'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO ram_options (model_id, dimm_size_gib, dimm_speed_mhz, sloted_count)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $model_id,
                    intval($row['O'] ?? 32),            // DIMM size
                    intval($row['P'] ?? 3200),          // Speed MHz
                    intval($row['Q'] ?? 2)              // Slot count
                ]);
            }

            // --- Disk Table ---
            if (!empty($row['R'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO disk_configs (model_id, disk_type, purpose, max_slots)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $model_id,
                    trim($row['R'] ?? 'SSD'),
                    trim($row['S'] ?? 'Capacity'),
                    intval($row['T'] ?? 10)
                ]);
            }

            // --- NIC Table ---
            if (!empty($row['U'])) {
                $stmt = $pdo->prepare(
                    "INSERT INTO nic_options (model_id, nic_type, port_count)
                     VALUES (?, ?, ?)"
                );
                $stmt->execute([
                    $model_id,
                    trim($row['U'] ?? '10GbE'),
                    intval($row['V'] ?? 2)
                ]);
            }

            $importedModels++;
        }
        $importStatus = "<span style='color: green;'>Imported $importedModels models and components into the database.</span>";
    } catch (Exception $e) {
        $importStatus = "<span style='color: red;'>Error importing: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
}

// --- ADMIN PAGE UI ---
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dell AX System Admin Import</title>
    <style>
      body { font-family: Arial, sans-serif; margin: 2rem; }
      .block { border: 1px solid #999; padding:1rem; margin-bottom:2rem; border-radius:5px; background:#fafaff;}
      .block h2 { margin-top:0; }
      button { font-size:1rem; padding:0.3rem 1rem; cursor:pointer; }
    </style>
</head>
<body>
    <h1>Dell AX System Hardware Import (Admin)</h1>
    <div><?php echo $dbStatus; ?></div>

    <div class="block">
        <h2>Step 1: Download the Latest Dell AX Hardware Spec Excel</h2>
        <form method="post">
            <button name="download_file">Download from Dell Internal Portal</button>
        </form>
        <?php if (!empty($downloadStatus)) echo $downloadStatus; ?>
    </div>

    <div class="block">
        <h2>Step 2: Upload Excel File</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file" accept=".xlsx">
            <button name="upload_file">Upload Excel</button>
        </form>
        <?php if (!empty($uploadStatus)) echo $uploadStatus; ?>
        <?php if (!empty($fileForImport) && file_exists($fileForImport)) echo "<div>Current file for import: <b>$fileForImport</b></div>"; ?>
    </div>

    <div class="block">
        <h2>Step 3: Parse and Import to MySQL</h2>
        <form method="post">
            <button name="import_file" onclick="return confirm('Proceed with import?');">Import to DB</button>
        </form>
        <?php if (!empty($importStatus)) echo $importStatus; ?>
    </div>
</body>
</html>