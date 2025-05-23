<?php
// Configuração de cores
$vermelho = "\e[91m";
$verde = "\e[92m";
$azul = "\e[34m";
$amarelo = "\e[93m";
$cln = "\e[0m";
$bold = "\e[1m";

function forjarLogsAntigos() {
    global $cln, $bold, $vermelho, $verde;
    
    // Data fictícia (3 dias atrás)
    $data_log = date('m-d H:i:s', strtotime('-3 days'));
    
    // Comandos para forjar logs
    $cmds = [
        // 1. Cria log fake no sistema
        "adb shell 'echo \"[FAKE] System event at $data_log\" > /data/local/tmp/.system_log'",
        
        // 2. Injeta no logcat com timestamp manipulado
        "adb shell log -t \"System\" -p v \"[KERNEL] Modified: $data_log\"",
        
        // 3. Corrompe logs reais (requer root)
        "adb shell 'find /data/system/dropbox -type f -exec rm -f {} +' 2>/dev/null",
        
        // 4. Altera timestamp da pasta
        "adb shell touch -t 202305200000 /storage/emulated/0/Android/data/com.dts.freefireth"
    ];
    
    foreach ($cmds as $cmd) {
        shell_exec($cmd);
    }
    
    echo $bold.$verde."[✓] Logs antigos forjados com sucesso!\n".$cln;
    echo $amarelo."[i] Data exibida: $data_log\n".$cln;
}

function substituirPastaStealth() {
    global $cln, $bold, $vermelho, $verde;
    
    $origem = "/storage/emulated/0/Pictures/TESTEPINS/PINSSALVOS/com.dts.freefireth";
    $destino = "/storage/emulated/0/Android/data/com.dts.freefireth";
    
    // 1. Backup de timestamps originais
    $timestamps = shell_exec("adb shell find \"$destino\" -type f -exec stat -c '%n %y' {} \;");
    
    // 2. Substituição silenciosa
    shell_exec("adb shell cp -rf \"$origem/\"* \"$destino/\" 2>/dev/null");
    
    // 3. Restaura timestamps
    foreach (explode("\n", $timestamps) as $linha) {
        if (!empty($linha)) {
            list($file, $date) = explode(" ", $linha, 2);
            shell_exec("adb shell touch -d \"$date\" \"$file\" 2>/dev/null");
        }
    }
    
    // 4. Forja logs
    forjarLogsAntigos();
    
    // 5. Limpeza final
    shell_exec("adb logcat -c");
    echo $bold.$verde."[✓] Substituição completa e indetectável!\n".$cln;
}

// Menu
echo $bold.$azul."\n[1] Substituir pasta stealth\n[2] Sair\n".$cln;
$opcao = readline("Selecione: ");

if ($opcao == 1) {
    substituirPastaStealth();
}
?>
