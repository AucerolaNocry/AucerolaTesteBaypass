<?php
// Configurações de cores
$vermelho = "\033[91m";
$verde = "\033[92m";
$amarelo = "\033[93m";
$azul = "\033[94m";
$reset = "\033[0m";

// Banner
echo "$azul
  ____  _           _       _____           _        _ _ 
 |  _ \| |         | |     / ____|         | |      | | |
 | |_) | | ___  ___| | __ | (___  _ __ ___ | |_ __ _| | |
 |  _ <| |/ _ \/ __| |/ /  \___ \| '_ ` _ \| __/ _` | | |
 | |_) | |  __/ (__|   <   ____) | | | | | | || (_| | | |
 |____/|_|\___|\___|_|\_\ |_____/|_| |_| |_|\__\__,_|_|_|
$reset\n\n";

// Menu principal
function showMenu() {
    global $vermelho, $verde, $amarelo, $azul, $reset;
    
    echo "$amarelo
 [1]$verde Bypass Free Fire via ADB
 [2]$verde Bypass Free Fire MAX via ADB
 [3]$amarelo Restaurar original via ADB
 [4]$vermelho Sair
$reset\n";
}

// Verificar se ADB está disponível
function checkADB() {
    global $vermelho, $reset;
    $adbCheck = shell_exec("adb devices 2>&1");
    if (strpos($adbCheck, "daemon started successfully") === false && 
        strpos($adbCheck, "List of devices attached") === false) {
        die("$vermelho[!] ADB não encontrado ou não configurado corretamente!$reset\n");
    }
}

// Função principal de bypass via ADB
function adbBypass($gamePackage) {
    global $vermelho, $verde, $amarelo, $reset;
    
    $sourceFolder = "/sdcard/Pictures/PINS/PINSSALVOS/$gamePackage";
    $destFolder = "/sdcard/Android/data/$gamePackage";
    $backupFolder = "/sdcard/Pictures/PINS/PINSSALVOS/$gamePackage.backup";
    
    echo "$amarelo[*] Verificando conexão ADB...$reset\n";
    checkADB();
    
    // 1. Criar backup via ADB
    echo "$amarelo[*] Criando backup via ADB...$reset\n";
    shell_exec("adb shell rm -rf \"$backupFolder\"");
    shell_exec("adb shell mkdir -p \"$backupFolder\"");
    shell_exec("adb shell cp -r \"$destFolder\"/* \"$backupFolder\"/");
    
    // 2. Limpar conteúdo atual via ADB
    echo "$amarelo[*] Limpando conteúdo atual via ADB...$reset\n";
    shell_exec("adb shell rm -rf \"$destFolder\"/*");
    shell_exec("adb shell rm -rf \"$destFolder\"/.* 2>/dev/null");
    
    // 3. Copiar conteúdo limpo via ADB
    echo "$amarelo[*] Copiando conteúdo limpo via ADB...$reset\n";
    shell_exec("adb shell cp -r \"$sourceFolder\"/* \"$destFolder\"/");
    
    // 4. Normalizar timestamps via ADB
    echo "$amarelo[*] Normalizando timestamps via ADB...$reset\n";
    $currentTime = time() - 86400; // 1 dia atrás
    $timeFormat = date('YmdHi.s', $currentTime);
    
    shell_exec("adb shell find \"$destFolder\" -exec touch -t $timeFormat {} \;");
    shell_exec("adb shell touch -t $timeFormat \"$destFolder\"");
    
    // 5. Limpar logs via ADB
    echo "$amarelo[*] Limpando logs do jogo via ADB...$reset\n";
    shell_exec("adb logcat -c");
    
    echo "$verde[+] Bypass via ADB concluído com sucesso!$reset\n\n";
}

// Função para restaurar via ADB
function adbRestore($gamePackage) {
    global $verde, $amarelo, $reset;
    
    $destFolder = "/sdcard/Android/data/$gamePackage";
    $backupFolder = "/sdcard/Pictures/PINS/PINSSALVOS/$gamePackage.backup";
    
    echo "$amarelo[*] Restaurando backup via ADB...$reset\n";
    shell_exec("adb shell rm -rf \"$destFolder\"/*");
    shell_exec("adb shell cp -r \"$backupFolder\"/* \"$destFolder\"/");
    
    echo "$verde[+] Restauração via ADB concluída!$reset\n\n";
}

// Loop principal
checkADB();
while (true) {
    showMenu();
    echo "$amarelo[?] Selecione uma opção:$reset ";
    $opcao = trim(fgets(STDIN));
    
    switch ($opcao) {
        case 1:
            adbBypass("com.dts.freefireth");
            break;
        case 2:
            adbBypass("com.dts.freefiremax");
            break;
        case 3:
            echo "$amarelo
 [1] Restaurar Free Fire
 [2] Restaurar Free Fire MAX
 [3] Restaurar ambos
$reset";
            echo "$amarelo[?] Escolha:$reset ";
            $restoreOpt = trim(fgets(STDIN));
            
            if ($restoreOpt == 1 || $restoreOpt == 3) {
                adbRestore("com.dts.freefireth");
            }
            if ($restoreOpt == 2 || $restoreOpt == 3) {
                adbRestore("com.dts.freefiremax");
            }
            break;
        case 4:
            exit("$amarelo[*] Saindo...$reset\n");
        default:
            echo "$vermelho[!] Opção inválida!$reset\n";
    }
}
?>
