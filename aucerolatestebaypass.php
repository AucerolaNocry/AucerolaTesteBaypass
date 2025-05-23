<?php

$origemLocal = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destinoAndroid = "/sdcard/Android/data/com.dts.freefireth";

echo "\n\033[1;34m[+] Iniciando Anti-KellerSS otimizado...\033[0m\n";

// Verifica ADB
$checkAdb = shell_exec("adb shell echo ADB_OK");
if (strpos($checkAdb, "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado.\033[0m\n";
    exit(1);
}

// Lista todos os arquivos
$lista = shell_exec("find \"$origemLocal\" -type f");
$arquivos = explode("\n", trim($lista));
$total = count($arquivos);
if ($total === 0) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado na pasta limpa.\033[0m\n";
    exit(1);
}

// Substituição otimizada com barra de progresso
echo "\033[1;36m[*] Substituindo arquivos: \033[0m";
$contador = 0;
foreach ($arquivos as $arquivo) {
    if (empty($arquivo)) continue;

    $relativo = str_replace($origemLocal . "/", "", $arquivo);
    $destinoFinal = $destinoAndroid . "/" . $relativo;
    $pasta = dirname($destinoFinal);

    // Cria a pasta e copia arquivo
    shell_exec("adb shell mkdir -p \"$pasta\"");
    shell_exec("adb push \"$arquivo\" \"$destinoFinal\" > /dev/null 2>&1");

    // Opcional: mascarar arquivo individual (pode comentar se quiser mais rápido)
    // $ts = date("YmdHi.00");
    // shell_exec("adb shell touch -m -t $ts \"$destinoFinal\" 2>/dev/null");

    // Progresso simples
    $contador++;
    if ($contador % 15 === 0) echo ".";
}
echo " [✓]\n";

// Ajuste final: sincronizar Modify == Change na pasta
$change = shell_exec("adb shell stat -c '%z' \"$destinoAndroid\"");
$change = trim(explode('.', $change)[0]);
$touchFormat = date("YmdHi.00", strtotime($change));
shell_exec("adb shell touch -m -t $touchFormat \"$destinoAndroid\" 2>/dev/null");

echo "\n\033[1;32m[✓] Anti-KellerSS aplicado com sucesso.\033[0m\n";
echo "\033[1;34m[~] Timestamps sincronizados. Execute o KellerSS para testar.\033[0m\n";
