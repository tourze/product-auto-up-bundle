<?php

namespace Tourze\ProductAutoUpBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ProductAutoUpBundle\Repository\AutoUpTimeConfigRepository;
use Tourze\ProductCoreBundle\Entity\Spu;

#[ORM\Table(name: 'product_auto_up_time_config', options: ['comment' => '商品自动上架时间配置'])]
#[ORM\Entity(repositoryClass: AutoUpTimeConfigRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_SPU_ID', columns: ['spu_id'])]
class AutoUpTimeConfig implements \Stringable
{
    use BlameableAware;
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotNull(message: 'SPU不能为空')]
    #[ORM\ManyToOne(targetEntity: Spu::class)]
    #[ORM\JoinColumn(name: 'spu_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Spu $spu = null;

    #[Groups(groups: ['admin_curd', 'restful_read'])]
    #[Assert\NotNull(message: '自动上架时间不能为空')]
    #[Assert\Type(type: '\DateTimeInterface', message: '自动上架时间必须为有效的日期时间')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '自动上架时间'])]
    private ?\DateTimeInterface $autoReleaseTime = null;

    public function __toString(): string
    {
        return sprintf('SPU-%d 自动上架于 %s',
            $this->spu?->getId() ?? 0,
            $this->autoReleaseTime?->format('Y-m-d H:i:s') ?? ''
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSpu(): ?Spu
    {
        return $this->spu;
    }

    public function setSpu(?Spu $spu): void
    {
        $this->spu = $spu;
    }

    public function getAutoReleaseTime(): ?\DateTimeInterface
    {
        return $this->autoReleaseTime;
    }

    public function setAutoReleaseTime(?\DateTimeInterface $autoReleaseTime): void
    {
        $this->autoReleaseTime = $autoReleaseTime;
    }
}
