<?php

namespace Tourze\ProductAutoUpBundle\Exception;

class SpuNotFoundException extends \Exception
{
    public function __construct(
        private readonly int $spuId,
    ) {
        parent::__construct("SPU {$spuId} 不存在");
    }

    public function getSpuId(): int
    {
        return $this->spuId;
    }
}
