<?php
/**
 * Description d'utilisation
 *
 * Fichier batch type
 *
 * Utilisation du batch veuillez vous référez à la documentation de doctrine pour savoir comment utilisé DBAL :
 * http://doctrine-dbal.readthedocs.org/en/latest/reference/data-retrieval-and-manipulation.html
 *
 * Variable disponible :
 * $conn est créé depuis le lanceur, vous pouvez l'utiliser directement.
 * $container est également disponible, pour utiliser des services et autres.
 *
 */


/**
 * Exemple d'utilisation de DBAL :
 */
$statement = $conn->prepare('SELECT * FROM utilisateur');
$statement->execute();
$retour = $statement->fetchAll();
dump($retour);


/**
 *  Exemple d'utilisation de la création de fonction
 *  pour évité la duplication de fonction si le fichier est utilisé plusieurs fois
 */
$batchContainer = new \stdClass();
$batchContainer->maFonction = function () {
    //Code de ma fonction
};

unset($batchContainer);

        