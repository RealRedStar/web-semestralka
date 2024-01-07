<?php

namespace redstar\Models;

class NationModel implements \JsonSerializable
{
    private string $tag;
    private string $name;
    private string $imageName;

    /**
     * @param string $tag
     * @param string $name
     * @param string $imageName
     */
    public function __construct(string $tag, string $name, string $imageName)
    {
        $this->tag = $tag;
        $this->name = $name;
        $this->imageName = $imageName;
    }

    public static function getNationByTag(string $tag): ?NationModel {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getNationByTag($tag);


        if (!isset($data["tag"]))
            return null;

        $nationTag = $data["tag"];
        $name = $data["name"];
        $imageName = $data["flag"];

        return new NationModel($nationTag, $name, $imageName);
    }

    public static function getNationByName(string $name): ?NationModel {

        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getNationByName($name);


        if (!isset($data["tag"]))
            return null;

        $nationTag = $data["tag"];
        $name = $data["name"];
        $imageName = $data["flag"];

        return new NationModel($nationTag, $name, $imageName);
    }

    public static function getPlayersNationFromMatch(int $playerId, int $matchId): ?NationModel {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getPlayersNationTagFromMatch($playerId, $matchId);

        if (!isset($data["desired_nation_tag"]))
            return self::getDefaultNation();


        $tag = $data["desired_nation_tag"];

        $nation = self::getNationByTag($tag);
        return $nation;
    }

    public static function getOccupiedNationsFromMatch(int $matchId): array
    {
        $db = DatabaseModel::getDatabaseModel();

        $tags = $db->getOccupiedNationTagsFromMatch($matchId);
        
        $nations = array();

        for ($i = 0; $i < sizeof($tags); $i++) {
            if (isset($tags[$i]["desired_nation_tag"]))
                $nations[$i] = self::getNationByTag($tags[$i]["desired_nation_tag"]);
        }

        return $nations;
    }

    public static function getAvailableNationsFromMatch(int $matchId): array
    {
        $db = DatabaseModel::getDatabaseModel();

        $occupiedNations = self::getOccupiedNationsFromMatch($matchId);
        $allNations = self::getAllNations();
        $defaultNation = self::getDefaultNation();

        $availableNations = array();

        $indexer = 0;
        for ($i = 0; $i < sizeof($allNations); $i++) {
            if ($allNations[$i] == $defaultNation or !in_array($allNations[$i], $occupiedNations)) {
                $availableNations[$indexer] = $allNations[$i];
                $indexer++;
            }
        }

        return $availableNations;
    }

    public static function getDefaultNation(): ?NationModel
    {
        $nation = self::getNationByTag("OTH");
        return $nation;
    }

    public static function getAllNations(): array {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getAllNationsFromDatabase();

        $nations = array();

        for ($i = 0; $i < sizeof($data); $i++) {
            $tag = $data[$i]["tag"];
            $name = $data[$i]["name"];
            $imageName = $data[$i]["flag"];

            $nations[$i] = new NationModel($tag, $name, $imageName);
        }

        return $nations;
    }
    public static function changePlayersNationFromMatch(int $playerId, int $matchId, string $nationName) {
        $db = DatabaseModel::getDatabaseModel();

        $nation = self::getNationByName($nationName);

        if (!isset($nation)) {
            return;
        }

        $db->changePlayersNationFromMatch($playerId, $matchId, $nation->getTag());
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }


    public function jsonSerialize(): mixed
    {
        return [
          "tag" => $this->tag,
          "name" => $this->name,
          "imageName" => $this->imageName
        ];
    }
}