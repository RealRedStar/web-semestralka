let selectedUserId

/**
 * pomocná funkce pro vybrání uživatele
 * @param id ID uživatele
 */
function targetUser(id) {
    selectedUserId = id
}

/**
 * Pomocná funkce pro volání AJAX funkce pro zabanování uživatele
 */
async function confirmBanUser() {
    await confirmBanAjax()
    window.location.href = "?page=users"
}

/**
 * Pomocná funkce pro volání AJAX funkce pro odbanování uživatele
 */
async function confirmUnbanUser() {
    await confirmUnbanAjax()
    window.location.href = "?page=users"
}
/**
 * Pomocná funkce pro volání AJAX funkce pro odstranění uživatele
 */
async function confirmCompletelyRemoveUser() {
    await completelyRemoveUserAjax()
    window.location.href = "?page=users"
}

/**
 *
 * @param idUser ID uživatele
 * @param idSelectElement ID seznamu
 */
async function changeUserRole(idUser, idSelectElement) {
    selectedUserId = idUser
    const selectEl = document.getElementById(`changeRoleSelect${idSelectElement}`)

    await changeUserRoleAjax(selectedUserId, selectEl.value)
    window.location.href = "?page=users"
}
/**
 * Ajax metoda pro změnu role uživatele
 */
async function changeUserRoleAjax(idUser, roleId) {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=users`,
            type: "post",
            dataType: 'text',
            data: {
                "change-role": true,
                "user-id": idUser,
                "role-id": roleId
            },
            success: function (result) {
                resolve(result)
            }
        })
    })
}
/**
 * Ajax metoda pro změnu zabanování uživatele
 */
async function confirmBanAjax() {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=users`,
            type: "post",
            dataType: 'text',
            data: {"ban-user": selectedUserId},
            success: function (result) {
                resolve(result)
            }
        })
    })
}
/**
 * Ajax metoda pro odbanování uživatele
 */
async function confirmUnbanAjax() {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=users`,
            type: "post",
            dataType: 'text',
            data: {"unban-user": selectedUserId},
            success: function (result) {
                resolve(result)
            }
        })
    })
}

/**
 * Ajax metoda pro odstranění uživatele
 */
async function completelyRemoveUserAjax() {
    return new Promise((resolve) => {
        $.ajax({
            url: `?page=users`,
            type: "post",
            dataType: 'text',
            data: {"completely-remove-user": selectedUserId},
            success: function (result) {
                resolve(result)
            }
        })
    })
}