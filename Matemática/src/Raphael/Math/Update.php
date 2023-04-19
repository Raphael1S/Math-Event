<?php

function atualizarPlugin($plugin, $pluginName, $pluginVersion) {
    $githubUrl = "https://api.github.com/repos/Raphael1S/Math-Event/releases/latest";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $githubUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:102.0) Gecko/20100101 Firefox/102.0");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    curl_close($ch);

if ($response === false) {
        // Não foi possível obter as informações do GitHub
        return;
    }

    $data = json_decode($response, true);
    $latestVersion = $data['tag_name'];

if ($latestVersion !== $pluginVersion) {
        // Uma nova versão está disponível
        $downloadUrl = $data['assets'][0]['browser_download_url'];
        $downloadMessage = "§cUma nova versão do plugin está disponível. Faça o download em $downloadUrl";
        $plugin->getLogger()->warning($downloadMessage);
        $plugin->getServer()->getPluginManager()->disablePlugin($plugin);
    }
}
