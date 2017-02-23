<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\SaisonBundle\Entity;


class Stock
{
    const extranetOk = 1;
    const extranetASaisir = 2;
    const extranetEnAttente = 3;
    const fluxXmlEnAttente = 4;
    const fluxXmlCodeNonVerifies = 5;
    const fluxXmlOkCodesVerifies = 6;

    public static $libelles = array(
        Stock::extranetOk => 'EXTRANET / ok',
        Stock::extranetASaisir => 'EXTRANET / à saisir',
        Stock::extranetEnAttente => 'EXTRANET / en attente',
        Stock::fluxXmlEnAttente => 'FLUX XML / en attente',
        Stock::fluxXmlCodeNonVerifies => 'FLUX XML / codes non vérifiés',
        Stock::fluxXmlOkCodesVerifies => 'FLUX XML / ok codes vérifiés'
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}