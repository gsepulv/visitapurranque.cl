-- Seed: Contenido real de Purranque
-- Ejecutar: mysql -u root visitapurranque < database/seed-contenido-real.sql

-- ═══════════════════════════════════════════
-- PASO 0: Corregir aniversario de Purranque
-- ═══════════════════════════════════════════
UPDATE eventos SET
  titulo = 'Aniversario N°115 de Purranque',
  descripcion = 'Celebración del aniversario N°115 de la comuna de Purranque, fundada el 17 de abril de 1911. Desfile cívico, actividades culturales, gastronomía típica y show artístico en la Plaza de Armas.',
  fecha_inicio = '2026-04-17 10:00:00',
  fecha_fin = '2026-04-19 23:00:00',
  lugar = 'Plaza de Armas, Purranque',
  destacado = 1
WHERE slug = 'aniversario-purranque-2026';

-- ═══════════════════════════════════════════
-- PASO 1: Fichas de atractivos reales
-- Categorías: 1=Playas, 2=Naturaleza, 3=Patrimonio, 4=Gastronomía,
--             5=Cultura, 6=Alojamiento, 7=Transporte, 8=Eventos,
--             9=Fauna, 10=Servicios
-- ═══════════════════════════════════════════

INSERT INTO fichas (nombre, slug, categoria_id, descripcion_corta, descripcion, direccion, latitud, longitud, telefono, whatsapp, activo, verificado, destacado, created_at, updated_at) VALUES

-- NATURALEZA (cat 2)
('Río Llico', 'rio-llico', 2,
'Río que atraviesa la comuna ideal para pesca deportiva',
'El río Llico nace en la cordillera de la costa y desemboca en el océano Pacífico. Es un destino popular para la pesca deportiva de trucha y salmón. Sus riberas ofrecen paisajes de bosque nativo y praderas verdes típicas del sur de Chile. Se puede acceder desde varios puntos de la comuna.',
'Sector Llico, Purranque', -40.7800000, -73.4500000, NULL, NULL, 1, 0, 0, NOW(), NOW()),

('Salto del Río Negro', 'salto-rio-negro', 2,
'Cascada natural en entorno de bosque valdiviano',
'Impresionante caída de agua rodeada de bosque valdiviano con helechos, nalcas y árboles nativos. Se accede por un sendero de dificultad media desde el camino principal. Ideal para fotografía y contemplación de la naturaleza.',
'Sector Río Negro, límite comunal', -40.8000000, -73.2500000, NULL, NULL, 1, 0, 0, NOW(), NOW()),

-- FAUNA (cat 9)
('Humedal El Boldo', 'humedal-el-boldo', 9,
'Ecosistema de humedal con diversa avifauna',
'Humedal periurbano que alberga una rica diversidad de aves acuáticas, incluyendo cisnes de cuello negro, taguas, garzas y patos. Es un sitio importante para la observación de aves y la educación ambiental.',
'Sector El Boldo, Purranque', -40.9200000, -73.1500000, NULL, NULL, 1, 0, 0, NOW(), NOW()),

-- GASTRONOMÍA (cat 4)
('Cervecería Artesanal del Sur', 'cerveceria-artesanal-del-sur', 4,
'Cerveza artesanal elaborada con ingredientes locales',
'Pequeña cervecería artesanal que produce cervezas con agua de vertiente y lúpulo regional. Ofrece degustaciones y venta directa. Variedades incluyen Amber Ale, Stout de avellana y Pale Ale con maqui.',
'Ruta 5 Sur, km 942, Purranque', -40.9050000, -73.1400000, '+56976543210', '56976543210', 1, 1, 0, NOW(), NOW()),

('Feria Libre de Purranque', 'feria-libre-purranque', 4,
'Mercado semanal con productos frescos y artesanía',
'La Feria Libre se realiza todos los sábados en el centro de Purranque. Ofrece verduras de huerta, quesos artesanales, miel, mermeladas, pan amasado, tortillas al rescoldo y artesanía local. Es el mejor lugar para conocer los sabores auténticos de la zona.',
'Calle O''Higgins, Purranque', -40.9115000, -73.1360000, NULL, NULL, 1, 1, 1, NOW(), NOW()),

