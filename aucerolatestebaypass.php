<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";
$busyboxBin = "/data/local/tmp/busybox"; // local executável no Android

// === 1. Usa busybox estático local ===
$busyboxLocal = __DIR__ . "/busybox";
if (!file_exists($busyboxLocal)) {
    echo "\033[1;31m[!] BusyBox estático não encontrado. Execute:\n";
    echo "curl -L -o busybox https://frippery.org/files/busybox/busybox-armv7l && chmod +x busybox\n\033[0m";
    exit(1);
}

// === 2. Copia busybox para o Android (executável) ===
echo "\033[1;36m[*] Enviando BusyBox para o Android...\033[0m\n";
shell_exec("adb push \"$busyboxLocal\" \"$busyboxBin\" > /dev/null");
shell_exec("adb shell chmod +x \"$busyboxBin\"");

// === 3. Entrada da hora da partida ===
echo "\033[1;34m[+] Digite a data e hora final da partida (formato: dd-mm HH:MM):\033[0m ";
$stdin = fopen("php://stdin", "r");
$entrada = trim(fgets($stdin));

if (!preg_match('/^\d{2}-\d{2} \d{2}:\d{2}$/', $entrada)) {
    echo "\033[1;31m[!] Formato inválido. Use: dd-mm HH:MM\033[0m\n";
    exit(1);
}

$entradaCompleta = "2025-" . substr($entrada, 3, 2) . "-" . substr($entrada, 0, 2) . " " . substr($entrada, 6);
$tsPartida = DateTime::createFromFormat("Y-m-d H:i", $entradaCompleta);
$tsFake = clone $tsPartida;
$tsFake->modify("-1 hour");
$tempoFake = $tsFake->format("YmdHi.00");

echo "\033[1;36m[*] Hora fake aplicada: {$tsFake->format('d-m-Y H:i:s')}\033[0m\n";

// === 4. Verifica ADB
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555'.\033[0m\n";
    exit(1);
}

// === 5. Remove tar antigo e cria novo com busybox estático
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && '$busyboxBin' tar -cf '$tarfile' '$subpasta'\"");

// === 6. Remove pasta original
echo "\033[1;33m[*] Removendo pasta antiga...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// === 7. Extrai e camufla
echo "\033[1;32m[*] Extraindo e aplicando camuflagem...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && '$busyboxBin' tar -xpf '$tarfile'\"");

// === 8. Camufla Modify
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// === 9. Limpa tar
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição camuflada executada com BusyBox estático.\033[0m\n";
echo "\033[1;30m[#] Modify ajustado para: {$tsFake->format('d/m/Y H:i:s')}\033[0m\n";
