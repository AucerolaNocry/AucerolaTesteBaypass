<?php

// CONFIGURAÇÕES
$pastaLimpa = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$pastaAlvo = "/sdcard/Android/data/com.dts.freefireth";
$pastaBackup = "/sdcard/Android/data/old_freefireth_" . rand(1000, 9999);
$dataFake = "202504281000.00"; // 28/04/2025 10:00:00

echo "\033[1;34m[+] Verificando conexão ADB...\033[0m\n";
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Verifique.\033[0m\n";
    exit(1);
}

// RENOMEIA A PASTA ANTIGA
echo "\033[1;33m[*] Renomeando pasta suja para: $pastaBackup\033[0m\n";
shell_exec("adb shell mv \"$pastaAlvo\" \"$pastaBackup\"");

// MOVE A LIMPA PARA O DESTINO
echo "\033[1;32m[*] Movendo pasta limpa para o local...\033[0m\n";
shell_exec("adb shell mv \"$pastaLimpa\" \"$pastaAlvo\"");

// LISTA DE PASTAS CRÍTICAS PARA CAMUFLAR
$pastas = [
    "/sdcard/Android",
    "/sdcard/Android/data",
    $pastaAlvo,
    "$pastaAlvo/files",
    "$pastaAlvo/files/contentcache",
    "$pastaAlvo/files/contentcache/optional",
    "$pastaAlvo/files/contentcache/optional/android",
    "$pastaAlvo/files/contentcache/optional/android/gameassetbundles",
    "$pastaAlvo/cache",
    "$pastaAlvo/files/MReplays",
];

// CAMUFLA COM TOUCH
echo "\033[1;36m[*] Aplicando hora falsa em pastas...\033[0m\n";
foreach ($pastas as $pasta) {
    shell_exec("adb shell touch -m -t $dataFake \"$pasta\"");
    echo "\033[1;30m[✓] $pasta\033[0m\n";
}

echo "\n\033[1;32m[✓] Substituição e camuflagem concluídas com sucesso.\033[0m\n";
echo "\033[1;30m[#] Modify camuflado para: 28/04/2025 10:00:00 — teste agora com KellerSS.\033[0m\n";
