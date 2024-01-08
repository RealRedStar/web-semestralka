<?php

namespace redstar\Views;

use redstar\Models\MatchModel;
use redstar\Models\NationModel;

class MatchView implements IView
{


    public function printOutput(array $tplData)
    {
        $headerView = new HeaderView();
        $headerView->printOutput($tplData);

        if (isset($tplData["success"])) {
            echo "<div class='alert alert-success mx-5 mt-3'><strong>Úspěch! </strong>" . $tplData["success"] . "</div>";
        }

        if (isset($tplData["error"])) {
            echo "<div class='alert alert-danger mx-5 mt-3'><strong>Chyba! </strong>". $tplData["error"] ."</div>";
        }

        if (isset($tplData["match"])) {
            $match = $tplData["match"];
            $matchId = $match->getId();
            $loggedUser = $tplData["user"];
            $isInGame = $tplData["user-is-in-game"];
            $isOwner = $match->getOwner() == $loggedUser;
            $dateFinished = $match->getDateFinished();
            $isFinished = $dateFinished != "";
            $permissions = $loggedUser->getRole()->getPermissions();
            $isFull = sizeof($match->getPlayers()) == $match->getMaxPlayers();

            if ($match->getImageName() == "") {
                $imageUrl = "/web-semestralka/app/Resources/logos/unknown.png";
            } else {
                $imageUrl = "/web-semestralka/app/Resources/user-images/matches/" . $match->getImageName();
            }

            ?>
            <h1 class="display-1 text-white text-center mb-5 mt-5 fw-bold">Info kampaně</h1>

            <div class="d-flex justify-content-center">
                <div id="match-div" class="card card-text text-white bg-dark mb-3 mx-5 my-5 border col-lg-8 col-12">
                    <div class="card-header d-inline-flex align-items-center">
                        <img class="border" src="<?php echo htmlspecialchars($imageUrl)?>" alt="Logo of the match" height="64" width="64">
                        <h1 class="ms-3"><?php echo $match->getName()?></h1>
                    </div>
                    <div class="card-body">
                        <p class="card-text" id="description">
                            <?php echo $match->getDescription()?>
                        </p>
                        <p class="card-text" id="description">
                            <b>Tvůrce kampaně: </b><?php echo htmlspecialchars($match->getOwner()->getUsername())?>
                        </p>
                        <p class="card-text" id="date-created">
                            <b>Datum vytvoření (Y-M-D): </b><?php echo htmlspecialchars($match->getDateCreated()) ?>
                        </p>
                        <p class="card-text" id="date-starting">
                            <b>Datum zahájení kampaně (Y-M-D): </b><?php echo htmlspecialchars($match->getDateStarting()) ?>
                        </p>
                        <?php
                            if ($isFinished) {
                                ?>
                                <p class="card-text" id="date-starting">
                                    <b>Datum ukončení kampaně (Y-M-D): </b><?php echo htmlspecialchars($dateFinished) ?>
                                </p>
                        <?php
                            }
                        ?>
                        <p class="card-text" id="players">
                            <b>Počet hráčů: </b><?php echo sizeof($match->getPlayers()) . "/" . htmlspecialchars($match->getMaxPlayers()) ?>
                        </p>
                        <h4>
                            Připojení hráči:
                        </h4>
                        <div class="overflow-x-auto">
                        <table class="table table-striped table-dark table-bordered">
                            <thead>
                            <tr>
                                <th class="bg-primary" scope="col">ID hráče</th>
                                <th class="bg-primary" scope="col">Uživatelské jméno</th>
                                <th class="bg-primary" scope="col">Preferovaná zem</th>
                                <th class="bg-primary" scope="col">Umístění</th>
                                <?php if (($isOwner or $permissions > 1) and !$isFinished) { echo '<th class="bg-primary" scope="col">Akce</th>'; } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $players = $match->getPlayers();
                            for($i = 0; $i < sizeof($players); $i++) {
                                $playerId = $players[$i]->getId();
                                $playerName = htmlspecialchars($players[$i]->getUsername());
                                $playerIsOwnerInTheGame = $loggedUser->getId() == $playerId;
                                $playerIsLoggedUser = $loggedUser->getId() == $playerId;
                                $playersNation = NationModel::getPlayersNationFromMatch($playerId, $matchId);
                                $playersStatus = MatchModel::getPlayersStatusFromMatch($playerId, $matchId) ?? "žádné";
                                $playersNation = $playersNation ?? NationModel::getDefaultNation();
                                $playersNationName = htmlspecialchars($playersNation->getName());
                                if (($isOwner or $permissions > 1) and !$isFinished) {
                                    echo
                                    "<tr class='table-active'>
                                        <th scope='row'>$playerId</th>
                                        <td>$playerName</td>
                                        <td>
                                            <select id='desiredNationSelect$i' onchange='changePlayersDesiredNation($playerId, $matchId, $i)'>";

                                    for ($k = 0; $k < sizeof($tplData["available-nations"]); $k++) {
                                        $nation = $tplData["available-nations"][$k];
                                        $nationTag = $nation->getTag();
                                        $nationName = $nation->getName();
                                        if ($nation != $playersNation)
                                            echo "<option value='$nationName'>$nationName</option>";
                                    }
                                    echo "<option value='$playersNationName' selected>$playersNationName</option>";
                                    echo "
                                            </select>
                                        </td>"; ?>
                                        <td>
                                            <?php echo "<select id='statusSelect$i' onchange='changePlayerStatus($playerId, $matchId, $i)'>" ?>
                                                <option value='vítězství' <?php if (($playersStatus) == 'vítězství') echo "selected"; ?>>vítězství</option>
                                                <option value='prohra' <?php if (($playersStatus) == 'prohra') echo "selected"; ?>>prohra</option>
                                                <option value='žádné' <?php if (($playersStatus) == 'žádné') echo "selected"; ?>>žádné</option>
                                            </select>
                                        </td>
                            <?php
                                        if (!$playerIsOwnerInTheGame)
                                            echo "<td><button name='ban-btn' value='$playerId' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#banModal' onclick='targetPlayer($playerId, $matchId)'>Vyhodit</button></td>";
                                        else
                                            echo "<td></td>";
                                    echo "</tr>";
                                } else {
                                    if ($playerIsLoggedUser and !$isFinished) {
                                        echo
                                        "<tr class='table-active'>
                                            <th scope='row'>$playerId</th>
                                            <td>$playerName</td>
                                            <td>
                                                <select id='desiredNationSelect$i' onchange='changePlayersDesiredNation($playerId, $matchId, $i)'>";
                                        for ($k = 0; $k < sizeof($tplData["available-nations"]); $k++) {
                                            $nation = $tplData["available-nations"][$k];
                                            $nationTag = $nation->getTag();
                                            $nationName = $nation->getName();
                                            if ($nation != $playersNation)
                                                echo "<option value='$nationName'>$nationName</option>";
                                        }
                                        echo "<option value='$playersNationName' selected>$playersNationName</option>";
                                        echo "
                                                </select>
                                            </td>";
                                        echo "<td>$playersStatus</td>";
                                        echo "</tr>";
                                    } else {
                                        echo
                                        "<tr class='table-active'>
                                            <th scope='row'>$playerId</th>
                                            <td>$playerName</td>
                                            <td>$playersNationName</td>
                                            <td>$playersStatus</td>
                                        </tr>";
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        </div>

                        <?php
                            $permissions = $loggedUser->getRole()->getPermissions();
                            if ($isOwner or $permissions > 1) {
                                ?>
                                <h4>
                                    Vyhození hráči:
                                </h4>
                                <div class="overflow-x-auto">
                                <table class="table table-striped table-dark table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="bg-primary" scope="col">ID hráče</th>
                                        <th class="bg-primary" scope="col">Uživatelské jméno</th>
                                        <?php if (!$isFinished) { echo "<th class='bg-primary' scope='col'>Akce</th>"; } ?>
                                    </tr>
                                    </thead>
                                    <tbody> <?php

                                    $bannedPlayers = $match->getBannedPlayers();
                                    for ($j = 0; $j < sizeof($bannedPlayers); $j++) {
                                        $playerId = $bannedPlayers[$j]->getId();
                                        $playerName = htmlspecialchars($bannedPlayers[$j]->getUsername());
                                        echo "
                                            <tr class='table-active'>
                                                <th scope='row'>$playerId</th>
                                                <td>$playerName</td>";
                                        if (!$isFinished) {
                                            echo "<td><button name='ban-btn' value='$playerId' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#unbanModal' onclick='targetPlayer($playerId, $matchId)'>Odbanovat</button></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                </div>
                        <?php
                            } ?>
                        <div class="d-inline-flex gap-3" id="buttons">
                            <?php
                            $loggedUserId = $loggedUser->getId();
                            if (!$isFinished) {
                                if (!$isInGame) {
                                    if (!$isFull) {
                                        echo "
                                    <form method='post' action='?page=match&match-id=$matchId'>
                                        <button type='submit' name='join-btn' value='$loggedUserId' class='btn btn-primary'>Připojit se</button>
                                    </form>";
                                    }
                                } else {
                                    echo "
                                    <form method='post' action='?page=match&match-id=$matchId'>
                                        <button type='submit' name='leave-btn' value='$loggedUserId' class='btn btn-danger'>Odpojit se</button>
                                    </form>";
                                }
                            }

                            if ($permissions > 1 or $isOwner) {
                                echo "
                                    <button type='button' name='remove-match-btn' value='$matchId' data-bs-toggle='modal' data-bs-target='#removeMatchModal' class='btn btn-danger'>Smazat kampaň</button>
                                ";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            

            <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tohoto hráče vyhodit?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" onclick="confirmBanPlayer()">Vyhodit</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="unbanModal" tabindex="-1" role="dialog" aria-labelledby="unbanModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tohoto hráče odbanovat?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="confirmUnbanPlayer()">Odbanovat</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="removeMatchModal" tabindex="-1" role="dialog" aria-labelledby="removeMatchModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tuto kampaň odstranit?
                        </div>
                        <div class="modal-footer">
                            <form method="post" action="?page=match">
                                <button type="submit" name="remove-match-btn" value="<?php echo $matchId ?>" class="btn btn-danger">Odstranit</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


<!--            <div id="match" class="card text-white bg-dark mb-3 mx-5 my-5 border">-->
<!--                <div class="card-header d-inline-flex align-items-center">-->
<!--                    <img class="border" src="${imageUrl}" alt="Logo of the match" height="64" width="64">-->
<!--                    <a href="?page=match&match=${matches[i]["id"]}" class="ms-3"><h1>${matches[i]["name"]}</h1></a>-->
<!--                </div>-->
<!--            </div>-->
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script src="../../../web-semestralka/app/Resources/scripts/match-page.js" type="application/javascript"></script>
<?php
        }


        $headerView->getHTMLFooter();
    }
}
?>