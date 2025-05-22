<?php
// Configurações e cores
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('R', "\033[91m");
define('G', "\033[92m");
define('Y', "\033[93m");
define('B', "\033[94m");
define('RST', "\033[0m");

// Caminhos
$GAME = "com.dts.freefireth";
$DEST = "/sdcard/Android/data/$GAME";
$SRC = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/$GAME";
$FAKE_NAME = date("Ymd_His");

// Banner
system("clear");
echo B . "\n====== BYPASS FF ANTI-KELLERSS ======\n" . RST;
shell_exec("adb start-server");

// Etapa 1: Backup invisível
shell_exec("adb shell cp -rn '$DEST' /sdcard/FF_BACKUP_$GAME");
echo Y . "[*] Backup invisível criado\n" . RST;

// Etapa 2: Injeta sem deletar
shell_exec("adb shell 'cp -rf $SRC/* $DEST/'");
echo G . "[+] Dados injetados sem apagar pastas\n" . RST;

// Etapa 3: Camuflagem avançada dos tempos (diferenças sutis entre A/M/C)
$files = [
    "$DEST/files/ShaderStripSettings",
    "$DEST/files/contentcache/optional/android/gameassetbundles/optionalab_117.shader",
    "$DEST/files/ffrtc_log.txt"
];

foreach ($files as $f) {
    $f_esc = escapeshellarg($f);
    shell_exec("adb shell 'touch -a -t 202505220930.00 $f_esc'"); // Access
    shell_exec("adb shell 'touch -m -t 202505220931.00 $f_esc'"); // Modify
    shell_exec("adb shell mv $f_esc $f_esc.tmp && mv $f_esc.tmp $f_esc"); // Change
    echo G . "[+] A/M/C camuflados: $f\n" . RST;
}

// Etapa 4: Replay fictício realista
$replay = "$DEST/files/MReplays/{$FAKE_NAME}_camuflado.bin";
shell_exec("adb shell 'echo legitdata > $replay'");
shell_exec("adb shell 'touch -t 202505220930.00 $replay'");
echo Y . "[*] Replay simulado injetado\n" . RST;

// Etapa 5: Simula uso real
shell_exec("adb shell monkey -p $GAME -c android.intent.category.LAUNCHER 1");
shell_exec("adb logcat -c");
echo G . "[+] Simulação de uso finalizada\n" . RST;

// Etapa 6: Atualiza pastas de forma indireta
$pastas = [
    "$DEST",
    "$DEST/files",
    "$DEST/files/contentcache",
    "$DEST/files/contentcache/optional",
    "$DEST/files/contentcache/optional/android",
];
foreach ($pastas as $p) {
    shell_exec("adb shell 'echo update > $p/.log && rm $p/.log'");
}
echo G . "[+] Pastas sincronizadas com método stealth\n" . RST;

echo B . "\n[✓] Processo finalizado. Anti-Scanner completo aplicado.\n" . RST;
?>
