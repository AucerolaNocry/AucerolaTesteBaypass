<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";
$busyboxBin = "/data/local/tmp/busybox"; // pasta executável!

// === 1. Verifica BusyBox no Termux ===
echo "\033[1;34m[+] Verificando BusyBox no Termux...\033[0m\n";
$busyboxRaw = shell_exec("which busybox");
$busyboxLocal = is_string($busyboxRaw) ? trim($busyboxRaw) : '';

if (!$busyboxLocal || !file_exists($busyboxLocal)) {
    echo "\033[1;33m[*] BusyBox não encontrado no Termux. Instalando...\033[0m\n";
    shell_exec("pkg update -y && pkg install busybox -y");
    $busyboxRaw = shell_exec("which busybox");
    $busyboxLocal = is_string($busyboxRaw) ? trim($busyboxRaw) : '';
}

if (!$busyboxLocal || !file_exists($busyboxLocal)) {
    echo "\033[1;31m[!] Falha ao instalar o BusyBox no Termux.\033[0m\n";
    exit(1);
}

// === 2. Copia busybox para local executável no Android ===
echo "\033[1;36m[*] Copiando BusyBox para o Android...\033[0m\n";
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

// Monta data completa com ano fixo 2025
$entradaCompleta = "2025-" . substr($entrada, 3, 2) . "-" . substr($entrada, 0, 2) . " " . substr($entrada, 6);
$tsPartida = DateTime::createFromFormat("Y-m-d H:i", $entradaCompleta);
$tsFake = clone $tsPartida;
$tsFake->modify("-1 hour");
$tempoFake = $tsFake->format("YmdHi.00");

echo "\033[1;36m[*] Hora fake aplicada: {$tsFake->format('d-m-Y H:i:s')}\033[0m\n";

// === 4. Verifica ADB ===
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555'.\033[0m\n";
    exit(1);
}

// === 5. Remove tar anterior e cria novo com busybox ===
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && '$busyboxBin' tar -cf '$tarfile' '$subpasta'\"");

// === 6. Remove pasta suja
echo "\033[1;33m[*] Removendo pasta original...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// === 7. Extrai tar mantendo timestamps com busybox
echo "\033[1;32m[*] Extraindo e camuflando...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && '$busyboxBin' tar -xpf '$tarfile'\"");

// === 8. Camufla Modify da pasta
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// === 9. Remove tar temporário
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição furtiva concluída com sucesso.\033[0m\n";
echo "\033[1;30m[#] Modify camuflado para: {$tsFake->format('d/m/Y H:i:s')}\033[0m\n";
