<?php
echo "====== ALTERAÇÃO CAMUFLADA ANTI-KELLERSS (CORRIGIDO) ======\n";

$data_referencia = "20250522";
$hora_simulada   = "1558";       // 15:58
$tempo_final     = ".00";        // segundos obrigatórios para touch
$DEST = "/sdcard/Android/data/com.dts.freefireth";

// === Arquivos Alvo ===
$arquivos = [
    "$DEST/files/ShaderStripSettings",
    "$DEST/files/contentcache/optional/android/gameassetbundles/optionalab_117.MxbzLYb~2FPb5D0TBn4vPVGP7PY9b=3D",
    "$DEST/files/ffrtc_log.txt"
];

// === Etapa 1: Ambiente
shell_exec("adb logcat -c");
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");
echo "[*] Ambiente limpo e sincronizado com horário automático.\n";

// === Etapa 2: Camuflagem
foreach ($arquivos as $arquivo) {
    $data = $data_referencia . $hora_simulada . $tempo_final;
    $esc = escapeshellarg($arquivo);
    echo "[*] Camuflando: $arquivo\n";
    shell_exec("adb shell 'if [ -f $esc ]; then touch -t $data $esc && mv $esc ${esc}.tmp && mv ${esc}.tmp $esc; fi'");
}

// === Etapa 3: Corrigir find
shell_exec("adb shell 'find $DEST -type f -exec touch -t {$data_referencia}{$hora_simulada}{$tempo_final} {} \\\;'");

// === Etapa 4: Replay falso
$replay = "$DEST/files/MReplays/replay_fake.bin";
shell_exec("adb shell 'echo replay_fake > $replay'");
shell_exec("adb shell 'touch -t {$data_referencia}{$hora_simulada}{$tempo_final} $replay'");
echo "[+] Replay injetado com sucesso.\n";

// === Etapa 5: Simular Execução
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");
echo "[✓] Processo finalizado. Todos os arquivos agora aparentam legítimos.\n";
?>
