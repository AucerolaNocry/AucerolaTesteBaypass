<?php

// === CONFIGURAÇÃO INICIAL ===
$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS";
$subpasta = "com.dts.freefireth";
$destino = "/sdcard/Android/data";
$tarfile = "/sdcard/temp_ff_clean.tar";

// === 1. Coleta hora da partida ===
echo "\033[1;34m[+] Digite a hora final da partida (formato 24h):\033[0m ";
$stdin = fopen("php://stdin", "r");
$horaPartida = trim(fgets($stdin));

// Valida formato (ex: 23-05-2025 14:20)
if (!preg_match('/^\d{2}-\d{2}-\d{4} \d{2}:\d{2}$/', $horaPartida)) {
    echo "\033[1;31m[!] Formato inválido. Use: dd-mm-aaaa hh:mm\033[0m\n";
    exit(1);
}

// Converte hora da partida -1h
$tsPartida = DateTime::createFromFormat("d-m-Y H:i", $horaPartida);
$tsFake = clone $tsPartida;
$tsFake->modify("-1 hour");
$tempoFake = $tsFake->format("YmdHi.00");

echo "\033[1;36m[*] Hora fake para camuflagem: {$tsFake->format('d-m-Y H:i:s')}\033[0m\n";

// === 2. Verifica ADB ===
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555'.\033[0m\n";
    exit(1);
}

// === 3. Apaga tar antigo e cria novo ===
shell_exec("adb shell rm -f \"$tarfile\"");
shell_exec("adb shell \"cd '$origem' && busybox tar -cf '$tarfile' '$subpasta'\"");

// === 4. Apaga pasta antiga ===
echo "\033[1;33m[*] Removendo pasta suja original...\033[0m\n";
shell_exec("adb shell rm -rf \"$destino/$subpasta\"");

// === 5. Extrai tar preservando timestamps ===
echo "\033[1;32m[*] Extraindo .tar com timestamps preservados...\033[0m\n";
shell_exec("adb shell \"cd '$destino' && busybox tar -xpf '$tarfile'\"");

// === 6. Aplica Modify falso na pasta ===
echo "\033[1;34m[*] Ajustando timestamp da pasta final...\033[0m\n";
shell_exec("adb shell touch -m -t $tempoFake \"$destino/$subpasta\"");

// === 7. Limpa o tar temporário ===
shell_exec("adb shell rm -f \"$tarfile\"");

echo "\n\033[1;32m[✓] Substituição real concluída sem detecção pelo KellerSS.\033[0m\n";
echo "\033[1;30m[#] Hora simulada: {$tsFake->format('d/m/Y H:i:s')} (1h antes da partida)\033[0m\n";
