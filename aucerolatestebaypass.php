<?php
// Cores para o terminal
$branco = "\e[97m";
$vermelho = "\e[91m";
$verde = "\e[92m";
$azul = "\e[34m";
$amarelo = "\e[93m";
$cln = "\e[0m";
$bold = "\e[1m";

function keller_banner() {
    echo "\n\e[36m
    ███████╗██╗  ██╗███████╗██████╗ ███████╗
    ██╔════╝██║  ██║██╔════╝██╔══██╗██╔════╝
    ███████╗███████║█████╗  ██████╔╝███████╗
    ╚════██║██╔══██║██╔══╝  ██╔══██╗╚════██║
    ███████║██║  ██║███████╗██║  ██║███████║
    ╚══════╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚══════╝\n\n{$cln}";
}

function substituirPastaFFStealth() {
    global $cln, $bold, $vermelho, $verde, $azul, $amarelo;

    echo $bold . $azul . "\n[+] Iniciando substituição stealth...\n" . $cln;

    // Caminho corrigido sem espaços
    $origem = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
    $destino = "/storage/emulated/0/Android/data/com.dts.freefireth";

    // Verificação ADB
    if (empty(shell_exec("adb devices 2>&1 | grep device"))) {
        echo $bold . $vermelho . "[!] Conecte o dispositivo via ADB primeiro!\n" . $cln;
        return;
    }

    // Verifica se as pastas existem
    if (!file_exists($origem)) {
        echo $bold . $vermelho . "[!] Pasta de origem não encontrada!\n" . $cln;
        return;
    }

    // Backup de timestamps
    echo $amarelo . "[+] Backup de timestamps...\n";
    $timestamps = shell_exec("adb shell find \"$destino\" -type f -exec stat -c '%n %y' {} \;");

    // Substituição
    echo $amarelo . "[+] Substituindo arquivos...\n";
    shell_exec("adb shell cp -rf \"$origem/\"* \"$destino/\" 2>/dev/null");

    // Restaura timestamps
    echo $amarelo . "[+] Restaurando timestamps...\n";
    foreach (explode("\n", $timestamps) as $linha) {
        if (!empty($linha)) {
            list($arquivo, $data) = explode(" ", $linha, 2);
            shell_exec("adb shell touch -d \"$data\" \"$arquivo\" 2>/dev/null");
        }
    }

    // Limpeza
    shell_exec("adb logcat -c");
    echo $bold . $verde . "[+] Substituição concluída com sucesso!\n" . $cln;
}

// Menu principal
system("clear");
keller_banner();

echo $bold . $azul . "
[1] Substituir pasta Free Fire
[2] Sair
\n" . $cln;

echo $bold . "Selecione uma opção: " . $cln;
$opcao = trim(fgets(STDIN));

switch ($opcao) {
    case 1:
        substituirPastaFFStealth();
        break;
    case 2:
        exit;
    default:
        echo $vermelho . "Opção inválida!\n";
}
?>
