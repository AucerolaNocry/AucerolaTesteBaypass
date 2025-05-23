<?php
// Cores ANSI
$vermelho = "\e[91m";
$verde = "\e[92m";
$azul = "\e[34m";
$amarelo = "\e[93m";
$cln = "\e[0m";
$bold = "\e[1m";

function forjarLogsAntigos() {
    global $cln, $bold, $vermelho, $verde, $amarelo;

    $data_log = date('m-d H:i:s', strtotime('-3 days'));

    $cmds = [
        "adb shell 'echo \"[FAKE] System event at $data_log\" > /data/local/tmp/.system_log'",
        "adb shell log -t \"System\" -p v \"[KERNEL] Modified: $data_log\"",
        "adb shell 'find /data/system/dropbox -type f -exec rm -f {} +' 2>/dev/null",
        "adb shell touch -t 202305200000 /storage/emulated/0/Android/data/com.dts.freefireth"
    ];

    foreach ($cmds as $cmd) {
        shell_exec($cmd);
    }

    echo $bold.$verde."[✓] Logs antigos forjados com sucesso!\n".$cln;
    echo $amarelo."[i] Data exibida: $data_log\n".$cln;
}

function ofuscarLogs() {
    global $verde, $bold, $cln;

    shell_exec("adb shell logcat -c");

    for ($i = 0; $i < 300; $i++) {
        $fakeTime = date('m-d H:i:s', strtotime("-".rand(2,5)." days ".rand(1,23)." hours ".rand(0,59)." minutes"));
        $msg = "FAKE_LOG_" . substr(md5(rand()), 0, 6);
        shell_exec("adb shell log -t 'SYS_EVENT' -p i '[LOG $fakeTime] $msg'");
    }

    echo $bold.$verde."[✓] Logcat preenchido com ruído falso e logs ofuscados!\n".$cln;
}

function substituirPastaStealth() {
    global $cln, $bold, $vermelho, $verde;

    $origem = "/storage/emulated/0/Pictures/TESTEPINS/PINSSALVOS/com.dts.freefireth";
    $destino = "/storage/emulated/0/Android/data/com.dts.freefireth";

    $timestamps = shell_exec("adb shell find \"$destino\" -type f -exec stat -c '%n %y' {} \;");

    shell_exec("adb shell cp -rf \"$origem/\"* \"$destino/\" 2>/dev/null");

    if (!empty($timestamps)) {
        foreach (explode("\n", $timestamps) as $linha) {
            if (!empty(trim($linha)) && str_contains($linha, " ")) {
                list($file, $date) = explode(" ", $linha, 2);
                shell_exec("adb shell touch -d \"$date\" \"$file\" 2>/dev/null");
            }
        }
    }

    forjarLogsAntigos();

    shell_exec("adb logcat -c");
    ofuscarLogs();

    echo $bold.$verde."[✓] Substituição completa e indetectável!\n".$cln;
}

// Menu
echo $bold.$azul."\n[1] Substituir pasta stealth\n[2] Sair\n".$cln;
$opcao = readline("Selecione: ");

if ($opcao == 1) {
    substituirPastaStealth();
}
?>
