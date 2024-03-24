<?php

namespace App\Entity;

enum WikiAccess: string {
    case All = 'all';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
    case Inherit = 'inherit';
}