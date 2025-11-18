<?php

namespace App\Interfaces;

interface ModelHasRelations
{

    static function relations() : array;

    static function secureDeleteRelations() : array;
}
