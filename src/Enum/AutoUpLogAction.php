<?php

namespace Tourze\ProductAutoUpBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AutoUpLogAction: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case SCHEDULED = 'scheduled';
    case EXECUTED = 'executed';
    case CANCELED = 'canceled';
    case ERROR = 'error';

    public function getLabel(): string
    {
        return match ($this) {
            self::SCHEDULED => '已安排',
            self::EXECUTED => '已执行',
            self::CANCELED => '已取消',
            self::ERROR => '执行出错',
        };
    }
}
