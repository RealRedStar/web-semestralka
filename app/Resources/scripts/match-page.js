// pomocná proměnná pro aktuálně vybraného uživatele
let selectedPlayerId
// pomocná proměnná pro zobrazenou kampaň
let currentMatchId

/**
 * Toto je pomocná metoda, která je vždy volána jednotlivými tlačítky, popřípadě výběrovými elementy
 * pro získání ID uživatele a ID kampaně pro budoucí použití u ostatních funkcí
 * @param id - ID uživatele
 * @param matchId - ID kampaně
 */
function targetPlayer(id, matchId) {
    selectedPlayerId = id
    currentMatchId = matchId
}

/**
 * Tato metoda je volána modalem pro potvrzení zabanování uživatele
 * Volá AJAX metodu pro vykonání požadavaku
 * Na konci se stránka obnoví
 */
async function confirmBanPlayer() {
    await banPlayerAjax()
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

/**
 * Tato metoda je volána modalem pro potvrzení odbanování uživatele
 * Volá AJAX metodu pro vykonání požadavaku
 * Na konci se stránka obnoví
 */
async function confirmUnbanPlayer() {
    await unbanPlayerAjax()
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

/**
 * Tato metoda vykoná POST požadavek pro zabanování hráče z kampaně
 * POST požadavek se dále vykoná na backendu v MatchController
 * @returns {Promise<unknown>} - nevyužito
 */
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

/**
 * Tato metoda vykoná POST požadavek pro odbanování hráče z kampaně
 * POST požadavek se dále vykoná na backendu v MatchController
 * @returns {Promise<unknown>} - nevyužito
 */
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

/**
 * Toto je pomocná funkce, která se vykoná po vybrání preferovaného národa ze seznamu
 * Volá funkci pro vyslání AJAX požadavku pro změnu národa.
 * Na konec stránku obnoví
 * @param playerId - ID hráče
 * @param matchId - ID zápasu
 * @param selectElementId - ID prvku seznamu
 */
async function changePlayersDesiredNation(playerId, matchId, selectElementId) {
    const selectElement = document.getElementById(`desiredNationSelect${selectElementId}`)

    currentMatchId = matchId


    await changePlayersDesiredNationAjax(playerId, matchId, selectElement.value)

    window.location.href = `?page=match&match-id=${currentMatchId}`
}

/**
 * Tato funkce vykoná POST požadavek pro změnu preferovaného národa hráče.
 * POST požadavek se vykoná na backendu ve třídě MatchController
 * @param playerId - ID hráče
 * @param matchId - ID zápasu
 * @param nationName - název země
 */
async function changePlayersDesiredNationAjax(playerId, matchId, nationName) {
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

/**
 * Pomocná funkce pro změnu umístění (výhra, prohra) hráče.
 * Je volána tím, že je vybráno umístění hráče ze seznamu.
 * Volá funkci pro vyslání AJAX požadavku pro změnu umístění.
 * Na konec stránku obnoví.
 * @param playerId - ID hráče
 * @param matchId - ID kampaně
 * @param selectElementId - ID prvku seznamu
 * @returns {Promise<void>} - nevyužito
 */
async function changePlayerStatus(playerId, matchId, selectElementId) {
    const selectElement = document.getElementById(`statusSelect${selectElementId}`)

    currentMatchId = matchId

    await changePlayerStatusAjax(playerId, matchId, selectElement.value)
    window.location.href = `?page=match&match-id=${currentMatchId}`
}

/**
 * Tato funkce vykoná POST požadavek pro změnu umístění hráče (výhra, prohra)
 * POST požadavek se vykoná na backendu ve třídě MatchController
 * @param playerId - ID hráče
 * @param matchId - ID kampaně
 * @param status - řetězec umístění (výhra, prohra)
 * @returns {Promise<unknown>} - nevyužito
 */
async function changePlayerStatusAjax(playerId, matchId, status) {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=match&match-id=${currentMatchId}`,
            type: "post",
            dataType: 'text',
            data: {
                "change-status": true,
                "status": status,
                "player-id": playerId
            },
            success: function (result) {
                resolve(result)
            },

        })
    })
}
