/**
 * Created by Stephane on 12/10/2016.
 */
"use strict";

/**************************************************
 * Gestion des évènements
 */
$(document).delegate('a[data-toggle="tab"]', 'shown.bs.tab', function (e) {
    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
});

/**************************************************
 * Gestion des fonctions
 */
/**
 * création d'un tableau de type datatable avec fonction de callback
 * @param $tableau
 * @param parametres
 * @param $progressBar
 * @param callback
 */
function genererDataTables($tableau, parametres, $progressBar, callback) {
    var table = $tableau.DataTable(parametres.datatable);
    if (callback != null) {
        callback(table, parametres, $progressBar);
    }
    return table;
}