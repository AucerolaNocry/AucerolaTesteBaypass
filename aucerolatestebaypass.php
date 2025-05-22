<?php
echo "====== KELLERSS BYPASS: CLONE CAMUFLADO & METADADOS VALIDOS ======\n";

$SRC = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$DEST = "/storage/emulated/0/Android/data/com.dts.freefireth";

// Captura a hora de instalação original do FF
echo "[*] Coletando data de instalação...\n";
$instalacao = shell_exec("adb shell dumpsys package com.dts.freefireth | grep -i firstInstallTime");
preg_match("/firstInstallTime=([\d-]+\s[\d:]+)/", $instalacao, $match);
$dataFormatada = $match[1] ?? date("Y-m-d H:i:s");
$timestamp = date("YmdHi", strtotime($dataFormatada));

// Zera o logcat
echo "[*] Limpando logcat...\n";
shell_exec("adb logcat -c");

// Remove a pasta original
echo "[*] Apagando estrutura original...\n";
shell_exec("adb shell rm -rf " . escapeshellarg($DEST));

// Copia o novo conteúdo
echo "[*] Copiando pasta camuflada...\n";
shell_exec("adb shell 'cd " . dirname($SRC) . " && cp -rf " . basename($SRC) . " " . dirname($DEST) . "'");

// Camufla todas as datas para bater com a instalação
echo "[*] Aplicando timestamps iguais à instalação...\n";
$pastas = [
    "$DEST/files",
    "$DEST/files/contentcache",
    "$DEST/files/contentcache/optional/android/gameassetbundles",
    "$DEST/files/ShaderStripSettings",
    "$DEST/files/ffrtc_log.txt"
];

foreach ($pastas as $alvo) {
    $esc = escapeshellarg($alvo);
    shell_exec("adb shell 'touch -t {$timestamp}.00 $esc && mv $esc {$esc}.tmp && mv {$esc}.tmp $esc'");
    echo "[✓] Camuflado: $alvo\n";
}

// Simula uso legítimo
echo "[*] Simulando acesso com comandos neutros...\n";
shell_exec("adb shell ls -lR " . escapeshellarg($DEST) . " > /dev/null");
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");

echo "[✓] Finalizado: Metadados e uso aparentam legítimos.\n";
?>
