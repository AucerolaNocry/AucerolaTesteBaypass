<?php
echo "====== ALTERAÇÃO CAMUFLADA ANTI-KELLERSS (SEM DETECÇÃO) ======\n";

// === CONFIGURAÇÃO ===
$data_referencia = "20250522";      // Mesmo dia da partida
$hora_simulada   = "155800";        // Antes da hora real da partida
$DEST = "/sdcard/Android/data/com.dts.freefireth";

// === 1. REINÍCIO DO ADB E LIMPEZA DE LOGS ===
shell_exec("adb start-server");
shell_exec("adb logcat -c");
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");
echo "[*] Ambiente limpo e sincronizado com horário automático.\n";

// === 2. CRIAÇÃO/ALTERAÇÃO DA PASTA (SIMULADA) ===
$arquivos = [
    "$DEST/files/ShaderStripSettings",
    "$DEST/files/contentcache/optional/android/gameassetbundles/optionalab_117.MxbzLYb~2FPb5D0TBn4vPVGP7PY9b=3D",
    "$DEST/files/ffrtc_log.txt"
];

foreach ($arquivos as $arquivo) {
    $esc = escapeshellarg($arquivo);
    echo "[*] Camuflando: $arquivo\n";
    shell_exec("adb shell 'touch -t {$data_referencia}{$hora_simulada} $esc'");
    shell_exec("adb shell 'mv $esc ${esc}.tmp && mv ${esc}.tmp $esc'");
}

// === 3. FORÇA access, modify, change ===
shell_exec("adb shell find $DEST -exec touch -t {$data_referencia}{$hora_simulada} {} \\;");
shell_exec("adb shell ls -lR $DEST > /dev/null");

// === 4. INJEÇÃO DE ARQUIVO REPLAY (COM .bin) ===
$replay = "$DEST/files/MReplays/replay_fake.bin";
shell_exec("adb shell 'echo bin_data > $replay'");
shell_exec("adb shell 'touch -t {$data_referencia}{$hora_simulada} $replay'");
echo "[+] Replay .bin legítimo injetado com tempo falso válido.\n";

// === 5. SIMULAÇÃO DE EXECUÇÃO DO JOGO ===
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");
echo "[✓] Bypass concluído. Alterações feitas sem serem detectadas pelo KellerSS.\n";
?>
