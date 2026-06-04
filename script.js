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

function changerTheme(mode) {
    const lien = document.getElementById("theme_stylesheet");
    if (!lien) {
        return;
    }

    if (mode === "sombre") {
        lien.setAttribute("href", "style_alt.css");
    } else {
        lien.setAttribute("href", "style.css");
        mode = "clair";
    }

    document.body.setAttribute("data-theme", mode);
    setCookie("theme_site", mode, 30);
}

function echapperHtml(texte) {
    return String(texte)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function regleChamp(champ) {
    const valeur = champ.value.trim();
    const regle = champ.dataset.rule || "";

    if (champ.closest(".cache") && valeur === "") {
        return "";
    }

    if (champ.required && valeur === "") {
        return "Champ obligatoire";
    }

    if (regle === "texte" && valeur.length < 2) {
        return "Au moins 2 caracteres";
    }

    if (regle === "login" && !/^[a-zA-Z0-9._-]{3,30}$/.test(valeur)) {
        return "Login invalide";
    }

    if (regle === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valeur)) {
        return "Email invalide";
    }

    if (regle === "password" && valeur.length < 4) {
        return "Mot de passe trop court";
    }

    if (regle === "telephone" && !/^\d{10}$/.test(valeur)) {
        return "Telephone invalide";
    }

    if (regle === "adresse" && valeur.length < 5) {
        return "Adresse trop courte";
    }

    if (regle === "naissance") {
        const date = new Date(valeur);
        const aujourdHui = new Date();
        let age = aujourdHui.getFullYear() - date.getFullYear();
        if (isNaN(age) || age < 13) {
            return "Age minimum : 13 ans";
        }
    }

    if (regle === "quantite") {
        const nombre = parseInt(valeur, 10);
        if (isNaN(nombre) || nombre < 0 || nombre > 20) {
            return "Quantite invalide";
        }
    }

    if (regle === "carte" && !/^\d{12,16}$/.test(valeur)) {
        return "Numero de carte invalide";
    }

    if (regle === "cvv" && !/^\d{3,4}$/.test(valeur)) {
        return "CVV invalide";
    }

    if (regle === "expiration" && valeur === "") {
        return "Date invalide";
    }

    if (regle === "datetime") {
        const bloc = document.getElementById("bloc_creneau");
        if (bloc && !bloc.classList.contains("cache") && valeur === "") {
            return "Choisissez une date";
        }
    }

    return "";
}

function afficherErreur(champ, message) {
    const zone = champ.parentElement.querySelector(".erreur_champ") || champ.closest("div")?.querySelector(".erreur_champ");
    champ.classList.toggle("input_error", message !== "");

    if (zone) {
        zone.textContent = message;
    }
}

function validerFormulaire(formulaire) {
    let valide = true;
    const champs = formulaire.querySelectorAll("[data-rule]");

    champs.forEach(function (champ) {
        const erreur = regleChamp(champ);
        afficherErreur(champ, erreur);

        if (erreur !== "") {
            valide = false;
        }
    });

    return valide;
}

function mettreCompteurs() {
    document.querySelectorAll(".compteur").forEach(function (compteur) {
        const id = compteur.dataset.for;
        const champ = document.getElementById(id);
        if (!champ) {
            return;
        }

        const max = champ.getAttribute("maxlength");
        const valeur = champ.value.length;
        compteur.textContent = max ? valeur + " / " + max : String(valeur);

        champ.addEventListener("input", function () {
            const longueur = champ.value.length;
            compteur.textContent = max ? longueur + " / " + max : String(longueur);
        });
    });
}

function activerMotsDePasse() {
    document.querySelectorAll(".toggle-password").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
            const champ = document.getElementById(bouton.dataset.target);
            if (!champ) {
                return;
            }

            if (champ.type === "password") {
                champ.type = "text";
                bouton.textContent = "Masquer";
            } else {
                champ.type = "password";
                bouton.textContent = "Afficher";
            }
        });
    });
}

function gererCreneau() {
    const bloc = document.getElementById("bloc_creneau");
    const radio = document.querySelector('input[name="type_livraison"]:checked');

    if (!bloc || !radio) {
        return;
    }

    if (radio.value === "differee") {
        bloc.classList.remove("cache");
    } else {
        bloc.classList.add("cache");
    }
}

function gererModeRetrait() {
    const bloc = document.getElementById("bloc_infos_livraison");
    const radio = document.querySelector('input[name="mode_retrait"]:checked');

    if (!bloc || !radio) {
        return;
    }

    if (radio.value === "livraison") {
        bloc.classList.remove("cache");
    } else {
        bloc.classList.add("cache");
    }
}

let platsCourants = [];

