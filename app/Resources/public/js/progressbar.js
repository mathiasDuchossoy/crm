/**
 * Created by Stephane on 12/10/2016.
 */
"use strict";

/***************************************************************
 * Gestion des fonctions
 */
/**
 * Permet d'attribuer la valeur [valeur] à l'objet $progressBar
 * @param $progressBar
 * @param valeur
 */
function chargerProgressBar($progressBar, valeur) {
    if ($progressBar != null && $progressBar.attr('role') == 'progressbar') {
        $progressBar.attr('aria-valuenow', parseFloat(valeur));
        $progressBar.css('width', parseFloat(valeur) + '%');
        $progressBar.html(parseInt(valeur) + '%');
    }
}
/**
 * supprime l'element parent ayant la classe .progress de l'objet $progressBar
 * @param $progressBar
 */
function supprimerProgressBar($progressBar) {
    if ($progressBar != null) {
        var $conteneur = $progressBar.closest('.progress');
        if ($conteneur != null) {
            $conteneur.remove();
        }
    }
}