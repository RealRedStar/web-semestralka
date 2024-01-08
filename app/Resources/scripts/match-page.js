let selectedPlayerId
let currentMatchId

async function confirmBanPlayer() {
    await banPlayerAjax()
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

async function confirmUnbanPlayer() {
    await unbanPlayerAjax()
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

async function banPlayerAjax() {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=match&match-id=${currentMatchId}`,
            type: "post",
            dataType: 'text',
            data: {"ban-player": selectedPlayerId},
            success: function (result) {
                resolve(result)
            }
        })
    })
}

async function unbanPlayerAjax() {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=match&match-id=${currentMatchId}`,
            type: "post",
            dataType: 'text',
            data: {"unban-player": selectedPlayerId},
            success: function (result) {
                resolve(result)
            }
        })
    })
}


async function changePlayersDesiredNation(playerId, matchId, selectElementId) {
    const selectElement = document.getElementById(`desiredNationSelect${selectElementId}`)
    // console.log(playerId)
    // console.log(selectElement.value);

    currentMatchId = matchId


    await changePlayersDesiredNationAjax(playerId, matchId, selectElement.value)

    window.location.href = `?page=match&match-id=${currentMatchId}`
}

async function changePlayersDesiredNationAjax(playerId, matchId, nationName) {
    // console.log(playerId, matchId, nationName)
    return new Promise((resolve) => {
         $.ajax({
            url: `?page=match&match-id=${currentMatchId}`,
            type: "post",
            dataType: 'text',
            data: {
                "change-nation": true,
                "nation-name": nationName,
                "player-id": playerId
            },
            success: function (result) {
                resolve(result)
            },

        })
    })
}


async function targetPlayer(id, matchId) {
    selectedPlayerId = id
    currentMatchId = matchId
}
