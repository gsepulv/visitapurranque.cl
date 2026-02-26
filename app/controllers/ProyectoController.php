<?php
/**
 * ProyectoController — Dashboard de seguimiento del proyecto
 */
class ProyectoController extends Controller
{
    private function checkAuth(): bool
    {
        $token = $_COOKIE['proyecto_token'] ?? '';
        $expected = hash('sha256', 'purranque2026' . date('Y-m'));
        return hash_equals($expected, $token);
    }

    public function index(): void
    {
        if (!$this->checkAuth()) {
            $error = $_SESSION['flash']['error'] ?? null;
            unset($_SESSION['flash']);
            require BASE_PATH . '/app/views/proyecto/login.php';
            return;
        }

        // Config
        $configRows = $this->db->query('SELECT clave, valor FROM proyecto_config')->fetchAll();
        $config = [];
        foreach ($configRows as $row) {
            $config[$row['clave']] = $row['valor'];
        }

        $proyectoInicio = $config['proyecto_inicio'] ?? '2026-03-02';
        $proyectoBeta   = $config['proyecto_beta'] ?? '2026-08-03';
        $horasSemanaMeta = (int)($config['horas_semana_meta'] ?? 10);

        // Semana actual
        $today = new DateTime();
        $inicio = new DateTime($proyectoInicio);
        $diffDays = (int)$today->diff($inicio)->format('%r%a');
        if ($diffDays < 0) {
            $semanaActual = 0;
        } else {
            $semanaActual = min((int)ceil(($diffDays + 1) / 7), 22);
        }

        // Fases
        $fases = $this->db->query('SELECT * FROM proyecto_fases ORDER BY orden')->fetchAll();

        // Tareas
        $tareas = $this->db->query(
            'SELECT t.*, f.nombre as fase_nombre, f.color as fase_color
             FROM proyecto_tareas t
             JOIN proyecto_fases f ON t.fase_id = f.id
             ORDER BY t.semana, t.orden'
        )->fetchAll();

        // Hitos
        $hitos = $this->db->query('SELECT * FROM proyecto_hitos ORDER BY semana')->fetchAll();

        // Stats
        $totalTareas = count($tareas);
        $completadas = 0;
        $enProgreso = 0;
        $bloqueadas = 0;
        $horasEstimadas = 0;
        $horasReales = 0;
        $tareasEstaSemana = [];
        $completadasEstaSemana = 0;

        foreach ($tareas as $t) {
            $horasEstimadas += (float)$t['horas_estimadas'];
            $horasReales += (float)$t['horas_reales'];
            if ($t['estado'] === 'completada') $completadas++;
            if ($t['estado'] === 'en_progreso') $enProgreso++;
            if ($t['estado'] === 'bloqueada') $bloqueadas++;
            if ((int)$t['semana'] === $semanaActual) {
                $tareasEstaSemana[] = $t;
                if ($t['estado'] === 'completada') $completadasEstaSemana++;
            }
        }

        // Sesiones totales
        $sesionesMin = (int)$this->db->query('SELECT COALESCE(SUM(duracion_minutos),0) FROM proyecto_sesiones')->fetchColumn();

        // Dias para beta
        $betaDate = new DateTime($proyectoBeta);
        $diasParaBeta = max(0, (int)$today->diff($betaDate)->format('%r%a'));
        if ($today > $betaDate) $diasParaBeta = 0;

        // Agrupar tareas por fase para Gantt
        $tareasPorFase = [];
        foreach ($tareas as $t) {
            $tareasPorFase[$t['fase_id']][] = $t;
        }

        // Hitos por semana
        $hitosPorSemana = [];
        foreach ($hitos as $h) {
            $hitosPorSemana[$h['semana']][] = $h;
        }

        // Total horas meta (22 semanas * horas_semana_meta)
        $horasMeta = 22 * $horasSemanaMeta;

        $data = compact(
            'config', 'proyectoInicio', 'proyectoBeta', 'horasSemanaMeta',
            'semanaActual', 'fases', 'tareas', 'hitos',
            'totalTareas', 'completadas', 'enProgreso', 'bloqueadas',
            'horasEstimadas', 'horasReales', 'horasMeta',
            'tareasEstaSemana', 'completadasEstaSemana',
            'sesionesMin', 'diasParaBeta',
            'tareasPorFase', 'hitosPorSemana'
        );

        extract($data);
        require BASE_PATH . '/app/views/proyecto/dashboard.php';
    }

    public function login(): void
    {
        $password = $_POST['password'] ?? '';

        if ($password === 'purranque2026') {
            $token = hash('sha256', 'purranque2026' . date('Y-m'));
            setcookie('proyecto_token', $token, [
                'expires'  => time() + 86400 * 30,
                'path'     => '/proyecto',
                'httponly'  => true,
                'samesite' => 'Lax',
            ]);
            header('Location: ' . url('/proyecto'));
            exit;
        }

        $_SESSION['flash'] = ['error' => 'Password incorrecto'];
        header('Location: ' . url('/proyecto'));
        exit;
    }

    public function logout(): void
    {
        setcookie('proyecto_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/proyecto',
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
        header('Location: ' . url('/proyecto'));
        exit;
    }

    public function tareaToggle(): void
    {
        if (!$this->checkAuth()) {
            $this->json(['error' => 'No autorizado'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $nuevoEstado = $input['estado'] ?? '';
        $horasReales = isset($input['horas_reales']) ? (float)$input['horas_reales'] : null;

        if (!$id || !in_array($nuevoEstado, ['pendiente', 'en_progreso', 'completada', 'bloqueada', 'saltada'])) {
            $this->json(['error' => 'Datos invalidos'], 400);
        }

        $fechaCompletada = $nuevoEstado === 'completada' ? date('Y-m-d H:i:s') : null;

        $sql = 'UPDATE proyecto_tareas SET estado = ?, fecha_completada = ?';
        $params = [$nuevoEstado, $fechaCompletada];

        if ($horasReales !== null) {
            $sql .= ', horas_reales = ?';
            $params[] = $horasReales;
        }

        $sql .= ' WHERE id = ?';
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->json(['ok' => true, 'estado' => $nuevoEstado]);
    }

    public function apiStats(): void
    {
        if (!$this->checkAuth()) {
            $this->json(['error' => 'No autorizado'], 401);
        }

        $stats = $this->db->query(
            "SELECT
                COUNT(*) as total,
                SUM(estado = 'completada') as completadas,
                SUM(estado = 'en_progreso') as en_progreso,
                SUM(estado = 'bloqueada') as bloqueadas,
                SUM(horas_estimadas) as horas_estimadas,
                SUM(horas_reales) as horas_reales
             FROM proyecto_tareas"
        )->fetch();

        $sesionesMin = (int)$this->db->query('SELECT COALESCE(SUM(duracion_minutos),0) FROM proyecto_sesiones')->fetchColumn();

        $this->json([
            'total'           => (int)$stats['total'],
            'completadas'     => (int)$stats['completadas'],
            'en_progreso'     => (int)$stats['en_progreso'],
            'bloqueadas'      => (int)$stats['bloqueadas'],
            'horas_estimadas' => (float)$stats['horas_estimadas'],
            'horas_reales'    => (float)$stats['horas_reales'],
            'sesiones_min'    => $sesionesMin,
        ]);
    }
}
