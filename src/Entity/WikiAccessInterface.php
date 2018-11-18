<?php

namespace App\Entity;

interface WikiAccessInterface {
    const ACCESS_ALL = 'all';
    const ACCESS_WG = 'wg';
    const ACCESS_ADMIN = 'admin';
    const ACCESS_INHERIT = 'inherit';

    const ACCESS_AG = 'wg';

    const ACCESS_LIST = [
        self::ACCESS_INHERIT,
        self::ACCESS_ALL,
        self::ACCESS_WG,
        self::ACCESS_ADMIN
    ];

    /**
     * @return string
     */
    public function getAccess();
}