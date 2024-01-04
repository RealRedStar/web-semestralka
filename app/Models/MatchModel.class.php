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
    private string $dateFinished;
    private string $imageName;

    /**
     * @param int $id
     * @param UserModel $owner
     * @param string $name
     * @param string $description
     * @param int $maxPlayers
     * @param string $dateCreated
     * @param string $dateFinished
     * @param string $imageName
     */
    public function __construct(int $id, UserModel $owner, string $name, string $description, int $maxPlayers, string $dateCreated, string $dateFinished, string $imageName)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->name = $name;
        $this->description = $description;
        $this->maxPlayers = $maxPlayers;
        $this->dateCreated = $dateCreated;
        $this->dateFinished = $dateFinished;
        $this->imageName = $imageName;
    }


//    public static function getMatchById(int $id): ?MatchModel {
//        $db = DatabaseModel::getDatabaseModel();
//        $data = $db->getMatchByIdFromDatabase($id);
//
//
//        if (!isset($data["id_match"])) {
//            return null;
//        } else {
//            $id = $data["id_match"];
//            $ownerId = $data["owner_id_user"];
//            $name = $data["name"];
//            $description = $data["description"];
//            $maxPlayers = $data["max_players"];
//            $imageName = $data["image_name"];
//
//            return new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $imageName);
//        }
//    }

    public static function getAllMatches() {
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
            return null;
        } else {
            for ($i = 0; $i < sizeof($data); $i++) {
                $id = $data[$i]["id_match"];
                $ownerId = UserModel::getUserById(($data[$i]["owner_id_user"]));
                $name = $purifier->purify($data[$i]["name"], config: $nothingAllowedConfig);
                $maxPlayers = $data[$i]["max_players"];
                $description = $purifier->purify($data[$i]["description"], config: $CKETagsAllowed);
                $dateCreated = $data[$i]["date_created"];
                $dateFinished = "";
                if (isset($data[$i]["date_finished"])) {
                    $dateFinished = $data[$i]["date_finished"];
                }
                $imageName = $data[$i]["image_name"];

                $matches[$i] = new MatchModel($id, $ownerId, $name, $description, $maxPlayers, $dateCreated, $dateFinished, $imageName);
            }
            return $matches;
        }
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
            'dateFinished' => $this->dateFinished,
            'imageName' => $this->imageName
        ];
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