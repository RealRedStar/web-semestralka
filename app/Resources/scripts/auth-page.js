/** vstupní element pro heslo */
const passwordInput = document.getElementById("password")
/** vstupní element pro potvrzení hesla */
const passwordVerifyInput = document.getElementById("password-verify")
/** vstupní element pro email*/
const emailInput = document.getElementById("email")
/** element registračního tlačítka */
const registerButton = document.getElementById("register-btn")
/** vstupní element pro uživ. jméno */
const usernameInput = document.getElementById("username")

/** alert pro nesprávný email */
const invalidEmailAlert = document.getElementById("invalid-email-alert")
/** alert pro již existující email */
const alreadyExistsEmailAlert = document.getElementById("email-already-exists-alert")
/** alert pro již existující uživ. jméno */
const alreadyExistsUsername = document.getElementById("username-already-exists-alert")
/** alert pro nesprávné heslo */
const invalidPasswordAlert = document.getElementById("invalid-password-alert")


/**
 * Metoda zkontroluje, zda již neexistuje uživatel s daným uživatelským jménem
 * Metoda vyšle post požadavek na controller. Ten následně vyšle požadavek do databázového modelu
 * pro získání uživatele na základě uživ. jména.
 * @param val - uživatelské jméno
 * @returns {Promise<unknown>} Vrací true pokud již existuje uživatel s daným uživatelským jménem, false pokud ne
 */
async function checkUsername(val) {
    return new Promise((resolve) => {
        $.ajax({
            url: "?page=auth",
            type: "post",
            dataType: 'text',
            data: {"check-username": val},
            success: function (result) {
                console.log(result)
                resolve(result.trim() === "true");
            }
        })
    });
}

/**
 * Metoda zkontroluje, zda již neexistuje uživatel s daným emailem
 * Metoda vyšle post požadavek na controller. Ten následně vyšle požadavek do databázového modelu
 * pro získání uživatele na základě emailu.
 * @param val - email
 * @returns {Promise<unknown>} Vrací true pokud již existuje uživatel s daným emailem, false pokud ne
 */
async function checkEmail(val) {
    return new Promise((resolve) => {
        $.ajax({
            url: "?page=auth",
            type: "post",
            dataType: 'text',
            data: {"check-email": val},
            success: function (result) {
                console.log(result)
                resolve(result.trim() === "true");
            }
        })
    });
}

/**
 * Tato metoda ověří, zda zadané uživatelské jméno:
 * - není prázdné
 * - neexistuje v databázi
 */
async function validateUsername() {
    let val = usernameInput.value;

    if (val === "") {
        usernameInput.classList.remove("border-success")
        usernameInput.classList.add("border-danger")
        alreadyExistsUsername.classList.add("visually-hidden")
    }
    else if (await checkUsername(val)) {
        usernameInput.classList.remove("border-success")
        usernameInput.classList.add("border-danger")
        alreadyExistsUsername.classList.remove("visually-hidden")
        validateButton()
    } else {
        usernameInput.classList.remove("border-danger")
        usernameInput.classList.add("border-success")
        alreadyExistsUsername.classList.add("visually-hidden")
        validateButton()
    }
}

/**
 * Tato metoda ověří, zda zadaná emailová adresa:
 * - není prázdná a je správná (obsahuje zavináč)
 * - není již v databázi
 */
async function validateEmail() {
    /** hodnota ze vstupu */
    let val = emailInput.value

    /** pomocná proměnná pro kontrolu stavu správného emailu */
    let validEmail = true

    /**
     * Pokud hodnota obsahuje zavináč, je email správně zadaný a element zezelená,
     * jinak element zčervená a zobrazí se alert
     */
    if (val.match("@") === null) {
        emailInput.classList.add("border-danger")
        emailInput.classList.remove("border-success")
        invalidEmailAlert.classList.remove("visually-hidden")
        alreadyExistsEmailAlert.classList.add("visually-hidden")
        validEmail = false
    } else {
        emailInput.classList.add("border-success")
        emailInput.classList.remove("border-danger")
        invalidEmailAlert.classList.add("visually-hidden")
    }

    /**
     * Pokud je email správný, je potřeba ověřit, zda se nenachází již v databázi
     * Pokud se nachází v poli emails získaný pomocí Ajax, element se začervená a vypíše se alert,
     * jinak element zezelená
     */
    if (validEmail) {
        if (await checkEmail(val)) {
            emailInput.classList.remove("border-success")
            emailInput.classList.add("border-danger")
            alreadyExistsEmailAlert.classList.remove("visually-hidden")
        } else {
            emailInput.classList.remove("border-danger")
            emailInput.classList.add("border-success")
            alreadyExistsEmailAlert.classList.add("visually-hidden")
        }
    }

    // zavoláme metodu pro zapnutí nebo vypnutí tlačítka
    validateButton()
}

/**
 * Tato metoda zkontroluje, zda heslo splňuje kritéria:
 * - má alespoň 6 znaků
 * - má číslovku
 * - hesla se v obou polích shodují
 */
function validatePassword() {
    /** hodnota ze vstupu pro heslo */
    let val = passwordInput.value
    /** hodnota ze vstupu pro kontrolu hesla */
    let confVal = passwordVerifyInput.value

    /** pomocná proměnná pro kontrolu, zda je vše ověřeno a správné */
    let everythingValid = true

    /**
     * Pokud heslo nesplňuje kritéria, element hesla se začervená,
     * jinak zezelená
     */
    if (val.length < 6 || val.match(/\d/) === null) {
        passwordInput.classList.add("border-danger")
        passwordInput.classList.remove("border-success")
        everythingValid = false
    } else {
        passwordInput.classList.remove("border-danger")
        passwordInput.classList.add("border-success")
    }

    /**
     * Pokud se hesla neshodují, element hesla pro kontrolu se začervená,
     * jinak zezelená
     */
    if (confVal === "" || val !== confVal) {
        passwordVerifyInput.classList.remove("border-success")
        passwordVerifyInput.classList.add("border-danger")
        everythingValid = false
    } else {
        passwordVerifyInput.classList.remove("border-danger")
        passwordVerifyInput.classList.add("border-success")
    }

    /**
     * Pokud není vše ověřeno a správné, vypíše se hláška
     */
    if (!everythingValid) {
        invalidPasswordAlert.classList.remove("visually-hidden")
    } else {
        invalidPasswordAlert.classList.add("visually-hidden")
    }

    // zavoláme metodu pro zapnutí nebo vypnutí tlačítka
    validateButton()
}

/**
 * Tato metoda zkontroluje, zda jsou všechna data ověřená a správná (respektive, zda jsou všechna pole ohraničená zeleně)
 * Pokud ano, tlačítko se aktivuje.
 * Pokud ne, tlačítko se deaktivuje.
 */
function validateButton() {
    if (passwordInput.classList.contains("border-success") && passwordVerifyInput.classList.contains("border-success") && usernameInput.classList.contains("border-success") && emailInput.classList.contains("border-success")) {
        registerButton.classList.remove("disabled")
    } else {
        registerButton.classList.add("disabled")
    }
}