/**
 * Created by Stephane on 21/12/2016.
 */
"use strict";
//        GESTION DES EVENEMENTS
$(document).delegate('a[data-toggle="tab"]', 'shown.bs.tab', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().draw();
});
//            ajoute la fonction modificationStock aux champs input dont le nom commence par name
$(document).delegate('.table-prestation-annexe-fournisseur-stocks input[name^="stocks"]', 'change', function (e) {
    modificationPrestationAnnexeFournisseurStock($(this));
});
//        gestion du bouton parent
$(parent.document).delegate('[name="btnEnregistrerPrestationAnnexeFournisseurStocks"]', 'click', enregistrerPrestationAnnexeFournisseurStocks);
//            ajoute la fonctionnalité de duplication du stock sur les éléments de classe .duplique-stocks (il est nécessaire de renseigner dans cet élément data-logement_id et data-periode_type_id
$(document).delegate('.duplique-prestation-annexe-fournisseur-stocks', 'click', function (e) {
//                récupération des attributs data-
    dupliquerPrestationAnnexeFournisseurStocks($(this).data());
});