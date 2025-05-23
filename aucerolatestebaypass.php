<?php
$localPastaLimpa = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

// Hora fake: 1h antes da hora final da partida
echo "\033[1;36m[+] Digite a hora final da partida (formato: HH:MM): \033[0m";
$horaFinal = trim(fgets(STDIN));
$dataBase = date("d-m-Y");
[$hora, $minuto] = explode(":", $horaFinal);
$timestampFake = mktime($hora - 1, $minuto, 0);
$fakeDate = date("YmdHi", $timestampFake);
$fakeTouch = date("YmdHi.00", $timestampFake);

echo "\033[1;34m[+] Iniciando substituição furtiva...\033[0m\n";

$arquivos = explode("\n", trim(shell_exec("find \"$localPastaLimpa\" -type f")));
foreach ($arquivos as $arquivo) {
    $relativo = str_replace("$localPastaLimpa/", "", $arquivo);
    $destinoFinal = "$destino/$relativo";
    $destinoDir = dirname($destinoFinal);

    shell_exec("adb shell mkdir -p \"$destinoDir\"");
    shell_exec("adb push \"$arquivo\" \"$destinoFinal\" > /dev/null");

    // Camuflagem: apply fake Modify e Access
    shell_exec("adb shell touch -m -t $fakeTouch \"$destinoFinal\"");
    sleep(1);
    shell_exec("adb shell touch -a -t $fakeTouch \"$destinoFinal\"");
}

echo "\n\033[1;32m[✓] Substituição completa sem remover o original!\033[0m\n";
echo "\033[1;30m[#] Modify/Access sincronizados para: " . date("d/m/Y H:i", $timestampFake) . "\033[0m\n";
