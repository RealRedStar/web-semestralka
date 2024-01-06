let selectedPlayerId
let currentMatchId

async function confirmBanPlayer() {
    $.ajax({
        url: `?page=match&match-id=${currentMatchId}`,
        type: "post",
        dataType: 'json',
        data: {"ban-player": selectedPlayerId},
        success: function (result) {
        }
    })
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

async function confirmUnbanPlayer() {
    $.ajax({
        url: `?page=match&match-id=${currentMatchId}`,
        type: "post",
        dataType: 'json',
        data: {"unban-player": selectedPlayerId},
        success: function (result) {
        }
    })
    window.location.href = `?page=match&match-id=${currentMatchId}`
}


async function targetPlayer(id, matchId) {
    selectedPlayerId = id
    currentMatchId = matchId
    console.log(selectedPlayerId)
    console.log(currentMatchId)
}
