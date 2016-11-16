/**
 * Created by Stephane on 19/10/2016.
 */
"use strict";
var stocksModifies = [];
var nbObjetsStocksModifies = 3;
if (maxInputVars == null) {
    var maxInputVars = 1000;
}
// récupère la valeur modifiée du stock, si la ligne est supérieure a maxInputVars, création une nouvelle ligne afin d'éviter un débordement
function modificationStock($obj) {
    var datas = $obj.data();
    var ajout = true;
    for (var i = 0; i < stocksModifies.length; i++) {
        if (stocksModifies[i].logementUnifieId == datas.logement) {
            for (var j = 0; j < stocksModifies[i].periodes.length; j++) {
                if (stocksModifies[i].periodes[j].id == datas.periode) {
                    ajout = false;
                    stocksModifies[i].periodes[j].stock = $obj.val();
                    break;
                }
            }
            if (stocksModifies[i].periodes.length < parseInt(maxInputVars / nbObjetsStocksModifies)) {
                if (ajout == true) {
                    ajout = false;
                    stocksModifies[i].periodes.push({'id': datas.periode, 'stock': $obj.val()});
                }
                break;
            }
        }
    }
    if (ajout == true) {
        var stock = {
            'logementUnifieId': datas.logement,
            'periodes': [{'id': datas.periode, 'stock': $obj.val()}]
        };
        stocksModifies.push(stock);
    }
}
//        copie le premier stock du logement dans le tableau sur les autres stocks
function dupliquerStocksLogement(logementId, typePeriodeId) {
    var $ref = $('input[data-logement=' + logementId + '][data-type_periode=' + typePeriodeId + ']:first');
    var valeur = $ref.val();
    $ref.attr('value', valeur);
    $('input[data-logement=' + logementId + '][data-type_periode=' + typePeriodeId + ']').each(function () {
        $(this).val(valeur);
        $(this).attr('value', valeur);
        modificationStock($(this));
    });
}
function chargerTableauStocks(table, parametres, $progressBar) {
    var logements = [];
    logements['logements'] = [];
    for (var i = 0; i < parametres.logements.length; i++) {
        logements['logements'][i] = parametres.logements[i].id;
    }
    ajouterLogementLocatifTableau(table, logements, parametres, $progressBar);
}
function ajouterLogementLocatifTableau(table, logements, parametres, $progressBar) {
    $.post(
        urls.logementChargerLocatif,
        {'logements[]': logements['logements']},
        function (datas) {
            var donnees = [];
            var data = null;
            for (var iDatas = 0; iDatas < datas['logements'].length; iDatas++) {
                donnees[iDatas] = [];
                data = datas['logements'][iDatas];
                donnees[iDatas]['logement'] = '<div><span data-logement_id="' + data.logementUnifie.id + '" data-periode_type_id="' + parametres.typePeriode.id + '" title="copie le stock de la première période de ce logement sur toutes les périodes du logement" class="duplique-stocks glyphicon glyphicon-play pull-right"> </span>' + data.nom + '</div>';
                var attribut = null;
                for (var i = 0; i < data.periodes.length; i++) {

                    if (data.periodes[i].type.id == parametres.typePeriode.id) {
                        attribut = 'periode' + data.periodes[i].id;
                        donnees[iDatas][attribut] = genererInputStock(data.logementUnifie.id, data.periodes[i], data.periodes[i].stock);
                    }
                }
                chargerProgressBar($progressBar, (parseFloat($progressBar.attr('aria-valuenow')) + parametres.pasProgressBar));
                if ($progressBar.attr('aria-valuenow') >= 99) {
                    $progressBar.closest('.progress').remove();
                }
            }
            table.rows.add(donnees).draw();
            if (datas['suivant'] != null) {
                var i = 0;
                while (i < logements['logements'].length && logements['logements'][i] != datas['suivant']) {
                    i++;
                }
                var indice = i;
                var newLogements = [];
                newLogements['logements'] = [];
                for (indice = i; indice < logements['logements'].length; indice++) {
                    newLogements['logements'].push(logements['logements'][indice]);
                }
                ajouterLogementLocatifTableau(table, newLogements, parametres, $progressBar);
            }
        }, 'json'
    );
}
function genererInputStock(idLogementUnifie, periode, stock) {
    return '<input data-logement="' + idLogementUnifie + '" data-type_periode="' + periode.type.id + '" data-periode="' + periode.id + '" name="stocks[' + idLogementUnifie + '][' + periode.id + ']" class="form-control" type="text" size="2" maxlength="2" value="' + stock + '"/>';
}
function chargerFournisseurHebergements(idHebergement, typePeriodes, callback) {
    var url = urls.hebergementChargerHebergementFournisseur;
    url = url.replace(/_idHebergement_/g, parseInt(idHebergement));
    $.get(
        url,
        function (data) {
            if (callback != null) {
                callback(data, idHebergement, typePeriodes);
            }
        },
        'json'
    )
}
function chargerProgressBar($progressBar, valeur) {
    $progressBar.attr('aria-valuenow', parseFloat(valeur));
    $progressBar.css('width', parseFloat(valeur) + '%');
    $progressBar.html(parseInt(valeur) + '%');
}
/**
 * permet de construire les onglets TypePeriodes ainsi que les div pour leurs contenus
 * @param $conteneur
 * @param $progressBar
 */
