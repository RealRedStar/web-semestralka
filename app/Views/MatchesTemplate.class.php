<?php

namespace redstar\Views;

use redstar\Controllers\HeaderController;

class MatchesTemplate implements IView
{

    public function printOutput(array $tplData)
    {
        $header = new HeaderController();
        $headerView = new HeaderView();

        $tplData = $header->show($tplData["title"]);

        $headerView->printOutput($tplData);

        ?>
        <h1 class="display-1 text-white text-center mb-5 mt-5 fw-bold">Seznam turnajů</h1>

        <div class="justify-content-center d-inline-flex">
            <div class="card text-white bg-dark mb-3 mx-5 my-5 border w-75">
                <div class="card-header d-inline-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"></path>
                    </svg>
                    <a href="#" class="ms-3"><h1>Player's game</h1></a>
                </div>
                <div class="card-body">
                    <p class="card-text" id="game-title">
                        Připojte se do boje!
                    </p>
                    <p class="card-text">
                        Počet hráčů: 0/24
                    </p>
                    <p class="card-text text-success">
                        Přístup: Veřejný
                    </p>
                    <p class="card-text text-danger">
                        Přístup: Privátní
                    </p>
                    <div>
                        <button class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
                                <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1"></path>
                            </svg>
                            Připojit se
                        </button>
                        <button class="btn btn-danger">Odpojit se</button>
                        <button class="btn btn-danger">Odstranit</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $headerView->getHTMLFooter();
    }
}