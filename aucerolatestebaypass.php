<?php
date_default_timezone_set("America/Sao_Paulo");

echo "[+] Iniciando ajuste de hora via ADB...\n";

// Verifica ADB conectado
$check = shell_exec("adb shell echo ADB_OK");
if (strpos($check, "ADB_OK") === false) {
    echo "[!] ADB não conectado!\n";
    exit(1);
}

// Solicita hora falsa
echo "[?] Digite a nova hora (formato HH:MM): ";
$horaInput = trim(fgets(STDIN));

if (!preg_match("/^\d{2}:\d{2}$/", $horaInput)) {
    echo "[!] Formato inválido. Use HH:MM (ex: 11:00)\n";
    exit(1);
}

list($hh, $mm) = explode(":", $horaInput);
$dataHoje = date("md"); // mmdd
$ano = date("Y"); // geralmente será 2025

$comandoData = "{$dataHoje}{$hh}{$mm}";
echo "[*] Hora falsa a ser aplicada: {$ano}-" . date("m-d") . " {$horaInput}:00\n";

// Desativa hora automática
shell_exec("adb shell settings put global auto_time 0");
shell_exec("adb shell settings put global auto_time_zone 0");

// Aplica hora fake
shell_exec("adb shell date {$comandoData}");

echo "[✓] Hora do sistema ajustada manualmente para {$horaInput}.\n";
