<?php
session_start();
if (!isset($_SESSION['docente'])) { header("Location: ../login/login.php"); exit(); }
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../assets/layout.php");

$sql = "SELECT * FROM vista_definitivas ORDER BY nombre_curso, apellidos";
$resultado = pg_query($conexion, $sql);

// Agrupar por curso para los filtros
$filas = [];
$cursos_unicos = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $filas[] = $fila;
    $cursos_unicos[$fila['nombre_curso']] = true;
}
$cursos_unicos = array_keys($cursos_unicos);
sort($cursos_unicos);

layout_header('Definitivas', 'definitivas', 1);
?>

<!-- jsPDF + autoTable desde CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<div class="page-header">
    <div>
        <h1>Notas Definitivas</h1>
        <div class="breadcrumb"><a href="../dashboard.php">Dashboard</a> › Definitivas</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <select id="filtroCurso" onchange="filtrarTabla()" style="width:auto;padding:8px 12px;font-size:.85rem">
            <option value="">— Todos los cursos —</option>
            <?php foreach ($cursos_unicos as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>
        <button onclick="descargarPDF()" class="btn btn-danger" id="btnPDF">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Descargar PDF
        </button>
    </div>
</div>

<div class="table-container">
    <div class="table-toolbar">
        <strong style="font-family:var(--font-main);font-size:.9rem;color:var(--moodle-blue)">
            Resumen de Definitivas
        </strong>
        <span id="contadorFilas" style="font-size:.8rem;color:var(--moodle-gray-text)"></span>
    </div>
    <table id="tablaDefinitivas">
        <thead>
            <tr>
                <th>Código</th>
                <th>Estudiante</th>
                <th>Curso</th>
                <th>Definitiva</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filas as $fila):
            $def = (float)$fila['definitiva'];
            $badgeClass = $def >= 3.0 ? 'badge-green' : 'badge-orange';
            $estado     = $def >= 3.0 ? 'Aprobado' : 'Reprobado';
        ?>
            <tr data-curso="<?= htmlspecialchars($fila['nombre_curso']) ?>">
                <td><span class="badge badge-blue"><?= htmlspecialchars($fila['cod_estudiante']) ?></span></td>
                <td><?= htmlspecialchars($fila['nombres'] . ' ' . $fila['apellidos']) ?></td>
                <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                <td><span class="badge <?= $badgeClass ?>" style="font-size:.88rem;padding:3px 10px"><?= htmlspecialchars($fila['definitiva']) ?></span></td>
                <td><span class="badge <?= $badgeClass ?>"><?= $estado ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($filas)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">🏆</div>
        <p>No hay notas definitivas registradas aún.</p>
    </div>
    <?php endif; ?>
</div>

<script>
// ── Filtro por curso ─────────────────────────────────────────────────────────
function filtrarTabla() {
    const filtro = document.getElementById('filtroCurso').value.toLowerCase();
    const filas  = document.querySelectorAll('#tablaDefinitivas tbody tr');
    let visibles = 0;

    filas.forEach(tr => {
        const curso = tr.dataset.curso.toLowerCase();
        const mostrar = !filtro || curso === filtro;
        tr.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });

    const total = filas.length;
    const contador = document.getElementById('contadorFilas');
    contador.textContent = filtro
        ? `Mostrando ${visibles} de ${total} registros`
        : `${total} registros en total`;
}

// Inicializar contador
filtrarTabla();

