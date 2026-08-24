<?php

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function categoryLabel(string $categoryName): string
{
    $map = [
        'キッチン' => 'KITCHEN',
        'インテリア' => 'INTERIOR',
        'ファブリック' => 'FABRIC',
        'アロマ' => 'AROMA',
    ];

    return $map[$categoryName] ?? $categoryName;
}

function paymentLabel(string $value): string
{
    $labels = [
        'card' => 'クレジットカード',
        'cod' => '代金引換',
    ];

    return $labels[$value] ?? $value;
}

function deliveryTimeLabel(string $value): string
{
    $labels = [
        '' => '指定なし',
        '午前中' => '午前中',
        '14-16' => '14:00〜16:00',
        '16-18' => '16:00〜18:00',
        '18-20' => '18:00〜20:00',
    ];

    return $labels[$value] ?? '指定なし';
}
