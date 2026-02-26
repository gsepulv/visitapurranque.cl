<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=JetBrains+Mono:wght@400;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/proyecto.css') ?>">
    <script>var SITE_URL = <?= json_encode(SITE_URL) ?>;</script>
</head>
<body>

<!-- ── Header ──────────────────────────────────────────── -->
<header class="proy-header">
    <div class="proy-header-left">
        <h1>visitapurranque.cl</h1>
        <span class="proy-badge">Proyecto</span>
    </div>
    <div class="proy-header-right">
        <?php if ($semanaActual > 0 && $semanaActual <= 22): ?>
            <span>Semana <?= $semanaActual ?> de 22</span>
        <?php elseif ($semanaActual === 0): ?>
            <span>Pre-proyecto</span>
        <?php else: ?>
            <span>Post-BETA</span>
        <?php endif; ?>
        <span><?= date('d M Y') ?></span>
        <a href="<?= url('/proyecto/logout') ?>">Salir</a>
    </div>
</header>

<div class="proy-container">

<!-- ── KPIs ─────────────────────────────────────────────── -->
<div class="kpi-grid">
    <?php
        $pctTotal = $totalTareas > 0 ? round($completadas / $totalTareas * 100) : 0;
        $pctSemana = count($tareasEstaSemana) > 0 ? round($completadasEstaSemana / count($tareasEstaSemana) * 100) : 0;
        $pctHoras = $horasMeta > 0 ? round($horasReales / $horasMeta * 100) : 0;
        $pctSemanaProgreso = $semanaActual > 0 ? round($semanaActual / 22 * 100) : 0;
    ?>
    <div class="kpi-card green">
        <div class="kpi-label">Progreso total</div>
        <div class="kpi-value"><?= $pctTotal ?>%</div>
        <div class="kpi-sub"><?= $completadas ?> / <?= $totalTareas ?> tareas</div>
        <div class="kpi-bar"><div class="kpi-bar-fill" style="width:<?= $pctTotal ?>%"></div></div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-label">Semana actual</div>
        <div class="kpi-value"><?= $semanaActual ?: '—' ?></div>
        <div class="kpi-sub"><?= $semanaActual > 0 ? "de 22 semanas" : "Inicia " . formatDate($proyectoInicio) ?></div>
        <div class="kpi-bar"><div class="kpi-bar-fill" style="width:<?= $pctSemanaProgreso ?>%"></div></div>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-label">Horas invertidas</div>
        <div class="kpi-value"><?= number_format($horasReales, 1) ?></div>
        <div class="kpi-sub">de ~<?= $horasMeta ?>h meta (<?= number_format($horasEstimadas, 0) ?>h estimadas)</div>
        <div class="kpi-bar"><div class="kpi-bar-fill" style="width:<?= min($pctHoras, 100) ?>%"></div></div>
    </div>
    <div class="kpi-card purple">
        <div class="kpi-label">Tareas esta semana</div>
        <div class="kpi-value"><?= $completadasEstaSemana ?>/<?= count($tareasEstaSemana) ?></div>
        <div class="kpi-sub"><?= $semanaActual > 0 ? "{$pctSemana}% completado" : "Sin tareas aun" ?></div>
        <div class="kpi-bar"><div class="kpi-bar-fill" style="width:<?= $pctSemana ?>%"></div></div>
    </div>
    <div class="kpi-card cyan">
        <div class="kpi-label">Dias para BETA</div>
        <div class="kpi-value"><?= $diasParaBeta ?></div>
        <div class="kpi-sub"><?= formatDate($proyectoBeta) ?></div>
    </div>
</div>