// ── Generar PDF ──────────────────────────────────────────────────────────────
function descargarPDF() {
    const btn = document.getElementById('btnPDF');
    btn.disabled = true;
    btn.textContent = 'Generando...';

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        const filtroCurso  = document.getElementById('filtroCurso').value;
        const tituloFiltro = filtroCurso ? ` — ${filtroCurso}` : ' — Todos los cursos';
        const ahora        = new Date().toLocaleDateString('es-CO', { year:'numeric', month:'long', day:'numeric' });

        // ── Encabezado ───────────────────────────────────────────────────────
        // Banda azul superior
        doc.setFillColor(0, 61, 107);          // --moodle-blue
        doc.rect(0, 0, 210, 28, 'F');

        // Logo "SN"
        doc.setFillColor(249, 128, 18);        // --moodle-orange
        doc.roundedRect(14, 6, 16, 16, 2, 2, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text('SN', 22, 17, { align: 'center' });

        // Título
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Sistema de Notas', 35, 13);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.text('Reporte de Notas Definitivas', 35, 20);

        // Fecha (derecha)
        doc.setFontSize(8);
        doc.text(ahora, 196, 13, { align: 'right' });

        // ── Subtítulo ────────────────────────────────────────────────────────
        doc.setTextColor(0, 61, 107);
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('Notas Definitivas' + tituloFiltro, 14, 38);

        // Línea separadora naranja
        doc.setDrawColor(249, 128, 18);
        doc.setLineWidth(0.8);
        doc.line(14, 41, 196, 41);

        // ── Recopilar datos visibles ─────────────────────────────────────────
        const filas = document.querySelectorAll('#tablaDefinitivas tbody tr');
        const body  = [];
        let aprobados = 0, reprobados = 0;

        filas.forEach(tr => {
            if (tr.style.display === 'none') return;
            const celdas = tr.querySelectorAll('td');
            const valor  = parseFloat(celdas[3].textContent.trim());
            const estado = valor >= 3.0 ? 'Aprobado' : 'Reprobado';
            if (valor >= 3.0) aprobados++; else reprobados++;

            body.push([
                celdas[0].textContent.trim(),
                celdas[1].textContent.trim(),
                celdas[2].textContent.trim(),
                valor.toFixed(2),
                estado
            ]);
        });

        // ── Tabla principal ──────────────────────────────────────────────────
        doc.autoTable({
            startY: 46,
            head: [['Código', 'Estudiante', 'Curso', 'Definitiva', 'Estado']],
            body: body,
            theme: 'grid',
            styles: {
                font: 'helvetica',
                fontSize: 9,
                cellPadding: 3,
                valign: 'middle',
                textColor: [30, 30, 30],
                lineColor: [220, 225, 230],
                lineWidth: 0.3
            },
            headStyles: {
                fillColor: [0, 61, 107],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                fontSize: 8.5,
                halign: 'left'
            },
            columnStyles: {
                0: { cellWidth: 22, halign: 'center' },
                1: { cellWidth: 58 },
                2: { cellWidth: 55 },
                3: { cellWidth: 24, halign: 'center', fontStyle: 'bold' },
                4: { cellWidth: 24, halign: 'center' }
            },
            alternateRowStyles: { fillColor: [248, 249, 251] },
            didDrawCell: (data) => {
                // Colorear celda de Estado y Definitiva
                if (data.section === 'body' && (data.column.index === 3 || data.column.index === 4)) {
                    const val = parseFloat(data.row.raw[3]);
                    const aprobado = val >= 3.0;
                    if (aprobado) {
                        doc.setTextColor(46, 125, 50);   // verde
                    } else {
                        doc.setTextColor(198, 40, 40);   // rojo
                    }
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    const cell = data.cell;
                    doc.text(
                        data.cell.raw,
                        cell.x + cell.width / 2,
                        cell.y + cell.height / 2 + 1,
                        { align: 'center' }
                    );
                    // reset
                    doc.setTextColor(30, 30, 30);
                    doc.setFont('helvetica', 'normal');
                }
            }
        });

        // ── Resumen estadístico ──────────────────────────────────────────────
        const finalY  = doc.lastAutoTable.finalY + 8;
        const total   = aprobados + reprobados;
        const pctAprob = total > 0 ? ((aprobados / total) * 100).toFixed(1) : '0.0';

        doc.setFillColor(248, 249, 251);
        doc.roundedRect(14, finalY, 182, 22, 2, 2, 'F');
        doc.setDrawColor(220, 225, 230);
        doc.setLineWidth(0.3);
        doc.roundedRect(14, finalY, 182, 22, 2, 2, 'S');

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8.5);
        doc.setTextColor(0, 61, 107);
        doc.text('Resumen', 20, finalY + 7);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8);
        doc.setTextColor(50, 50, 50);
        doc.text(`Total estudiantes: ${total}`, 20, finalY + 15);

        doc.setTextColor(46, 125, 50);
        doc.setFont('helvetica', 'bold');
        doc.text(`Aprobados: ${aprobados} (${pctAprob}%)`, 80, finalY + 15);

        doc.setTextColor(198, 40, 40);
        doc.text(`Reprobados: ${reprobados} (${(100 - parseFloat(pctAprob)).toFixed(1)}%)`, 140, finalY + 15);

        // ── Pie de página ────────────────────────────────────────────────────
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setDrawColor(200, 200, 200);
            doc.setLineWidth(0.3);
            doc.line(14, 287, 196, 287);
            doc.setTextColor(150, 150, 150);
            doc.setFontSize(7.5);
            doc.setFont('helvetica', 'normal');
            doc.text('Sistema de Gestión de Notas', 14, 292);
            doc.text(`Página ${i} de ${pageCount}`, 196, 292, { align: 'right' });
        }

        // ── Nombre del archivo ───────────────────────────────────────────────
        const nombreArchivo = filtroCurso
            ? `definitivas_${filtroCurso.replace(/\s+/g, '_')}.pdf`
            : `definitivas_${new Date().toISOString().slice(0,10)}.pdf`;

        doc.save(nombreArchivo);

    } catch (e) {
        alert('Error al generar el PDF: ' + e.message);
        console.error(e);
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg> Descargar PDF`;
    }
}
</script>

<?php layout_footer(); ?>
