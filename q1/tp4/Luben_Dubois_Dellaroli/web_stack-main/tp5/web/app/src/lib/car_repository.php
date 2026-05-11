<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function findUsers(): array
{
    return findUsersFromDb(db());
}

function findCarsByUser(?int $userId): array
{
    return findCarsByUserFromDb(db(), $userId);
}

function createUser(string $username, string $fullName): int
{
    return createUserFromDb(dbMaster(), $username, $fullName);
}

function createCar(int $userId, string $brand, string $model, string $registration, ?int $year): int
{
    return createCarFromDb(dbMaster(), $userId, $brand, $model, $registration, $year);
}

function deleteUser(int $userId): void
{
    deleteUserFromDb(dbMaster(), $userId);
}

function deleteCar(int $carId): void
{
    deleteCarFromDb(dbMaster(), $carId);
}

function findUsersFromDb(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, username, full_name FROM users ORDER BY full_name');
    return $stmt->fetchAll();
}

function findCarsByUserFromDb(PDO $pdo, ?int $userId): array
{
    $sql = 'SELECT c.id, u.full_name AS owner, c.brand, c.model, c.registration, c.year
            FROM cars c
            JOIN users u ON u.id = c.user_id';

    if ($userId !== null) {
        $sql .= ' WHERE c.user_id = :user_id';
    }

    $sql .= ' ORDER BY u.full_name, c.brand, c.model';
    $stmt = $pdo->prepare($sql);

    if ($userId !== null) {
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetchAll();
}

function createUserFromDb(PDO $pdo, string $username, string $fullName): int
{
    $stmt = $pdo->prepare('INSERT INTO users (username, full_name) VALUES (:username, :full_name)');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':full_name', $fullName);
    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

function createCarFromDb(PDO $pdo, int $userId, string $brand, string $model, string $registration, ?int $year): int
{
    $stmt = $pdo->prepare('INSERT INTO cars (user_id, brand, model, registration, year) VALUES (:user_id, :brand, :model, :registration, :year)');
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':brand', $brand);
    $stmt->bindValue(':model', $model);
    $stmt->bindValue(':registration', $registration);

    if ($year === null) {
        $stmt->bindValue(':year', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
    }

    $stmt->execute();

    return (int) $pdo->lastInsertId();
}

function deleteUserFromDb(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :user_id');
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

function deleteCarFromDb(PDO $pdo, int $carId): void
{
    $stmt = $pdo->prepare('DELETE FROM cars WHERE id = :car_id');
    $stmt->bindValue(':car_id', $carId, PDO::PARAM_INT);
    $stmt->execute();
}
