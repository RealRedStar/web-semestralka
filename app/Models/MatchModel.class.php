<?php

namespace redstar\Models;

/**
 * Třída MatchModel reprezentuje kampaň
 * Obsahuje statické metody pro manipulaci s kampani
 */
class MatchModel implements \JsonSerializable
{
    /** @var int Id kampaně */
    private int $id;
    /** @var UserModel majitel kampaně  */
    private UserModel $owner;
    /** @var string název kampaně */
    private string $name;
    /** @var string popis kampaně */
    private string $description;
    /** @var int maximální počet hráčů */
    private int $maxPlayers;
    /** @var string datum vytvoření kampaně */
    private string $dateCreated;
    /** @var string datum zahájení kampaně */
    private string $dateStarting;
    /** @var string datum ukončení kampaně */
    private string $dateFinished;
    /** @var string kód pro připojení do hry */
    private string $joinCode;
    /** @var string heslo pro připojení do hry */
    private string $joinPassword;
    /** @var string název obrázku kampaně */
    private string $imageName;
    /** @var array hráči kampaně */
    private array $players;
    /** @var array vyhození hráči kampaně */
    private array $bannedPlayers;

    /**
     * @param int $id - ID kampaně
     * @param UserModel $owner - majitel
     * @param string $name - název
     * @param string $description - popis
     * @param int $maxPlayers - max. počet hráčů
     * @param string $dateCreated - datum vytvoření
     * @param string $dateStarting - datum zahájení
     * @param string $dateFinished - datum ukončení
     * @param string $joinCode - kód pro připojení
     * @param string $joinPassword - heslo pro připojení
     * @param string $imageName - název obrázku
     * @param array $players - hráči
     * @param array $bannedPlayers - vyhození hráči
     */
    public function __construct(int $id, UserModel $owner, string $name, string $description, int $maxPlayers, string $dateCreated, string $dateStarting, string $dateFinished, string $joinCode, string $joinPassword, string $imageName, array $players, array $bannedPlayers)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
        $this->description = $description;
        $this->maxPlayers = $maxPlayers;
        $this->dateCreated = $dateCreated;
        $this->dateStarting = $dateStarting;
        $this->dateFinished = $dateFinished;
        $this->joinCode = $joinCode;
        $this->joinPassword = $joinPassword;
        $this->imageName = $imageName;
        $this->players = $players;
        $this->bannedPlayers = $bannedPlayers;
    }

    /**
     * Vrátí kampaň dle ID, pokud existuje
     * @param int $id - ID kampaně
     * @return MatchModel|null - kampaň, pokud existuje
     */
    public static function getMatchById(int $id): ?MatchModel {
        $db = DatabaseModel::getDatabaseModel();
        $purifier = \HTMLPurifier::getInstance();

        // Přes HTMLPurifier odstraníme nebezpečné znaky názvu a popisku
        $nothingAllowedConfig = \HTMLPurifier_Config::createDefault();
        $CKETagsAllowed = \HTMLPurifier_Config::createDefault();
        $nothingAllowedConfig->set('HTML.Allowed', '');

        $CKETagsAllowed->set('HTML.Allowed', '');

        $data = $db->getMatchByIdFromDatabase($id);

        $players = UserModel::getPlayersFromMatchId($id);

        $bannedPlayers = UserModel::getBannedPlayersFromMatchId($id);


        if (!isset($data["id_match"])) {
            return null;
        } else {
            $id = $data["id_match"];
            $ownerId = UserModel::getUserById(($data["owner_id_user"]));
            $name = $purifier->purify($data["name"], config: $nothingAllowedConfig);
            $maxPlayers = $data["max_players"];
            $description = $purifier->purify($data["description"], config: $CKETagsAllowed);
            $dateCreated = $data["date_created"];
            $dateStarting = $data["date_starting"];
            $dateFinished = $data["date_finished"] ?? "";
            $joinCode = $data["join_code"] ?? "";
            $joinPassword = $data["join_password"] ?? "";
            $imageName = $data["image_name"] ?? "";

            return new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $dateCreated, $dateStarting, $dateFinished, $joinCode, $joinPassword, $imageName, $players, $bannedPlayers);
        }

    }

    /**
     * Vrátí pole všech kampaní
     * @return array pole všech kampaní
     */
    public static function getAllMatches(): array {
        $db = DatabaseModel::getDatabaseModel();
        $purifier = \HTMLPurifier::getInstance();

        $nothingAllowedConfig = $purifier->config;
        $nothingAllowedConfig->set('HTML.Allowed', '');

        $CKETagsAllowed = $purifier->config;
        $CKETagsAllowed->set('HTML.Allowed', '');

        $data = $db->getAllMatchesFromDatabase();

        $matches = array();

        if (!isset($data)) {
            return array();
        } else {
            for ($i = 0; $i < sizeof($data); $i++) {
                $id = $data[$i]["id_match"];
                $ownerId = UserModel::getUserById(($data[$i]["owner_id_user"]));
                $name = $purifier->purify($data[$i]["name"], config: $nothingAllowedConfig);
                $maxPlayers = $data[$i]["max_players"];
                $description = $purifier->purify($data[$i]["description"], config: $CKETagsAllowed);
                $dateCreated = $data[$i]["date_created"];
                $dateStarting = $data[$i]["date_starting"];
                $dateFinished = $data[$i]["date_finished"] ?? "";
                $joinCode = $data[$i]["join_code"] ?? "";
                $joinPassword = $data[$i]["join_password"] ?? "";
                $imageName = $data[$i]["image_name"] ?? "";
                $players = UserModel::getPlayersFromMatchId($id);
                $bannedPlayers = UserModel::getBannedPlayersFromMatchId($id);

                $matches[$i] = new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $dateCreated, $dateStarting, $dateFinished, $joinCode, $joinPassword, $imageName, $players, $bannedPlayers);
            }
            return $matches;
        }
    }

    /**
     * Přidá hráče do kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     */
    public static function addPlayerToMatch(int $playerId, int $matchId) {
        $match = self::getMatchById($matchId);
        $player = UserModel::getUserById($playerId);
        if (!isset($match))
            return;

        if (!isset($player))
            return;

        if (in_array($player, $match->getPlayers()))
            return;

        $db = DatabaseModel::getDatabaseModel();
        $db->addPlayerToMatch($playerId, $matchId);
    }

    /**
     * Vyhodí hráče z kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     */
    public static function banPlayerFromMatch(int $playerId, int $matchId)
    {
        $match = self::getMatchById($matchId);
        $player = UserModel::getUserById($playerId);
        if (!isset($match))
            return;

        if (!isset($player))
            return;

        if (!in_array($player, $match->getPlayers()))
            return;

        $db = DatabaseModel::getDatabaseModel();
        $db->removePlayerFromMatch($playerId, $matchId);
        $db->banPlayerFromMatch($playerId, $matchId);
    }

    /**
     * Odebere hráče z kampaně
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     */
    public static function removePlayerFromMatch(int $playerId, int $matchId)
    {
        $match = self::getMatchById($matchId);
        $player = UserModel::getUserById($playerId);
        if (!isset($match))
            return;

        if (!isset($player))
            return;

        if (!in_array($player, $match->getPlayers()))
            return;

        $db = DatabaseModel::getDatabaseModel();
        $db->removePlayerFromMatch($playerId, $matchId);
    }

    /**
     * Kompletně smaže kampaň
     * @param int $matchId - ID kampaně
     */
    public static function removeMatch(int $matchId) {
        $db = DatabaseModel::getDatabaseModel();

        $match = self::getMatchById($matchId);

        if (!isset($match)) {
            return;
        }

        // smažeme připojené hráče z kampaně
        foreach ($match->getPlayers() as $player) {
            $db->removePlayerFromMatch($player->getId(), $match->getId());
        }
        // smažeme vyhozené hráče z kampaně
        foreach ($match->getBannedPlayers() as $player) {
            $db->unbanPlayerFromMatch($player->getId(), $match->getId());
        }

        if ($match->getImageName() !== null and $match->getImageName() !== "") {
            $targetDir = realpath(__DIR__ . "/../Resources/user-images/matches/") . "/" . $match->getImageName();
            if (file_exists($targetDir)) {
                unlink($targetDir);
            }
        }

        // smažeme kampaň z databáze
        $db->removeMatchFromDatabase($matchId);
    }

    /**
     * Povolí hráči přistupovat ke kampani
     * @param int $playerId - ID hráče
     * @param int $matchId - ID kampaně
     */
    public static function unbanPlayerFromMatch(int $playerId, int $matchId)
    {
        $match = self::getMatchById($matchId);
        $player = UserModel::getUserById($playerId);
        if (!isset($match))
            return;

        if (!isset($player))
            return;

        if (!in_array($player, $match->getBannedPlayers()))
            return;

        $db = DatabaseModel::getDatabaseModel();
        $db->unbanPlayerFromMatch($playerId, $matchId);
    }

    /**
     * Vrátí umístění hráče
     * @param $playerId - ID hráče
     * @param $matchId - ID kampaně
     * @return mixed - řetězec umístění, pokud uživatel má umístění, jinak null
     */
    public static function getPlayersStatusFromMatch($playerId, $matchId)
    {
        $match = self::getMatchById($matchId);
        $player = UserModel::getUserById($playerId);
        if (!isset($match))
            return null;

        if (!isset($player))
            return null;

        if (!in_array($player, $match->getPlayers()))
            return null;

        $db = DatabaseModel::getDatabaseModel();
        $data = $db->getPlayersStatusFromMatch($playerId, $matchId);

        if (!isset($data)) {
            return null;
        }

        $status = $data["status"];
        return $status;
    }

    /**
     * Nastaví uživateli umístění
     * @param int $playerId - ID uživatele
     * @param int $matchId - ID kampaně
     * @param string $status - umístění
     */
    public static function setPlayerStatusFromMatch(int $playerId, int $matchId, string $status)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->setPlayerStatusFromMatch($playerId, $matchId, $status);
    }

    /**
     * Nastaví kampani připojovací údaje
     * @param int $matchId ID kampaně
     * @param string $joinCode kód pro připojení
     * @param string $joinPassword heslo pro připojení
     */
    public static function changeJoiningCredentials(int $matchId, string $joinCode, string $joinPassword)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->changeMatchJoiningCredentials($matchId, $joinCode, $joinPassword);
    }

    /**
     * Nastaví kampani datum ukončení a tím ji ukončí
     * @param int $matchId ID kampaně
     * @param string $dateTime datum ukončení
     */
    public static function setMatchFinishDate(int $matchId, string $dateTime)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->setMatchFinishDate($matchId, $dateTime);
    }

    /**
     * Vytvoří novou kampaň
     * @param string $name jméno kampaně
     * @param string $description popis kampaně
     * @param int $ownerId ID majitele
     * @param int $maxPlayers maximální počet hráčů
     * @param string $dateCreated datum vytvoření
     * @param string $dateStarting očekávaný datum odstartování
     * @param string $imageName název loga
     * @return bool true pokud vše proběhlo úspěšně, jinak false
     */
    public static function saveNewMatch(string $name, string $description, int $ownerId, int $maxPlayers, string $dateCreated, string $dateStarting, string $imageName): bool
    {
        $db = DatabaseModel::getDatabaseModel();

        return $db->createNewMatch($name, $description, $ownerId, $maxPlayers, $dateCreated, $dateStarting, $imageName);
    }

    /**
     * Převede model na řetězec json ve formě asociativního pole
     * @return array - json asociativní pole aktuální kampaně
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'owner' => $this->owner,
            'name' => $this->name,
            'description' => $this->description,
            'maxPlayers' => $this->maxPlayers,
            'dateCreated' => $this->dateCreated,
            'dateStarting' => $this->dateStarting,
            'dateFinished' => $this->dateFinished,
            'joinCode' => $this->joinCode,
            'joinPassword' => $this->joinPassword,
            'imageName' => $this->imageName,
            'players' => $this->players,
            'bannedPlayers' => $this->bannedPlayers
        ];
    }

    /**
     * @return string - datum vytvoření
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @return string - datum ukončení
     */
    public function getDateFinished(): string
    {
        return $this->dateFinished;
    }

    /**
     * @return int - ID kampaně
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UserModel - majitele kampaně
     */
    public function getOwner(): UserModel
    {
        return $this->owner;
    }

    /**
     * @return string - název kampaně
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string popis kampaně
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int maximální počet hráčů
     */
    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    /**
     * @return string název obrázku
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }

    /**
     * @return string datum zahájení kampaně
     */
    public function getDateStarting(): string
    {
        return $this->dateStarting;
    }

    /**
     * @return array pole hráčů
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return array pole vyhozených hráčů
     */
    public function getBannedPlayers(): array
    {
        return $this->bannedPlayers;
    }

    /**
     * @return string kód pro připojení
     */
    public function getJoinCode(): string
    {
        return $this->joinCode;
    }

    /**
     * @return string heslo pro připojení
     */
    public function getJoinPassword(): string
    {
        return $this->joinPassword;
    }



}