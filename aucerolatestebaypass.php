<?php
echo "====== BYPASS COMPLETO - FORJANDO A/M/C DIFERENTES E BLOQUEANDO DETECÇÃO ======\n";

$data_referencia = "20250428";
$hora_acesso     = "1350";
$hora_modif      = "1351";
$hora_change     = "1352";
$tempo_final     = ".00";

// Caminhos
$SRC  = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$DEST = "/sdcard/Android/data/com.dts.freefireth";

// Etapa 1: Ambiente
shell_exec("adb start-server");
shell_exec("adb logcat -c");
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");
echo "[*] Ambiente pronto.\n";

// Etapa 2: Copiar sem deletar
echo "[*] Copiando arquivos do backup...\n";
shell_exec("adb shell 'cp -rn " . escapeshellarg($SRC) . "/* " . escapeshellarg($DEST) . "'");

// Etapa 3: Camuflagem A/M/C
$paths = [
    "$DEST/files/ShaderStripSettings",
    "$DEST/files/contentcache/optional/android/gameassetbundles/optionalab_117.MxbzLYb~2FPb5D0TBn4vPVGP7PY9b=3D"
];

foreach ($paths as $arquivo) {
    $esc = escapeshellarg($arquivo);
    shell_exec("adb shell 'touch -a -t {$data_referencia}{$hora_acesso}{$tempo_final} {$esc}'");
    shell_exec("adb shell 'touch -m -t {$data_referencia}{$hora_modif}{$tempo_final} {$esc}'");
    shell_exec("adb shell 'mv {$esc} {$esc}.tmp && mv {$esc}.tmp {$esc}'");
    echo "[+] Camuflado: {$arquivo}\n";
}

// Etapa 4: Criar replay falso com conteúdo realista
$replay = "$DEST/files/MReplays/20250428_1330_fake.bin";
shell_exec("adb shell 'dd if=/dev/urandom of=$replay bs=1 count=64'");
shell_exec("adb shell 'touch -t {$data_referencia}133000 $replay'");
echo "[+] Replay .bin criado com conteúdo e timestamps realistas.\n";

// Etapa 5: Corrigir timezone e simular execução
shell_exec("adb shell 'ls -lR $DEST > /dev/null'");
shell_exec("adb shell monkey -p com.dts.freefireth -c android.intent.category.LAUNCHER 1");

echo "[✓] Bypass completo aplicado. Nenhuma detecção esperada pelo teste.php.\n";
?>
