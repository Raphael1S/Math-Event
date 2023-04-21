<?php

function UpdateVe($plugin) {
        $url = 'https://github.com/Raphael1S/Math-Event/releases/download/Versão/version.txt';
        $options = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            ),
        );
        $context = stream_context_create($options);
        $content = file_get_contents($url, false, $context);
        $destination = $plugin->getDataFolder() . 'version.txt';
        file_put_contents($destination, $content);

        $local_version = $plugin->getDescription()->getVersion();
        $remote_version = '';
        $remote_link = '';

        if ($content !== false) {
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $parts = explode(":", $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    if ($key === 'ultima_versão') {
                        $remote_version = $value;
                    } elseif ($key === 'link_ultima_versão') {
                        $remote_link = $value;
                    }
                }
            }
        }

        if ($local_version == $remote_version) {

         } else {
        $plugin->getLogger()->warning("§dHá uma nova versão disponível: $remote_version. Baixe-a em: $remote_link");
    }
 }
