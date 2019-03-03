<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static WikiAccess All()
 * @method static WikiAccess Admin()
 * @method static WikiAccess SuperAdmin()
 * @method static WikiAccess Inherit()
 */
class WikiAccess extends Enum {
    private const All = 'all';
    private const Admin = 'admin';
    private const SuperAdmin = 'super_admin';
    private const Inherit = 'inherit';
}