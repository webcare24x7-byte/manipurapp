<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$lockFile = $root . '/storage/install.lock';

if (is_file($lockFile)) {
    http_response_code(403);
    echo '<!doctype html><html><body style="font-family:system-ui;padding:40px"><h1>ManipurApp is already installed</h1><p>Remove <code>storage/install.lock</code> only if you intentionally want to reinstall.</p></body></html>';
    exit;
}

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

function detectBasePath(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/install/index.php');
    $base = preg_replace('#/install/index\.php$#', '', $script) ?? '';
    return rtrim($base, '/') . '/public';
}

$errors = [];
$success = false;
$defaults = [
    'host' => '127.0.0.1',
    'port' => '8889',
    'database' => 'manipurapp',
    'username' => 'root',
    'password' => 'root',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($defaults as $key => $default) {
        $defaults[$key] = trim((string)($_POST[$key] ?? $default));
    }

    $seedDemo = isset($_POST['seed_demo']) && $_POST['seed_demo'] === '1';
    $dbName = $defaults['database'];

    if (!preg_match('/^[A-Za-z0-9_\-]+$/', $dbName)) {
        $errors[] = 'Database name contains unsupported characters.';
    }

    if (!$errors) {
        try {
            $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $defaults['host'], (int)$defaults['port']);
            $pdo = new PDO($dsn, $defaults['username'], $defaults['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $pdo->exec('USE `' . str_replace('`', '``', $dbName) . '`');

            $files = [
                $root . '/installer/schema.sql',
            ];
            if ($seedDemo) {
                $files[] = $root . '/installer/demo_seed.sql';
            }
            $files[] = $root . '/installer/constraints.sql';

            foreach ($files as $file) {
                if (!is_file($file)) {
                    throw new RuntimeException('Installer file missing: ' . basename($file));
                }
                $sql = file_get_contents($file);
                if ($sql === false) {
                    throw new RuntimeException('Unable to read installer file: ' . basename($file));
                }
                // Execute statements individually so the installer does not depend on
                // multi-statement PDO settings. SQL strings are respected while splitting.
                $buffer = '';
                $inString = false;
                $escaped = false;
                $length = strlen($sql);
                for ($i = 0; $i < $length; $i++) {
                    $char = $sql[$i];
                    if ($inString) {
                        $buffer .= $char;
                        if ($escaped) {
                            $escaped = false;
                        } elseif ($char === "\\") {
                            $escaped = true;
                        } elseif ($char === "'") {
                            if ($i + 1 < $length && $sql[$i + 1] === "'") {
                                $buffer .= $sql[++$i];
                            } else {
                                $inString = false;
                            }
                        }
                        continue;
                    }
                    if ($char === "'") {
                        $inString = true;
                        $buffer .= $char;
                        continue;
                    }
                    if ($char === ';') {
                        $statement = trim($buffer);
                        $statement = preg_replace('/(?m)^\s*--.*$/', '', $statement);
                        $statement = preg_replace('/\/\*.*?\*\//s', '', $statement);
                        $statement = trim($statement);
                        if ($statement !== '' && !preg_match('/^SET\s+SQL_MODE/i', $statement)) {
                            $pdo->exec($statement);
                        }
                        $buffer = '';
                        continue;
                    }
                    $buffer .= $char;
                }
                $statement = trim($buffer);
                $statement = preg_replace('/(?m)^\s*--.*$/', '', $statement);
                $statement = preg_replace('/\/\*.*?\*\//s', '', $statement);
                $statement = trim($statement);
                if ($statement !== '' && !preg_match('/^SET\s+SQL_MODE/i', $statement)) {
                    $pdo->exec($statement);
                }
            }

            // Always use a fresh demo/admin credential for public installs.
            // This does not alter the schema or normal application authentication behavior.
            if ($seedDemo) {
                $hash = password_hash('Demo@12345', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, password=?, status=? WHERE id=1 AND tenant_id=1');
                $stmt->execute(['ManipurApp Demo Owner', 'demo@manipurapp.local', $hash, 'Active']);
            }

            $configPath = $root . '/config/database.php';
            $config = "<?php\n\ndeclare(strict_types=1);\n\nreturn [\n    'host' => " . var_export($defaults['host'], true) . ",\n    'port' => " . (int)$defaults['port'] . ",\n    'database' => " . var_export($dbName, true) . ",\n    'username' => " . var_export($defaults['username'], true) . ",\n    'password' => " . var_export($defaults['password'], true) . ",\n    'charset' => 'utf8mb4',\n];\n";
            if (file_put_contents($configPath, $config, LOCK_EX) === false) {
                throw new RuntimeException('Could not write config/database.php. Check file permissions.');
            }

            $appConfigPath = $root . '/config/app.php';
            if (is_file($appConfigPath)) {
                $appConfig = file_get_contents($appConfigPath);
                $basePath = detectBasePath();
                $appConfig = preg_replace("/'base_path'\\s*=>\\s*'[^']*'/", "'base_path' => " . var_export($basePath, true), $appConfig, 1);
                $appConfig = preg_replace("/'env'\\s*=>\\s*'development'/", "'env' => 'development'", $appConfig, 1);
                if ($appConfig !== null) {
                    file_put_contents($appConfigPath, $appConfig, LOCK_EX);
                }
            }

            $storage = $root . '/storage';
            if (!is_dir($storage) && !mkdir($storage, 0775, true) && !is_dir($storage)) {
                throw new RuntimeException('Could not create storage directory.');
            }
            file_put_contents($lockFile, date(DATE_ATOM) . "\n", LOCK_EX);
            $success = true;
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Install ManipurApp</title>
<style>
:root{--green:#087f5b;--ink:#10251d;--muted:#6d7c76;--line:#dce7e2;--bg:#f4f8f6}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--ink)}
.wrap{min-height:100vh;display:grid;place-items:center;padding:28px}.card{width:min(720px,100%);background:#fff;border:1px solid var(--line);border-radius:24px;box-shadow:0 20px 60px rgba(15,45,34,.08);overflow:hidden}.hero{padding:34px 36px;background:linear-gradient(135deg,#063d2d,#0b7d5b);color:#fff}.brand{font-size:14px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;opacity:.9}.hero h1{font-size:32px;margin:10px 0 8px}.hero p{margin:0;opacity:.86;line-height:1.6}.body{padding:30px 36px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.field{margin-bottom:16px}.field.full{grid-column:1/-1}label{display:block;font-size:13px;font-weight:700;margin-bottom:7px}input{width:100%;padding:13px 14px;border:1px solid var(--line);border-radius:12px;font-size:15px}input:focus{outline:2px solid rgba(8,127,91,.15);border-color:var(--green)}.check{display:flex;gap:10px;align-items:flex-start;background:#f2faf6;padding:15px;border-radius:14px;margin:4px 0 20px}.check input{width:auto;margin-top:3px}.check label{margin:0;line-height:1.5}.btn{width:100%;border:0;border-radius:13px;padding:14px;background:var(--green);color:#fff;font-weight:800;font-size:15px;cursor:pointer}.note{font-size:12px;color:var(--muted);line-height:1.6;margin-top:16px}.error{background:#fff1f1;color:#a32626;border:1px solid #f1caca;padding:13px;border-radius:12px;margin-bottom:18px}.success{background:#effaf4;border:1px solid #bde5cd;padding:18px;border-radius:14px}.success code{background:#e2f4ea;padding:3px 6px;border-radius:6px}.success a{display:inline-block;margin-top:14px;color:var(--green);font-weight:800;text-decoration:none}@media(max-width:620px){.grid{grid-template-columns:1fr}.hero,.body{padding:26px 22px}.hero h1{font-size:27px}}
</style>
</head>
<body><div class="wrap"><section class="card">
<div class="hero"><div class="brand">ManipurApp v1.0</div><h1>Install ManipurApp</h1><p>Create the database, install the current schema and optionally load the safe demo workspace.</p></div>
<div class="body">
<?php if ($success): ?>
<div class="success"><strong>Installation complete.</strong><p>ManipurApp is ready. The demo workspace and sanitized seed data have been installed.</p><p><strong>Demo login:</strong><br>Email: <code>demo@manipurapp.local</code><br>Password: <code>Demo@12345</code></p><p class="note">For security, remove or protect the <code>/install</code> directory after installation.</p><a href="../public/">Open ManipurApp →</a></div>
<?php else: ?>
<?php foreach($errors as $error): ?><div class="error"><?=h($error)?></div><?php endforeach; ?>
<form method="post">
<div class="grid">
<div class="field"><label>Database Host</label><input name="host" value="<?=h($defaults['host'])?>" required></div>
<div class="field"><label>Port</label><input name="port" value="<?=h($defaults['port'])?>" required></div>
<div class="field"><label>Database Name</label><input name="database" value="<?=h($defaults['database'])?>" required></div>
<div class="field"><label>Database User</label><input name="username" value="<?=h($defaults['username'])?>" required></div>
<div class="field full"><label>Database Password</label><input type="password" name="password" value="<?=h($defaults['password'])?>"></div>
</div>
<div class="check"><input type="checkbox" id="seed_demo" name="seed_demo" value="1" checked><label for="seed_demo"><strong>Install demo workspace</strong><br>Recommended. Loads sanitized demo data derived from the current tenant #1 test dataset so GitHub users can immediately explore ManipurApp.</label></div>
<button class="btn" type="submit">Install ManipurApp</button>
<p class="note">The installer writes your database credentials to <code>config/database.php</code>. Do not commit that file with real production credentials.</p>
</form>
<?php endif; ?>
</div></section></div></body></html>
