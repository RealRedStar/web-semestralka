const matchDiv = document.getElementById("match-div")

async function loadMatch(id, loggedUser) {
    let match = await retrieveMatch(id)
    let div = document.createElement('div');
    div.id = `match${match["id"]}`;
    let imageUrl = "";
    if (match["imageName"] === "") {
        imageUrl = "./app/Resources/logos/unknown.png"
    } else {
        imageUrl = "./app/Resources/user-images/matches/" + match["imageName"]
    }

    let statusElement

    //TODO: Dodělat mechanismus pro zjištění, zda je kampaň plná
    let isFull = false


    if (isFull) {
        statusElement = `
                 <p class="card-text text-danger" id="status">
                    <b>Stav: </b>Plný
                </p>
        `
    }

    if (match["dateFinished"] !== "") {
        statusElement = `
                <p class="card-text" id="date-finished">
                    <b>Datum ukončení: </b>${match["dateFinished"]}
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
                <a href="?page=match&match-id=${match["id"]}" class="ms-3"><h1>${match["name"]}</h1></a>
            </div>
            <div class="card-body">
                <p class="card-text" id="description">
                    ${match["description"]}
                </p>
                <p class="card-text" id="date-created">
                    <b>Datum vytvoření: </b>${match["dateCreated"]}
                </p>
                <p class="card-text" id="players">
                    <b>Počet hráčů: </b>0/${match["maxPlayers"]}
                </p>
                ${statusElement}
                <div>
                    <a href="?page=match&match-id=${match["id"]}" class="btn btn-primary">Zobrazit</a>
                </div>
            </div>`
    matchDiv.appendChild(div);
}

async function retrieveMatch(id) {
    return new Promise((resolve) => {
        $.ajax({
            url: "?page=match",
            type: "post",
            dataType: 'json',
            data: {"load-match": id},
            success: function (result) {
                console.log(result)
                resolve(result)
            }
        })
    });
}