<!-- ── Carta Gantt ──────────────────────────────────────── -->
<h2 class="proy-section-title">Carta Gantt</h2>
<div class="gantt-wrapper">
    <table class="gantt-table">
        <thead>
            <tr>
                <th class="gantt-label"></th>
                <?php
                    // Meses: Mar(S1-4), Abr(S5-8/9), May(S9/10-13), Jun(S14-17), Jul(S18-21), Ago(S22)
                    $meses = [
                        ['Marzo', 1, 4],
                        ['Abril', 5, 8],
                        ['Mayo', 9, 13],
                        ['Junio', 14, 17],
                        ['Julio', 18, 21],
                        ['Agosto', 22, 22],
                    ];
                    foreach ($meses as $m):
                        $span = $m[2] - $m[1] + 1;
                ?>
                    <th colspan="<?= $span ?>"><?= $m[0] ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th class="gantt-label">Tarea</th>
                <?php for ($s = 1; $s <= 22; $s++): ?>
                    <th class="<?= $s === $semanaActual ? 'gantt-current-week' : '' ?>">S<?= $s ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fases as $fase): ?>
                <tr>
                    <td class="gantt-label gantt-fase" style="color:<?= e($fase['color']) ?>">
                        <?= e($fase['nombre']) ?>
                    </td>
                    <?php for ($s = 1; $s <= 22; $s++): ?>
                        <?php
                            $inRange = $s >= (int)$fase['semana_inicio'] && $s <= (int)$fase['semana_fin'];
                            $classes = [];
                            if ($s === $semanaActual) $classes[] = 'gantt-current-week';
                        ?>
                        <td class="<?= implode(' ', $classes) ?>"
                            style="<?= $inRange ? 'background:' . e($fase['color']) . '22;' : '' ?>">
                            <?php if ($inRange): ?>
                                <span style="display:inline-block;width:100%;height:6px;background:<?= e($fase['color']) ?>;border-radius:3px;opacity:0.7;"></span>
                            <?php endif; ?>
                            <?php if (isset($hitosPorSemana[$s])):
                                foreach ($hitosPorSemana[$s] as $hito): ?>
                                    <span class="gantt-hito" title="<?= e($hito['titulo']) ?>">◆</span>
                                <?php endforeach;
                            endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php
                    $faseTareas = $tareasPorFase[$fase['id']] ?? [];
                    foreach ($faseTareas as $tarea):
                ?>
                <tr class="<?= $tarea['estado'] === 'completada' ? 'gantt-completada' : '' ?>">
                    <td class="gantt-label gantt-tarea"><?= e($tarea['titulo']) ?></td>
                    <?php for ($s = 1; $s <= 22; $s++): ?>
                        <?php
                            $isTarea = (int)$tarea['semana'] === $s;
                            $cellClasses = [];
                            if ($s === $semanaActual) $cellClasses[] = 'gantt-current-week';
                        ?>
                        <td class="<?= implode(' ', $cellClasses) ?>">
                            <?php if ($isTarea): ?>
                                <span class="gantt-cell-active" style="display:inline-block;width:100%;height:4px;background:<?= e($fase['color']) ?>;border-radius:2px;<?= $tarea['estado'] === 'completada' ? 'opacity:0.4;' : '' ?>"></span>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ── Detalle Semana Actual ────────────────────────────── -->
<h2 class="proy-section-title" data-collapsible>
    <?php if ($semanaActual > 0 && $semanaActual <= 22):
        $semanaInicio = (new DateTime($proyectoInicio))->modify('+' . ($semanaActual - 1) * 7 . ' days');
        $semanaFin = (clone $semanaInicio)->modify('+6 days');
    ?>
        Semana <?= $semanaActual ?> — <?= $semanaInicio->format('d M') ?> al <?= $semanaFin->format('d M Y') ?>
    <?php else: ?>
        Tareas de la semana
    <?php endif; ?>
</h2>

<?php if (empty($tareasEstaSemana)): ?>
    <div class="empty-state">
        <?= $semanaActual === 0 ? 'El proyecto inicia el ' . formatDate($proyectoInicio) : 'No hay tareas para esta semana' ?>
    </div>
