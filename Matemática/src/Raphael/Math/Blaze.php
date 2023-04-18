<?php

namespace Raphael\Math;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Player;

class Blaze extends PluginBase implements Listener {

    public bool $mathInProgress = false;
    public int $mathResult;
    public int $mathStartTime;

    public function onEnable() {
        $this->saveResource("config.yml");
        $config = yaml_parse_file($this->getDataFolder() . "config.yml");
        $this->tempo = $config["Tempo"];
        $this->economyapi = $config["EconomyAPI"];
        $this->economyapidinheiro = $config["Recompensa"];
        $this->logs = $config["Logs Console"];
        if ($this->economyapi == "sim") {
            $this->getLogger()->warning("§aEconomyAPI habilitado. Habilitando a recompensa.");
            if ($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") === null) {
                $this->getLogger()->warning("§cPlugin EconomyAPI não encontrado. Desabilitando a recompensa.");
                $this->economyapi = "não";
            }
        } else {
            $this->getLogger()->warning("§cEconomyAPI desabilitado. Recompensa desativada.");
        }
            $this->chave = $config["Chave"];
    $actual = $this->getMain();
    if ($this->chave !== $actual) {
        $this->getLogger()->warning("§cChave inválida. O plugin foi desativado.");
      $this->getServer()->getPluginManager()->disablePlugin($this);
       return;
    }   
        $this->getLogger()->info("§dMath habilitado! @ Raphael S.");  
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new MathTask($this, $this), 20 * 60 * $this->tempo);

    }
       public function getMain() {
    return md5_file(__FILE__);
}
    
public function usarEconomyAPI(Player $player) {
    if ($this->economyapi == "sim" && $this->getServer()->getPluginManager()->getPlugin("EconomyAPI") !== null) {
        $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI"); 
        $economy->addMoney($player, $this->economyapidinheiro);
    if ($this->logs == "sim") {
        $this->getLogger()->warning("§bRecompensa de " . $this->economyapidinheiro . " adicionada a " . $player->getName());
    }
  }
}

public function onChat(PlayerChatEvent $event) {
    $player = $event->getPlayer();
    $message = $event->getMessage();

    if ($this->mathInProgress && is_numeric($message)) {
        $message = str_replace(',', '.', $message); // Substitui vírgulas por pontos
        if ($message == $this->mathResult) {
            $player->sendMessage("§aParabéns, você acertou a conta e foi recompensado.");
            $this->mathInProgress = false;
            $this->getServer()->broadcastMessage("§bEVENTO §fMATEMÁTICO \n§7A conta foi resolvida corretamente por " . $player->getName() .". O resultado era " . $this->mathResult);
        }
        $this->usarEconomyAPI($player);
    }
}




public function startNewMathTask() {
$num1 = rand(-100, 100);
$num2 = rand(-100, 100);
$operator = rand(0, 3);
if ($operator == 0) {
$this->mathResult = $num1 + $num2;
$this->getServer()->broadcastMessage("§e§bEVENTO §fMATEMÁTICO \n§7Qual é o resultado de $num1 + $num2? Responda com um número.");
} else if ($operator == 1) {
$this->mathResult = $num1 - $num2;
$this->getServer()->broadcastMessage("§bEVENTO §fMATEMÁTICO \n§7Qual é o resultado de $num1 - $num2? Responda com um número.");
} else if ($operator == 2) {
$this->mathResult = $num1 * $num2;
$this->getServer()->broadcastMessage("§bEVENTO §fMATEMÁTICO \n§7Qual é o resultado de $num1 x $num2? Responda com um número.");
} else {
$this->mathResult = round($num1 / $num2, 2);
$this->getServer()->broadcastMessage("§bEVENTO §fMATEMÁTICO \n§7Qual é o resultado de $num1 ÷ $num2? Responda com um número inteiro.");
}
$this->mathInProgress = true;
$this->mathStartTime = time();
    if ($this->logs == "sim") {
    $this->getLogger()->info("§fResultado: " . $this->mathResult);
}
}

    public function isMathInProgress(): bool {
        return $this->mathInProgress;
    }
}

class MathTask extends Task {

    private Blaze $owner;
    private int $lastMathSentTime;

    public function __construct(Blaze $owner) {
        $this->owner = $owner;
        $this->lastMathSentTime = 0;
    }

    public function onRun(int $currentTick) {
        $plugin = $this->owner;
        if (!$plugin->isMathInProgress()) {
            $plugin->startNewMathTask();
            $this->lastMathSentTime = time();
        } else if ((time() - $this->lastMathSentTime) > 1) {
            $plugin->getServer()->broadcastMessage("§cNinguém respondeu corretamente à conta a tempo. A próxima conta será enviada em alguns minutos.");
            $plugin->mathInProgress = false;
        }
    }
}
