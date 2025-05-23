<?php
date_default_timezone_set("America/Sao_Paulo");

// Funções de cor
$azul = "\033[1;34m";
$verde = "\033[1;32m";
$vermelho = "\033[1;31m";
$amarelo = "\033[1;33m";
$branco = "\033[1;37m";
$reset = "\033[0m";

// Função auxiliar
function executar($comando) {
    return shell_exec($comando);
}

echo "{$azul}[+] Anti-KellerSS — Inicializando bypass de horário...{$reset}\n";

// Passo 1: desativa ajuste automático
echo "{$amarelo}[*] Desativando horário automático...{$reset}\n";
executar("adb shell settings put global auto_time 0 > /dev/null 2>&1");

// Passo 2: aplica hora manual falsa (30 minutos atrás)
$agora = new DateTime();
$falsa = $agora->modify('-30 minutes');
$dataFake = $falsa->format('Ymd.His');
echo "{$verde}[*] Hora falsa aplicada: {$falsa->format('d/m/Y H:i:s')}{$reset}\n";
executar("adb shell su -c 'date -s {$dataFake}' || adb shell date -s {$dataFake}");

// Passo 3: simula uso real com logs legítimos
echo "{$amarelo}[*] Rodando ações para gerar logs...{$reset}\n";
executar("adb shell input keyevent 3"); // Voltar à home
sleep(1);
executar("adb shell am start -a android.intent.action.VIEW -d https://www.google.com");
sleep(2);

// Passo 4: exporta logcat e limpa real
$logPath = "/sdcard/log_falso_keller.log";
executar("adb logcat -d > $logPath");
executar("adb logcat -c");
echo "{$verde}[✓] Log original salvo e logcat limpo com sucesso!{$reset}\n";

// Passo 5: reativa hora automática
echo "{$amarelo}[*] Reativando horário automático...{$reset}\n";
executar("adb shell settings put global auto_time 1 > /dev/null 2>&1");

// Passo 6: continuar com substituição camuflada
$origem = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
$destino = "/sdcard/Android/data/com.dts.freefireth";

echo "{$azul}[+] Iniciando substituição furtiva da pasta com.dts.freefireth...{$reset}\n";

// Gera lista de arquivos
$lista = executar("find \"$origem\" -type f");
$arquivos = explode("\n", trim($lista));

foreach ($arquivos as $arquivo) {
    if (empty($arquivo)) continue;
    $relativo = str_replace($origem . "/", "", $arquivo);
    $destinoFinal = $destino . "/" . $relativo;

    // Cria subpasta no destino
    $pasta = dirname($destinoFinal);
    executar("adb shell mkdir -p \"$pasta\"");

    // Copia conteúdo via cat para evitar mudança no inode (menos suspeito)
    executar("adb shell 'cat > \"$destinoFinal\"' < \"$arquivo\"");

    // Altera timestamps com touch antigo
    $ts = $falsa->format("YmdHi.00");
    executar("adb shell touch -m -t $ts \"$destinoFinal\" 2>/dev/null");
    echo "{$verde}[*] Copiado: $relativo{$reset}\n";
}

echo "{$verde}\n[✓] Pasta substituída e camuflada com hora falsa.\n";
echo "[#] Bypass horário + substituição executado com sucesso!\n{$reset}";
