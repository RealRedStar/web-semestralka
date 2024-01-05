const matchesDiv = document.getElementById("matches-div")


async function loadMatches() {
    let matches = await retrieveMatches()
    for (let i = 0; i<matches.length; i++) {
        let div = document.createElement('div');
        div.id = `match${matches[i]["id"]}`;
        let imageUrl = "";
        if (matches[i]["imageName"] === "") {
            imageUrl = "./app/Resources/logos/ananas_match_1.png"
        } else {
            imageUrl = "./app/Resources/user-images/matches/" + matches[i]["imageName"]
        }

        let statusElement = ""

        //TODO: Dodělat mechanismus pro zjištění, zda je kampaň plná
        let isFull = false
        if (isFull) {
            statusElement = `
                     <p class="card-text text-danger" id="status">
                        <b>Stav: </b>Plný
                    </p>
            `
        }

        if (matches[i]["dateFinished"] !== "") {
            statusElement = `
                    <p class="card-text" id="date-finished">
                        <b>Datum ukončení: </b>${matches[i]["dateFinished"]}
                    </p>
                    <p class="card-text text-danger" id="status">
                        <b>Stav: </b>Ukončený
                    </p>`
        } else {
            statusElement = `
                     <p class="card-text text-success" id="status">
                        <b>Stav: </b>Volný
                    </p>`
        }
        div.classList.add("card", "text-white", "bg-dark", "mb-3", "mx-5", "my-5", "border")
        div.innerHTML = `
                <div class="card-header d-inline-flex align-items-center">
                    <img class="border" src="${imageUrl}" alt="Logo of the match" height="64" width="64">
                    <a href="?page=match&match-id=${matches[i]["id"]}" class="ms-3"><h1>${matches[i]["name"]}</h1></a>
                </div>
                <div class="card-body">
                    <p class="card-text" id="description">
                        ${matches[i]["description"]}
                    </p>
                    <p class="card-text" id="date-created">
                        <b>Datum vytvoření: </b>${matches[i]["dateCreated"]}
                    </p>
                    <p class="card-text" id="players">
                        <b>Počet hráčů: </b>0/${matches[i]["maxPlayers"]}
                    </p>
                    ${statusElement}
                    <div>
                        <a href="?page=match&match-id=${matches[i]["id"]}" class="btn btn-primary">Zobrazit</a>
<!--                        <button class="btn btn-primary">-->
<!--                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">-->
<!--                                <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1"></path>-->
<!--                            </svg>-->
<!--                            Připojit se-->
<!--                        </button>-->
<!--                        <button class="btn btn-danger">Odpojit se</button>-->
<!--                        <button class="btn btn-danger">Odstranit</button>-->
                    </div>
                </div>`
        matchesDiv.appendChild(div);
    }
}

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