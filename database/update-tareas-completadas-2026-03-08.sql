-- Actualización de proyecto_tareas: marcar 26 tareas como completadas
-- Basado en verificación real del código existente (2026-03-08)
-- Tareas 12-13: Home hero, countdown, categorías, destacados
-- Tareas 15-22: Deploy, categorías, fichas, paginación, breadcrumbs, Schema.org
-- Tareas 23-28: Mapa interactivo, reseñas, vistas, compartir, relacionados
-- Tareas 29-33: Eventos con Schema.org Event, Blog con Schema.org Article
-- Tareas 35-38: Contacto, buscador, FAQ con FAQPage, páginas legales
-- Tarea 61: Admin editor menú drag & drop

UPDATE proyecto_tareas
SET estado = 'completada', fecha_completada = '2026-03-08 15:25:36'
WHERE id IN (12,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,35,36,37,38,61)
  AND estado != 'completada';
