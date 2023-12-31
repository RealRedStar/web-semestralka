const passwordInput = document.getElementById("password")
const passwordVerifyInput = document.getElementById("password-verify")
const emailInput = document.getElementById("email")
const registerButton = document.getElementById("register-btn")
const usernameInput = document.getElementById("username")

let usernames = []
let emails = []

loadBoth()
async function loadBoth() {
    await loadUsernames()
    await loadEmails()
}
async function loadUsernames() {
    $.ajax({
        url: "?page=auth",    //the page containing php script
        type: "post",    //request type,
        dataType: 'text',
        data: {usernames: "load"},
        success: function (result) {
            usernames = JSON.parse(result)
            console.log(usernames)
        }
    })
    await validateUsername();
    setTimeout(loadUsernames, 10000);
}

async function loadEmails() {
    $.ajax({
        url: "?page=auth",    //the page containing php script
        type: "post",    //request type,
        dataType: 'text',
        data: {emails: "load"},
        success: function (result) {
            emails = JSON.parse(result)
            console.log(emails)
        }
    })
    await validateUsername();
    setTimeout(loadEmails, 10000);
}

async function validateUsername() {
    let val = usernameInput.value;

    if (val === "") {
        return
    }

    if (usernames.includes(val)) {
        usernameInput.classList.remove("border-success")
        usernameInput.classList.add("border-danger")
        await validateButton()
    } else {
        usernameInput.classList.remove("border-danger")
        usernameInput.classList.add("border-success")
        await validateButton()
    }
}
async function validateEmail() {
    let val = emailInput.value

    if (val.match("@") === null) {
        emailInput.classList.add("border-danger")
        emailInput.classList.remove("border-success")
        await validateButton()
        return
    } else {
        emailInput.classList.add("border-success")
        emailInput.classList.remove("border-danger")
        await validateButton()
    }

    if (emails.includes(val)) {
        emailInput.classList.remove("border-success")
        emailInput.classList.add("border-danger")
        await validateButton()
    } else {
        emailInput.classList.remove("border-danger")
        emailInput.classList.add("border-success")
        await validateButton()
    }
}
async function validatePassword() {
    let val = passwordInput.value
    let confVal = passwordVerifyInput.value

    if (confVal !== "" && val === confVal) {
        passwordVerifyInput.classList.remove("border-danger")
        passwordVerifyInput.classList.add("border-success")
        await validateButton()
    } else {
        passwordVerifyInput.classList.remove("border-success")
        passwordVerifyInput.classList.add("border-danger")
        await validateButton()
    }

    if (val.length < 6) {
        passwordInput.classList.add("border-danger")
        await validateButton()
        return
    }

    if (val.match(/\d/) === null) {
        passwordInput.classList.add("border-danger")
        await validateButton()
        return
    }


    passwordInput.classList.remove("border-danger")
    passwordInput.classList.add("border-success")
    await validateButton()
}

async function validateButton() {
    if (passwordInput.classList.contains("border-success") && passwordVerifyInput.classList.contains("border-success") && usernameInput.classList.contains("border-success") && emailInput.classList.contains("border-success")) {
        registerButton.classList.remove("disabled")
    } else {
        registerButton.classList.add("disabled")
    }
}