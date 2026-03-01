<?php
/**
 * Modelo MenuItem — visitapurranque.cl
 * CRUD + reorder para ítems de menú (principal, footer_legal)
 */
class MenuItem
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los ítems de un menú, ordenados, con jerarquía
     */
    public function getByMenu(string $menu): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM menu_items WHERE menu = ? ORDER BY orden ASC, id ASC"
        );
        $stmt->execute([$menu]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un ítem por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crear nuevo ítem
     */
    public function crear(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO menu_items (menu, parent_id, titulo, url, tipo, referencia_id, target, icono, orden, activo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['menu'],
            $data['parent_id'] ?: null,
            $data['titulo'],
            $data['url'] ?: null,
            $data['tipo'],
            $data['referencia_id'] ?: null,
            $data['target'] ?? '_self',
            $data['icono'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar ítem existente
     */
    public function actualizar(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE menu_items
             SET menu=?, parent_id=?, titulo=?, url=?, tipo=?, referencia_id=?, target=?, icono=?, orden=?, activo=?
             WHERE id = ?"
        );
        return $stmt->execute([
            $data['menu'],
            $data['parent_id'] ?: null,
            $data['titulo'],
            $data['url'] ?: null,
            $data['tipo'],
            $data['referencia_id'] ?: null,
            $data['target'] ?? '_self',
            $data['icono'] ?: null,
            (int)($data['orden'] ?? 0),
            (int)($data['activo'] ?? 1),
            $id,
        ]);
    }

    /**
     * Eliminar ítem (hijos quedan huérfanos → se pasan a parent_id NULL)
     */
    public function eliminar(int $id): bool
    {
        // Mover hijos a nivel raíz antes de eliminar
        $stmt = $this->db->prepare("UPDATE menu_items SET parent_id = NULL WHERE parent_id = ?");
        $stmt->execute([$id]);

        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Toggle activo/inactivo
     */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE menu_items SET activo = NOT activo WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Reordenar ítems en batch: recibe array [{id, orden, parent_id}, ...]
     */
    public function reordenar(array $items): bool
    {
        $stmt = $this->db->prepare("UPDATE menu_items SET orden = ?, parent_id = ? WHERE id = ?");
        foreach ($items as $item) {
            $stmt->execute([
                (int)$item['orden'],
                $item['parent_id'] ? (int)$item['parent_id'] : null,
                (int)$item['id'],
            ]);
        }
        return true;
    }

    /**
     * Categorías activas (para selector tipo=categoria)
     */
    public function getCategoriasActivas(): array
    {
        return $this->db->query(
            "SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre ASC"
        )->fetchAll();
    }

    /**
     * Páginas activas (para selector tipo=pagina)
     */
    public function getPaginasActivas(): array
    {
        return $this->db->query(
            "SELECT id, titulo, slug FROM paginas WHERE activo = 1 ORDER BY titulo ASC"
        )->fetchAll();
    }

    /**
     * Contar ítems por menú
     */
    public function countByMenu(string $menu): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM menu_items WHERE menu = ?");
        $stmt->execute([$menu]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener ítems raíz de un menú (para selector de parent)
     */
    public function getItemsRaiz(string $menu, ?int $excludeId = null): array
    {
        $sql = "SELECT id, titulo FROM menu_items WHERE menu = ? AND parent_id IS NULL";
        $params = [$menu];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $sql .= " ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
