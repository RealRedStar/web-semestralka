<?php

namespace redstar\Views;

/**
 * Šablona pro zobrazení úvodní stránky
 * @package redstar\Views
 */
class IntroductionTemplate implements IView
{

    public function printOutput(array $tplData)
    {
        $tplHeaders = new TemplateBasics();

        $tplHeaders->getHTMLHeader($tplData["title"]);

        ?>
<div class="mt-5 text-center justify-content-center d-flex text-white" data-bs-theme="dark">
    <div class="justify-content-center align-items-center text-center col-lg-6 mx-2">
        <h1 class="display-1 fw-bold mb-5">Vzhůru do boje!</h1>
        <h3 class="display-6 mb-5">Hearts of Competition je webová aplikace pro organizování turnajů do hry Hearts of Iron IV.</h3>
        <h3 class="display-6">Zaregistrujte se pro přístup do místnosti s turnaji, přihlašte se a ukažte svoje strategické schopnosti, nebo vytvořte vlastní turnaj a doveďte vaše spojence k vítězství!</h3>
    </div>
</div>
<div class="d-flex mt-3 justify-content-center gap-3">
    <button class="btn btn-primary">Zaregistrovat se</button>
    <button class="btn btn-secondary">Do seznamu turnajů</button>
</div>
<h6 class="display-6 mt-5 fw-bold text-white text-center">Proč se registrovat?</h6>

<div class="text-center justify-content-center d-lg-inline-flex text-white">
    <div class="card text-white bg-dark mb-3 col-lg-3 mx-5 my-5 border">
        <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"></path>
            </svg>
        </div>
        <div class="card-body">
            <h4 class="card-title">Jednoduchý zápis</h4>
            <p class="card-text">
                Žádné dlouhé čekání na to, než se připojí více hráčů.
                Vyberte si z turnajů, které vás svým popisem a časem zaujmou, vyberte si preferovaný národ a vzhůru do boje!
            </p>
        </div>
    </div>
    <div class="card text-white bg-dark mb-3 col-lg-3 mx-5 my-5 border">
        <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
                <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"></path>
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"></path>
            </svg>
        </div>
        <div class="card-body">
            <h4 class="card-title">Organizace na prvním místě</h4>
            <p class="card-text">
                Organizování delších kampaní nikdy nebylo snažší!
                Již žádné dlouhé domlouvání, kdo si vybere jako zem.
                Žádné čekání na to, než si všichni nainstalují vaše modifikace.
                S Hearts of Competition máte nad vaší kampaní plnou moc!
            </p>
        </div>
    </div>
    <div class="card text-white bg-dark mb-3 col-lg-3 mx-5 my-5 border">
        <div class="card-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path>
            </svg>
        </div>
        <div class="card-body">
            <h4 class="card-title">První svého druhu</h4>
            <p class="card-text">
                Klasická organizace turnajů a kampaní v HOI4 byla velice obyčejná a měla spoustu nedostatků.
                Doposud neexistovala žádná aplikace pro správu HOI4 kampaní.
                Hearts of Competition je první svého druhu. Buďte u zrodu něčeho velkolepého!
            </p>
        </div>
    </div>
</div>

<?php

        $tplHeaders->getHTMLFooter();
    }
}