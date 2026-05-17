// =========================================================================
// LE GESTIONNAIRE DE THÈME (Mode Clair / Sombre + Cookies)
// =========================================================================

// L'ARCHIVISTE : Fonction pour lire un cookie
function getCookie(nom) {
    let nomRecherche = nom + "=";
    let tableauCookies = document.cookie.split(';');
    for(let i = 0; i < tableauCookies.length; i++) {
        let c = tableauCookies[i].trim();
        if (c.indexOf(nomRecherche) === 0) {
            return c.substring(nomRecherche.length, c.length);
        }
    }
    return "";
}

// L'ÉCRIVAIN : Fonction pour créer/modifier un cookie (valable 30 jours)
function setCookie(nom, valeur, jours) {
    let date = new Date();
    date.setTime(date.getTime() + (jours * 24 * 60 * 60 * 1000));
    let expiration = "expires=" + date.toUTCString();
    document.cookie = nom + "=" + valeur + ";" + expiration + ";path=/";
}

// L'INTERRUPTEUR : Activer/Désactiver le mode sombre
const btnTheme = document.getElementById("btn_theme");

if (btnTheme) { // On vérifie que le bouton existe sur la page
    
    // Au chargement de la page, on regarde ce que dit le cookie
    let themeSauvegarde = getCookie("theme_choisi");
    if (themeSauvegarde === "sombre") {
        document.body.classList.add("dark-mode");
    }

    // L'ESPION : On écoute le clic sur le bouton "Thème"
    btnTheme.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche le lien de remonter en haut de la page
        
        // On bascule la classe (si elle y est, on l'enlève, sinon on la met)
        document.body.classList.toggle("dark-mode");
        
        // On sauvegarde le nouveau choix dans le cookie pour les autres pages
        if (document.body.classList.contains("dark-mode")) {
            setCookie("theme_choisi", "sombre", 30);
        } else {
            setCookie("theme_choisi", "clair", 30);
        }
    });
}