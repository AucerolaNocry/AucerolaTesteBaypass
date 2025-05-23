function substituirPastaFFStealth() {
    global $cln, $bold, $vermelho, $fverde, $azul;

    echo $bold . $azul . "\n[+] Iniciando substituição indetectável...\n" . $cln;

    // Pasta de origem (modificada)
    $origem = "/storage/emulated/0/Pictures/TESTE/PINS/PINSSALVOS/com.dts.freefireth";
    // Pasta de destino (original do jogo)
    $destino = "/storage/emulated/0/Android/data/com.dts.freefireth";

    // 1. Backup do timestamp original
    $timestamp = shell_exec("adb shell stat -c '%y' " . escapeshellarg($destino) . " 2>&1");
    if (strpos($timestamp, 'No such file') !== false) {
        echo $bold . $vermelho . "[!] Pasta de destino não encontrada!\n" . $cln;
        return;
    }

    // 2. Substituição arquivo por arquivo
    $comando = "adb shell find " . escapeshellarg($origem) . " -type f | while read file; do
        dest_file=\"" . $destino . "/\${file#*/com.dts.freefireth/}\";
        cp \"\$file\" \"\$dest_file\" 2>/dev/null;
    done";
    shell_exec($comando);

    // 3. Restaurar timestamp
    shell_exec("adb shell touch -d " . escapeshellarg(trim($timestamp)) . " " . escapeshellarg($destino));

    // 4. Limpar logs
    shell_exec("adb logcat -c");

    echo $bold . $fverde . "[+] Substituição concluída sem rastros.\n" . $cln;
}
