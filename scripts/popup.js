let pristine = true;
let nextUrl = null;
let canLeave = false;

let initialData = "";
let leaveModal;

$(() => {

    leaveModal = new bootstrap.Modal("#leaveModal");

    // sauvegarde valeurs du formulaire au chargement
    initialData = $("#itemForm").serialize();

    // à chaque modification, compare avec état initial
    $("#itemForm input, #itemForm textarea, #itemForm select").on("keyup change", () => {
        pristine = $("#itemForm").serialize() === initialData;
    });

    // si submit => quitter autorisé
    $("#itemForm").submit(() => {
        canLeave = true;
    });

    // clique sur un lien
    $("a").click(function (e) {

        // si rien changé ou sortie autorisée
        if (canLeave || pristine) {
            return;
        }

        // bloque navigation
        e.preventDefault();

        nextUrl = $(this).attr("href");

        leaveModal.show();
    });

    $("#confirmLeave").click(() => {

        canLeave = true;

        // redirection vers lien cliqué
        window.location.href = nextUrl;
    });

});