function construireOngletsTypePeriodes($conteneur, $progressBar, callback) {
    $.post(urls.periodeTypePeriodeOnglets,
        {'idConteneur': $conteneur.attr('id')},
        function (html) {
            if ($progressBar != null) {
                chargerProgressBar($progressBar, 100);
            }
            $conteneur.html(html);
            if (callback != null) {
                callback(html);
            }
        }
    ), 'html';
}
function chargerOngletStocksHebergement(idHebergement) {
//            récupération de la progress-bar à gérer lors de l'ajout des onglets TypePeriode
    var $progressBar = $('#progress-bar_onglets_type_periode_hebergement_' + idHebergement);
    var $conteneur = $('#data_hebergement_stocks');
    $.post(urls.periodeHebergementStocks,
        function (typePeriodes) {
            chargerProgressBar($progressBar, 50);
            construireOngletsTypePeriodes($conteneur, $progressBar, function () {
                var idTypePeriode = null;
                chargerFournisseurHebergements(idHebergement, typePeriodes, function (data, idHebergement, typePeriodes) {
                    for (var i = 0; i < typePeriodes.length; i++) {
                        if (typePeriodes[i].periodes != null && typePeriodes[i].periodes.length > 0) {
                            idTypePeriode = typePeriodes[i].id;
                            $progressBar = $('#' + $conteneur.attr('id') + '_progress-bar_type_periode_' + typePeriodes[i].id);
                            var colonnes = [{'mDataProp': "logement", title: 'logement'}];
                            var debut = null;
                            var fin = null;
                            var moisDebut = null;
                            var jourDebut = null;
                            var moisFin = null;
                            var jourFin = null;
                            var attribut = null;
                            var stock = null;
                            for (var k = 0; k < typePeriodes[i].periodes.length; k++) {
                                // console.log(typePeriodes[i].periodes[k].debut.date.substring(0,10));
                                debut = new Date(typePeriodes[i].periodes[k].debut.date.substring(0, 10).replace(/-/g, "/"));
                                fin = new Date(typePeriodes[i].periodes[k].fin.date.substring(0, 10).replace(/-/g, "/"));
                                // console.log(debut);
                                moisDebut = (debut.getMonth() + 1).toString().length > 1 ? (debut.getMonth() + 1) : '0' + (debut.getMonth() + 1);
                                jourDebut = debut.getDate().toString().length > 1 ? debut.getDate() : '0' + debut.getDate();
                                moisFin = (fin.getMonth() + 1).toString().length > 1 ? (fin.getMonth() + 1) : '0' + (fin.getMonth() + 1);
                                jourFin = fin.getDate().toString().length > 1 ? fin.getDate() : '0' + fin.getDate();
                                attribut = 'periode' + typePeriodes[i].periodes[k].id;
                                colonnes.push({
                                    mDataProp: attribut,
                                    title: 'du ' + jourDebut + '-' + moisDebut + '-' + debut.getFullYear().toString() + ' au ' + jourFin + '-' + moisFin + '-' + fin.getFullYear().toString()
                                });
                            }
                            var $div = $conteneur.find('#' + $conteneur.attr('id') + '_data_type_periode_' + idTypePeriode + ' .panel-group');
                            for (var j = 0; j < data.fournisseurHebergements.length; j++) {
                                var $panelFournisseur = $('<div class="panel panel-default"> <div class="panel-heading"> <div class="panel-title"> <h2> <a data-toggle="collapse" href="#stocks_type_periode_' + typePeriodes[i].id + '_fournisseur_' + data.fournisseurHebergements[j].fournisseur.id + '">' + data.fournisseurHebergements[j].fournisseur.enseigne + '</a> </h2> </div> </div> <div id="stocks_type_periode_' + typePeriodes[i].id + '_fournisseur_' + data.fournisseurHebergements[j].fournisseur.id + '" class="panel-collapse collapse in"></div> </div>');
                                $div.append($panelFournisseur);
                                var $tableau = $('<table class="table-hebergement-stocks table table-striped nowrap display" width="100%" id="type-periode-' + idTypePeriode + '-indice-' + data.fournisseurHebergements[j].fournisseur.id + '"></table>');
                                $panelFournisseur.find('#stocks_type_periode_' + idTypePeriode + '_fournisseur_' + data.fournisseurHebergements[j].fournisseur.id).append($tableau);
                                var parametres = {
                                    'donnees': {
                                        hebergement: {id: idHebergement},
                                        typePeriode: {id: idTypePeriode},
                                        fournisseur: {
                                            id: data.fournisseurHebergements[j].fournisseur.id
                                        },
                                        logements: data.fournisseurHebergements[j].logements,
                                        nbTotalLogements: data.nbLogements,
                                        pasProgressBar: 100 / data.nbLogements
                                    },
                                    datatable: {
                                        data: null,
                                        "aoColumns": colonnes,
                                        "fnCreatedRow": function (nRow, aData, iDataIndex) {
                                            $(nRow).find('td').attr('style', 'vertical-align: middle;');
                                        },
                                        "bAutoWidth": true,
                                        aoColumnDefs: [
                                            {"bSortable": true, aTargets: [0]},
                                            {"bSortable": false, aTargets: ['_all']},
                                        ],
                                        paging: true,
                                        info: false,
                                        pageLength: 25,
                                        scrollX: true,
                                        scrollY: false,
                                        scrollCollapse: true,
                                        fixedColumns: true,
                                        scroller: {
                                            rowHeight: 30
                                        },
                                        language: datatableLangue.language,
                                    }
                                };
                                var table = genererDataTables($tableau, parametres, $progressBar, function (table, parametres, $progressBar) {
                                    chargerTableauStocks(table, parametres.donnees, $progressBar);
                                });
                            }
                        }
                    }

                });
                $conteneur.append('<button data-loading-text="<span class=\'glyphicon glyphicon-refresh glyphicon-refresh-animate\'></span> Enregistrement en cours" type="button" name="btnEnregistrerStocks" class="btn btn-default">' + langue.enregistrer.stock + '</button> <div name="btnEnregistrerStocksReponse"></div>');
            });
        }, 'json');
}
// function enregistrerStocks() {
//     var $button = $(this);
//     var $element = $button.parent();
//     var $reponse = $element.find('[name="btnEnregistrerStocksReponse"]');
//     $button.button('loading');
//     if (stocksModifies.length > 0) {
// //            lance la requête ajax de recherche des fournisseurs de type hébergement
//         $.ajax({
//             url: urls.catalogueEnregistrerStockLocatif,
//             type: 'POST',
//             data: {"stocks": stocksModifies},
//             success: function (json) {
//                 if (json.valid) {
//                     stocksModifies = Array();
//                     $reponse.html('<div class="alert alert-success">' + langue.enregistrer.stock_ok + '</div>');
//                     $button.button('reset');
//                 } else {
//                     $reponse.html('<div class="alert alert-danger">' + langue.enregistrer.stock_pas_ok + '</div>');
//                     $button.button('reset');
//                 }
//             }
//         }, 'json');
//     } else {
//         $reponse.html('<div class="alert alert-info">' + langue.enregistrer.stock_aucun + '</div>');
//         $button.button('reset');
//     }
// }
function enregistrerStocks() {
    var $button = $(this);
    var $element = $button.parent();
    var $reponse = $element.find('[name="btnEnregistrerStocksReponse"]');
    $reponse.html('');
    $button.button('loading');
    if (stocksModifies.length > 0) {
        enregistrerStock($button, $reponse, 0);
    } else {
        $reponse.html('<div class="alert alert-info">' + langue.enregistrer.stock_aucun + '</div>');
        $button.button('reset');
    }
}
// enregistrer le stock ligne par ligne
function enregistrerStock($button, $reponse, indice) {
    var stocks = [];
    stocks.push(stocksModifies[indice]);

    $.ajax({
        url: urls.catalogueEnregistrerStockLocatif,
        type: 'POST',
        data: {"stocks": stocks},
        success: function (json) {
            if (json.valid) {
                indice++;
                if (stocksModifies[indice] == null) {
                    stocksModifies = [];
                    $reponse.html('<div class="alert alert-success">' + langue.enregistrer.stock_ok + '</div>');
                    $button.button('reset');
                }
                else {
                    enregistrerStock($button, $reponse, indice);
                }
            } else {
                $reponse.html('<div class="alert alert-danger">' + langue.enregistrer.stock_pas_ok + '</div>');
                $button.button('reset');
            }
        }
    }, 'json');
}