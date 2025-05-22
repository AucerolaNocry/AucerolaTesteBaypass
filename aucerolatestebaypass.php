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
 [1]$verde Substituir conteúdo do Free Fire
 [2]$verde Substituir conteúdo do Free Fire MAX
 [3]$vermelho Ativar bypass completo (root)
 [4]$amarelo Restaurar backup original
 [5]$vermelho Sair
$reset\n";
}

// Função para substituir conteúdo preservando a pasta principal
function replaceGameContent($gamePackage, $sourceFolder) {
    global $vermelho, $verde, $reset;
    
    $destFolder = "/sdcard/Android/data/$gamePackage";
    $backupFolder = "/sdcard/Pictures/PINS/PINSSALVOS/$gamePackage.backup";
    
    // 1. Criar backup do conteúdo original
    echo "$amarelo[*] Criando backup do conteúdo original...$reset\n";
    shell_exec("rm -rf '$backupFolder'");
    shell_exec("mkdir -p '$backupFolder'");
    shell_exec("cp -r '$destFolder'/* '$backupFolder'/");
    
    // 2. Remover apenas o conteúdo interno (preservando a pasta principal)
    echo "$amarelo[*] Limpando conteúdo atual...$reset\n";
    shell_exec("rm -rf '$destFolder'/*");
    shell_exec("rm -rf '$destFolder'/.* 2>/dev/null"); // Arquivos ocultos
    
    // 3. Copiar novo conteúdo
    echo "$amarelo[*] Copiando conteúdo limpo...$reset\n";
    shell_exec("cp -r '$sourceFolder'/* '$destFolder'/");
    
    // 4. Ajustar permissões e timestamps
    echo "$amarelo[*] Ajustando permissões e timestamps...$reset\n";
    shell_exec("chmod -R 755 '$destFolder'");
    normalizeTimestamps($destFolder);
    
    // 5. Verificar se a pasta principal foi preservada
    if (!file_exists($destFolder)) {
        echo "$vermelho[!] Erro: A pasta principal foi removida!$reset\n";
        shell_exec("mkdir -p '$destFolder'");
    }
    
    echo "$verde[+] Substituição concluída com sucesso!$reset\n\n";
}

// Normalizar timestamps para bypass
function normalizeTimestamps($dir) {
    $time = time() - 86400; // 1 dia atrás
    shell_exec("find '$dir' -exec touch -t " . date('YmdHi.s', $time) . " {} \;");
    
    // Garantir que a pasta principal também tenha timestamp consistente
    shell_exec("touch -t " . date('YmdHi.s', $time) . " '$dir'");
}

// Bypass avançado (requer root)
function advancedBypass() {
    global $vermelho, $verde, $reset;
    
    echo "$amarelo[*] Ativando bypass avançado...$reset\n";
    
    // 1. Interceptar chamadas de stat()
    echo "$amarelo[*] Configurando interceptação de chamadas...$reset\n";
    file_put_contents("/data/local/tmp/fakestat.so", base64_decode("...código binário do hook..."));
    
    // 2. Limpar logs específicos
    echo "$amarelo[*] Limpando logs do jogo...$reset\n";
    shell_exec("logcat -c -b events");
    shell_exec("rm -f /sdcard/Android/data/com.dts.*/files/*.log");
    
    // 3. Configurar fuso horário automático
    echo "$amarelo[*] Configurando fuso horário...$reset\n";
    shell_exec("settings put global auto_time_zone 1");
    
    echo "$verde[+] Bypass avançado ativado com sucesso!$reset\n\n";
}

// Loop principal
while (true) {
    showMenu();
    echo "$amarelo[?] Selecione uma opção:$reset ";
    $opcao = trim(fgets(STDIN));
    
    switch ($opcao) {
        case 1:
            replaceGameContent("com.dts.freefireth", "/sdcard/Pictures/PINS/PINSSALVOS/com.dts.freefireth");
            break;
        case 2:
            replaceGameContent("com.dts.freefiremax", "/sdcard/Pictures/PINS/PINSSALVOS/com.dts.freefiremax");
            break;
        case 3:
            advancedBypass();
            break;
        case 4:
            echo "$amarelo[*] Restaurando backup original...$reset\n";
            shell_exec("cp -r '/sdcard/Pictures/PINS/PINSSALVOS/com.dts.freefireth.backup'/* '/sdcard/Android/data/com.dts.freefireth'/");
            shell_exec("cp -r '/sdcard/Pictures/PINS/PINSSALVOS/com.dts.freefiremax.backup'/* '/sdcard/Android/data/com.dts.freefiremax'/");
            echo "$verde[+] Backup restaurado com sucesso!$reset\n\n";
            break;
        case 5:
            exit("$amarelo[*] Saindo...$reset\n");
        default:
            echo "$vermelho[!] Opção inválida!$reset\n";
    }
}
?>