('Ahumados Don Héctor', 'ahumados-don-hector', 4,
'Productos ahumados artesanales del sur de Chile',
'Producción artesanal de carnes y embutidos ahumados con leña nativa. Especialidades: jamón ahumado, longanizas, costillar y salmón ahumado. Tradición familiar de más de 30 años.',
'Sector Hueyusca, Purranque', -40.9300000, -73.1800000, '+56987654321', '56987654321', 1, 1, 0, NOW(), NOW()),

-- ALOJAMIENTO (cat 6)
('Cabañas Los Alerces', 'cabanas-los-alerces', 6,
'Cabañas equipadas en entorno rural tranquilo',
'Conjunto de 4 cabañas de madera nativa totalmente equipadas. Cada cabaña cuenta con cocina, baño privado, calefacción a leña y estacionamiento. Ubicadas en un predio de 2 hectáreas con jardines y quincho para asados.',
'Camino a Corte Alto, km 3, Purranque', -40.9000000, -73.1600000, '+56954321000', '56954321000', 1, 1, 1, NOW(), NOW()),

('Camping Río Llico', 'camping-rio-llico', 6,
'Camping a orillas del río con servicios básicos',
'Sitios de camping con acceso directo al río Llico. Cuenta con baños, duchas con agua caliente, quincho comunitario y leña disponible. Capacidad para 15 carpas y 5 motorhomes.',
'Sector Llico, Purranque', -40.7850000, -73.4400000, '+56912121212', '56912121212', 1, 0, 0, NOW(), NOW()),

-- CULTURA (cat 5)
('Museo Comunitario de Purranque', 'museo-comunitario-purranque', 5,
'Historia y patrimonio de la comuna desde la colonización',
'Pequeño museo que reúne objetos, fotografías y documentos de la historia de Purranque desde la llegada de los colonos alemanes y la presencia ancestral del pueblo Huilliche. Incluye herramientas agrícolas antiguas, vestimenta tradicional y mapas históricos.',
'Calle Señoret 320, Purranque', -40.9112000, -73.1345000, NULL, NULL, 1, 0, 0, NOW(), NOW()),

('Comunidad Huilliche de Manquemapu', 'comunidad-huilliche-manquemapu', 5,
'Turismo comunitario indígena en la costa de Purranque',
'La comunidad Huilliche de Manquemapu ofrece experiencias de turismo comunitario: recorridos por el bosque de alerce, relatos de tradición oral, gastronomía costera y hospedaje familiar. Una oportunidad única de conocer la cultura viva del pueblo originario.',
'Caleta Manquemapu, Costa de Purranque', -40.5850000, -73.7350000, '+56990534372', '56990534372', 1, 1, 1, NOW(), NOW()),

-- SERVICIOS (cat 10)
('Terminal de Buses Purranque', 'terminal-buses-purranque', 10,
'Principal punto de conexión terrestre de la comuna',
'Punto de partida de buses interurbanos y rurales. Conexiones frecuentes a Osorno (30 min), Puerto Montt (1.5 h), Puerto Varas, Frutillar y Santiago. Empresas: Buses Lagos del Sur, TranSantin, Pullman Bus.',
'Calle Eleuterio Ramírez, Purranque', -40.9125000, -73.1355000, NULL, NULL, 1, 1, 0, NOW(), NOW()),

('Oficina de Turismo Municipal', 'oficina-turismo-municipal', 10,
'Información turística oficial de la comuna',
'Oficina municipal que entrega información sobre atractivos, alojamiento, transporte y eventos de Purranque. Material impreso disponible con mapas y guías. Horario: lunes a viernes 8:30 a 17:30.',
'Municipalidad de Purranque, Plaza de Armas', -40.9117000, -73.1347000, '+56642531000', NULL, 1, 1, 0, NOW(), NOW());

-- ═══════════════════════════════════════════
-- PASO 2: Eventos del calendario anual
-- Nota: No hay columna 'tipo' en eventos
-- ═══════════════════════════════════════════

INSERT INTO eventos (titulo, slug, descripcion, fecha_inicio, fecha_fin, lugar, latitud, longitud, activo, destacado, created_at, updated_at) VALUES

('Fiesta Costumbrista de Purranque 2026', 'fiesta-costumbrista-2026',
'Gran fiesta costumbrista con gastronomía típica campesina, música folclórica, artesanía, rodeo, juegos tradicionales y muestra de tradiciones rurales del sur de Chile. Entrada liberada.',
'2026-02-21 10:00:00', '2026-02-22 20:00:00',
'Medialuna de Purranque', -40.9150000, -73.1400000, 1, 1, NOW(), NOW()),

