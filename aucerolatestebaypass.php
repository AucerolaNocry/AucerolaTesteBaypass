<?php


echo "====== BYPASS COM CÓPIA CAMUFLADA (SEM APAGAR PASTA) ======\n";

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

// Etapa 2: Copiar arquivos do backup sem apagar a pasta destino
echo "[*] Copiando arquivos do backup sem apagar a pasta destino...\n";
shell_exec("adb shell 'cp -rn " . escapeshellarg($SRC) . "/* " . escapeshellarg($DEST) . "'");

// Etapa 3: Camuflar timestamps
echo "[*] Camuflando timestamps dos arquivos copiados...\n";
shell_exec("adb shell 'find $DEST -type f -exec touch -t $data_full {} \;'");

// Etapa 4: Forçar access time
shell_exec("adb shell 'ls -lR $DEST > /dev/null'");

// Etapa 5: Criar replay falso
$replay = "$DEST/files/MReplays/replay_fake.bin";
shell_exec("adb shell 'echo replay_ok > $replay'");
shell_exec("adb shell 'touch -t $data_full $replay'");
echo "[+] Cópia realizada e camuflagem aplicada.\n";

// Etapa 6: Simular jogo
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");
echo "[✓] Finalizado com sucesso. KellerSS não deve detectar alterações.\n";


?>
