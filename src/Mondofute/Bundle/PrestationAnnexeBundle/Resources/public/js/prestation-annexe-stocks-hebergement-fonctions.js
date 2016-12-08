"use strict";
if (!globals) {
    var globals = {};
}
globals.prestationAnnexesStockHebergementModifies = [];
globals.nbObjetsPrestationAnnexesStockHebergementModifies = 4;
//            redimensionne les datatables lorsqu'on clique sur un onglet

$(document).ready(function () {
//            récupération du conteneur
    var $conteneur = $('#prestation-annexes-stocks-hebergements');

    $.ajax(urls.periodeTypePeriodeListe, {type: 'post'}).done(
//                récupération des typePeriodes avec les périodes à l'intérieur
        function (typePeriodes) {
            if (typePeriodes.length > 0) {
//                        affichage des onglets de type période
                construireOngletsTypePeriodes($conteneur, null, function () {
                    var idTypePeriode = null;
                    var $progressBar = null;
//                            balaye les typePeriodes
                    for (var indiceTypePeriode = 0; indiceTypePeriode < typePeriodes.length; indiceTypePeriode++) {
//                                test si une période existe pour ce type de période
                        if (typePeriodes[indiceTypePeriode].periodes != null && typePeriodes[indiceTypePeriode].periodes.length > 0) {
                            idTypePeriode = typePeriodes[indiceTypePeriode].id;
                            $progressBar = $('#' + $conteneur.attr('id') + '_progress-bar_type_periode_' + typePeriodes[indiceTypePeriode].id);
                            var colonnes = [{
                                'mDataProp': 'familleSousFamille',
                                title: 'famille / sous famille'
                            }, {'mDataProp': "prestationAnnexe", title: 'prestation annexe'}];
                            var debut = null;
                            var fin = null;
                            var moisDebut = null;
                            var jourDebut = null;
                            var moisFin = null;
                            var jourFin = null;
                            var attribut = null;
                            for (var k = 0; k < typePeriodes[indiceTypePeriode].periodes.length; k++) {
                                debut = new Date(typePeriodes[indiceTypePeriode].periodes[k].debut.date.substring(0, 10).replace(/-/g, "/"));
                                fin = new Date(typePeriodes[indiceTypePeriode].periodes[k].fin.date.substring(0, 10).replace(/-/g, "/"));
                                moisDebut = (debut.getMonth() + 1).toString().length > 1 ? (debut.getMonth() + 1) : '0' + (debut.getMonth() + 1);
                                jourDebut = debut.getDate().toString().length > 1 ? debut.getDate() : '0' + debut.getDate();
                                moisFin = (fin.getMonth() + 1).toString().length > 1 ? (fin.getMonth() + 1) : '0' + (fin.getMonth() + 1);
                                jourFin = fin.getDate().toString().length > 1 ? fin.getDate() : '0' + fin.getDate();
                                attribut = 'periode' + typePeriodes[indiceTypePeriode].periodes[k].id;
                                colonnes.push({
                                    mDataProp: attribut,
                                    title: 'du ' + jourDebut + '-' + moisDebut + '-' + debut.getFullYear().toString() + ' au ' + jourFin + '-' + moisFin + '-' + fin.getFullYear().toString()
                                });
                            }
                            var $div = $conteneur.find('#' + $conteneur.attr('id') + '_data_type_periode_' + idTypePeriode + ' .panel-group');
                            var $tableau = $('<table class="table-prestation-annexe-hebergement-stocks table table-striped nowrap display" width="100%" id="type-periode-' + idTypePeriode + '-prestation-annexes"></table>');
                            $div.append($tableau);
                            var parametres = {
                                'donnees': {
                                    typePeriode: {
                                        id: idTypePeriode,
                                        periodes: typePeriodes[indiceTypePeriode].periodes
                                    },
                                    prestationAnnexes: globals.prestationAnnexes,
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
                                        {"bSortable": true, aTargets: [1]},
                                        {"bSortable": false, aTargets: ['_all']},
                                        {"aDataSort": [0, 1], "aTargets": [0]},
                                    ],
                                    paging: true,
                                    info: false,
                                    pageLength: 25,
                                    scrollX: true,
                                    scrollY: false,
                                    scrollCollapse: true,
                                    fixedColumns: {
                                        "iLeftColumns": 2,
                                    },
                                    scroller: {
                                        rowHeight: 30
                                    },
                                    language: datatableLangue.language,
                                }
                            };
                            var table = genererDataTables($tableau, parametres, $progressBar, function (table, parametres, $progressBar) {
                                var data = [];
                                if (parametres.donnees.prestationAnnexes.length > 0) {
                                    parametres.pasProgressBar = 100 / parametres.donnees.prestationAnnexes.length;
                                    for (var indicePrestationAnnexe = 0; indicePrestationAnnexe < parametres.donnees.prestationAnnexes.length; indicePrestationAnnexe++) {
                                        data = [];
                                        data['familleSousFamille'] = parametres.donnees.prestationAnnexes[indicePrestationAnnexe].famille.libelle;
                                        if (parametres.donnees.prestationAnnexes[indicePrestationAnnexe].sousFamille != null) {
                                            data['familleSousFamille'] += '/' + parametres.donnees.prestationAnnexes[indicePrestationAnnexe].sousFamille.libelle;
                                        }
//                                            data['prestationAnnexe'] = parametres.donnees.prestationAnnexes[indicePrestationAnnexe].libelle;
                                        data['prestationAnnexe'] = '<div><span data-fournisseur-hebergement="' + globals.fournisseurHebergementId + '" data-prestation-annexe-id="' + parametres.donnees.prestationAnnexes[indicePrestationAnnexe].id + '" data-periode-type-id="' + parametres.donnees.typePeriode.id + '" title="' + langue.dupliquePrestationAnnexeAebergementStocks.texte + '" class="duplique-prestation-annexe-hebergement-stocks glyphicon glyphicon-play pull-right cliquable"> </span>' + parametres.donnees.prestationAnnexes[indicePrestationAnnexe].libelle + '</div>';
                                        parametres.progressBar = $progressBar;
                                        ajouterLignePrestationAnnexeStockHebergement(table, parametres, data, indicePrestationAnnexe);
                                    }
                                } else {
                                    $progressBar.closest('.progress').remove();
                                }
                            });
                        }
                    }
                    $conteneur.append('<button data-loading-text="<span class=\'glyphicon glyphicon-refresh glyphicon-refresh-animate\'></span> Enregistrement en cours" type="button" name="btnEnregistrerPrestationAnnexeHebergementStocks" class="btn btn-default">' + langue.enregistrer.stock + '</button> <div name="btnEnregistrerPrestationAnnexeHebergementStocksReponse"></div>');

                });
            }
        }
    );
});
// récupère la valeur modifiée du stock, si la ligne est supérieure a maxInputVars, création une nouvelle ligne afin d'éviter un débordement
function modificationPrestationAnnexeHebergementStock($obj) {
    var datas = $obj.data();
    var ajout = true;
    for (var i = 0; i < globals.prestationAnnexesStockHebergementModifies.length; i++) {
        if (globals.prestationAnnexesStockHebergementModifies[i].fournisseurHebergement == datas.fournisseurHebergement && globals.prestationAnnexesStockHebergementModifies[i].fournisseurPrestationAnnexe == datas.fournisseurPrestationAnnexe) {
            for (var j = 0; j < globals.prestationAnnexesStockHebergementModifies[i].periodes.length; j++) {
                if (globals.prestationAnnexesStockHebergementModifies[i].periodes[j].id == datas.periode) {
                    ajout = false;
                    globals.prestationAnnexesStockHebergementModifies[i].periodes[j].stock = $obj.val();
                    break;
                }
            }
            if (globals.prestationAnnexesStockHebergementModifies[i].periodes.length < parseInt(globals.maxInputVars / globals.nbObjetsPrestationAnnexesStockHebergementModifies)) {
                if (ajout == true) {
                    ajout = false;
                    globals.prestationAnnexesStockHebergementModifies[i].periodes.push({
                        'id': datas.periode,
                        'stock': $obj.val()
                    });
                }
                break;
            }
        }
    }
    if (ajout == true) {
        var stock = {
            'fournisseurHebergement': datas.fournisseurHebergement,
            'fournisseurPrestationAnnexe': datas.fournisseurPrestationAnnexe,
            'periodes': [{'id': datas.periode, 'stock': $obj.val()}]
        };
        globals.prestationAnnexesStockHebergementModifies.push(stock);
    }
}
function ajouterLignePrestationAnnexeStockHebergement(table, parametres, data, indicePrestationAnnexe) {
    var url = urls.mondofute_fournisseur_prestation_annexe_stock_hebergement_charger;
    var stock = null;
    url = url.replace(/__idPrestationAnnexe__/g, parametres.donnees.prestationAnnexes[indicePrestationAnnexe].id).replace(/__idFournisseurHebergement__/g, globals.fournisseurHebergementId).replace(/__idTypePeriode__/g, parametres.donnees.typePeriode.id);
    $.ajax(url, {type: 'post'}).done(function (reponse) {
        var attribut = null;
        for (var iPeriodes = 0; iPeriodes < parametres.donnees.typePeriode.periodes.length; iPeriodes++) {
            stock = 0;
            attribut = 'periode' + parametres.donnees.typePeriode.periodes[iPeriodes].id;
            for (var iStock = 0; iStock < reponse.stocks.length; iStock++) {
                if (reponse.stocks[iStock].periode == parametres.donnees.typePeriode.periodes[iPeriodes].id) {
                    stock = reponse.stocks[iStock].stock;
                }
            }
            data[attribut] = genererInputStockPrestationAnnexeHebergement(globals.fournisseurHebergementId, parametres.donnees.prestationAnnexes[indicePrestationAnnexe].id, parametres.donnees.typePeriode.periodes[iPeriodes], parametres.donnees.typePeriode.id, stock);
        }
        table.row.add(data).draw();
        table.columns.adjust();
        chargerProgressBar(parametres.progressBar, (parseFloat(parametres.progressBar.attr('aria-valuenow')) + parametres.pasProgressBar));
        if (parametres.progressBar.attr('aria-valuenow') >= 99) {
            parametres.progressBar.closest('.progress').remove();
        }
    });
}
function chargerProgressBar($progressBar, valeur) {
    $progressBar.attr('aria-valuenow', parseFloat(valeur));
    $progressBar.css('width', parseFloat(valeur) + '%');
    $progressBar.html(parseInt(valeur) + '%');
}
function genererInputStockPrestationAnnexeHebergement(idFournisseurHebergement, idFournisseurPrestationAnnexe, periode, periodeType, stock) {
    return '<input data-periode-type="' + periodeType + '" data-fournisseur-hebergement="' + idFournisseurHebergement + '" data-fournisseur-prestation-annexe="' + idFournisseurPrestationAnnexe + '"  data-periode="' + periode.id + '" name="stocks[' + idFournisseurHebergement + '][' + idFournisseurPrestationAnnexe + '][' + periode.id + ']" class="form-control numeric" type="text" size="2" maxlength="2" value="' + stock + '"/>';
}
function enregistrerPrestationAnnexeHebergementStocks() {
    var $button = $(this);
    var $element = $button.parent();
    var $reponse = $element.find('[name="btnEnregistrerPrestationAnnexeHebergementStocksReponse"]');
    $reponse.html('');
    $button.button('loading');
    if (globals.prestationAnnexesStockHebergementModifies.length > 0) {
        enregistrerPrestationAnnexeHebergementStock($button, $reponse, 0);
    } else {
        $reponse.html('<div class="alert alert-info">' + langue.enregistrer.stock_aucun + '</div>');
        $button.button('reset');
    }
}
//        copie le premier stock du logement dans le tableau sur les autres stocks
function dupliquerPrestationAnnexeHebergementStocks(datas) {
    console.log(datas);
    var $ref = $('input[data-fournisseur-hebergement="' + datas.fournisseurHebergement + '"][data-fournisseur-prestation-annexe="' + datas.prestationAnnexeId + '"][data-periode-type="' + datas.periodeTypeId + '"]:first');
    console.log($ref);
    var valeur = $ref.val();
    $ref.attr('value', valeur);
    $('input[data-fournisseur-hebergement=' + datas.fournisseurHebergement + '][data-fournisseur-prestation-annexe=' + datas.prestationAnnexeId + '][data-periode-type=' + datas.periodeTypeId + ']').each(function () {
        $(this).val(valeur);
        $(this).attr('value', valeur);
        modificationPrestationAnnexeHebergementStock($(this));
    });
}
// enregistrer le stock ligne par ligne
function enregistrerPrestationAnnexeHebergementStock($button, $reponse, indice) {
    var stocks = [];
    stocks.push(globals.prestationAnnexesStockHebergementModifies[indice]);

    $.ajax({
        url: urls.mondofute_fournisseur_prestation_annexe_stock_hebergement_enregistrer,
        type: 'POST',
        data: {"stocks": stocks},
        success: function (json) {
            if (json.valid) {
                indice++;
                if (globals.prestationAnnexesStockHebergementModifies[indice] == null) {
                    globals.prestationAnnexesStockHebergementModifies = [];
                    $reponse.html('<div class="alert alert-success">' + langue.enregistrer.stock_ok + '</div>');
                    $button.button('reset');
                }
                else {
                    enregistrerPrestationAnnexeHebergementStock($button, $reponse, indice);
                }
            } else {
                $reponse.html('<div class="alert alert-danger">' + langue.enregistrer.stock_pas_ok + '</div>');
                $button.button('reset');
            }
        }
    }, 'json');
}