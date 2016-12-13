/**
 * Created by Stephane on 12/10/2016.
 */
"use strict";

/*************************************************************
 * Gestion des fonctions
 */
/**
 * permet de construire les onglets TypePeriodes ainsi que les div pour leurs contenus
 * @param $conteneur
 * @param $progressBar
 */
function construireOngletsTypePeriodes($conteneur, $progressBar, callback) {
    if(urlTypePeriodeOnglets == null){
        var urlTypePeriodeOnglets = urls.urlTypePeriodeOnglets;
    }
    $.post(urlTypePeriodeOnglets,
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
    ), 'json';
}