function rendreCartesProduits(plats) {
    const conteneur = document.getElementById("liste_plats");
    const info = document.getElementById("resultat_plats_info");

    if (!conteneur) {
        return;
    }

    info.textContent = plats.length + " element(s)";
    conteneur.innerHTML = "";

    if (plats.length === 0) {
        conteneur.innerHTML = '<p class="info">Aucun produit ne correspond aux filtres.</p>';
        return;
    }

    plats.forEach(function (plat) {
        let html = '<article class="carte_plat">';
        html += '<img src="' + echapperHtml(plat.image) + '" alt="' + echapperHtml(plat.nom) + '">';
        html += '<div>';
        html += '<span class="badge">' + echapperHtml(plat.type) + '</span>';
        html += '<h3>' + echapperHtml(plat.nom) + '</h3>';
        html += '<p>' + echapperHtml(plat.description) + '</p>';
        html += '<p><strong>' + Number(plat.prix).toFixed(2).replace(".", ",") + ' EUR</strong></p>';
        html += '<p>Categorie : ' + echapperHtml(plat.categorie) + '</p>';

        if (window.estClient) {
            html += '<form action="traitements/process_panier.php" method="POST" class="petit_formulaire">';
            html += '<input type="hidden" name="action" value="ajouter">';
            html += '<input type="hidden" name="nom" value="' + echapperHtml(plat.nom) + '">';
            html += '<label>Quantite</label>';
            html += '<input type="number" name="quantite" min="1" max="20" value="1">';
            html += '<button type="submit">Ajouter au panier</button>';
            html += '</form>';
        } else {
            html += '<p><a href="connexion.php">Connectez-vous</a> pour commander.</p>';
        }

        html += '</div></article>';
        conteneur.insertAdjacentHTML("beforeend", html);
    });
}

function trierPlats(liste, typeTri) {
    const copie = [...liste];

    if (typeTri === "prix_asc") {
        copie.sort(function (a, b) { return a.prix - b.prix; });
    } else if (typeTri === "prix_desc") {
        copie.sort(function (a, b) { return b.prix - a.prix; });
    } else if (typeTri === "popularite") {
        copie.sort(function (a, b) { return b.popularite - a.popularite; });
    } else if (typeTri === "nom") {
        copie.sort(function (a, b) { return a.nom.localeCompare(b.nom); });
    }

    return copie;
}

function chargerFiltresPlats() {
    const formulaire = document.getElementById("form_filtres_plats");
    if (!formulaire) {
        return;
    }

    function rafraichir() {
        const donnees = new FormData(formulaire);
        const params = new URLSearchParams();

        params.append("recherche", donnees.get("recherche") || "");
        params.append("categorie", donnees.get("categorie") || "");
        params.append("regime", donnees.get("regime") || "");
        params.append("gout", donnees.get("gout") || "");

        fetch("traitements/api_plats.php?" + params.toString())
            .then(function (reponse) { return reponse.json(); })
            .then(function (data) {
                platsCourants = data.plats || [];
                const tri = formulaire.querySelector("#tri").value;
                rendreCartesProduits(trierPlats(platsCourants, tri));
            });
    }

    formulaire.addEventListener("change", function (event) {
        if (event.target && event.target.id === "tri") {
            rendreCartesProduits(trierPlats(platsCourants, event.target.value));
        } else {
            rafraichir();
        }
    });

    formulaire.addEventListener("input", function (event) {
        if (event.target && event.target.id === "recherche") {
            rafraichir();
        }
    });

    platsCourants = window.platsInitiaux || [];
    rendreCartesProduits(platsCourants);
}

function gererMenuAleatoire() {
    const bouton = document.getElementById("btn_menu_aleatoire");
    const bloc = document.getElementById("bloc_aleatoire");
    const resultat = document.getElementById("resultat_aleatoire");

    if (!bouton || !bloc || !resultat) {
        return;
    }

    bouton.addEventListener("click", function () {
        const source = platsCourants.length > 0 ? platsCourants : (window.platsInitiaux || []);

        if (source.length === 0) {
            resultat.innerHTML = '<p class="info">Aucun produit disponible.</p>';
            bloc.classList.remove("cache");
            return;
        }

        const index = Math.floor(Math.random() * source.length);
        const plat = source[index];
        let html = '<article class="carte_plat carte_grande">';
        html += '<img src="' + echapperHtml(plat.image) + '" alt="' + echapperHtml(plat.nom) + '">';
        html += '<div>';
        html += '<h3>' + echapperHtml(plat.nom) + '</h3>';
        html += '<p>' + echapperHtml(plat.description) + '</p>';
        html += '<p><strong>' + Number(plat.prix).toFixed(2).replace(".", ",") + ' EUR</strong></p>';
        html += '<p>Categorie : ' + echapperHtml(plat.categorie) + '</p>';
        html += '</div></article>';
        resultat.innerHTML = html;
        bloc.classList.remove("cache");
    });
}

