<?php

namespace redstar\Views;

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
            $loggedUser = $tplData["user"];

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
                        <p class="card-text" id="date-created">
                            <b>Datum vytvoření: </b><?php echo $match->getDateCreated() ?>
                        </p>
                        <p class="card-text" id="date-starting">
                            <b>Datum vytvoření: </b><?php echo $match->getDateStarting() ?>
                        </p>
                        <p class="card-text" id="players">
                            <b>Počet hráčů: </b><?php echo sizeof($match->getPlayers()) . "/" . $match->getMaxPlayers() ?>
                        </p>
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
            <?php
//                echo '<script src="../../../web-semestralka/app/Resources/scripts/match-page.js" type="application/javascript" onload="loadMatch('.$matchId . ', ' . '\'' . $loggedUser . '\'' .')"></script>';
        }


        $headerView->getHTMLFooter();
    }
}
?>