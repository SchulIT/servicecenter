<?php

namespace App\Repository;

use App\Entity\WikiCategory;

interface WikiCategoryRepositoryInterface {

    /**
     * @param WikiCategory|null $category
     * @return WikiCategory[]
     */
    public function findByParent(WikiCategory $category = null);

    public function persist(WikiCategory $category);

    public function remove(WikiCategory $category);
}