function gererProfil() {
    const bouton = document.getElementById("btn_modifier_profil");
    const boutonAnnuler = document.getElementById("btn_annuler_profil");
    const formulaire = document.getElementById("form_profil");
    const message = document.getElementById("message_profil");

    if (!bouton || !formulaire) {
        return;
    }

    bouton.addEventListener("click", function () {
        formulaire.classList.remove("cache");
    });

    if (boutonAnnuler) {
        boutonAnnuler.addEventListener("click", function () {
            formulaire.classList.add("cache");
            message.textContent = "";
        });
    }

    formulaire.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!validerFormulaire(formulaire)) {
            return;
        }

        fetch("traitements/api_profil.php", {
            method: "POST",
            body: new FormData(formulaire)
        })
            .then(function (reponse) { return reponse.json(); })
            .then(function (data) {
                if (!data.ok) {
                    message.textContent = data.message;
                    return;
                }

                document.getElementById("profil_nom").textContent = data.utilisateur.nom;
                document.getElementById("profil_prenom").textContent = data.utilisateur.prenom;
                document.getElementById("profil_email").textContent = data.utilisateur.email;
                document.getElementById("profil_adresse").textContent = data.utilisateur.adresse;
                document.getElementById("profil_telephone").textContent = data.utilisateur.telephone;
                document.getElementById("profil_infos_complementaires").textContent = data.utilisateur.infos_complementaires !== "" ? data.utilisateur.infos_complementaires : "Aucune";
                message.textContent = "Profil mis a jour.";
                formulaire.classList.add("cache");
            });
    });
}

function gererAdmin() {
    document.querySelectorAll(".btn-admin-user").forEach(function (bouton) {
        if (bouton.dataset.ready === "1") {
            return;
        }

        bouton.dataset.ready = "1";
        bouton.addEventListener("click", function () {
            const donnees = new FormData();
            donnees.append("user_id", bouton.dataset.userId);
            donnees.append("action", bouton.dataset.action);

            fetch("traitements/api_admin_utilisateur.php", {
                method: "POST",
                body: donnees
            })
                .then(function (reponse) { return reponse.json(); })
                .then(function (data) {
                    const message = document.getElementById("message_admin");
                    message.textContent = data.message;

                    if (data.ok) {
                        const ligne = document.getElementById("ligne_user_" + bouton.dataset.userId);
                        ligne.querySelector(".statut_user").textContent = data.statut;
                        bouton.dataset.action = data.action;
                        bouton.textContent = data.action === "bloquer" ? "Bloquer" : "Debloquer";
                    }
                });
        });
    });
}

function gererCommandesResto() {
    document.querySelectorAll(".btn-commande-resto").forEach(function (bouton) {
        if (bouton.dataset.ready === "1") {
            return;
        }

        bouton.dataset.ready = "1";
        bouton.addEventListener("click", function () {
            const id = bouton.dataset.commandeId;
            const select = document.querySelector('.select_livreur[data-commande-id="' + id + '"]');
            const donnees = new FormData();
            donnees.append("commande_id", id);
            donnees.append("action", bouton.dataset.action);
            donnees.append("livreur_id", select ? select.value : "");

            fetch("traitements/api_commande_restaurateur.php", {
                method: "POST",
                body: donnees
            })
                .then(function (reponse) { return reponse.json(); })
                .then(function (data) {
                    const message = document.getElementById("message_commande_resto");
                    message.textContent = data.message;

                    if (!data.ok) {
                        return;
                    }

                    const ligne = document.getElementById("ligne_commande_" + id);
                    ligne.querySelector(".statut_commande").textContent = data.statut_libelle;
                    const cellule = ligne.querySelector(".cell_actions");

                    if (data.prochaine_action === "preparer") {
                        cellule.innerHTML = '<button type="button" class="btn-commande-resto" data-commande-id="' + id + '" data-action="preparer">Passer en preparation</button>';
                    } else if (data.prochaine_action === "prete") {
                        cellule.innerHTML = '<button type="button" class="btn-commande-resto" data-commande-id="' + id + '" data-action="prete">Marquer prete</button>';
                    } else if (data.prochaine_action === "assigner") {
                        cellule.innerHTML = '<button type="button" class="btn-commande-resto" data-commande-id="' + id + '" data-action="assigner">Assigner au livreur</button>';
                    } else {
                        cellule.textContent = "-";
                    }

                    gererCommandesResto();
                });
        });
    });
}

