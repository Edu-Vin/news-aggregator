<?php

namespace App\Contracts\Source;

use App\Entities\Source\SourceEntity;

interface SourceInterface {

    public function getSources();
    public function getSourceByName(string $name): ?SourceEntity;
}
