<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

echo "\n\033[1;34m[+] Modo Ninja Anti-KellerSS ativado...\033[0m\n";

// Verificar ADB
$check = shell_exec("adb shell echo ADB_OK");
if (strpos($check, "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Conecte via Wi-Fi antes.\033[0m\n";
    exit(1);
}

// Coletar lista de arquivos na origem
$lista = shell_exec("adb shell find \"$origem\" -type f");
$arquivos = explode("\n", trim($lista));
$total = count($arquivos);

if ($total === 0) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado para copiar.\033[0m\n";
    exit(1);
}

echo "\033[1;36m[*] Substituindo conteúdo interno sem alterar metadados...\033[0m\n";

foreach ($arquivos as $origemAbsoluto) {
    if (empty($origemAbsoluto)) continue;

    $relativo = str_replace($origem . "/", "", $origemAbsoluto);
    $destinoFinal = $destino . "/" . $relativo;

    // Sobrescreve conteúdo usando cat (sem alterar inode, sem mudar metadado de pasta)
    $comando = "cat \"$origemAbsoluto\" > \"$destinoFinal\"";
    shell_exec("adb shell '$comando'");
}

// Não mexe no timestamp da pasta!
echo "\033[1;32m[✓] Substituição realizada com zero alterações de pasta.\033[0m\n";
echo "\033[1;34m[#] Execute o KellerSS — ele não deve detectar nada agora.\033[0m\n";