('Semana Purranquina 2026', 'semana-purranquina-2026',
'Una semana completa de actividades culturales, deportivas y artísticas en celebración de la identidad local. Incluye desfile, elección de reina, campeonato de cueca, feria gastronómica y show de cierre.',
'2026-04-14 09:00:00', '2026-04-20 23:00:00',
'Diversos puntos de Purranque', -40.9117000, -73.1347000, 1, 1, NOW(), NOW()),

('Feria de la Sidra y el Queso', 'feria-sidra-queso-2026',
'Muestra de productores locales de sidra artesanal y quesos de campo. Degustaciones, maridajes, música en vivo y venta directa del productor al consumidor.',
'2026-03-14 11:00:00', '2026-03-15 19:00:00',
'Plaza de Armas, Purranque', -40.9117000, -73.1347000, 1, 1, NOW(), NOW()),

('Festival de la Leña y el Fogón', 'festival-lena-fogon-2026',
'Celebración de las tradiciones de cocina a fuego lento. Competencia de asadores, fogón comunitario, música de acordeón y guitarras. Platos preparados con productos locales.',
'2026-06-20 12:00:00', '2026-06-21 20:00:00',
'Sector rural Purranque', -40.9200000, -73.1500000, 1, 0, NOW(), NOW()),

('Temporada de Trekking Mapu Lahual 2026-2027', 'temporada-trekking-mapu-lahual-2026',
'Apertura de la temporada de trekking guiado en el Parque Mapu Lahual. Senderos entre bosques de alerce milenario con guías huilliche. Reservar con anticipación.',
'2026-11-15 08:00:00', '2027-03-31 18:00:00',
'Parque Mapu Lahual, Costa de Purranque', -40.6500000, -73.7200000, 1, 1, NOW(), NOW()),

('Noche de San Juan — Wetripantu', 'noche-san-juan-wetripantu-2026',
'Celebración del Wetripantu (Año Nuevo Mapuche-Huilliche) coincidente con la noche más larga del año. Fogatas, cantos, comida comunitaria y ceremonias ancestrales.',
'2026-06-23 18:00:00', '2026-06-24 08:00:00',
'Comunidad Huilliche de Manquemapu', -40.5850000, -73.7350000, 1, 1, NOW(), NOW()),

('Feria Navideña de Purranque', 'feria-navidena-2026',
'Feria de emprendedores locales con productos para regalos navideños: artesanía en madera, tejidos, dulces, conservas, miel y productos gourmet del sur.',
'2026-12-18 10:00:00', '2026-12-23 20:00:00',
'Plaza de Armas, Purranque', -40.9117000, -73.1347000, 1, 0, NOW(), NOW());

-- ═══════════════════════════════════════════
-- PASO 3: Artículos del blog
-- Nota: autor_id (FK), no text. Usar autor_id = 1 (admin)
-- ═══════════════════════════════════════════

INSERT INTO blog_posts (titulo, slug, extracto, contenido, tipo, estado, destacado, autor_id, publicado_at, created_at, updated_at) VALUES

('Cómo llegar a la costa de Purranque: guía práctica 2026', 'como-llegar-costa-purranque-2026',
'Todo lo que necesitas saber sobre rutas, transporte y preparación para llegar a Manquemapu y Bahía San Pedro.',
'Llegar a la costa de Purranque es una aventura en sí misma. La ruta U-910 sale desde Purranque hacia el oeste y recorre aproximadamente 80 kilómetros de camino de ripio hasta las caletas costeras.

**Tiempo estimado:** Entre 2 y 3 horas dependiendo del estado del camino y el clima.

**Vehículo recomendado:** Camioneta o SUV con tracción 4x4, especialmente en invierno. En verano se puede transitar con vehículo normal con precaución.

**Transporte público:** Existe un bus subsidiado que sale desde Purranque hacia Manquemapu. Consultar horarios en el Terminal de Buses o en la Municipalidad.

**Qué llevar:**
- Ropa impermeable (llueve más de 3.000 mm al año)
- Zapatos de trekking impermeables
- Efectivo (no hay cajeros en la costa)
- Alimentos y agua
- Estanque lleno de bencina (no hay estaciones en la ruta)
- Linterna y batería extra para el celular

**Dónde dormir:** Hospedajes familiares en Manquemapu ofrecen alojamiento con alimentación incluida. También hay camping en las caletas. Reservar con anticipación llamando a los contactos locales.

**Contactos útiles:**
- Guía local Javier Ancapan: +569 90534372
- Transporte a la costa don Carlos: +569 98631541

**Mejor época:** Diciembre a marzo, cuando los caminos están en mejor estado y el clima es más favorable.',
'guia', 'publicado', 1, 1, NOW(), NOW(), NOW()),

