
(function () {
    var debut = document.getElementById("date-debut");
    var fin = document.getElementById("date-fin");
    if (debut && fin) {
        debut.addEventListener("change", function () {
            var next = new Date(debut.value);
            next.setDate(next.getDate() + 1);
            fin.min = next.toISOString().slice(0, 10);
        });
    }
})();

function submitForm(event) {
    if (event) event.preventDefault();

    var successBox = document.getElementById("success-message");
    var errorBox = document.getElementById("error-message");
    successBox.style.display = "none";
    errorBox.style.display = "none";

    var voitureId = document.getElementById("vehicule").value;
    var agenceId = document.getElementById("agence").value;
    var dateDebut = document.getElementById("date-debut").value;
    var dateFin = document.getElementById("date-fin").value;

    if (!voitureId) {
        errorBox.textContent = "Merci de sélectionner un véhicule.";
        errorBox.style.display = "block";
        return false;
    }
    if (!agenceId) {
        errorBox.textContent = "Merci de sélectionner une agence.";
        errorBox.style.display = "block";
        return false;
    }
    if (new Date(dateFin) <= new Date(dateDebut)) {
        errorBox.textContent = "La date de fin doit être après la date de début.";
        errorBox.style.display = "block";
        return false;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status === 200 && this.responseText.trim() === "success") {
                successBox.textContent = "Réservation effectuée avec succès ! Retrouvez-la dans \"Mes réservations\".";
                successBox.style.display = "block";
                document.getElementById("reservation-form").reset();
            } else {
                errorBox.textContent = this.responseText.trim() || "Une erreur est survenue, merci de réessayer.";
                errorBox.style.display = "block";
            }
        }
    };
    xhttp.open("POST", "reservation1.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(
        "voiture_id=" + encodeURIComponent(voitureId) +
        "&agence_id=" + encodeURIComponent(agenceId) +
        "&date-debut=" + encodeURIComponent(dateDebut) +
        "&date-fin=" + encodeURIComponent(dateFin)
    );

    return false;
}
