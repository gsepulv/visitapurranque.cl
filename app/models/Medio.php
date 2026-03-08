<?php

class Medio
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(int $limit = 20, int $offset = 0, string $tipo = '', string $q = ''): array
    {
        $where = '1=1';
        $params = [];

        if ($tipo !== '') {
            $where .= ' AND tipo LIKE ?';
            $params[] = $tipo . '%';
        }
        if ($q !== '') {
            $where .= ' AND (nombre LIKE ? OR alt LIKE ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare(
            "SELECT * FROM medios WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $tipo = '', string $q = ''): int
    {
        $where = '1=1';
        $params = [];

        if ($tipo !== '') {
            $where .= ' AND tipo LIKE ?';
            $params[] = $tipo . '%';
        }
        if ($q !== '') {
            $where .= ' AND (nombre LIKE ? OR alt LIKE ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM medios WHERE $where");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM medios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO medios (nombre, archivo, tipo, tamano, ancho, alto, alt, carpeta, usuario_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $datos['nombre'],
            $datos['archivo'],
            $datos['tipo'],
            $datos['tamano'],
            $datos['ancho'] ?? null,
            $datos['alto'] ?? null,
            $datos['alt'] ?? null,
            $datos['carpeta'] ?? 'general',
            $datos['usuario_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $fields = [];
        $params = [];
        foreach (['nombre', 'alt', 'carpeta'] as $key) {
            if (array_key_exists($key, $datos)) {
                $fields[] = "$key = ?";
                $params[] = $datos[$key];
            }
        }
        if (empty($fields)) return;
        $params[] = $id;
        $this->db->prepare("UPDATE medios SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public function eliminar(int $id): ?string
    {
        $medio = $this->getById($id);
        if (!$medio) return null;

        $this->db->prepare("DELETE FROM medios WHERE id = ?")->execute([$id]);
        return $medio['archivo'];
    }

    public function buscar(string $q, string $tipo = '', int $limit = 20): array
    {
        $where = '(nombre LIKE ? OR alt LIKE ?)';
        $params = ['%' . $q . '%', '%' . $q . '%'];

        if ($tipo !== '') {
            $where .= ' AND tipo LIKE ?';
            $params[] = $tipo . '%';
        }

        $params[] = $limit;
        $stmt = $this->db->prepare(
            "SELECT * FROM medios WHERE $where ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotalSize(): int
    {
        return (int)$this->db->query("SELECT COALESCE(SUM(tamano), 0) FROM medios")->fetchColumn();
    }
}
