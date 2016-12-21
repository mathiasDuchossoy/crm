/**
 * Created by Stephane on 21/12/2016.
 */
"use strict";
if (!globals) {
    var globals = {};
}
globals.prestationAnnexesStockFournisseurModifies = [];
globals.nbObjetsPrestationAnnexesStockFournisseurModifies = 4;
function chargerColonnesPeriodesDatatable(periodes) {
    var colonnes = [];
    var debut = null;
    var fin = null;
    var moisDebut = null;
    var jourDebut = null;
    var moisFin = null;
    var jourFin = null;
    var attribut = null;
    for (var k = 0; k < periodes.length; k++) {
        debut = new Date(periodes[k].debut.date.substring(0, 10).replace(/-/g, "/"));
        fin = new Date(periodes[k].fin.date.substring(0, 10).replace(/-/g, "/"));
        moisDebut = (debut.getMonth() + 1).toString().length > 1 ? (debut.getMonth() + 1) : '0' + (debut.getMonth() + 1);
        jourDebut = debut.getDate().toString().length > 1 ? debut.getDate() : '0' + debut.getDate();
        moisFin = (fin.getMonth() + 1).toString().length > 1 ? (fin.getMonth() + 1) : '0' + (fin.getMonth() + 1);
        jourFin = fin.getDate().toString().length > 1 ? fin.getDate() : '0' + fin.getDate();
        attribut = 'periode' + periodes[k].id;
        colonnes.push({
            mDataProp: attribut,
            title: 'du ' + jourDebut + '-' + moisDebut + '-' + debut.getFullYear().toString() + ' au ' + jourFin + '-' + moisFin + '-' + fin.getFullYear().toString()
        });
    }
    return colonnes;
}
function ajouterLignePrestationAnnexeStockFournisseur(table, parametres, data, indiceFournisseurPrestationAnnexe) {
    var url = globals.urls.chargerFournisseurPrestationAnnexeStockFournisseur;
    var stock = null;
    url = url.replace(/__idFournisseurPrestationAnnexe__/g, parametres.donnees.fournisseurPrestationAnnexes[indiceFournisseurPrestationAnnexe].id).replace(/__idTypePeriode__/g, parametres.donnees.typePeriode.id);
    $.ajax(url, {type: 'post'}).done(function (reponse) {
//                console.log(reponse);
        var attribut = null;
        for (var iPeriodes = 0; iPeriodes < parametres.donnees.typePeriode.periodes.length; iPeriodes++) {
            stock = 0;
            attribut = 'periode' + parametres.donnees.typePeriode.periodes[iPeriodes].id;
            for (var iStock = 0; iStock < reponse.stocks.length; iStock++) {
                if (reponse.stocks[iStock].periode == parametres.donnees.typePeriode.periodes[iPeriodes].id) {
                    stock = reponse.stocks[iStock].stock;
                }
            }
            data[attribut] = genererInputStockPrestationAnnexeFournisseur(parametres.donnees.fournisseurPrestationAnnexes[indiceFournisseurPrestationAnnexe].id, parametres.donnees.typePeriode.periodes[iPeriodes], parametres.donnees.typePeriode.id, stock);
        }
        table.row.add(data).draw();
        table.columns.adjust();
        chargerProgressBar(parametres.progressBar, (parseFloat(parametres.progressBar.attr('aria-valuenow')) + parametres.pasProgressBar));
        if (parametres.progressBar.attr('aria-valuenow') >= 99) {
            parametres.progressBar.closest('.progress').remove();
        }
    });
}
function genererInputStockPrestationAnnexeFournisseur(idFournisseurPrestationAnnexe, periode, periodeType, stock) {
    return '<input data-periode-type="' + periodeType + '" data-fournisseur-prestation-annexe="' + idFournisseurPrestationAnnexe + '"  data-periode="' + periode.id + '" name="stocks[' + idFournisseurPrestationAnnexe + '][' + periode.id + ']" class="form-control numeric" type="text" size="2" maxlength="2" value="' + stock + '"/>';
}
// récupère la valeur modifiée du stock, si la ligne est supérieure a maxInputVars, création une nouvelle ligne afin d'éviter un débordement
function modificationPrestationAnnexeFournisseurStock($obj) {
    var datas = $obj.data();
    var ajout = true;
    for (var i = 0; i < globals.prestationAnnexesStockFournisseurModifies.length; i++) {
        if (globals.prestationAnnexesStockFournisseurModifies[i].fournisseurPrestationAnnexe == datas.fournisseurPrestationAnnexe) {
            for (var j = 0; j < globals.prestationAnnexesStockFournisseurModifies[i].periodes.length; j++) {
                if (globals.prestationAnnexesStockFournisseurModifies[i].periodes[j].id == datas.periode) {
                    ajout = false;
                    globals.prestationAnnexesStockFournisseurModifies[i].periodes[j].stock = $obj.val();
                    break;
                }
            }
            if (globals.prestationAnnexesStockFournisseurModifies[i].periodes.length < parseInt(globals.maxInputVars / globals.nbObjetsPrestationAnnexesStockFournisseurModifies)) {
                if (ajout == true) {
                    ajout = false;
                    globals.prestationAnnexesStockFournisseurModifies[i].periodes.push({
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
            'fournisseurPrestationAnnexe': datas.fournisseurPrestationAnnexe,
            'periodes': [{'id': datas.periode, 'stock': $obj.val()}]
        };
        globals.prestationAnnexesStockFournisseurModifies.push(stock);
    }
}
function enregistrerPrestationAnnexeFournisseurStocks() {
    var $button = $(this);
    var $element = $button.parent();
    var $reponse = $element.find('[name="btnEnregistrerPrestationAnnexeFournisseurStocksReponse"]');
    $reponse.html('');
    if (globals.prestationAnnexesStockFournisseurModifies.length > 0) {
        enregistrerPrestationAnnexeFournisseurStock($button, $reponse, 0);
//                $button.button('reset');
//                $reponse.html('<div class="alert alert-info">' + globals.langue.enregistrer.stock_aucun + '</div>');
    } else {
        $reponse.html('<div class="alert alert-info">' + globals.langue.enregistrer.stock_aucun + '</div>');
//                $button.button('reset');
    }
}
// enregistrer le stock ligne par ligne
function enregistrerPrestationAnnexeFournisseurStock($button, $reponse, indice) {
    var stocks = [];
    stocks.push(globals.prestationAnnexesStockFournisseurModifies[indice]);

    $.ajax({
        url: globals.urls.enregistrerFournisseurPrestationAnnexeStockFournisseur,
        type: 'POST',
        data: {"stocks": stocks},
        success: function (json) {
            if (json.valid) {
                indice++;
                if (globals.prestationAnnexesStockFournisseurModifies[indice] == null) {
                    globals.prestationAnnexesStockFournisseurModifies = [];
                    $reponse.html('<div class="alert alert-success">' + globals.langue.enregistrer.stock_ok + '</div>');
//                            $button.button('reset');
                }
                else {
                    enregistrerPrestationAnnexeFournisseurStock($button, $reponse, indice);
                }
            } else {
                $reponse.html('<div class="alert alert-danger">' + globals.langue.enregistrer.stock_pas_ok + '</div>');
//                        $button.button('reset');
            }
        }
    }, 'json');
}
//        copie le premier stock du logement dans le tableau sur les autres stocks
function dupliquerPrestationAnnexeFournisseurStocks(datas) {
//            console.log(datas);
    var $ref = $('input[data-fournisseur-prestation-annexe="' + datas.prestationAnnexeId + '"][data-periode-type="' + datas.periodeTypeId + '"]:first');
//            console.log($ref);
    var valeur = $ref.val();
    $ref.attr('value', valeur);
    $('input[data-fournisseur-prestation-annexe=' + datas.prestationAnnexeId + '][data-periode-type=' + datas.periodeTypeId + ']').each(function () {
        $(this).val(valeur);
        $(this).attr('value', valeur);
        modificationPrestationAnnexeFournisseurStock($(this));
    });
}