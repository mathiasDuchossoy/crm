<?php
/**
 * Created by PhpStorm.
 * User: mathias
 * Date: 01/12/2016
 * Time: 11:10
 */

namespace Mondofute\Bundle\DecoteBundle\Entity;


class Type
{
    const visible = 1;
    const masquee = 2;

    public static $libelles = array(
        Type::visible => 'Visible',
        Type::masquee => 'Masquée',
    );

    static public function getLibelle($id)
    {
        return self::$libelles[$id];
    }
}