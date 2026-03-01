<?php
/**
 * Modelo ContactoMensaje — visitapurranque.cl
 * Mensajes del formulario de contacto público
 */
class ContactoMensaje
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Listar mensajes con filtros y paginación
     */
    public function getAll(array $filtros = [], int $pagina = 1, int $porPagina = ADMIN_PER_PAGE): array
    {
        $where = ['1=1'];
        $params = [];

        if ($filtros['leido'] !== '') {
            $where[] = 'leido = ?';
            $params[] = (int)$filtros['leido'];
        }

        if ($filtros['respondido'] !== '') {
            $where[] = 'respondido = ?';
            $params[] = (int)$filtros['respondido'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(nombre LIKE ? OR email LIKE ? OR asunto LIKE ? OR mensaje LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);
        $offset = ($pagina - 1) * $porPagina;

        $sql = "SELECT * FROM contacto_mensajes
                WHERE {$whereSql}
                ORDER BY created_at DESC
                LIMIT {$porPagina} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Contar mensajes con filtros
     */
    public function count(array $filtros = []): int
    {
        $where = ['1=1'];
        $params = [];

        if ($filtros['leido'] !== '') {
            $where[] = 'leido = ?';
            $params[] = (int)$filtros['leido'];
        }

        if ($filtros['respondido'] !== '') {
            $where[] = 'respondido = ?';
            $params[] = (int)$filtros['respondido'];
        }

        if (!empty($filtros['q'])) {
            $where[] = '(nombre LIKE ? OR email LIKE ? OR asunto LIKE ? OR mensaje LIKE ?)';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
            $params[] = '%' . $filtros['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM contacto_mensajes WHERE {$whereSql}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener mensaje por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM contacto_mensajes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Marcar como leído
     */
    public function marcarLeido(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE contacto_mensajes SET leido = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Toggle leído
     */
    public function toggleLeido(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE contacto_mensajes SET leido = NOT leido WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Guardar respuesta
     */
    public function responder(int $id, string $respuesta): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE contacto_mensajes SET respuesta = ?, respuesta_fecha = NOW(), respondido = 1, leido = 1 WHERE id = ?"
        );
        return $stmt->execute([$respuesta, $id]);
    }

    /**
     * Eliminar mensaje
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM contacto_mensajes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar no leídos
     */
    public function countNoLeidos(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM contacto_mensajes WHERE leido = 0"
        )->fetchColumn();
    }

    /**
     * Stats rápidas
     */
    public function stats(): array
    {
        $total = (int)$this->db->query("SELECT COUNT(*) FROM contacto_mensajes")->fetchColumn();
        $noLeidos = (int)$this->db->query("SELECT COUNT(*) FROM contacto_mensajes WHERE leido = 0")->fetchColumn();
        $respondidos = (int)$this->db->query("SELECT COUNT(*) FROM contacto_mensajes WHERE respondido = 1")->fetchColumn();
        return [
            'total'       => $total,
            'no_leidos'   => $noLeidos,
            'respondidos' => $respondidos,
            'sin_respuesta' => $total - $respondidos,
        ];
    }
}
