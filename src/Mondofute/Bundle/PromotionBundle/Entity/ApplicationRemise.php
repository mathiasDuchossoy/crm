<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 16/01/2017
 * Time: 16:56
 */

namespace Mondofute\Bundle\PromotionBundle\Entity;


class ApplicationRemise
{
    const deuxiemeSemaine = 1;
    const semaineMoinsChere = 2;

    public static $libelles = array(
        ApplicationRemise::deuxiemeSemaine => 'sur la 2éme semaine',
        ApplicationRemise::semaineMoinsChere => 'sur la semaine la moins chère',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}