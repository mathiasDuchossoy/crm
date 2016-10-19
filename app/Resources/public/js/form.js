/**
 * Created by Stephane on 19/10/2016.
 */
"use strict";

/**
 * Gestion des évènements
 */
//  ajoute des fonctionnalités aux éléments button[type="submit"]
$(document).delegate('button[type="submit"]', 'click', function (e) {
    // charge le form contenant le bouton cliqué
    var $form = $(this).closest('form');
    // vérifie la validité du formulaire
    if ($form.get(0).checkValidity() == true) {
        // si le formulaire est valide donne l'attribut data-loading-text au bouton submit si ce dernier n'en a pas
        if ($(this).attr('data-loading-text') == null) {
            $(this).attr('data-loading-text', '<span class=\'glyphicon glyphicon-refresh glyphicon-refresh-animate\'></span> Enregistrement en cours');
        }
        // affiche la donnée data-loading-text (message de chargement dans le bouton submit)
        $(this).button('loading');
    }
});

/**
 * Cette fonction permet de désactiver les champs contenu dans $conteneur
 *
 * @param $conteneur conteneur dans lequel on va désactiver les champs répondants à conditions
 * @param conditions tableau de selector que nous allons désactiver
 */
function desactiveChamps($conteneur, conditions) {
    for (var i = 0; i < conditions.length; i++) {
        $conteneur.find(conditions[i]).prop("disabled", true);
    }
}
/**
 * Cette fonction permet d'activer les champs contenu dans $conteneur
 *
 * @param $conteneur conteneur dans lequel on va activer les champs répondants à "conditions"
 * @param conditions tableau de selector que nous allons activer
 */
function activeChamps($conteneur, conditions) {
    for (var i = 0; i < conditions.length; i++) {
        $conteneur.find(conditions[i]).prop("disabled", false);
    }
}