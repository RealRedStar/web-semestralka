<?php

namespace redstar\Views;

class AuthPageView implements IView
{

    public function printOutput(array $tplData)
    {
        $headerView = new HeaderView();

        $headerView->printOutput($tplData);

        if (isset($tplData["register-status"])) {
            if ($tplData["register-status"] == "Success") {
                ?>
                    <div class="alert alert-dismissible alert-success mx-5 mt-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <strong>Úspěch!</strong> Registrace proběhla úspěšně
                    </div>
                <?php
            } else if ($tplData["register-status"] == "Fail") {
                ?>
                    <div class="alert alert-dismissible alert-danger mx-5 mt-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <strong>Chyba!</strong> Nastala chyba při registraci. Zkuste to prosím znovu později
                    </div>
                <?php
            }
        }

        if (isset($tplData["part"]) and $tplData["part"] == "login") {

        ?>
        <section id="login-part">
        <h1 class="display-1 text-center fw-bold my-5">Přihlášení:</h1>
        <div class="justify-content-center d-flex mx-5">
            <div class="card text-white bg-dark border col-lg-5 col-12 py-3 px-5">
                <form method="post">
                    <label for="username" class="form-label">Uživatelské jméno:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                            </svg>
                        </span>
                        <input type="text" name="username" class="form-control" placeholder="Uživatelské jméno">
                    </div>
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
                    <div class="mb-3">
                        <button type="submit" name="login-btn" value="login" class="btn btn-primary">Přihlásit</button>
                    </div>
                </form>
                <hr>
                <div class="d-inline-flex justify-content-between">
                    <a class="link-primary" href="?page=auth&part=registration">Vytvořit účet</a>
                </div>
            </div>

        </div>
        </section>

        <?php
        } else {
            ?>
            <section id="registration-part">
            <h1 class="display-1 text-center fw-bold mt-5">Registrace:</h1>
            <div class="mx-5 my-5 d-flex justify-content-center">
                <div class="card text-white bg-dark border col-lg-5 col-12 py-3 px-5">
                    <form method="post">
                        <label for="email" class="form-label">*Email:</label>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        @
                                    </span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" onchange="validateEmail()" required>
                        </div>
                        <div id="invalid-email-alert" class="alert alert-danger visually-hidden"><strong>Email: </strong>zadejte platnou emailovou adresu</div>
                        <div id="email-already-exists-alert" class="alert alert-danger visually-hidden"><strong>Email: </strong>účet se zadanou emailovou adresou již existuje</div>
                        <label for="username" class="form-label">*Uživatelské jméno:</label>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                        </svg>
                                    </span>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Uživatelské jméno" onchange="validateUsername()" required>
                        </div>
                        <div id="username-already-exists-alert" class="alert alert-danger visually-hidden"><strong>Uživatelské jméno: </strong>Účet se zadaným uživatelským jménem již existuje</div>
                        <label for="first-name" class="form-label">Křestní jméno:</label>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                        </svg>
                                    </span>
                            <input type="text" id="first-name" name="first-name" class="form-control" placeholder="Křestní jméno (nepovinné)">
                        </div>
                        <label for="last-name" class="form-label">Příjmení:</label>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                        </svg>
                                    </span>
                            <input type="text" id="last-name" name="last-name" class="form-control" placeholder="Příjmení (nepovinné)">
                        </div>
                        <label for="password" class="form-label">*Heslo:</label>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                          <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"></path>
                                          <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path>
                                        </svg>
                                    </span>
                            <input id="password" type="password" name="password" class="form-control" placeholder="Heslo" oninput="validatePassword()" required>
                        </div>
                        <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                          <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"></path>
                                          <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"></path>
                                        </svg>
                                    </span>
                            <input id="password-verify" type="password" name="password-verify" class="form-control" placeholder="Zopakujte heslo" oninput="validatePassword()" required>
                        </div>
                        <div id="invalid-password-alert" class="alert alert-danger visually-hidden">
                            <strong>Heslo: </strong>
                            Heslo nesplňuje požadavky:<br>
                            - Minimální délka 6 znaků<br>
                            - Musí obsahovat číslovku<br>
                            - Obě hesla se musí shodovat
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="register-btn" id="register-btn" value="register" class="btn btn-primary disabled">Zaregistrovat se</button>
                        </div>
                    </form>
                    <hr>
                    <div>
                        <a class="link-primary" href="?page=auth&part=login">Již mám účet</a>
                    </div>
                </div>
            </div>
            </section>
<?php
        }

        ?>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="../../../web-semestralka/app/Resources/scripts/auth-page.js"></script>
        <?php
        $headerView->getHTMLFooter();

    }
}