<?php
// Configurações básicas
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cores para terminal
define('RED', "\033[91m");
define('GREEN', "\033[92m");
define('YELLOW', "\033[93m");
define('BLUE', "\033[94m");
define('RESET', "\033[0m");

// Caminhos base
define('BASE_PATH', '/storage/emulated/0');
define('BACKUP_PATH', BASE_PATH . '/FF_BACKUP');
define('CLEAN_FILES_PATH', BASE_PATH . '/Pictures/PINS/PINSSALVOS');
define('GAME_PATH', BASE_PATH . '/Android/data');

// Banner
function showBanner() {
    echo BLUE . "
   ___  _____  ____  _   _ 
  / _ \|  ___|| __ )| | | |
 | | | | |_   |  _ \| | | |
 | |_| |  _|  | |_) | |_| |
  \___/|_|    |____/ \___/ 

" . RESET . "\n";
}

// Verificar ADB
function checkADB() {
    $output = shell_exec("adb devices 2>&1");
    if (strpos($output, "device") === false) {
        die(RED . "[!] Conecte um dispositivo via ADB primeiro!\n" . RESET);
    }
    return true;
}

// Menu principal
function mainMenu() {
    showBanner();
    echo YELLOW . "
 [1] Bypass Free Fire
 [2] Bypass Free Fire MAX
 [3] Restaurar Backup
 [4] Sair
" . RESET;

    echo GREEN . "\n [?] Selecione: " . RESET;
    $option = trim(fgets(STDIN));

    return $option;
}

// Executar bypass
function runBypass($game) {
    checkADB();

    $gamePath = GAME_PATH . "/$game";
    $cleanPath = CLEAN_FILES_PATH . "/$game";
    $backupPath = BACKUP_PATH . "/$game";

    echo YELLOW . "\n [+] Iniciando bypass para $game..." . RESET;

    // 1. Backup
    echo YELLOW . "\n [*] Criando backup..." . RESET;
    shell_exec("adb shell mkdir -p '$backupPath'");
    shell_exec("adb shell cp -r '$gamePath' '$backupPath'");

    // 2. Limpar dados
    echo YELLOW . "\n [*] Limpando dados..." . RESET;
    shell_exec("adb shell rm -rf '$gamePath/*'");

    // 3. Restaurar dados limpos
    echo YELLOW . "\n [*] Restaurando dados limpos..." . RESET;
    shell_exec("adb shell cp -r '$cleanPath/*' '$gamePath/'");

    // 4. Ajustar timestamps
    echo YELLOW . "\n [*] Ajustando timestamps..." . RESET;
    $time = date('YmdHi.s', time() - 86400);
    shell_exec("adb shell 'find $gamePath -exec touch -t $time {} +'");

    echo GREEN . "\n [+] Bypass concluído com sucesso!\n" . RESET;
}

// Restaurar backup
function restoreBackup($game) {
    checkADB();

    $gamePath = GAME_PATH . "/$game";
    $backupPath = BACKUP_PATH . "/$game";

    echo YELLOW . "\n [+] Restaurando $game..." . RESET;
    shell_exec("adb shell rm -rf '$gamePath'");
    shell_exec("adb shell cp -r '$backupPath' '$gamePath'");
    echo GREEN . "\n [+] Restauração concluída!\n" . RESET;
}

// Loop principal
while (true) {
    $option = mainMenu();

    switch ($option) {
        case '1':
            runBypass('com.dts.freefireth');
            break;
        case '2':
            runBypass('com.dts.freefiremax');
            break;
        case '3':
            echo YELLOW . "\n [1] Free Fire\n [2] Free Fire MAX\n [3] Ambos\n" . RESET;
            echo GREEN . " [?] Escolha: " . RESET;
            $restoreOpt = trim(fgets(STDIN));

            if ($restoreOpt == '1' || $restoreOpt == '3') {
                restoreBackup('com.dts.freefireth');
            }
            if ($restoreOpt == '2' || $restoreOpt == '3') {
                restoreBackup('com.dts.freefiremax');
            }
            break;
        case '4':
            exit(GREEN . "\n [+] Saindo...\n" . RESET);
        default:
            echo RED . "\n [!] Opção inválida!\n" . RESET;
    }
}
?>