function gererLivraisons() {
    document.querySelectorAll(".btn-livraison").forEach(function (bouton) {
        if (bouton.dataset.ready === "1") {
            return;
        }

        bouton.dataset.ready = "1";
        bouton.addEventListener("click", function () {
            const donnees = new FormData();
            donnees.append("commande_id", bouton.dataset.commandeId);
            donnees.append("action", bouton.dataset.action || "livree");

            fetch("traitements/api_livraison.php", {
                method: "POST",
                body: donnees
            })
                .then(function (reponse) { return reponse.json(); })
                .then(function (data) {
                    const message = document.getElementById("message_livraison");
                    message.textContent = data.message;

                    if (data.ok) {
                        const carte = document.getElementById("carte_livraison_" + bouton.dataset.commandeId);
                        carte.querySelector(".statut_livraison").textContent = data.statut;
                        const zoneActions = carte.querySelector(".zone_actions_livraison");
                        if (zoneActions) {
                            zoneActions.remove();
                        }
                    }
                });
        });
    });
}

function recalculerCommandeClient() {
    const form = document.getElementById("form_modif_commande_client");
    if (!form || !window.commandeCourante) {
        return;
    }

    let total = 0;
    const prix = form.querySelectorAll(".prix_ligne");
    const quantites = form.querySelectorAll(".js-qte-commande");

    quantites.forEach(function (champ, index) {
        const qte = parseInt(champ.value || "0", 10);
        const prixLigne = parseFloat(prix[index].value);
        total += qte * prixLigne;
    });

    const selectAjout = document.getElementById("ajout_nom");
    const qteAjout = parseInt(document.getElementById("ajout_quantite").value || "0", 10);

    if (selectAjout && selectAjout.value !== "") {
        const option = selectAjout.options[selectAjout.selectedIndex];
        total += parseFloat(option.dataset.prix) * qteAjout;
    }

    const difference = total - parseFloat(window.commandeCourante.total);
    document.getElementById("nouveau_total").textContent = total.toFixed(2).replace(".", ",");
    document.getElementById("difference_total").textContent = difference.toFixed(2).replace(".", ",");

    const blocPaiement = document.getElementById("bloc_paiement_complement");
    if (difference > 0) {
        blocPaiement.classList.remove("cache");
    } else {
        blocPaiement.classList.add("cache");
    }
}

function gererModificationCommande() {
    const form = document.getElementById("form_modif_commande_client");
    if (!form) {
        return;
    }

    recalculerCommandeClient();
    form.querySelectorAll("input, select").forEach(function (champ) {
        champ.addEventListener("input", recalculerCommandeClient);
        champ.addEventListener("change", recalculerCommandeClient);
    });

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!validerFormulaire(form)) {
            return;
        }

        fetch("traitements/api_commande_client.php", {
            method: "POST",
            body: new FormData(form)
        })
            .then(function (reponse) { return reponse.json(); })
            .then(function (data) {
                const message = document.getElementById("message_modif_commande");
                message.textContent = data.message;

                if (data.ok) {
                    window.location.reload();
                }
            });
    });
}

function verifierSession() {
    if (document.body.dataset.connecte !== "1") {
        return;
    }

    setInterval(function () {
        fetch("traitements/api_session.php", { cache: "no-store" })
            .then(function (reponse) { return reponse.json(); })
            .then(function (data) {
                if (data.statut === "bloque") {
                    window.location.href = "connexion.php?erreur=compte_bloque";
                }
            });
    }, 8000);
}

document.addEventListener("DOMContentLoaded", function () {
    const boutonTheme = document.getElementById("btn_theme");
    const modeSauvegarde = getCookie("theme_site");

    if (modeSauvegarde === "sombre" || modeSauvegarde === "clair") {
        changerTheme(modeSauvegarde);
    }

    if (boutonTheme) {
        boutonTheme.addEventListener("click", function (event) {
            event.preventDefault();
            const nouveauMode = document.body.getAttribute("data-theme") === "sombre" ? "clair" : "sombre";
            changerTheme(nouveauMode);
        });
    }

    mettreCompteurs();
    activerMotsDePasse();
    gererCreneau();
    gererModeRetrait();
    document.querySelectorAll('input[name="type_livraison"]').forEach(function (radio) {
        radio.addEventListener("change", gererCreneau);
    });
    document.querySelectorAll('input[name="mode_retrait"]').forEach(function (radio) {
        radio.addEventListener("change", gererModeRetrait);
    });

    document.querySelectorAll(".js-validate-form").forEach(function (formulaire) {
        formulaire.querySelectorAll("[data-rule]").forEach(function (champ) {
            champ.addEventListener("input", function () {
                afficherErreur(champ, regleChamp(champ));
            });
        });

        formulaire.addEventListener("submit", function (event) {
            if (!validerFormulaire(formulaire)) {
                event.preventDefault();
            }
        });
    });

    chargerFiltresPlats();
    gererMenuAleatoire();
    gererProfil();
    gererAdmin();
    gererCommandesResto();
    gererLivraisons();
    gererModificationCommande();
    verifierSession();
});
