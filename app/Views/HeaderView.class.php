<?php

namespace redstar\Views;

use redstar\Models\UserModel;

class HeaderView implements IView
{

    /**
     * Přetypovaná metoda interface IView, která volá metodu getHTMLHeader pro vypsání šablony
     * @param array $tplData - Data pro šablonu
     * @return void
     */
    public function printOutput(array $tplData)
    {
        $this->getHTMLHeader($tplData["title"], $tplData["user"], $tplData);
    }

    /**
     * Tato metoda vypíše šablonu Headeru
     * @param string $pageTitle - Titulní název stránky
     * @param UserModel|null $user - Přihlášený uživatel (může být null, pokud není přihlášen)
     * @param array $tplData - Ostatní data
     * @return void
     */
    public function getHTMLHeader(string $pageTitle, ?UserModel $user, array $tplData) {
        ?>

        <!doctype html>
        <html lang="cs">
        <head>
            <meta charset="UTF-8">
            <title><?php echo $pageTitle ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
            <link href="../../../web-semestralka/app/Resources/styles.css" rel="stylesheet">
        </head>
        <body class="d-flex flex-column min-vh-100 bg-black" data-bs-theme="dark">
        <header>
        <nav class="navbar navbar-expand-lg bg-dark border-bottom" data-bs-theme="dark">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="../../../web-semestralka/app/Resources/logos/navbar-logo.png" alt="Logo hlavní stránky" height="64" width="64">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor02">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo $pageTitle == 'Úvodní stránka' ? 'active' : '';?>" href="?">Domů</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $pageTitle == 'Seznam turnajů' ? 'active' : '';?>" href="?page=matches">Seznam turnajů</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $pageTitle == 'TODO' ? 'active' : '';?>" href="#">O projektu</a>
            </li>
        </ul>
        <ul class="navbar-nav">

        <?php

        if (isset($user)) {
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link " href="user-account/<?php echo $user->getUsername(); ?>"> <?php echo $user->getUsername(); ?></a>
            </li>
            <li>
                <form method="post">
                    <button href="#" class="nav-item nav-link" type="submit" name="logout-btn" value="logout">Odhlásit se</button>
                </form>
            </li>
            </ul>
            </div>
            </div>
            </nav>
            </header>
            <?php
        }
        else {
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Přihlásit se
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <form class="px-4" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Uživatelské jméno:</label>
                            <div class="input-group mb-3">
                                                    <span class="input-group-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                                        </svg>
                                                    </span>
                                <input type="text" name="username" class="form-control" placeholder="Uživatelské jméno">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Heslo:</label>
                            <div class="input-group mb-3">
                                                    <span class="input-group-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                                          <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"></path>
                                                          <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path>
                                                        </svg>
                                                    </span>
                                <input type="password" name="password" class="form-control" placeholder="Heslo">
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="login-btn" value="login" class="btn btn-primary">Přihlásit</button>
                        </div>
                    </form>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item btn-violet" href="#">Zapomenuté heslo</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Registrace</a>
            </li>
            </ul>
            </div>
            </div>
            </nav>
            </header>
        <?php }

        if (isset($tplData["login-status"])) {
            if ($tplData["login-status"] == "Success") {
                echo '
                <div class="alert alert-dismissible alert-success mx-5 mt-3">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Úspěch!</strong> Přihlášení proběhlo úspěšně
                </div>';


            } elseif ($tplData["login-status"] == "Fail") {
                echo '
                <div class="alert alert-dismissible alert-danger mx-5 mt-3">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Chyba!</strong> Zadali jste špatné uživatelské jméno a nebo heslo
                </div>';
            } elseif ($tplData["login-status"] == "Logout") {
                echo '
                <div class="alert alert-dismissible alert-primary mx-5 mt-3">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>Úspěch!</strong> Odhlášení proběhlo úspěšně.
                </div>';
            }
        }
    }

    /**
     * Metoda vypíše patičku stránky
     * @return void
     */
    public function getHTMLFooter() {
        ?>
        <footer class="bg-dark align-items-center mt-auto pt-3 border-top" data-bs-theme="dark">
            <p class="align-items-center text-center justify-content-center text-muted">&#169 2023 Ondřej Moravcsík</p>
        </footer>
        </body>
        </html>
        <?php
    }
}