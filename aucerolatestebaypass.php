<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";
$dataFake = date("YmdHi.00"); // Timestamp camuflado (modifique se necessário)

echo "\n\033[1;34m[+] Anti-KellerSS: substituindo pasta suja por limpa...\033[0m\n";

// Verifica se ADB está funcional
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não conectado. Execute 'adb tcpip 5555' e conecte com IP.\033[0m\n";
    exit(1);
}

// Cria lista de arquivos da pasta limpa
$listaArquivos = explode("\n", trim(shell_exec("adb shell find \"$origem\" -type f")));
if (empty($listaArquivos)) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado na pasta de origem limpa.\033[0m\n";
    exit(1);
}

// Substitui arquivos um por um
foreach ($listaArquivos as $origemAbsoluta) {
    $relativo = trim(str_replace("$origem/", "", $origemAbsoluta));
    $destinoFinal = "$destino/$relativo";

    $destinoDir = dirname($destinoFinal);
    shell_exec("adb shell mkdir -p \"$destinoDir\"");
    shell_exec("adb shell cp \"$origemAbsoluta\" \"$destinoFinal\"");

    // Aplica timestamp camuflado
    shell_exec("adb shell touch -m -t $dataFake \"$destinoFinal\" 2>/dev/null");

    echo "\033[1;32m[*] Substituído: $relativo\033[0m\n";
}

// Camufla a pasta principal
shell_exec("adb shell touch -m -t $dataFake \"$destino\" 2>/dev/null");

echo "\n\033[1;32m[✓] Substituição completa, sem apagar a pasta.\033[0m\n";
echo "\033[1;30m[#] Execute o KellerSS para verificar se a detecção foi invalidada.\033[0m\n";
