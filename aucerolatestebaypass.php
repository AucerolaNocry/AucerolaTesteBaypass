<?php

$origem = "/sdcard/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

// 1. Verifica ADB
echo "\033[1;34m[+] Verificando ADB...\033[0m\n";
if (strpos(shell_exec("adb shell echo ADB_OK"), "ADB_OK") === false) {
    echo "\033[1;31m[!] ADB não está conectado. Use 'adb tcpip 5555'.\033[0m\n";
    exit(1);
}

// 2. Lista arquivos da pasta limpa
echo "\033[1;36m[*] Coletando arquivos da pasta limpa...\033[0m\n";
$lista = shell_exec("adb shell find \"$origem\" -type f");
$arquivos = explode("\n", trim($lista));
$total = count($arquivos);

if ($total === 0) {
    echo "\033[1;31m[!] Nenhum arquivo encontrado.\033[0m\n";
    exit(1);
}

// 3. Substitui com 'cat' um a um
echo "\033[1;32m[*] Substituindo arquivos com cat...\033[0m\n";
foreach ($arquivos as $origemAbs) {
    if (empty($origemAbs)) continue;

    $rel = str_replace("$origem/", "", $origemAbs);
    $dest = "$destino/$rel";

    shell_exec("adb shell 'cat \"$origemAbs\" > \"$dest\"'");
}

// 4. Camufla arquivos críticos (.bin, .json, shaders)
echo "\033[1;36m[*] Camuflando arquivos suspeitos...\033[0m\n";
$fakeTime = date("YmdHi.00", strtotime("-2 days"));
foreach ($arquivos as $origemAbs) {
    if (empty($origemAbs)) continue;

    $rel = str_replace("$origem/", "", $origemAbs);
    $dest = "$destino/$rel";

    if (preg_match('/\.bin$|\.json$|optionalab_|shaders|\.unity3d$/', $rel)) {
        shell_exec("adb shell touch -m -t $fakeTime \"$dest\" 2>/dev/null");
    }
}

echo "\n\033[1;32m[✓] Substituição silenciosa concluída sem apagar pastas.\033[0m\n";
echo "\033[1;30m[#] Teste agora com o KellerSS para verificar se ele detecta.\033[0m\n";
