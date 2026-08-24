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
