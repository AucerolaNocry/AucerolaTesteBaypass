<?php

$origemLocal = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destinoAndroid = "/sdcard/Android/data/com.dts.freefireth";

echo "\033[1;34m[+] Iniciando Anti-KellerSS...\033[0m\n";

// Verifica ADB
$checkAdb = shell_exec("adb shell echo ADB_OK");
if (strpos($checkAdb, "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado.\033[0m\n";
    exit(1);
}

// Gera lista de arquivos da pasta limpa
$lista = shell_exec("find \"$origemLocal\" -type f");
$arquivos = explode("\n", trim($lista));

if (empty($arquivos)) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado na pasta limpa.\033[0m\n";
    exit(1);
}

// Copia os arquivos um a um
foreach ($arquivos as $arquivo) {
    if (empty($arquivo)) continue;

    $relativo = str_replace($origemLocal . "/", "", $arquivo);
    $destinoFinal = $destinoAndroid . "/" . $relativo;

    echo "\033[1;32m[*] Substituindo: $relativo\033[0m\n";

    $pasta = dirname($destinoFinal);
    shell_exec("adb shell mkdir -p \"$pasta\"");
    shell_exec("adb push \"$arquivo\" \"$destinoFinal\" > /dev/null 2>&1");

    // Timestamp falso para arquivos
    $ts = date("YmdHi.00");
    shell_exec("adb shell touch -m -t $ts \"$destinoFinal\" 2>/dev/null");
}

// Captura o novo Change da pasta
$change = shell_exec("adb shell stat -c '%z' \"$destinoAndroid\"");
$change = trim(explode('.', $change)[0]); // remove milissegundos
$touchFormat = date("YmdHi.00", strtotime($change));

// Aplica o mesmo valor no Modify para evitar detecção
shell_exec("adb shell touch -m -t $touchFormat \"$destinoAndroid\" 2>/dev/null");

echo "\n\033[1;32m[✓] Substituição concluída sem apagar a pasta.\033[0m\n";
echo "\033[1;34m[~] Timestamp sincronizado: Modify == Change: $touchFormat\033[0m\n";
echo "\033[1;30m[#] Agora rode o KellerSS e verifique se ele detecta algo.\033[0m\n";
