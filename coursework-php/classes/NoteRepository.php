<?php
declare(strict_types=1);

final class NoteRepository {
    public function __construct(private PDO $pdo) {}

    public function allByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title, string $body): void {
        $stmt = $this->pdo->prepare("INSERT INTO notes (user_id, title, body) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $title, $body]);
    }

    public function findOneById(int $noteId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$noteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(int $noteId, string $title, string $body): void {
        $stmt = $this->pdo->prepare("UPDATE notes SET title = ?, body = ? WHERE id = ?");
        $stmt->execute([$title, $body, $noteId]);
    }

    public function delete(int $noteId): void {
        $stmt = $this->pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$noteId]);
    }
}
