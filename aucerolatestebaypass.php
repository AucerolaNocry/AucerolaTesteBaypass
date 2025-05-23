<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

echo "\n\033[1;34m[+] Anti-KellerSS FINAL — Execução Ninja Iniciada...\033[0m\n";

// 1. Verifica ADB
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555' e conecte via IP.\033[0m\n";
    exit(1);
}

// 2. Lista todos os arquivos da pasta limpa
$lista = shell_exec("adb shell find \"$origem\" -type f");
$arquivos = explode("\n", trim($lista));
$total = count($arquivos);

if ($total === 0) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado na pasta de origem.\033[0m\n";
    exit(1);
}

echo "\033[1;36m[*] Substituindo conteúdo sem alterar metadados da pasta...\033[0m\n";

// 3. Substitui todos os arquivos com 'cat' (modo ninja)
foreach ($arquivos as $origemAbs) {
    if (empty($origemAbs)) continue;

    $rel = str_replace("$origem/", "", $origemAbs);
    $dest = "$destino/$rel";

    shell_exec("adb shell 'cat \"$origemAbs\" > \"$dest\"'");
}

// 4. Camufla arquivos críticos (shaders, .bin, .json, .unity3d)
echo "\033[1;36m[*] Camuflando arquivos sensíveis (shaders, replays)...\033[0m\n";
$timestampFake = date("YmdHi.00", strtotime("-2 days"));

foreach ($arquivos as $origemAbs) {
    if (empty($origemAbs)) continue;

    $rel = str_replace("$origem/", "", $origemAbs);
    $dest = "$destino/$rel";

    if (preg_match('/\.bin$|\.json$|\.unity3d$|optionalab_/', $rel)) {
        shell_exec("adb shell touch -m -t $timestampFake \"$dest\" 2>/dev/null");
    }
}

// 5. Sincroniza Modify da pasta com o Change
$change = shell_exec("adb shell stat -c '%z' \"$destino\"");
$change = trim(explode('.', $change)[0]);
$touchFormat = date("YmdHi.00", strtotime($change));
shell_exec("adb shell touch -m -t $touchFormat \"$destino\" 2>/dev/null");

echo "\n\033[1;32m[✓] Pasta e arquivos limpos substituídos e camuflados.\033[0m\n";
echo "\033[1;34m[~] KellerSS não deve mais detectar nenhum bypass.\033[0m\n";
