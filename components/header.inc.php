<header>
    <nav class="navbar navbar-expand-lg bg-dark border-bottom" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../app/Resources/logos/navbar-logo.png" alt="Logo hlavní stránky" height="64" width="64">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor02">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Domů</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Seznam turnajů</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">O projektu</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Přihlásit se
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <form class="px-4">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Uživatelské jméno:</label>
                                    <div class="input-group mb-3">
                                                <span class="input-group-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                                      <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                                    </svg>
                                                </span>
                                        <input type="text" class="form-control" placeholder="Uživatelské jméno">
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
                                        <input type="password" class="form-control" placeholder="Heslo">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Přihlásit</button>
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
