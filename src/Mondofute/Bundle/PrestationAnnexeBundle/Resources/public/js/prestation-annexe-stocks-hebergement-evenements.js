"use strict";
//        GESTION DES EVENEMENTS
$(document).delegate('a[data-toggle="tab"]', 'shown.bs.tab', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().draw();
});
//            ajoute la fonction modificationStock aux champs input dont le nom commence par name
$(document).delegate('.table-prestation-annexe-hebergement-stocks input[name^="stocks"]', 'change', function (e) {
    modificationPrestationAnnexeHebergementStock($(this));
});

$(document).delegate('[name="btnEnregistrerPrestationAnnexeHebergementStocks"]', 'click', enregistrerPrestationAnnexeHebergementStocks);
//            ajoute la fonctionnalité de duplication du stock sur les éléments de classe .duplique-stocks (il est nécessaire de renseigner dans cet élément data-logement_id et data-periode_type_id
$(document).delegate('.duplique-prestation-annexe-hebergement-stocks', 'click', function (e) {
//                récupération des attributs data-
    dupliquerPrestationAnnexeHebergementStocks($(this).data());
});