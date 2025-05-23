<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";
$busyboxBin = "/sdcard/busybox";

// === 1. Verifica se busybox está instalado no Termux ===
echo "\033[1;34m[+] Verificando BusyBox no Termux...\033[0m\n";
$busyboxLocal = trim(shell_exec("which busybox"));

if (!$busyboxLocal || !file_exists($busyboxLocal)) {
    echo "\033[1;33m[*] BusyBox não encontrado no Termux. Instalando...\033[0m\n";
    shell_exec("pkg update -y && pkg install busybox -y");
    $busyboxLocal = trim(shell_exec("which busybox"));
}

if (!$busyboxLocal || !file_exists($busyboxLocal)) {
    echo "\033[1;31m[!] Falha ao instalar o BusyBox no Termux.\033[0m\n";
    exit(1);
}

// === 2. Copia busybox para Android ===
echo "\033[1;36m[*] Copiando BusyBox para o Android via ADB...\033[0m\n";
shell_exec("adb push \"$busyboxLocal\" \"$busyboxBin\" > /dev/null");

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

// === 5. Remove tar anterior e cria novo via busybox no Android ===
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && '$busyboxBin' tar -cf '$tarfile' '$subpasta'\"");

// === 6. Remove pasta antiga
echo "\033[1;33m[*] Removendo pasta antiga...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// === 7. Extrai tar via busybox ===
echo "\033[1;32m[*] Extraindo e aplicando camuflagem...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && '$busyboxBin' tar -xpf '$tarfile'\"");

// === 8. Ajusta timestamp da pasta
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// === 9. Limpa
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição e camuflagem concluídas com BusyBox portátil.\033[0m\n";
echo "\033[1;30m[#] Hora simulada: {$tsFake->format('d/m/Y H:i:s')}\033[0m\n";
