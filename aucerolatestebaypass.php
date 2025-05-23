<?php

// Cores
$verde = "\033[1;32m";
$azul = "\033[1;34m";
$vermelho = "\033[1;31m";
$amarelo = "\033[1;33m";
$reset = "\033[0m";

echo "{$azul}[+] Iniciando ajuste de horário Anti-KellerSS...{$reset}\n";

// Passo 1: Desativar horário automático
echo "{$amarelo}[i] Desativando horário automático do sistema...{$reset}\n";
shell_exec("adb shell settings put global auto_time 0");

// Passo 2: Calcular horário falso (30 min atrás)
date_default_timezone_set("America/Sao_Paulo");
$horaFalsa = date("Ymd.Hi", strtotime("-30 minutes"));
echo "{$verde}[✓] Hora falsa gerada: {$horaFalsa} (formato Ymd.Hi){$reset}\n";

// Passo 3: Aplicar data falsa
echo "{$amarelo}[*] Aplicando hora falsa ao dispositivo via ADB...{$reset}\n";
shell_exec("adb shell date -s $horaFalsa");

// Passo 4: Simular atividade normal para preencher o logcat
echo "{$amarelo}[*] Simulando atividade comum no sistema...{$reset}\n";
shell_exec("adb shell input keyevent 3");
shell_exec("adb shell am start -a android.intent.action.VIEW -d https://www.google.com");

// Pausa breve para gerar logs
sleep(3);

// Passo 5: Limpar logcat
echo "{$amarelo}[*] Limpando logcat para ocultar alterações de horário...{$reset}\n";
shell_exec("adb logcat -c");

// Passo 6: Restaurar horário automático
echo "{$amarelo}[i] Reativando sincronização automática de data/hora...{$reset}\n";
shell_exec("adb shell settings put global auto_time 1");

echo "{$verde}[✓] Ajuste de horário concluído e camuflado com sucesso!{$reset}\n";
echo "{$azul}[#] Pronto para rodar o KellerSS e validar o teste.{$reset}\n";
