
<?php

// Cores para output no terminal
$verde = "\033[1;32m";
$vermelho = "\033[1;31m";
$amarelo = "\033[1;33m";
$azul = "\033[1;34m";
$reset = "\033[0m";

// Passo 1: Desativa ajuste automático de data/hora
echo "{$azul}[+] Desativando ajuste automático de data/hora...{$reset}\n";
shell_exec("adb shell settings put global auto_time 0");
shell_exec("adb shell settings put global auto_time_zone 0");

// Passo 2: Abre tela de configuração de hora para o usuário alterar manualmente
echo "{$amarelo}[!] Altere manualmente a hora do dispositivo para ANTES da partida.{$reset}\n";
echo "{$amarelo}[!] Pressione ENTER após alterar a hora.{$reset}\n";
shell_exec("adb shell am start -a android.settings.DATE_SETTINGS");
readline("[ENTER] Hora alterada? Continuando...\n");

// Passo 3: Salva logcat atual (antes da alteração da hora voltar ao normal)
echo "{$azul}[+] Salvando logcat atual...{$reset}\n";
$logfile = "/data/data/com.termux/files/home/log_antigo.txt";
shell_exec("adb logcat -d > $logfile");

// Passo 4: Reativa hora automática
echo "{$azul}[+] Reativando ajuste automático de hora...{$reset}\n";
shell_exec("adb shell settings put global auto_time 1");
shell_exec("adb shell settings put global auto_time_zone 1");

// Passo 5: Limpa o logcat para esconder alterações de hora
echo "{$azul}[+] Limpando logcat...{$reset}\n";
shell_exec("adb logcat -c");

// Passo 6: Simula reaplicação de logs antigos para não deixar o logcat vazio
echo "{$verde}[✓] Logcat limpo. Aplicando logs visuais antigos...{$reset}\n";
shell_exec("adb shell log -t system \"boot_completed\"");
shell_exec("adb shell log -t app_process \"app: com.dts.freefireth iniciado\"");
shell_exec("adb shell log -t system \"modo avião desativado\"");

// Final







echo "\n{$verde}[✓] Processo concluído. KellerSS não deve detectar alterações.{$reset}\n";
echo "{$amarelo}[#] Log original salvo em: $logfile{$reset}\n";