('Wetripantu: el Año Nuevo Huilliche en Purranque', 'wetripantu-ano-nuevo-huilliche',
'Conoce la celebración ancestral del Wetripantu y cómo las comunidades de Purranque mantienen viva esta tradición.',
'Cada 24 de junio, cuando la noche más larga del año da paso a un nuevo ciclo solar, las comunidades Huilliche de Purranque celebran el Wetripantu o Año Nuevo Indígena.

Esta ceremonia ancestral tiene raíces profundas en la cosmovisión mapuche-huilliche. El Wetripantu marca el regreso del sol (We = nuevo, Tripantu = año/salida del sol) y simboliza la renovación de la naturaleza.

En la costa de Purranque, particularmente en Manquemapu, las comunidades mantienen viva esta tradición con:

- Fogatas comunitarias que arden toda la noche
- Cantos y relatos de los ancianos
- Comida preparada en comunidad: sopaipillas, muday, tortillas al rescoldo
- Ceremonias de agradecimiento a la tierra
- Amanecer conjunto esperando el primer rayo de sol del nuevo ciclo

Para los visitantes, participar del Wetripantu es una experiencia profundamente transformadora. Se recomienda contactar a las comunidades con anticipación y respetar los protocolos culturales.

El municipio de Purranque reconoce el 24 de junio como fecha significativa y apoya las actividades de celebración de las comunidades originarias de la comuna.',
'articulo', 'publicado', 1, 1, NOW(), NOW(), NOW()),

('Los alerces milenarios de Mapu Lahual: patrimonio vivo', 'alerces-milenarios-mapu-lahual-patrimonio',
'Descubre por qué los bosques de alerce de la costa de Purranque son considerados patrimonio natural de la humanidad.',
'Los alerces (Fitzroya cupressoides) del Parque Mapu Lahual en la costa de Purranque son verdaderos monumentos vivientes. Algunos ejemplares superan los 3.000 años de edad, lo que los convierte en los árboles más longevos de Sudamérica y los segundos más antiguos del planeta después de los Bristlecone Pine de California.

El nombre Mapu Lahual significa "Tierra de Alerces" en mapudungun, y es administrado por las propias comunidades Huilliche de la costa.

**Características del alerce:**
- Altura: hasta 50 metros
- Diámetro: hasta 4 metros
- Madera: rojiza, resistente a la humedad, históricamente usada en tejuelas
- Estado: Monumento Natural desde 1976, prohibida su tala

Los senderos de Mapu Lahual permiten caminar entre estos gigantes en un entorno de bosque valdiviano prístino, con arrayanes, olivillos costeros, helechos y una atmósfera de humedad permanente.

Para visitar los senderos es obligatorio contratar guías locales huilliche, quienes comparten no solo el conocimiento ecológico sino también la relación espiritual que su pueblo mantiene con estos árboles ancestrales.

**Temporada:** Noviembre a marzo
**Dificultad:** Media
**Duración:** 3 a 6 horas según el circuito
**Reservas:** Javier Ancapan +569 90534372',
'articulo', 'publicado', 1, 1, NOW(), NOW(), NOW()),

('Gastronomía de Purranque: 7 sabores que no te puedes perder', 'gastronomia-purranque-7-sabores',
'Desde el cordero al palo hasta la sidra artesanal, un recorrido por los sabores imperdibles de la comuna.',
'Purranque es tierra de sabores auténticos. Su gastronomía refleja la mezcla de tradiciones campesinas, herencia de colonos alemanes y cultura huilliche costera. Estos son los 7 sabores que definen a la comuna:

**1. Cordero al palo:** El rey de las fiestas costumbristas. Cordero cocinado lentamente sobre brasas de leña nativa durante horas. La piel queda crujiente y la carne jugosa. Se sirve con pebre y ensalada chilena.

**2. Sidra artesanal:** Purranque tiene tradición sidrera heredada de los colonos. Manzanas de huerto familiar fermentadas naturalmente producen una sidra fresca, ligeramente ácida, perfecta para acompañar asados.

**3. Queso de campo:** Los campos de Purranque producen leche de vacas alimentadas con pasto natural. Los quesos artesanales (fresco, mantecoso, ahumado) son un producto estrella de la feria local.

**4. Milcao y chapalele:** Preparaciones de papa rallada típicas del sur. El milcao es una tortilla frita y el chapalele se cocina al vapor. Ambos son parte esencial del curanto.

**5. Curanto en hoyo:** Preparación ancestral huilliche. Mariscos, carnes, papas, milcaos y chapaleles cocidos sobre piedras calientes en un hoyo en la tierra, tapado con hojas de nalca.

**6. Pan amasado y tortillas al rescoldo:** Pan horneado en horno de barro o directamente en las cenizas del fogón. Se come caliente con mantequilla fresca.

**7. Mermeladas de murta y maqui:** Frutos nativos del bosque valdiviano convertidos en conservas caseras. La murta tiene un sabor dulce intenso y el maqui un toque ácido con propiedades antioxidantes.',
'guia', 'publicado', 1, 1, NOW(), NOW(), NOW()),

