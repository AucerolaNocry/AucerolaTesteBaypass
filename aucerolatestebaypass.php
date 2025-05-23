<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";

echo "\n\033[1;34m[+] Anti-KellerSS — Substituição furtiva via .tar...\033[0m\n";

// 1. Solicita hora final da partida (sem ano)
echo "\033[1;34m[+] Digite a data e hora final da partida (formato: dd-mm HH:MM):\033[0m ";
$stdin = fopen("php://stdin", "r");
$entrada = trim(fgets($stdin));

// Validação (ex: 03-04 14:45)
if (!preg_match('/^\d{2}-\d{2} \d{2}:\d{2}$/', $entrada)) {
    echo "\033[1;31m[!] Formato inválido. Use: dd-mm HH:MM\033[0m\n";
    exit(1);
}

// Monta data completa com ano fixo 2025
$entradaCompleta = "2025-" . substr($entrada, 3, 2) . "-" . substr($entrada, 0, 2) . " " . substr($entrada, 6);

// Converte e subtrai 1 hora
$tsPartida = DateTime::createFromFormat("Y-m-d H:i", $entradaCompleta);
$tsFake = clone $tsPartida;
$tsFake->modify("-1 hour");
$tempoFake = $tsFake->format("YmdHi.00");

echo "\033[1;36m[*] Hora fake aplicada: {$tsFake->format('d-m-Y H:i:s')}\033[0m\n";

// 2. Verifica ADB
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado.\033[0m\n";
    exit(1);
}

// 3. Apaga tar antigo e cria novo
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && busybox tar -cf '$tarfile' '$subpasta'\"");

// 4. Remove pasta suja
echo "\033[1;33m[*] Removendo pasta original...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// 5. Extrai .tar mantendo timestamps
echo "\033[1;32m[*] Extraindo e camuflando pasta...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && busybox tar -xpf '$tarfile'\"");

// 6. Camufla pasta raiz
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// 7. Remove o tar
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição 100% camuflada realizada com sucesso.\033[0m\n";
echo "\033[1;30m[#] Modify sincronizado para: {$tsFake->format('d/m/Y H:i:s')}\033[0m\n";
