<?php
/**
 * Model Pagina — visitapurranque.cl
 * Páginas estáticas con versionamiento de contenido
 */

class Pagina
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Listado con paginación */
    public function getAll(?string $q = null): array
    {
        $sql = "SELECT * FROM paginas";
        $params = [];

        if ($q) {
            $sql .= " WHERE titulo LIKE ? OR slug LIKE ?";
            $params = ["%{$q}%", "%{$q}%"];
        }

        $sql .= " ORDER BY orden, titulo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Buscar por ID */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM paginas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Verificar si slug ya existe */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM paginas WHERE slug = ?";
        $params = [$slug];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Crear página */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO paginas (titulo, slug, contenido, meta_title, meta_description, template, activo, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['contenido'] ?? '',
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['template'] ?? 'default',
            $data['activo'] ?? 1,
            $data['orden'] ?? 0,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Actualizar página */
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE paginas SET titulo = ?, slug = ?, contenido = ?, meta_title = ?, meta_description = ?,
             template = ?, activo = ?, orden = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['contenido'] ?? '',
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['template'] ?? 'default',
            $data['activo'] ?? 1,
            $data['orden'] ?? 0,
            $id,
        ]);
    }

    /** Eliminar página y sus versiones */
    public function delete(int $id): void
    {
        $this->db->prepare("DELETE FROM paginas_versiones WHERE pagina_id = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM paginas WHERE id = ?")->execute([$id]);
    }

    /** Toggle activo */
    public function toggleActivo(int $id): string
    {
        $stmt = $this->db->prepare("UPDATE paginas SET activo = NOT activo WHERE id = ?");
        $stmt->execute([$id]);

        $stmt = $this->db->prepare("SELECT activo FROM paginas WHERE id = ?");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn() ? 'activada' : 'desactivada';
    }

    // ── Versionamiento ──────────────────────────

    /** Guardar versión antes de editar */
    public function saveVersion(int $paginaId, string $contenido, int $usuarioId, ?string $nota = null): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO paginas_versiones (pagina_id, contenido, usuario_id, nota)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$paginaId, $contenido, $usuarioId, $nota]);
        return (int)$this->db->lastInsertId();
    }

    /** Obtener versiones de una página */
    public function getVersiones(int $paginaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, u.nombre AS usuario_nombre
             FROM paginas_versiones v
             LEFT JOIN usuarios u ON u.id = v.usuario_id
             WHERE v.pagina_id = ?
             ORDER BY v.created_at DESC
             LIMIT 20"
        );
        $stmt->execute([$paginaId]);
        return $stmt->fetchAll();
    }

    /** Obtener una versión específica */
    public function getVersion(int $versionId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, u.nombre AS usuario_nombre
             FROM paginas_versiones v
             LEFT JOIN usuarios u ON u.id = v.usuario_id
             WHERE v.id = ?"
        );
        $stmt->execute([$versionId]);
        return $stmt->fetch() ?: null;
    }

    /** Restaurar contenido desde una versión */
    public function restoreVersion(int $paginaId, string $contenido): void
    {
        $stmt = $this->db->prepare("UPDATE paginas SET contenido = ? WHERE id = ?");
        $stmt->execute([$contenido, $paginaId]);
    }

    /** Total de páginas */
    public function count(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM paginas")->fetchColumn();
    }
}
