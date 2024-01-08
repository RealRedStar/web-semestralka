<?php

namespace redstar\Models;

class MatchModel implements \JsonSerializable
{
    private int $id;
    private UserModel $owner;
    private string $name;
    private string $description;
    private int $maxPlayers;
    private string $dateCreated;
    private string $dateStarting;
    private string $dateFinished;
    private string $imageName;
    private array $players;
    private array $bannedPlayers;

    /**
     * @param int $id
     * @param UserModel $owner
     * @param string $name
     * @param string $description
     * @param int $maxPlayers
     * @param string $dateCreated
     * @param string $dateStarting
     * @param string $dateFinished
     * @param string $imageName
     * @param array $players
     * @param array $bannedPlayers
     */
    public function __construct(int $id, UserModel $owner, string $name, string $description, int $maxPlayers, string $dateCreated, string $dateStarting, string $dateFinished, string $imageName, array $players, array $bannedPlayers)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
        $this->description = $description;
        $this->maxPlayers = $maxPlayers;
        $this->dateCreated = $dateCreated;
        $this->dateStarting = $dateStarting;
        $this->dateFinished = $dateFinished;
        $this->imageName = $imageName;
        $this->players = $players;
        $this->bannedPlayers = $bannedPlayers;
    }

    public static function getMatchById(int $id): ?MatchModel {
        $db = DatabaseModel::getDatabaseModel();
        $purifier = \HTMLPurifier::getInstance();

        $nothingAllowedConfig = \HTMLPurifier_Config::createDefault();
        $CKETagsAllowed = \HTMLPurifier_Config::createDefault();
        $nothingAllowedConfig->set('HTML.Allowed', '');
        // TODO: doplnit tagy
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
            $dateFinished = "";
            if (isset($data["date_finished"])) {
                $dateFinished = $data["date_finished"];
            }
            $imageName = $data["image_name"] ?? "";

            return new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $dateCreated, $dateStarting, $dateFinished, $imageName, $players, $bannedPlayers);
        }

    }
    public static function getAllMatches(): ?array {
        $db = DatabaseModel::getDatabaseModel();
        $purifier = \HTMLPurifier::getInstance();

        $nothingAllowedConfig = $purifier->config;
        $nothingAllowedConfig->set('HTML.Allowed', '');

        $CKETagsAllowed = $purifier->config;
        // TODO: doplnit tagy
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
                $dateFinished = "";
                if (isset($data[$i]["date_finished"])) {
                    $dateFinished = $data[$i]["date_finished"];
                }
                $imageName = $data[$i]["image_name"] ?? "";
                $players = UserModel::getPlayersFromMatchId($id);
                $bannedPlayers = UserModel::getBannedPlayersFromMatchId($id);

                $matches[$i] = new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $dateCreated, $dateStarting, $dateFinished, $imageName, $players, $bannedPlayers);
            }
            return $matches;
        }
    }

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

    public static function removeMatch(int $matchId) {
        $db = DatabaseModel::getDatabaseModel();

        $match = self::getMatchById($matchId);

        if (!isset($match)) {
            return;
        }

        foreach ($match->getPlayers() as $player) {
            $db->removePlayerFromMatch($player->getId(), $match->getId());
        }

        foreach ($match->getBannedPlayers() as $player) {
            $db->unbanPlayerFromMatch($player->getId(), $match->getId());
        }

        $db->removeMatchFromDatabase($matchId);
    }

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

    public static function setPlayerStatusFromMatch(int $playerId, int $matchId, string $status)
    {
        $db = DatabaseModel::getDatabaseModel();

        $db->setPlayerStatusFromMatch($playerId, $matchId, $status);
    }

    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    public function getDateFinished(): string
    {
        return $this->dateFinished;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): UserModel
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function jsonSerialize(): mixed
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
            'imageName' => $this->imageName,
            'players' => $this->players,
            'bannedPlayers' => $this->bannedPlayers
        ];
    }

    public function getDateStarting(): string
    {
        return $this->dateStarting;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getBannedPlayers(): array
    {
        return $this->bannedPlayers;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

}