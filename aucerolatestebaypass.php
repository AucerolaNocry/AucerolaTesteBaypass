<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";
$busyboxBin = "/data/local/tmp/busybox";
$localBusybox = __DIR__ . "/busybox";

// === 1. Baixa BusyBox estático, se não existir ===
if (!file_exists($localBusybox)) {
    echo "\033[1;36m[*] Baixando BusyBox estático funcional...\033[0m\n";
    shell_exec("curl -L -o busybox https://raw.githubusercontent.com/Elektordi/busybox-static/main/busybox-armv7l");
    shell_exec("chmod +x busybox");
}

if (!file_exists($localBusybox)) {
    echo "\033[1;31m[!] Falha ao baixar o BusyBox. Verifique a conexão.\033[0m\n";
    exit(1);
}

// === 2. Copia BusyBox para Android ===
echo "\033[1;36m[*] Copiando BusyBox para o Android...\033[0m\n";
shell_exec("adb push \"$localBusybox\" \"$busyboxBin\" > /dev/null");
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

// === 4. Verifica ADB ===
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555'.\033[0m\n";
    exit(1);
}

// === 5. Cria tar da pasta limpa com BusyBox ===
echo "\033[1;36m[*] Criando tar da pasta limpa com busybox...\033[0m\n";
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && '$busyboxBin' tar -cf '$tarfile' '$subpasta'\"");

// === 6. Remove pasta original suja ===
echo "\033[1;33m[*] Removendo pasta original...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// === 7. Extrai e camufla com BusyBox ===
echo "\033[1;32m[*] Extraindo e aplicando camuflagem...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && '$busyboxBin' tar -xpf '$tarfile'\"");

// === 8. Aplica Modify fake à pasta
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// === 9. Remove tar temporário
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição e camuflagem concluídas com sucesso!\033[0m\n";
echo "\033[1;30m[#] Modify ajustado para: {$tsFake->format('d/m/Y H:i:s')}\033[0m\n";
