<?php

namespace redstar\Models;

/**
 * Třída NationModel reprezentuje národ
 * Obsahuje statické metody pro manipulaci s nimi
 */
class NationModel implements \JsonSerializable
{
    /** @var string TAG země */
    private string $tag;
    /** @var string název země */
    private string $name;
    /** @var string název obrázku země */
    private string $imageName;

    /**
     * @param string $tag TAG země
     * @param string $name název země
     * @param string $imageName název obrázku země
     */
    public function __construct(string $tag, string $name, string $imageName)
    {
        $this->tag = $tag;
        $this->name = $name;
        $this->imageName = $imageName;
    }

    /**
     * Vrátí národ podle TAGu
     * @param string $tag TAG národa
     * @return NationModel|null Pokud národ existuje, vrátí ho, jinak vrací null
     */
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

    /**
     * Vrátí národ podle názvu
     * @param string $name název národa
     * @return NationModel|null Pokud národ existuje, vrátí ho, jinak vrací null
     */
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

    /**
     * Vrátí vybraný národ hráče z kampaně
     * @param int $playerId ID hŕače
     * @param int $matchId ID kampaně
     * @return NationModel|null Pokud národ existuje, vrátí ho, jinak vrací null
     */
    public static function getPlayersNationFromMatch(int $playerId, int $matchId): ?NationModel {
        $db = DatabaseModel::getDatabaseModel();

        $data = $db->getPlayersNationTagFromMatch($playerId, $matchId);

        if (!isset($data["desired_nation_tag"]))
            return self::getDefaultNation();


        $tag = $data["desired_nation_tag"];

        $nation = self::getNationByTag($tag);
        return $nation;
    }

    /**
     * Vrátí již okupované národy z kampaně
     * @param int $matchId ID kampaně
     * @return array pole již okupovaných národů
     */
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

    /**
     * Vrátí volné národy z kampaně
     * @param int $matchId ID kampaně
     * @return array pole volných národů
     */
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

    /**
     * Vrátí výchozí národ
     * @return NationModel výchozí národ
     */
    public static function getDefaultNation(): NationModel
    {
        $nation = self::getNationByTag("OTH");
        return $nation;
    }

    /**
     * Vrátí všechny národy
     * @return array pole všech národů
     */
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

    /**
     * Změní vybraný národ hráče dané kampaně
     * @param int $playerId ID hráče
     * @param int $matchId ID kampaně
     * @param string $nationName název národa
     * @return void
     */
    public static function changePlayersNationFromMatch(int $playerId, int $matchId, string $nationName) {
        $db = DatabaseModel::getDatabaseModel();

        $nation = self::getNationByName($nationName);

        if (!isset($nation)) {
            return;
        }

        $db->changePlayersNationFromMatch($playerId, $matchId, $nation->getTag());
    }

    /**
     * @return string tag národa
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return string název národa
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string název obrázku národa
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }

    /**
     * Převede model na řetězec json ve formě asociativního pole
     * @return array - json asociativní pole národa
     */
    public function jsonSerialize(): array
    {
        return [
          "tag" => $this->tag,
          "name" => $this->name,
          "imageName" => $this->imageName
        ];
    }
}