<?php else: ?>
    <div class="tarea-list">
        <?php foreach ($tareasEstaSemana as $t): ?>
            <div class="tarea-card <?= $t['estado'] === 'completada' ? 'completada' : '' ?>" data-id="<?= (int)$t['id'] ?>">
                <div class="tarea-check">
                    <input type="checkbox" <?= $t['estado'] === 'completada' ? 'checked' : '' ?>>
                </div>
                <div class="tarea-body">
                    <div class="tarea-title"><?= e($t['titulo']) ?></div>
                    <?php if (!empty($t['descripcion'])): ?>
                        <div class="tarea-desc"><?= e($t['descripcion']) ?></div>
                    <?php endif; ?>
                    <div class="tarea-meta">
                        <span class="tarea-badge tarea-badge-estado <?= $t['estado'] ?>"><?= str_replace('_', ' ', $t['estado']) ?></span>
                        <span class="tarea-badge <?= $t['prioridad'] ?>"><?= $t['prioridad'] ?></span>
                        <span class="tarea-horas">
                            <?= number_format((float)$t['horas_estimadas'], 1) ?>h est.
                            / <input type="number" class="horas-reales-input" value="<?= number_format((float)$t['horas_reales'], 1) ?>" step="0.5" min="0" max="99">h real
                        </span>
                        <?php if (!empty($t['prompt_ref'])): ?>
                            <span class="tarea-prompt"><?= e($t['prompt_ref']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ── Vista Mensual ────────────────────────────────────── -->
<h2 class="proy-section-title" data-collapsible>Vista mensual</h2>
<div class="month-grid">
    <?php
        $mesesData = [
            ['Marzo 2026',  1, 4],
            ['Abril 2026',  5, 8],
            ['Mayo 2026',   9, 13],
            ['Junio 2026',  14, 17],
            ['Julio 2026',  18, 21],
            ['Agosto 2026', 22, 22],
        ];
        foreach ($mesesData as $md):
            $mesNombre = $md[0];
            $semDesde = $md[1];
            $semHasta = $md[2];

            $mesTareas = array_filter($tareas, function($t) use ($semDesde, $semHasta) {
                return (int)$t['semana'] >= $semDesde && (int)$t['semana'] <= $semHasta;
            });
            $mesTareasTotal = count($mesTareas);
            $mesCompletadas = count(array_filter($mesTareas, function($t) { return $t['estado'] === 'completada'; }));
            $mesHorasEst = array_sum(array_column($mesTareas, 'horas_estimadas'));
            $mesHorasReal = array_sum(array_column($mesTareas, 'horas_reales'));
            $mesPct = $mesTareasTotal > 0 ? round($mesCompletadas / $mesTareasTotal * 100) : 0;

            $mesHitos = array_filter($hitos, function($h) use ($semDesde, $semHasta) {
                return (int)$h['semana'] >= $semDesde && (int)$h['semana'] <= $semHasta;
            });
    ?>
        <div class="month-card">
            <h3><?= $mesNombre ?></h3>
            <div class="month-info">Semanas <span><?= $semDesde ?>–<?= $semHasta ?></span></div>
            <div class="month-info">Tareas: <span><?= $mesCompletadas ?>/<?= $mesTareasTotal ?></span> (<?= $mesPct ?>%)</div>
            <div class="month-info">Horas: <span><?= number_format($mesHorasReal, 1) ?></span> / <?= number_format($mesHorasEst, 0) ?>h est.</div>
            <div class="month-bar"><div class="month-bar-fill" style="width:<?= $mesPct ?>%"></div></div>
            <?php foreach ($mesHitos as $hito): ?>
                <div class="month-hito"><?= e($hito['titulo']) ?> (S<?= $hito['semana'] ?>)</div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- ── Reglas del Proyecto ──────────────────────────────── -->
<h2 class="proy-section-title" data-collapsible>Reglas del proyecto</h2>
<div class="rules-grid">
    <div class="rule-card">
        <h4>Ritmo</h4>
        <ul>
            <li>22 semanas: 2 Mar — 3 Ago 2026</li>
            <li>~10 horas/semana de desarrollo</li>
            <li>Sesiones de 2-3 horas concentradas</li>
            <li>76 tareas, 8 hitos</li>
        </ul>
    </div>
    <div class="rule-card">
        <h4>Metodo</h4>
        <ul>
            <li>Una tarea a la vez, completar antes de seguir</li>
            <li>Cada tarea tiene un prompt de referencia</li>
            <li>Registrar horas reales vs estimadas</li>
            <li>Commit al finalizar cada tarea</li>
        </ul>
    </div>
    <div class="rule-card">
        <h4>Fases</h4>
        <ul>
            <?php foreach ($fases as $f): ?>
                <li><span style="color:<?= e($f['color']) ?>">●</span> <?= e($f['nombre']) ?> — S<?= $f['semana_inicio'] ?>–S<?= $f['semana_fin'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="rule-card">
        <h4>Stack</h4>
        <ul>
            <li>PHP 8.3 vanilla MVC, sin framework</li>
            <li>MySQL 8, PDO</li>
            <li>CSS vanilla, sin frameworks</li>
            <li>JS vanilla, sin librerias</li>
        </ul>
    </div>
</div>

</div><!-- .proy-container -->

<script src="<?= asset('js/proyecto.js') ?>"></script>
</body>
</html>
