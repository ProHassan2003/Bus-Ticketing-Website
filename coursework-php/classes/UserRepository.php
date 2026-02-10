<?php
declare(strict_types=1);

final class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $name, string $email, string $hash): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $hash]);
    }
}
