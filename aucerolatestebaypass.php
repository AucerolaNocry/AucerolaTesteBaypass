<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

// Verifica se ADB está ativo
echo "\e[36m[+] Verificando ADB...\e[0m\n";
$adbOk = shell_exec("adb shell echo ADB_OK");
if (strpos($adbOk, "ADB_OK") === false) {
    echo "\e[31m[!] ADB não está conectado. Use 'adb tcpip 5555' e conecte via IP.\e[0m\n";
    exit(1);
}

// Verifica se pasta destino existe
echo "\e[36m[+] Verificando se a pasta destino existe...\e[0m\n";
$existe = trim(shell_exec("adb shell '[ -d \"$destino\" ] && echo SIM || echo NAO'"));
if ($existe !== "SIM") {
    echo "\e[31m[!] Pasta $destino não encontrada. Inicie o jogo uma vez antes.\e[0m\n";
    exit(1);
}

// Copiando arquivos da pasta limpa
echo "\e[32m[*] Copiando arquivos da pasta limpa para a pasta do jogo...\e[0m\n";
shell_exec("adb shell 'cp -r \"$origem\"/* \"$destino\"/'");

// Tentar mascarar com touch
$dataTouch = date("YmdHi.00"); // Ex: 202505231530.00
echo "\e[33m[*] Aplicando touch para mascarar modificação...\e[0m\n";
shell_exec("adb shell 'find \"$destino\" -type f -exec touch -m -t $dataTouch {} \;' 2>/dev/null");
shell_exec("adb shell 'touch -m -t $dataTouch \"$destino\"' 2>/dev/null");

echo "\n\e[32m[✓] Pasta atualizada com arquivos limpos, sem apagar a original.\e[0m\n";
echo "\e[90m[#] Agora execute o KellerSS e veja se detecta algo.\e[0m\n";
