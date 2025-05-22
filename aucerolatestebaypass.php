<?php
echo "====== SUBSTITUIR PASTA E CAMUFLAR PARA BURLAR O KELLERSS ======\n";

// Caminhos de origem e destino
$SRC = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$DEST = "/storage/emulated/0/Android/data/com.dts.freefireth";
$DATA = "20240501";

// Arquivos a camuflar com seus horários
$ARQUIVOS = [
    "$DEST/files/ShaderStripSettings" => "0930.00",
    "$DEST/files" => "0945.00",
    "$DEST/files/contentcache" => "1005.00",
    "$DEST/files/contentcache/optional" => "1015.00",
    "$DEST/files/contentcache/optional/android" => "1025.00",
    "$DEST/files/contentcache/optional/android/gameassetbundles" => "1035.00",
    "$DEST" => "1045.00",
    "$DEST/files/ffrtc_log.txt" => "2300.00"
];

// Limpa logs para remover rastros do processo
echo "[*] Limpando logcat para evitar rastreamento...\n";
shell_exec("adb logcat -c");

// Remove a pasta antiga
echo "[*] Removendo pasta alvo original...\n";
shell_exec("adb shell rm -rf " . escapeshellarg($DEST));

// Copia nova pasta (usando comando encadeado para evitar timestamps suspeitos)
echo "[*] Copiando pasta camuflada...\n";
shell_exec("adb shell 'cd " . dirname($SRC) . " && cp -rf " . basename($SRC) . " " . dirname($DEST) . "'");

// Aplica camuflagem de data/hora sem deixar rastros diretos
echo "[*] Camuflando metadados...\n";
foreach ($ARQUIVOS as $arquivo => $hora) {
    $esc = escapeshellarg($arquivo);
    $touch = "adb shell 'touch -t {$DATA}{$hora} $esc && mv $esc {$esc}.tmp && mv {$esc}.tmp $esc'";
    shell_exec($touch);
    echo "[✓] Camuflado: $arquivo\n";
}

// Acesso indireto para forçar atualização de uso real
echo "[*] Forçando access time legítimo via ls...\n";
shell_exec("adb shell ls -lR " . escapeshellarg($DEST) . " > /dev/null");

echo "[✓] Processo concluído com camuflagem avançada anti-KellerSS.\n";
?>
