<?php

// Script StealthTime para Free Fire (PHP + Termux)
// Gerencia data/hora E fuso horário automaticamente
// Autor: [Seu Nome] | Atualizado: 2025

function exec_adb($cmd) {
    return shell_exec("adb shell $cmd 2>&1");
}

// [1] Desativa sincronizações automáticas
exec_adb('settings put global auto_time 0');
exec_adb('settings put global auto_time_zone 0');

// [2] Abre menu de configurações
shell_exec('am start -a android.settings.DATE_SETTINGS');

echo "🔧 Altere MANUALMENTE:\n";
echo "1. Data/Hora\n";
echo "2. Fuso Horário (America/Sao_Paulo)\n";
echo "✅ Pressione ENTER quando concluir...";
fgets(STDIN);

// [3] Reativa sincronização automática
exec_adb('settings put global auto_time 1');
exec_adb('settings put global auto_time_zone 1');

// [4] Ofuscação avançada
exec_adb('stop logd');
exec_adb('start logd');

// Gera logs plausíveis
$formats = [
    'SystemEvent: Service started at %s',
    'BackgroundProcess: Scheduled task %s',
    'TimeSync: NTP update %s'
];

for ($i = 0; $i < 300; $i++) {
    $fakeDate = date('m-d H:i:s', strtotime("-".rand(1,72)." hours"));
    $logMsg = sprintf($formats[array_rand($formats)], $fakeDate);
    exec_adb("log -t 'KERNEL' -p d '$logMsg'");
}

// Verificação final
$autoTime = trim(exec_adb('settings get global auto_time'));
$autoZone = trim(exec_adb('settings get global auto_time_zone'));

echo "\n🛡️ Configuração Stealth Ativada:\n";
echo "• Sincronização de Horário: " . ($autoTime == 1 ? "ATIVO" : "INATIVO") . "\n";
echo "• Fuso Automático: " . ($autoZone == 1 ? "ATIVO" : "INATIVO") . "\n";
echo "⚠️ Dica: Reinicie o dispositivo para sincronização total\n";

?>
