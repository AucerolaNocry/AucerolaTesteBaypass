function ofuscarLogs() {
    // Preenche o logcat com dados aleatórios
    shell_exec("adb shell logcat -c && for i in {1..500}; do logcat -s 'SYS_' -v brief -t ".date('m-d')." ".rand(10,23).":".rand(10,59).":".rand(10,59)." FAKE_LOG: System_Event_".md5(rand())."; done");
    
    // Sobrescreve os logs atuais
    shell_exec("adb shell logcat -b all -c");
    
    // Cria logs falsos com timestamps antigos
    $old_date = date('m-d H:i:s', strtotime('-3 days'));
    shell_exec("adb shell am broadcast -a android.intent.action.BOOT_COMPLETED --es 'TIMESTAMP' '$old_date'");
}
