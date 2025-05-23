<?php

// Script PHP para bypass de tempo no Free Fire (via Termux)
// Autor: [Seu Nome]
// Data: 2025

// Função para executar comandos ADB
function exec_adb($cmd) {
    return shell_exec("adb shell $cmd");
}

// [1] Desativa o horário automático
exec_adb('settings put global auto_time 0');

// [2] Abre as configurações de data/hora
shell_exec('am start -a android.settings.DATE_SETTINGS');

echo "✅ Altere a data/hora MANUALMENTE nas configurações do Android.\n";
echo "🛑 Pressione ENTER aqui no Termux quando terminar...";
trim(fgets(STDIN)); // Aguarda input do usuário

// [3] Reativa o horário automático
exec_adb('settings put global auto_time 1');

// [4] Limpa rastros reiniciando serviços
exec_adb('stop logd');
exec_adb('start logd');

// [5] Cria logs fictícios para ofuscar
for ($i = 0; $i < 200; $i++) {
    $fake_time = date('m-d H:i:s', strtotime('-1 hour'));
    exec_adb("log -t SYSTEM -p d 'Processo em background: $fake_time'");
}

echo "🛡️ Bypass completo! Horário automático reativado sem rastros.\n";
echo "⚠️ Lembre-se: Jogue em modo avião para evitar detecção.\n";

?>
