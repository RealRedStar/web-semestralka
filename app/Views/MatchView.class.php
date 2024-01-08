<?php

namespace redstar\Views;

use redstar\Models\NationModel;
use redstar\Models\UserModel;

class MatchView implements IView
{


    public function printOutput(array $tplData)
    {
        $headerView = new HeaderView();
        $headerView->printOutput($tplData);

        if (isset($tplData["error"])) {
            echo "<div class='alert alert-danger mx-5 mt-3'><strong>Chyba! </strong>". $tplData["error"] ."</div>";
        }

        if (isset($tplData["match"])) {
            $match = $tplData["match"];
            $matchId = $match->getId();
            $loggedUser = $tplData["user"];
            $isInGame = $tplData["user-is-in-game"];
            $isOwner = $match->getOwner() == $loggedUser;
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
                        <img class="border" src="<?php echo $imageUrl?>" alt="Logo of the match" height="64" width="64">
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
                            <b>Datum vytvoření (Y-M-D): </b><?php echo $match->getDateCreated() ?>
                        </p>
                        <p class="card-text" id="date-starting">
                            <b>Datum zahájení kampaně (Y-M-D): </b><?php echo $match->getDateStarting() ?>
                        </p>
                        <p class="card-text" id="players">
                            <b>Počet hráčů: </b><?php echo sizeof($match->getPlayers()) . "/" . $match->getMaxPlayers() ?>
                        </p>
                        <h4>
                            Připojení hráči:
                        </h4>
                        <div class="overflow-x-auto">
                        <table class="table border">
                            <thead class="table-primary">
                            <tr>
                                <th scope="col">ID hráče</th>
                                <th scope="col">Uživatelské jméno</th>
                                <th scope="col">Preferovaná zem</th>
                                <th scope="col">Umístění</th>
                                <?php if ($isOwner or $permissions > 1) { echo '<th scope="col">Akce</th>'; } ?>
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
                                $playersNation = $playersNation ?? NationModel::getDefaultNation();
                                $playersNationName = $playersNation->getName();
                                if ($isOwner or $permissions > 1) {
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
                                        </td>
                                        <td>Column content</td>";
                                        if (!$playerIsOwnerInTheGame)
                                            echo "<td><button name='ban-btn' value='$playerId' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#banModal' onclick='targetPlayer($playerId, $matchId)'>Vyhodit</button></td>";
                                        else
                                            echo "<td></td>";
                                    echo "</tr>";
                                } else {
                                    if ($playerIsLoggedUser) {
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
                                            </td>
                                            <td>Column content</td>
                                        </tr>";
                                    } else {
                                        echo
                                        "<tr class='table-active'>
                                            <th scope='row'>$playerId</th>
                                            <td>$playerName</td>
                                            <td>$playersNationName</td>
                                            <td>Column content</td>
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
                                <table class="table border">
                                    <thead class="table-primary">
                                    <tr>
                                        <th scope="col">ID hráče</th>
                                        <th scope="col">Uživatelské jméno</th>
                                        <th scope="col">Akce</th>
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
                                                <td>$playerName</td>
                                                <td><button name='ban-btn' value='$playerId' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#unbanModal' onclick='targetPlayer($playerId, $matchId)'>Odbanovat</button></td>
                                            </tr>
                                        ";
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

                            if ($permissions > 1 or $isOwner) {
                                echo "
                                    <button class='btn btn-danger'>Smazat kampaň</button>
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