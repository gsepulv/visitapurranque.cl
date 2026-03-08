-- Seed: Plantillas de Email para visitapurranque.cl
-- Ejecutar: mysql -u root visitapurranque < database/seed-emails.sql

INSERT INTO email_templates (nombre, slug, asunto, cuerpo_html, variables, activo) VALUES
('Contacto - Confirmación', 'contacto-confirmacion', 'Hemos recibido tu mensaje — Visita Purranque',
'<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
<h2 style="color:#1a5632;">¡Gracias por contactarnos, {{nombre}}!</h2>
<p>Hemos recibido tu mensaje y te responderemos a la brevedad.</p>
<p><strong>Tu mensaje:</strong></p>
<blockquote style="background:#f0fdf4;border-left:4px solid #1a5632;padding:12px 16px;margin:16px 0;">{{mensaje}}</blockquote>
<p style="color:#6b7280;font-size:.9rem;">Este es un correo automático, por favor no respondas directamente.</p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
<p style="color:#9ca3af;font-size:.8rem;">Visita Purranque — Guía Turística de Purranque</p>
</div>',
'["nombre", "mensaje"]', 1),

('Contacto - Respuesta Admin', 'contacto-respuesta', 'Re: Tu consulta — Visita Purranque',
'<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
<h2 style="color:#1a5632;">Hola {{nombre}},</h2>
<p>{{respuesta}}</p>
<p style="margin-top:20px;">Saludos cordiales,<br><strong>Equipo Visita Purranque</strong></p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
<p style="color:#9ca3af;font-size:.8rem;">Visita Purranque — Guía Turística de Purranque</p>
</div>',
'["nombre", "respuesta"]', 1),

('Reseña - Aprobada', 'resena-aprobada', 'Tu reseña ha sido publicada — Visita Purranque',
'<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
<h2 style="color:#1a5632;">¡Gracias por tu reseña, {{nombre}}!</h2>
<p>Tu reseña sobre <strong>{{ficha_nombre}}</strong> ha sido aprobada y ya está visible en el sitio.</p>
<p>Tu calificación: {{rating}} &#11088;</p>
<p><a href="{{ficha_url}}" style="display:inline-block;padding:10px 20px;background:#1a5632;color:#fff;text-decoration:none;border-radius:6px;">Ver tu reseña</a></p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
<p style="color:#9ca3af;font-size:.8rem;">Visita Purranque — Guía Turística de Purranque</p>
</div>',
'["nombre", "ficha_nombre", "rating", "ficha_url"]', 1),

('Bienvenida - Nuevo Usuario', 'bienvenida-usuario', 'Bienvenido a Visita Purranque',
'<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
<h2 style="color:#1a5632;">¡Bienvenido, {{nombre}}!</h2>
<p>Se ha creado tu cuenta en el panel de administración de Visita Purranque.</p>
<p><strong>Email:</strong> {{email}}<br>
<strong>Rol:</strong> {{rol}}</p>
<p><a href="{{login_url}}" style="display:inline-block;padding:10px 20px;background:#1a5632;color:#fff;text-decoration:none;border-radius:6px;">Iniciar sesión</a></p>
<p style="color:#6b7280;font-size:.9rem;">Si no solicitaste esta cuenta, puedes ignorar este correo.</p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
<p style="color:#9ca3af;font-size:.8rem;">Visita Purranque — Guía Turística de Purranque</p>
</div>',
'["nombre", "email", "rol", "login_url"]', 1)

ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
