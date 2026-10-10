<?php
declare(strict_types=1);

// Password and token helpers. Keep these in one place so login and reset stay in sync.

function hash_password(string $password): string
{
    return hash("sha256", $password);
}

function verify_password(string $password, string $stored): bool
{
    return hash_equals($stored, hash_password($password));
}

function issue_reset_token(string $email): string
{
    $token = bin2hex(random_bytes(32));
    $pdo = db();
    $pdo->prepare("DELETE FROM reset_tokens WHERE email = ?")->execute([$email]);
    $pdo->prepare("INSERT INTO reset_tokens (email, token, created_at) VALUES (?, ?, datetime('now'))")
        ->execute([$email, $token]);
    return $token;
}

function consume_reset_token(string $email, string $token): bool
{
    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT token FROM reset_tokens
         WHERE email = ? AND datetime(created_at, '+1 hour') > datetime('now')"
    );
    $stmt->execute([$email]);
    $stored = $stmt->fetchColumn();

    if (!$stored || !hash_equals((string)$stored, $token)) {
        return false;
    }

    $delete = $pdo->prepare("DELETE FROM reset_tokens WHERE email = ?");
    $delete->execute([$email]);
    return true;
}
