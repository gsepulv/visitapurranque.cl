<?php
/**
 * Model TextoEditable — visitapurranque.cl
 * CRUD para textos editables del sitio
 */

class TextoEditable
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** Todos los textos agrupados por sección */
    public function allGrouped(): array
    {
        $rows = $this->db->query(
            "SELECT * FROM textos_editables ORDER BY seccion, clave"
        )->fetchAll();

        $grupos = [];
        foreach ($rows as $r) {
            $grupos[$r['seccion']][] = $r;
        }
        return $grupos;
    }

    /** Secciones únicas */
    public function secciones(): array
    {
        return $this->db->query(
            "SELECT DISTINCT seccion FROM textos_editables ORDER BY seccion"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Buscar por ID */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM textos_editables WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Buscar por clave */
    public function findByClave(string $clave): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM textos_editables WHERE clave = ?");
        $stmt->execute([$clave]);
        return $stmt->fetch() ?: null;
    }

    /** Crear texto */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO textos_editables (clave, valor, valor_default, seccion, tipo, descripcion)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['clave'],
            $data['valor'],
            $data['valor'],
            $data['seccion'],
            $data['tipo'],
            $data['descripcion'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Actualizar valor de un texto */
    public function updateValor(int $id, string $valor): void
    {
        $stmt = $this->db->prepare("UPDATE textos_editables SET valor = ? WHERE id = ?");
        $stmt->execute([$valor, $id]);
    }

    /** Actualizar todo el registro */
    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            "UPDATE textos_editables SET clave = ?, valor = ?, valor_default = ?, seccion = ?, tipo = ?, descripcion = ? WHERE id = ?"
        );
        $stmt->execute([
            $data['clave'],
            $data['valor'],
            $data['valor_default'],
            $data['seccion'],
            $data['tipo'],
            $data['descripcion'] ?? null,
            $id,
        ]);
    }

    /** Eliminar */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM textos_editables WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Guardar batch — actualiza valores por sección */
    public function saveBatch(array $valores): int
    {
        $count = 0;
        $stmt = $this->db->prepare("UPDATE textos_editables SET valor = ? WHERE id = ?");
        foreach ($valores as $id => $valor) {
            $stmt->execute([trim($valor), (int)$id]);
            $count++;
        }
        return $count;
    }

    /** Restaurar un texto a su valor default */
    public function restore(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE textos_editables SET valor = valor_default WHERE id = ?");
        $stmt->execute([$id]);
    }

    /** Restaurar todos los textos */
    public function restoreAll(): int
    {
        return $this->db->exec("UPDATE textos_editables SET valor = valor_default");
    }

    /** Total count */
    public function count(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM textos_editables")->fetchColumn();
    }
}
