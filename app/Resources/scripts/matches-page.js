/** odkaz na div, kam budeme zobrazovat zápasy */
const matchesDiv = document.getElementById("matches-div")


/**
 * Funkce získá a zpracová zápasy
 * @param loadAll booleovská hodnota zda se mají vypsat i ukončené a plné zápasy
 */
async function loadMatches(loadAll) {
    // získáme zápasy
    let matches = await retrieveMatches()

    // budeme procházet skrze všechny zápasy
    for (let i = 0; i<matches.length; i++) {
        // vytvoříme si prázdný element, kam budeme vkládat údaje zápasu a nastavíme ID divu
        let div = document.createElement('div');
        div.id = `match${matches[i]["id"]}`;

        // získáme obrázek zápasu
        let imageUrl = "";
        if (matches[i]["imageName"] === "") {
            imageUrl = "./app/Resources/logos/unknown.png"
        } else {
            imageUrl = "./app/Resources/user-images/matches/" + matches[i]["imageName"]
        }

        // pomocná proměnná pro ukládání stavového elementu
        let statusElement = ""

        // proměnná kolik je hráčů v kampani
        let playersCount = Object.keys(matches[i]["players"]).length

        // proměnná zda je kampaň plná
        let isFull = playersCount === parseInt(matches[i]["maxPlayers"])

        // pokud ano, a zároveň nenačítáme všechny kampaně, tak ji přeskočíme..
        if (isFull) {
            if (!loadAll) {
                continue;
            }

            // ..jinak načteme stavový element, stavový element bude říkat, že je kampaň plná
            statusElement = `
                     <p class="card-text text-danger" id="status">
                        <b>Stav: </b>Plný
                    </p>
            `
        }
        else if (matches[i]["dateFinished"] !== "") { // pokud má kampaň nastaveno datum ukončení, je brána jako ukončená
            // nastavíme stavový element, aby ukazoval, že je kampaň ukončená
            statusElement = `
                    <p class="card-text" id="date-finished">
                        <b>Datum ukončení: </b>${matches[i]["dateFinished"]}
                    </p>
                    <p class="card-text text-danger" id="status">
                        <b>Stav: </b>Ukončený
                    </p>`
        } else {
            // pokud není kampaň ukončená a nebo plná, nastavíme stavový element, aby ukazoval, že je kampaň volná
            statusElement = `
                     <p class="card-text text-success" id="status">
                        <b>Stav: </b>Volný
                    </p>`
        }

        // přidáme potřebné bootstrap třídy
        div.classList.add("card", "text-white", "bg-dark", "mb-3", "mx-5", "my-5", "border")

        // přidáme informace o kampani divu
        div.innerHTML = `
                <div class="card-header d-inline-flex align-items-center overflow-x-auto">
                    <img class="border" src="${imageUrl}" alt="Logo of the match" height="64" width="64">
                    <a href="?page=match&match-id=${matches[i]["id"]}" class="ms-3"><h1>${matches[i]["name"]}</h1></a>
                </div>
                <div class="card-body">
                    <p class="card-text" id="description">
                        ${matches[i]["description"]}
                    </p>
                    <p class="card-text" id="owner">
                        <b>Tvůrce kampaně: </b>${matches[i]["owner"]["username"]}
                    </p>
                    <p class="card-text" id="date-created">
                        <b>Datum vytvoření (Y-M-D): </b>${matches[i]["dateCreated"]}
                    </p>
                    <p class="card-text" id="date-created">
                        <b>Datum zahájení (Y-M-D): </b>${matches[i]["dateStarting"]}
                    </p>
                    <p class="card-text" id="players">
                        <b>Počet hráčů: </b>${playersCount}/${matches[i]["maxPlayers"]}
                    </p>
                    ${statusElement}
                    <div>
                        <a href="?page=match&match-id=${matches[i]["id"]}" class="btn btn-primary">Zobrazit</a>
                    </div>
                </div>`
        // připneme div k hlavnímu divu stránky
        matchesDiv.appendChild(div);
    }
}

/**
 * AJAX metoda pro získání kampaní z backendu.
 * @return {Promise<unknown>} - nevyužito
 */
async function retrieveMatches() {
    return new Promise((resolve) => {
        $.ajax({
            url: "?page=matches",
            type: "post",
            dataType: 'json',
            data: {"load-matches": 1},
            success: function (result) {
                console.log(result)
                resolve(result)
            }
        })
    });
}