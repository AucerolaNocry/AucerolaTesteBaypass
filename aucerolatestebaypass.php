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

// RENOMEIA A PASTA ORIGINAL SUJA
echo "\033[1;33m[*] Renomeando pasta suja para: $pastaBackup\033[0m\n";
shell_exec("adb shell mv \"$pastaAlvo\" \"$pastaBackup\"");

// COPIA A PASTA LIMPA SEM APAGAR A ORIGINAL
echo "\033[1;32m[*] Copiando pasta limpa para o local...\033[0m\n";
shell_exec("adb shell cp -r \"$pastaLimpa\" \"$pastaAlvo\"");

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

// APLICA FAKE DATE COM TOUCH
echo "\033[1;36m[*] Aplicando hora falsa nas pastas...\033[0m\n";
foreach ($pastas as $pasta) {
    shell_exec("adb shell touch -m -t $dataFake \"$pasta\"");
    echo "\033[1;30m[✓] $pasta\033[0m\n";
}

echo "\n\033[1;32m[✓] Substituição com cópia e camuflagem concluída com sucesso.\033[0m\n";
echo "\033[1;30m[#] Data de modificação camuflada para: 28/04/2025 10:00:00 — teste com KellerSS.\033[0m\n";
