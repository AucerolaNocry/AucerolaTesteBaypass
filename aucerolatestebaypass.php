<?php
echo "====== BYPASS COM CÓPIA TOTAL CAMUFLADA ======\n";

// Configurações
$data_referencia = "20250522";
$hora_simulada   = "1558";
$tempo_final     = ".00";
$data_full       = $data_referencia . $hora_simulada . $tempo_final;

$SRC  = "/storage/emulated/0/Pictures/TESTE/PINSSALVOS/com.dts.freefireth";
$DEST = "/sdcard/Android/data/com.dts.freefireth";

// Etapa 1: Preparar ambiente
shell_exec("adb start-server");
shell_exec("adb logcat -c");
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");
echo "[*] Ambiente limpo e pronto.\n";

// Etapa 2: Apagar pasta antiga e copiar nova
echo "[*] Substituindo pasta por backup camuflado...\n";
shell_exec("adb shell rm -rf " . escapeshellarg($DEST));
shell_exec("adb shell 'cp -r " . escapeshellarg($SRC) . " " . escapeshellarg(dirname($DEST)) . "'");

// Etapa 3: Camuflar todos os arquivos com timestamp coerente
echo "[*] Aplicando camuflagem de timestamps...\n";
$cmd_touch = "adb shell 'find $DEST -type f -exec touch -t $data_full {} \\;'";
shell_exec($cmd_touch);

// Etapa 4: Forçar access time com leitura neutra
shell_exec("adb shell 'ls -lR $DEST > /dev/null'");

// Etapa 5: Replay falso para reforço
$replay = "$DEST/files/MReplays/replay_fake.bin";
shell_exec("adb shell 'echo binario > $replay'");
shell_exec("adb shell 'touch -t $data_full $replay'");

echo "[+] Backup copiado, camuflado e funcional.\n";

// Etapa 6: Simular execução
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");
echo "[✓] Finalizado. KellerSS não deve detectar alterações.\n";
?>
