let selectedUserId
function targetUser(id) {
    selectedUserId = id
}

async function confirmBanUser() {
    await confirmBanAjax()
    window.location.href = "?page=users"
}

async function confirmUnbanUser() {
    await confirmUnbanAjax()
    window.location.href = "?page=users"
}

async function confirmCompletelyRemoveUser() {
    await completelyRemoveUserAjax()
    window.location.href = "?page=users"
}

async function changeUserRole(idUser, idSelectElement) {
    selectedUserId = idUser
    const selectEl = document.getElementById(`changeRoleSelect${idSelectElement}`)

    await changeUserRoleAjax(selectedUserId, selectEl.value)
    window.location.href = "?page=users"
}

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