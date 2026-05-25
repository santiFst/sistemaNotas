<?php
// layout helper — include at top of each page
// Usage: include once, then call layout_header($title, $active, $depth)
//        and layout_footer()

function layout_header(string $title, string $active = '', int $depth = 0): void {
    $root = str_repeat('../', $depth);
    $nav  = [
        'dashboard'     => ['icon' => '&#x1F3E0;', 'label' => 'Dashboard',      'href' => 'dashboard.php'],
        'estudiantes'   => ['icon' => '&#x1F393;', 'label' => 'Estudiantes',    'href' => 'estudiantes/listar.php'],
        'cursos'        => ['icon' => '&#x1F4DA;', 'label' => 'Cursos',         'href' => 'cursos/listar.php'],
        'evaluaciones'  => ['icon' => '&#x1F4DD;', 'label' => 'Evaluaciones',   'href' => 'evaluaciones/listar.php'],
        'inscripciones' => ['icon' => '&#x1F4CB;', 'label' => 'Inscripciones',  'href' => 'inscripciones/listar.php'],
        'calificaciones'=> ['icon' => '&#x1F522;', 'label' => 'Calificaciones', 'href' => 'calificaciones/listar.php'],
        'definitivas'   => ['icon' => '&#x1F3C6;', 'label' => 'Definitivas',    'href' => 'definitivas/listar.php'],
    ];

    // Nombre e inicial del docente desde sesion
    $nombre_docente  = 'Docente';
    $inicial_docente = 'D';
    if (!empty($_SESSION['nombre_docente'])) {
        $nombre_docente = $_SESSION['nombre_docente'];
        // Inicial robusta sin depender de mbstring
        preg_match('/^\S/u', $nombre_docente, $m);
        $inicial_docente = isset($m[0]) ? strtoupper($m[0]) : 'D';
    }
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> &mdash; Sistema de Notas</title>
    <link rel="stylesheet" href="<?= $root ?>assets/css/styles.css">
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
    <a href="<?= $root ?>dashboard.php" class="topbar-brand">
        <div class="topbar-logo">SN</div>
        <span class="topbar-title">Sistema de Notas</span>
    </a>
    <div class="topbar-spacer"></div>
    <div class="topbar-user">
        <div class="topbar-avatar"><?= htmlspecialchars($inicial_docente) ?></div>
        <div class="topbar-user-info">
            <span class="topbar-user-name"><?= htmlspecialchars($nombre_docente) ?></span>
            <span class="topbar-user-role">Docente</span>
        </div>
        <a href="<?= $root ?>login/logout.php" class="topbar-logout">Cerrar sesi&oacute;n</a>
    </div>
</header>

<!-- SIDEBAR -->
<nav class="sidebar">
    <div class="sidebar-section-label">Men&uacute; principal</div>
    <ul class="sidebar-nav">
        <?php foreach ($nav as $key => $item): ?>
        <li>
            <a href="<?= $root . $item['href'] ?>"
               class="<?= $active === $key ? 'active' : '' ?>">
                <span><?= $item['icon'] ?></span>
                <?= $item['label'] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<!-- MAIN -->
<main class="page-wrapper">
    <?php
}

function layout_footer(): void {
    ?>
</main>
<footer class="page-footer">
    &copy; <?= date('Y') ?> Sistema de Gesti&oacute;n de Notas &mdash; Desarrollado con PHP &amp; PostgreSQL
</footer>
</body>
</html>
    <?php
}
