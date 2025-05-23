function ofuscarLogs() {
    global $verde, $bold, $cln;

    // Limpa logs antigos
    shell_exec("adb shell logcat -c");

    // Gera entradas falsas de log com mensagem aleatória
    for ($i = 0; $i < 300; $i++) {
        $fakeTime = date('m-d H:i:s', strtotime("-".rand(2,5)." days ".rand(1,23)." hours ".rand(0,59)." minutes"));
        $msg = "FAKE_LOG_" . substr(md5(rand()), 0, 6);
        shell_exec("adb shell log -t 'SYS_EVENT' -p i '[LOG $fakeTime] $msg'");
    }

    // Confirmação final
    echo $bold.$verde."[✓] Logcat preenchido com ruído falso e logs ofuscados!\n".$cln;
        }
