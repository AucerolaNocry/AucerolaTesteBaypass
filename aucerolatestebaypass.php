<?php

echo "====== BYPASS DEFINITIVO ANTI-SCANNER - INCLUINDO JSON, SHADERS, PASTAS ======\n";

$data       = "20250522";
$hora_alvo  = "1600"; // antes de 17:08:45
$tempo      = ".00";
$timestamp  = $data . $hora_alvo . $tempo;

$SRC  = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$DEST = "/sdcard/Android/data/com.dts.freefireth";

// Ambiente
shell_exec("adb start-server");
shell_exec("adb logcat -c");
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");
echo "[*] Ambiente sincronizado.\n";

// Cópia
shell_exec("adb shell 'cp -rn " . escapeshellarg($SRC) . "/* " . escapeshellarg($DEST) . "'");

// Camuflagem pastas críticas
$pastas = [
    "$DEST/files/contentcache/Optional/android/gameassetbundles",
    "$DEST/files/contentcache/Optional/android/optionalavatarres",
    "$DEST/files/contentcache/Optional/android/optionalavatarres/gameassetbundles",
    "$DEST/files/contentcache/Optional/android",
    "$DEST/files/contentcache/Optional",
    "$DEST/files/contentcache",
    "$DEST/files",
    "$DEST",
    "/sdcard/Android/data",
    "/sdcard/Android",
    "/sdcard/Android/obb/com.dts.freefireth"
];

foreach ($pastas as $pasta) {
    $p = escapeshellarg($pasta);
    shell_exec("adb shell 'touch -t $timestamp $p'");
    shell_exec("adb shell 'mv $p $p.tmp && mv $p.tmp $p'");
    echo "[✓] Pasta camuflada: $pasta\n";
}

// Criar bin e json realistas
$replayName = "20250522_1600_replay";
$bin = "$DEST/files/MReplays/{$replayName}.bin";
$json = "$DEST/files/MReplays/{$replayName}.json";

shell_exec("adb shell 'dd if=/dev/urandom of=$bin bs=1 count=128'");
shell_exec("adb shell 'echo {\"score\":99,\"valid\":true} > $json'");
shell_exec("adb shell 'touch -t $timestamp $bin'");
shell_exec("adb shell 'touch -t $timestamp $json'");
echo "[✓] Replay .bin e .json criados com metadados válidos.\n";

// Camuflar shaders
$shader = "$DEST/files/contentcache/Optional/android/gameassetbundles/shaders_modificado";
shell_exec("adb shell 'echo UnityFSshaderData > $shader'");
shell_exec("adb shell 'touch -a -t $timestamp $shader'");
shell_exec("adb shell 'touch -m -t $timestamp $shader'");
shell_exec("adb shell 'mv $shader {$shader}.tmp && mv {$shader}.tmp $shader'");
echo "[✓] Shader simulado com A/M/C falsos.\n";

// Final
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");
echo "[✓] Script concluído. Scanner não deve detectar nada.\n";

?>
