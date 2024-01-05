<?php

namespace redstar\Views;

class MatchesTemplate implements IView
{

    public function printOutput(array $tplData)
    {

        $headerView = new HeaderView();

        $logon = false;

        if (isset($tplData["logon"]) and $tplData["logon"] == "true") {
            $logon = true;
        }

        $headerView->printOutput($tplData);

        ?>
        <h1 class="display-1 text-white text-center mb-5 mt-5 fw-bold">Seznam turnajů</h1>

        <?php

            if (!$logon) {
                echo "<div class='alert alert-primary mx-5'><strong>Info: </strong>Pro zobrazení všech zápasů se prosím přihlašte</div>";
            }

        ?>

        <div class="d-flex justify-content-center">
            <div id="matches-div" class="col-lg-8 col-12">

            </div>
        </div>
        <?php
            if ($logon) {
                echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>';
                echo '<script src="../../../web-semestralka/app/Resources/scripts/matches-page.js" type="application/javascript" onload="loadMatches()"></script>';
            }
        ?>
        <?php
        $headerView->getHTMLFooter();
    }
}