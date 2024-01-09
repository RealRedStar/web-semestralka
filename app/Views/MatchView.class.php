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

        if (isset($tplData["edit"]) and $tplData["edit"] == true and !isset($tplData["error"])) {
            ?>
            <h1 class="display-1 text-white text-center mb-5 mt-5 fw-bold">Úprava kampaně</h1>
            <div class="justify-content-center d-flex mx-5 my-5">
                <div class="card text-white bg-dark border col-lg-5 col-12 py-3 px-5">
                    <form method="post" action="?page=match" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">*Název:</label>
                            <input type="text" name="match-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Popis:</label>
                            <textarea class="form-control" name="match-description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">*Maximální počet hráčů:</label>
                            <input type="number" min="2" max="32" class="form-control" name="match-max-players" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">*Očekávaný datum zahájení kampaně:</label>
                            <input type="datetime-local" class="form-control" name="match-date-starting" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Obrázek kampaně</label>
                            <input type="file" class="form-control" name="match-image" accept="image/png">
                        </div>
                        <input type="submit" name="save-match" class="btn btn-primary" value="Uložit">
                    </form>
                </div>

            </div>

            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script src="../../../web-semestralka/app/Resources/scripts/match-page.js" type="application/javascript"></script> <?php
        } else if (isset($tplData["match"]) and $tplData["match"] != "new") {
            $match = $tplData["match"];
            $matchId = $match->getId();
            $loggedUser = $tplData["user"];
            $isInGame = $tplData["user-is-in-game"];
            $isOwner = $match->getOwner() == $loggedUser;
            $dateFinished = $match->getDateFinished();
            $isStarted = htmlspecialchars($match->getJoinCode()) != "";
            $joinCode = htmlspecialchars($match->getJoinCode());
            $joinPassword = htmlspecialchars($match->getJoinPassword());
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

                        <?php

                        if (($isInGame or $isOwner or $permissions > 1) and ($isStarted or $isFinished)) {
                            echo "<p>ID kód pro připojení: $joinCode</p>";
                            echo "<p>Heslo pro připojení: $joinPassword</p>";
                        }

                        if ($isFinished) {
                            echo "<p class='text-danger'>Hra skončila!</p>";
                        } else if ($isStarted) {
                            echo "<p class='text-info'>Hra právě probíhá!</p>";
                        }

                        ?>
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
                                <?php if (($isOwner or $permissions > 1) and (!$isFinished and !$isStarted)) { echo '<th class="bg-primary" scope="col">Akce</th>'; } ?>
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

                                ?>
                                <tr>
                                    <td><?php echo $playerId?></td>
                                    <td><?php echo $playerName?></td>
<?php
                                    if (($isOwner or $playerIsLoggedUser or $permissions > 1) and !$isStarted) {
?>
                                            <td>
                                                <select id="desiredNationSelect<?php echo $i?>" onchange="changePlayersDesiredNation(<?php echo "$playerId, $matchId, $i"?>)">
<?php
                                            for ($k = 0; $k < sizeof($tplData["available-nations"]); $k++) {
                                            $nation = $tplData["available-nations"][$k];
                                            $nationTag = $nation->getTag();
                                            $nationName = $nation->getName();
                                            if ($nation != $playersNation)
                                                    echo "<option value='$nationName'>$nationName</option>";
                                            }
                                            echo "<option value='$playersNationName' selected>$playersNationName</option>";
?>
                                                </select>
                                            </td>
<?php
                                    } else {
                                        ?>
                                        <td><?php echo htmlspecialchars($playersNationName)?></td>
                                    <?php
                                    }

                                    if (($isOwner or $permissions > 1) and !$isFinished) {
?>
                                            <td>
                                                <select id="statusSelect<?php echo $i ?>" onchange="changePlayerStatus(<?php echo "$playerId, $matchId, $i" ?>)">
                                                    <option value='vítězství' <?php if (($playersStatus) == 'vítězství') echo "selected"; ?>>vítězství</option>
                                                    <option value='prohra' <?php if (($playersStatus) == 'prohra') echo "selected"; ?>>prohra</option>
                                                    <option value='žádné' <?php if (($playersStatus) == 'žádné') echo "selected"; ?>>žádné</option>
                                                </select>
                                            </td>
<?php
                                    } else {
                                        ?>
                                        <td><?php echo htmlspecialchars($playersStatus)?></td>
                                    <?php
                                    }

                                    if (($isOwner or $permissions > 1) and !$isStarted) {
                                        if (!$playerIsOwnerInTheGame) {
                                            echo "<td><button name='ban-btn' value='$playerId' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#banModal' onclick='targetPlayer($playerId, $matchId)'>Vyhodit</button></td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                    }

?>
                                </tr>
                            <?php
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
                                        <?php if (!$isFinished and !$isStarted) { echo "<th class='bg-primary' scope='col'>Akce</th>"; } ?>
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
                                        if (!$isFinished and !$isStarted) {
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
                            if (!$isFinished and !$isStarted) {
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
                                if (!$isStarted) {
                                    echo "<button type='button' name='start-match-btn' value='$matchId' data-bs-toggle='modal' data-bs-target='#startMatchModal' class='btn btn-success'>Zahájit kampaň</button>";
                                } else {
                                    if (!$isFinished) {
                                        echo "<button type='button' name='stop-match-btn' value='$matchId' data-bs-toggle='modal' data-bs-target='#stopMatchModal' class='btn btn-danger'>Ukončit kampaň</button>";
                                    }
                                }
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

            <div class="modal fade" id="startMatchModal" tabindex="-1" role="dialog" aria-labelledby="startMatchModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Odstartování kampaně</h5>
                        </div>
                        <form method="post" action="?page=match&match-id=<?php echo $matchId?>">
                            <div class="modal-body">
                                <p>Pro odstartování kampaně vyplňte přihlašovací kód kampaně v HOI4 herní místnosti</p>
                                <label>*Připojovací kód:</label>
                                <input type="text" name="join-code" class="form-control" required>
                                <label>Přístupové heslo (bude zveřejněno hráčům):</label>
                                <input type="text" name="join-password" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="start-match" value="<?php echo $matchId ?>" class="btn btn-success">Odstartovat</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="stopMatchModal" tabindex="-1" role="dialog" aria-labelledby="stopMatchModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Potvrzení akce</h5>
                        </div>
                        <div class="modal-body">
                            Opravdu si přejete tuto kampaň ukončit? Zkontrolujte umístění jednotlivých hráčů! Po ukončení již nebudete moci kampaň upravovat.
                        </div>
                        <div class="modal-footer">
                            <form method="post" action="?page=match&match-id=<?php echo $matchId ?>">
                                <button type="submit" name="stop-match" value="<?php echo $matchId?>" class="btn btn-danger">Ukončit kampaň</button>
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