('Guía de observación de aves en Purranque', 'guia-observacion-aves-purranque',
'Purranque es un paraíso para el avistamiento de aves. Descubre las mejores zonas y las especies que puedes encontrar.',
'La diversidad de ecosistemas de Purranque — desde humedales urbanos hasta bosque valdiviano costero — lo convierten en un destino privilegiado para la observación de aves.

**Zonas recomendadas:**

**1. Humedal El Boldo (periurbano):** Cisne de cuello negro, tagua, garza cuca, pato real, pidén. Acceso fácil desde el centro de Purranque.

**2. Costa de Manquemapu:** Pelícano, cormorán, gaviota dominicana, pilpilén, churrete costero. Aves marinas y costeras.

**3. Bosques de Mapu Lahual:** Carpintero negro, chucao, hued-hued, rayadito, comesebo. Aves del bosque valdiviano.

**4. Campos y praderas:** Tiuque, traro, bandurria, queltehue, loica. Aves de campo abierto.

**5. Ríos y esteros:** Martín pescador, huala, pato cortacorrientes, garza chica.

**Especies destacadas:**
- **Carpintero negro** (Campephilus magellanicus): El más grande de Chile, frecuente en bosques costeros
- **Cisne de cuello negro:** Emblema de los humedales del sur
- **Chucao:** Su canto es inconfundible en el bosque
- **Cóndor:** Ocasionalmente avistado en zonas cordilleranas

**Mejor época:** Todo el año, con mayor actividad en primavera (septiembre-noviembre)

**Recomendaciones:** Binoculares, ropa discreta, paciencia y guía de aves de Chile.',
'guia', 'publicado', 0, 1, NOW(), NOW(), NOW());

-- ═══════════════════════════════════════════
-- PASO 4: Reseñas para fichas nuevas
-- ═══════════════════════════════════════════

INSERT INTO resenas (ficha_id, nombre, email, rating, tipo_experiencia, comentario, estado, created_at) VALUES

((SELECT id FROM fichas WHERE slug = 'feria-libre-purranque'), 'Roberto Cárcamo', 'roberto@example.com', 5, 'gastronomia', 'La mejor feria del sur. Los quesos y la miel son increíbles. Ir temprano porque se acaba rápido.', 'aprobada', NOW()),

((SELECT id FROM fichas WHERE slug = 'feria-libre-purranque'), 'Claudia Vargas', 'claudia@example.com', 4, 'gastronomia', 'Variedad de productos frescos y precios justos. Los sábados es imperdible.', 'aprobada', NOW()),

((SELECT id FROM fichas WHERE slug = 'cabanas-los-alerces'), 'Martín Saavedra', 'martin@example.com', 5, 'alojamiento', 'Cabañas impecables, muy bien equipadas. El entorno es precioso y la atención muy cálida.', 'aprobada', NOW()),

((SELECT id FROM fichas WHERE slug = 'comunidad-huilliche-manquemapu'), 'Isabel Torres', 'isabel@example.com', 5, 'visita_cultural', 'Experiencia inolvidable. Los guías huilliche comparten su cultura con mucho orgullo y respeto. Recomendado 100%.', 'aprobada', NOW());

-- ═══════════════════════════════════════════
-- PASO 5: Actualizar proyecto_tareas
-- ═══════════════════════════════════════════

UPDATE proyecto_tareas SET estado = 'completada', fecha_completada = NOW() WHERE id IN (70, 71, 72, 73);
