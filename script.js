function getCookie(nom) {
    const recherche = nom + "=";
    const morceaux = document.cookie.split(";");

    for (let i = 0; i < morceaux.length; i++) {
        const cookie = morceaux[i].trim();

        if (cookie.indexOf(recherche) === 0) {
            return cookie.substring(recherche.length);
        }
    }

    return "";
}

function setCookie(nom, valeur, jours) {
    const date = new Date();
    date.setTime(date.getTime() + jours * 24 * 60 * 60 * 1000);
    document.cookie = nom + "=" + valeur + ";expires=" + date.toUTCString() + ";path=/";
}

const boutonTheme = document.getElementById("btn_theme");

if (getCookie("theme_site") === "sombre") {
    document.body.classList.add("dark-mode");
}

if (boutonTheme) {
    boutonTheme.addEventListener("click", function (event) {
        event.preventDefault();
        document.body.classList.toggle("dark-mode");

        if (document.body.classList.contains("dark-mode")) {
            setCookie("theme_site", "sombre", 30);
        } else {
            setCookie("theme_site", "clair", 30);
        }
    });
}

const radiosLivraison = document.querySelectorAll('input[name="type_livraison"]');
const blocCreneau = document.getElementById("bloc_creneau");

function gererCreneau() {
    if (!blocCreneau) {
        return;
    }

    const radioSelectionne = document.querySelector('input[name="type_livraison"]:checked');

    if (radioSelectionne && radioSelectionne.value === "differee") {
        blocCreneau.style.display = "block";
    } else {
        blocCreneau.style.display = "none";
    }
}

if (radiosLivraison.length > 0) {
    gererCreneau();

    radiosLivraison.forEach(function (radio) {
        radio.addEventListener("change", gererCreneau);
    });
}
