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

    public static function getPlayersNationFromMatch(int $playerId, int $matchId): ?NationModel {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getPlayersNationTagFromMatch($playerId, $matchId);

        if (!isset($data["desired_nation_tag"]))
            return null;


        $tag = $data["desired_nation_tag"];

        $nation = self::getNationByTag($tag);
        return $nation;
    }

    public static function getDefaultNation(): ?NationModel
    {
        $nation = self::getNationByTag("OTH");
        return $nation;
    }

    //TODO: dodělat funkce
    public static function changePlayersNationFromMatch(int $playerId, int $matchId, string $nationTag) {

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