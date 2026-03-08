-- Seed permisos iniciales (2026-03-08)
INSERT INTO permisos (nombre, slug, modulo, descripcion) VALUES
('Ver fichas', 'fichas.ver', 'fichas', 'Puede ver listado de fichas'),
('Crear fichas', 'fichas.crear', 'fichas', 'Puede crear fichas nuevas'),
('Editar fichas', 'fichas.editar', 'fichas', 'Puede editar fichas existentes'),
('Eliminar fichas', 'fichas.eliminar', 'fichas', 'Puede eliminar fichas'),
('Ver categorías', 'categorias.ver', 'categorias', 'Puede ver categorías'),
('Gestionar categorías', 'categorias.gestionar', 'categorias', 'Puede crear/editar/eliminar categorías'),
('Ver eventos', 'eventos.ver', 'eventos', 'Puede ver eventos'),
('Gestionar eventos', 'eventos.gestionar', 'eventos', 'Puede crear/editar/eliminar eventos'),
('Ver blog', 'blog.ver', 'blog', 'Puede ver posts'),
('Gestionar blog', 'blog.gestionar', 'blog', 'Puede crear/editar/eliminar posts'),
('Moderar reseñas', 'resenas.moderar', 'resenas', 'Puede aprobar/rechazar reseñas'),
('Gestionar medios', 'medios.gestionar', 'medios', 'Puede subir/eliminar medios'),
('Gestionar usuarios', 'usuarios.gestionar', 'usuarios', 'Puede crear/editar usuarios'),
('Gestionar SEO', 'seo.gestionar', 'configuracion', 'Puede editar SEO'),
('Gestionar apariencia', 'apariencia.gestionar', 'configuracion', 'Puede editar apariencia'),
('Ver estadísticas', 'estadisticas.ver', 'configuracion', 'Puede ver reportes'),
('Gestionar configuración', 'configuracion.gestionar', 'configuracion', 'Acceso total a configuración');

-- Admin: todos los permisos
INSERT INTO rol_permisos (rol_id, permiso_id) SELECT 1, id FROM permisos;

-- Editor: permisos básicos de contenido
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 2, id FROM permisos WHERE slug IN ('fichas.ver','fichas.crear','fichas.editar','categorias.ver','eventos.ver','eventos.gestionar','blog.ver','blog.gestionar','medios.gestionar','estadisticas.ver');

-- Colaborador: permisos mínimos
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 3, id FROM permisos WHERE slug IN ('fichas.ver','fichas.crear','categorias.ver','eventos.ver','blog.ver','medios.gestionar');
