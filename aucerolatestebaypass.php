<?php

// Cores
$verde = "\033[1;32m";
$azul = "\033[1;34m";
$vermelho = "\033[1;31m";
$amarelo = "\033[1;33m";
$reset = "\033[0m";

echo "{$azul}[+] Anti-KellerSS - Camuflagem Manual de Horário...{$reset}\n";
echo "{$amarelo}[*] Pausando para você ajustar a hora MANUALMENTE no dispositivo!{$reset}\n";
echo "{$azul}[#] Após ajustar a hora falsa (30min antes da partida), pressione ENTER aqui...{$reset}\n";
fgets(STDIN);

// Simula atividade normal no sistema
echo "{$amarelo}[*] Simulando atividade legítima...{$reset}\n";
shell_exec("adb shell input keyevent 3");
shell_exec("adb shell am start -a android.intent.action.VIEW -d https://www.google.com");
sleep(3);

// Limpa o logcat
echo "{$amarelo}[*] Limpando logcat para remover evidências...{$reset}\n";
shell_exec("adb logcat -c");

// Restaura horário automático
echo "{$amarelo}[i] Reativando sincronização automática de data/hora...{$reset}\n";
shell_exec("adb shell settings put global auto_time 1");

echo "{$verde}[✓] Log limpo e hora restaurada com sucesso. Pronto para o teste com o scanner.{$reset}\n";
