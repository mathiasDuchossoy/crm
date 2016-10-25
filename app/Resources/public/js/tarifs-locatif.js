/**
 * Created by Stephane on 12/10/2016.
 */
"use strict";

/******************************************************************
 * Gestion des variables
 */
var donneesModifiees = Array();

/******************************************************************
 * Gestion des fonctions
 */
/**
 * Appel Ajax pour l'enregistrement des Tarifs Locatif
 */
function enregistrerTarifsLocatif() {
    var $button = $(this);
    $button.button('loading');
    var $element = $button.parent();
    var $reponse = $element.find('[name="btnEnregistrerTarifsLocatifReponse"]');
    if (donneesModifiees.length > 0) {
        $.post(
            urlEnregistrementTarifs,
            {'tarifs': donneesModifiees, 'logementUnifieId': logementUnifieId},
            function (data) {
                if (data.valid == true) {
                    $reponse.html('<div class="alert alert-success">Les données ont bien été enregistrés</div>');
                    $button.button('reset');
                    donneesModifiees = Array();
                } else {
                    $reponse.html('<div class="alert alert-danger">Une erreur est survenue lors de l\'enregistrement</div>');
                    $button.button('reset');
                }
            },
            'json'
        ).fail(function () {
            $reponse.html('<div class="alert alert-danger">Une erreur est survenue lors de l\'enregistrement</div>');
            $button.button('reset');
        })
        ;
    } else {
        $reponse.html('<div class="alert alert-info">Aucune données à enregistrer</div>');
        $button.button('reset');
    }
}
/**
 * ajoute à l'objet donneesModifiees[idPeriode] les tarifs et stock afin de gérer l'enregistrement
 * @param $obj
 */
function modificationDonneesTarifs($obj) {
    var datas = $obj.data();
    var remplace = false;
    for (var i = 0; i < donneesModifiees.length; i++) {
        //  test si les tarifs pour la periode modifiée sont déjà présents afin de les modifier si l'élément est déjà présent
        if (donneesModifiees[i].periodeId == datas.periode_id) {
            donneesModifiees[i] = {
                periodeId: datas.periode_id,
                prixPublic: $('input[name="prixPublic[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
                prixFournisseur: $('input[name="prixFournisseur[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
                prixAchat: $('input[name="prixAchat[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
                stock: $('input[name="stock[' + datas.periode_id + ']"]').val(),
            };
            remplace = true;
            break;
        }
    }
    if (remplace == false) {
        //  tarifs à modifier non présent dans le tableau des "donneesModifiees" nous insérons alors dans le tableau un nouvel élement
        donneesModifiees.push({
            periodeId: datas.periode_id,
            prixPublic: $('input[name="prixPublic[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
            prixFournisseur: $('input[name="prixFournisseur[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
            prixAchat: $('input[name="prixAchat[' + datas.periode_id + ']"]').val().replace(/,/g, '.'),
            stock: $('input[name="stock[' + datas.periode_id + ']"]').val(),
        });
    }
}
/**
 * Création d'un champ texte pour le tableau des tarifs location
 * @param name
 * @param valeur
 * @param idPeriode
 * @param length
 * @returns {string}
 */
function genererInputTarifLocatif(name, valeur, idPeriode, length) {
    return '<input class="form-control" data-periode_id="' + idPeriode + '" name="' + name + '[' + idPeriode + ']" value="' + valeur + '"/>';
}
/**
 * création de la zone permettant de gérer les tarifs locatif dans l'objet jquery $conteneur
 * @param $conteneur
 */
function chargerTarifsLocatif($conteneur) {
    var existeTypePeriode = false;
    $.post(urlChargerTypePeriodeListe,
        function (typePeriodes) {
            construireOngletsTypePeriodes($conteneur, null, function () {
                var idTypePeriode = null;
                var $progressBar = null;
                var parametres = null;
                var $tableau = null;
                for (var indiceTypePeriode = 0; indiceTypePeriode < typePeriodes.length; indiceTypePeriode++) {
                    idTypePeriode = typePeriodes[indiceTypePeriode].id;
                    parametres = null;
                    $tableau = null;
                    $progressBar = null;
                    if (typePeriodes[indiceTypePeriode].periodes != null) {
                        existeTypePeriode = true;
                        $progressBar = $('#data_logement_locatif_tarifs_progress-bar_type_periode_' + idTypePeriode);

                        parametres = {
                            idTypePeriode: idTypePeriode,
                        };
                        // {#
                        // }#

                        $tableau = $('<table class="table-tarifs-logement table table-striped nowrap display" width="100%" id="tarifs-type-periode-' + idTypePeriode + '"></table>');
                        $('#data_logement_locatif_tarifs').find('#data_logement_locatif_tarifs_data_type_periode_' + idTypePeriode + ' .panel-group').append($tableau);
                        chargerProgressBar($progressBar, '25');
                        //création des paramètres pour la datatable
                        parametres.datatable = {
                            columns: datatableLangue.colonnes,
                            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                                $(nRow).find('td').attr('style', 'vertical-align: middle;');
                            },
                            paging: true,
                            info: false,
                            aoColumnDefs: [
                                {"orderable": true, aTargets: 0},
                                {"orderable": false, aTargets: ['_all']},
                            ],
                            "order": [[0, "asc"]],
                            "aaSorting": [[0, 'asc']],
                            pageLength: 25,
                            scrollX: true,
                            scrollY: false,
                            scrollCollapse: true,
                            fixedColumns: true,
                            scroller: {
                                rowHeight: 50,
                            },
                            language: datatableLangue.language,
                        };
                        genererDataTables($tableau, parametres, $progressBar, function (table, parametres, $progressBar) {
                            chargerProgressBar($progressBar, '50');
                            table.rows.add(donnees[parametres.idTypePeriode]).draw();
                            chargerProgressBar($progressBar, '75');
                            table.tables({visible: true, api: true}).columns.adjust();
                            chargerProgressBar($progressBar, '100');
                            supprimerProgressBar($progressBar);
                        });
                    }
                }
                if (existeTypePeriode == true) {
                    $conteneur.append('<button data-loading-text="<span class=\'glyphicon glyphicon-refresh glyphicon-refresh-animate\'></span> Enregistrement en cours" type="button" name="btnEnregistrerTarifsLocatif" class="btn btn-primary">Enregistrer Tarifs</button> <div name="btnEnregistrerTarifsLocatifReponse"></div>');
                }
            });
        });
}

/******************************************************************
 * Gestion des évènements
 */
/**
 * ajoute la fonction modificationDonneesTarifs lorsqu'on modifie un champs de type input dans un tableau dont la classe est table-tarifs-logement
 */
$(document).delegate('.table-tarifs-logement input', 'change', function (e) {
    modificationDonneesTarifs($(this));
});
/**
 * ajoute la fonction enregistrerTarifsLocatif lorsqu'on click sur un bouton dont le name est btnEnregistrerTarifsLocatif
 */
$(document).delegate('[name="btnEnregistrerTarifsLocatif"]', 'click', enregistrerTarifsLocatif);
