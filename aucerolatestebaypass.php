<?php

$localPastaLimpa = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$pastaOriginal = "/sdcard/Android/data/com.dts.freefireth";
$pastaBackup = "/sdcard/Android/data/old_freefireth_" . rand(1000,9999);

// Verifica ADB
echo "\033[1;34m[+] Verificando ADB...\033[0m\n";
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado.\033[0m\n";
    exit(1);
}

// Renomeia a pasta suja
echo "\033[1;33m[*] Renomeando pasta suja para: $pastaBackup\033[0m\n";
shell_exec("adb shell mv \"$pastaOriginal\" \"$pastaBackup\"");

// Move a pasta limpa para o local esperado
echo "\033[1;32m[*] Substituindo pela pasta limpa...\033[0m\n";
shell_exec("adb shell mv \"$localPastaLimpa\" \"$pastaOriginal\"");

// Aplica data falsa opcional (comente se não quiser)
$dataFake = date("YmdHi.00", strtotime("-2 days"));
echo "\033[1;36m[*] Aplicando hora falsa na nova pasta...\033[0m\n";
shell_exec("adb shell touch -m -t $dataFake \"$pastaOriginal\"");

// Finalização
echo "\n\033[1;32m[✓] Pasta limpa aplicada com sucesso e camuflada.\033[0m\n";
echo "\033[1;30m[#] Teste agora no KellerSS.\033[0m\n";
