<?php

function primaryProductImageSql(): string
{
    return "(
        SELECT pi.image_path
        FROM product_images AS pi
        WHERE pi.product_id = p.id
        ORDER BY pi.sort_order ASC, pi.id ASC
        LIMIT 1
    )";
}

function fetchNewProducts(PDO $pdo, int $limit = 4): array
{
    $limit = max(1, $limit);
    $imageSql = primaryProductImageSql();

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            $imageSql AS image_path
        FROM products AS p
        ORDER BY p.created_at DESC, p.id DESC
        LIMIT $limit
    ";

    return $pdo->query($sql)->fetchAll();
}

function fetchProductsByIds(PDO $pdo, array $ids): array
{
    $ids = array_values(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0));

    if (empty($ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $imageSql = primaryProductImageSql();

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            $imageSql AS image_path
        FROM products AS p
        WHERE p.id IN ($placeholders)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);

    $map = [];

    foreach ($stmt->fetchAll() as $row) {
        $map[(int)$row['id']] = $row;
    }

    $products = [];

    foreach ($ids as $id) {
        if (isset($map[$id])) {
            $products[] = $map[$id];
        }
    }

    return $products;
}

function fetchAllProductsWithImage(PDO $pdo): array
{
    $imageSql = primaryProductImageSql();

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            $imageSql AS image_path
        FROM products AS p
        ORDER BY p.id ASC
    ";

    return $pdo->query($sql)->fetchAll();
}

function fetchRecommendedProducts(PDO $pdo, array $excludeIds = [], int $limit = 3): array
{
    $excludeIds = array_values(array_filter(array_map('intval', $excludeIds), static fn (int $id): bool => $id > 0));
    $limit = max(1, $limit);
    $imageSql = primaryProductImageSql();

    $sql = "
        SELECT
            p.id,
            p.name,
            p.price,
            $imageSql AS image_path
        FROM products AS p
    ";

    $params = [];

    if (!empty($excludeIds)) {
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $sql .= " WHERE p.id NOT IN ($placeholders)";
        $params = $excludeIds;
    }

    $sql .= " ORDER BY p.id ASC LIMIT $limit";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
