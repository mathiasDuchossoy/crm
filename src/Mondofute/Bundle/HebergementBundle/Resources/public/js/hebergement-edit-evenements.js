/**
 * Created by Stephane on 19/10/2016.
 */
"use strict";
//            redimensionne les datatables lorsqu'on clique sur un onglet
$(document).delegate('a[data-toggle="tab"]', 'shown.bs.tab', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
});
//            redimensionne les datatables lorsqu'on affiche un panel-collapse
$(document).delegate('.panel-collapse', 'shown.bs.collapse', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
});
//            ajoute la fonctionnalité de duplication du stock sur les éléments de classe .duplique-stocks (il est nécessaire de renseigner dans cet élément data-logement_id et data-periode_type_id
$(document).delegate('.duplique-stocks', 'click', function (e) {
//                récupération des attributs data-
    var datas = $(this).data();
    dupliquerStocksLogement(datas.logement_id, datas.periode_type_id);
});
//            ajoute la fonction modificationStock aux champs input dont le nom commence par name
$(document).delegate('.table-hebergement-stocks input[name^="stocks"]', 'change', function (e) {
    modificationStock($(this));
});
$(document).on('resize', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
});
$(document).delegate('[name="btnEnregistrerStocks"]', 'click', enregistrerStocks);
//        descative les champs stocks du formulaire quand on clique sur enregistrer la fiche hébergement
$(document).delegate('button#hebergement_unifie_submit', 'click', function (e) {
//            récupère le form qui contient le bouton cliqué
    var $form = $(this).closest('form');
//            desactive les champs stocks seulement si le formulaire est valide
    if ($form.get(0).checkValidity() == true) {
        desactiveChamps($form, ['input[name^="stocks"]']);
    }
});