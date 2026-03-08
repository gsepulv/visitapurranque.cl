<?php

class Tag
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /** Listar todos con conteo de uso */
    public function getAll(): array
    {
        return $this->db->query(
            "SELECT t.*, COUNT(tg.tag_id) AS uso
             FROM tags t
             LEFT JOIN taggables tg ON tg.tag_id = t.id
             GROUP BY t.id
             ORDER BY t.nombre ASC"
        )->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear(array $datos): int
    {
        $nombre = trim($datos['nombre'] ?? '');
        $slug = slugify($nombre);

        // Asegurar slug único
        $base = $slug;
        $suffix = 2;
        while ($this->slugExists($slug)) {
            $slug = $base . '-' . $suffix++;
        }

        $this->db->prepare(
            "INSERT INTO tags (nombre, slug) VALUES (?, ?)"
        )->execute([$nombre, $slug]);

        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $nombre = trim($datos['nombre'] ?? '');
        $slug = slugify($nombre);

        $base = $slug;
        $suffix = 2;
        while ($this->slugExistsExcept($slug, $id)) {
            $slug = $base . '-' . $suffix++;
        }

        $this->db->prepare(
            "UPDATE tags SET nombre = ?, slug = ? WHERE id = ?"
        )->execute([$nombre, $slug, $id]);
    }

    public function eliminar(int $id): void
    {
        $this->db->prepare("DELETE FROM taggables WHERE tag_id = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM tags WHERE id = ?")->execute([$id]);
    }

    /** Tags de una entidad específica */
    public function getByEntidad(string $tipo, int $id): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.* FROM tags t
             JOIN taggables tg ON tg.tag_id = t.id
             WHERE tg.taggable_type = ? AND tg.taggable_id = ?
             ORDER BY t.nombre ASC"
        );
        $stmt->execute([$tipo, $id]);
        return $stmt->fetchAll();
    }

    /** Reemplazar tags de una entidad */
    public function sincronizar(string $tipo, int $id, array $tagIds): void
    {
        $this->db->prepare(
            "DELETE FROM taggables WHERE taggable_type = ? AND taggable_id = ?"
        )->execute([$tipo, $id]);

        if (empty($tagIds)) return;

        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO taggables (tag_id, taggable_id, taggable_type) VALUES (?, ?, ?)"
        );
        foreach ($tagIds as $tagId) {
            $stmt->execute([(int)$tagId, $id, $tipo]);
        }
    }

    /** Los más usados */
    public function getPopulares(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, COUNT(tg.tag_id) AS uso
             FROM tags t
             JOIN taggables tg ON tg.tag_id = t.id
             GROUP BY t.id
             ORDER BY uso DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /** Buscar para autocompletado */
    public function buscar(string $termino, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, slug FROM tags WHERE nombre LIKE ? ORDER BY nombre ASC LIMIT ?"
        );
        $stmt->execute(['%' . $termino . '%', $limit]);
        return $stmt->fetchAll();
    }

    private function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tags WHERE slug = ?");
        $stmt->execute([$slug]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function slugExistsExcept(string $slug, int $exceptId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tags WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $exceptId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
