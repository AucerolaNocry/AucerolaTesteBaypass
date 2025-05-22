<?php
echo "====== SUBSTITUIR PASTA E CAMUFLAR TODOS OS ARQUIVOS ======\n";

// Caminhos
$SRC = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$DEST = "/storage/emulated/0/Android/data/com.dts.freefireth";
$DATA = "20240501";

// Lista de arquivos e pastas para aplicar camuflagem
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

// 1. Remover pasta antiga
echo "[*] Removendo pasta antiga...\n";
shell_exec("adb shell rm -rf " . escapeshellarg($DEST));

// 2. Copiar nova pasta
echo "[*] Copiando nova pasta...\n";
shell_exec("adb shell cp -rf " . escapeshellarg($SRC) . " " . escapeshellarg(dirname($DEST)));

// 3. Aplicar camuflagem nos arquivos
echo "[*] Aplicando camuflagem de data nos arquivos...\n";
foreach ($ARQUIVOS as $arquivo => $hora) {
    shell_exec("adb shell touch " . escapeshellarg($arquivo));
    shell_exec("adb shell touch -t {$DATA}{$hora} " . escapeshellarg($arquivo));
    shell_exec("adb shell mv " . escapeshellarg($arquivo) . " " . escapeshellarg($arquivo . ".tmp"));
    shell_exec("adb shell mv " . escapeshellarg($arquivo . ".tmp") . " " . escapeshellarg($arquivo));
    echo "[✓] Camuflado: $arquivo\n";
}

echo "[✓] Substituição e camuflagem completa concluída.\n";
?>
