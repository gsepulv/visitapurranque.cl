SET NAMES utf8mb4;
-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: visitapurranque
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `atributos`
--

LOCK TABLES `atributos` WRITE;
/*!40000 ALTER TABLE `atributos` DISABLE KEYS */;
/*!40000 ALTER TABLE `atributos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
INSERT INTO `audit_log` (`id`, `usuario_id`, `accion`, `modulo`, `registro_id`, `registro_tipo`, `datos_antes`, `datos_despues`, `ip`, `user_agent`, `created_at`) VALUES (4,1,'crear','categorias',NULL,'categoria',NULL,'{\"detalle\": \"Subcategoría #2: Senderos de trekking (en Naturaleza y Senderos)\"}','127.0.0.1','curl/8.18.0','2026-03-01 05:44:56'),(49,1,'login','auth',NULL,NULL,NULL,'{\"detalle\": \"Inicio de sesión\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0','2026-03-01 23:14:05'),(50,1,'login','auth',NULL,NULL,NULL,'{\"detalle\": \"Inicio de sesión\"}','127.0.0.1','curl/8.18.0','2026-03-01 23:16:45'),(51,1,'login','auth',NULL,NULL,NULL,'{\"detalle\": \"Inicio de sesión\"}','127.0.0.1','curl/8.18.0','2026-03-01 23:22:42'),(52,1,'login','auth',NULL,NULL,NULL,'{\"detalle\": \"Inicio de sesión\"}','127.0.0.1','curl/8.18.0','2026-03-01 23:23:03');
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_autores`
--

LOCK TABLES `blog_autores` WRITE;
/*!40000 ALTER TABLE `blog_autores` DISABLE KEYS */;
INSERT INTO `blog_autores` (`id`, `usuario_id`, `nombre`, `slug`, `bio`, `avatar`, `email`, `twitter`, `instagram`, `activo`, `created_at`) VALUES (1,1,'Gustavo','gustavo','Director de PurranQUE.INFO y apasionado por Purranque',NULL,'contacto@purranque.info',NULL,NULL,1,'2026-02-26 19:50:55');
/*!40000 ALTER TABLE `blog_autores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_categorias`
--

LOCK TABLES `blog_categorias` WRITE;
/*!40000 ALTER TABLE `blog_categorias` DISABLE KEYS */;
INSERT INTO `blog_categorias` (`id`, `nombre`, `slug`, `descripcion`, `emoji`, `color`, `meta_title`, `meta_description`, `orden`, `activo`, `created_at`) VALUES (1,'Noticias Locales','noticias-locales',NULL,'📰','#3b82f6',NULL,NULL,1,1,'2026-02-26 19:50:55'),(2,'Turismo y Naturaleza','turismo-y-naturaleza',NULL,'🏔','#22c55e',NULL,NULL,2,1,'2026-02-26 19:50:55'),(3,'Cultura y Tradiciones','cultura-y-tradiciones',NULL,'🎭','#ec4899',NULL,NULL,3,1,'2026-02-26 19:50:55'),(4,'Gastronomía','gastronomia',NULL,'🍽','#f59e0b',NULL,NULL,4,1,'2026-02-26 19:50:55'),(5,'Guías Prácticas','guias-practicas',NULL,'📋','#6366f1',NULL,NULL,5,1,'2026-02-26 19:50:55'),(6,'Historia de Purranque','historia-de-purranque',NULL,'📜','#a855f7',NULL,NULL,6,1,'2026-02-26 19:50:55'),(7,'Emprendimiento Local','emprendimiento-local',NULL,'💼','#14b8a6',NULL,NULL,7,1,'2026-02-26 19:50:55'),(8,'Comunidad Huilliche','comunidad-huilliche',NULL,'🌿','#22c55e',NULL,NULL,8,1,'2026-02-26 19:50:55'),(9,'Deportes y Recreación','deportes-y-recreacion',NULL,'⚽','#ef4444',NULL,NULL,9,1,'2026-02-26 19:50:55'),(10,'Opinión','opinion',NULL,'💭','#64748b',NULL,NULL,10,1,'2026-02-26 19:50:55');
/*!40000 ALTER TABLE `blog_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_comentarios`
--

LOCK TABLES `blog_comentarios` WRITE;
/*!40000 ALTER TABLE `blog_comentarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_post_series`
--

LOCK TABLES `blog_post_series` WRITE;
/*!40000 ALTER TABLE `blog_post_series` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_post_series` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` (`id`, `titulo`, `slug`, `extracto`, `contenido`, `imagen_portada`, `tipo`, `categoria_id`, `autor_id`, `fuente_nombre`, `fuente_url`, `estado`, `publicado_at`, `programado_at`, `destacado`, `permite_comentarios`, `tiempo_lectura`, `vistas`, `compartidos`, `meta_title`, `meta_description`, `eliminado`, `eliminado_at`, `created_at`, `updated_at`) VALUES (1,'Fiesta Costumbrista de Hueyusca 2026 Actualizado','fiesta-costumbrista-de-hueyusca-2026-actualizado','Gran evento cultural','Contenido actualizado de la noticia. Mas detalles sobre el evento.',NULL,'noticia',NULL,NULL,'Diario Austral',NULL,'archivado','2026-03-01 03:10:04',NULL,1,1,1,0,0,NULL,NULL,1,'2026-03-01 02:10:22','2026-03-01 06:09:16','2026-03-01 06:10:22'),(2,'Guía completa para visitar la costa de Purranque','guia-costa-purranque','Todo lo que necesitas saber para planificar tu viaje a Manquemapu, San Pedro y Mapu Lahual.','La costa de Purranque es uno de los destinos más auténticos del sur de Chile.\n\nCómo llegar: Desde Purranque, tomar la ruta U-910 hacia la costa. El camino es de ripio y toma entre 2 y 3 horas en vehículo 4x4. También existe un bus subsidiado.\n\nQué llevar: Ropa impermeable, zapatos de trekking, protector solar y efectivo (no hay cajeros en la costa).\n\nDónde dormir: Hospedajes familiares en Manquemapu con alimentación incluida. Camping en las caletas costeras.\n\nActividades: Trekking en Mapu Lahual, pesca artesanal, observación de fauna marina, fotografía de paisajes.',NULL,'guia',NULL,NULL,NULL,NULL,'publicado','2026-03-01 11:27:14',NULL,1,1,2,6,0,NULL,NULL,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:15:06'),(3,'5 platos típicos que debes probar en Purranque','platos-tipicos-purranque','Desde el asado al palo hasta la sopaipilla pasada, descubre los sabores del sur.','La gastronomía de Purranque refleja su identidad campesina y costera.\n\n1. Asado al palo: Cordero o chivo cocinado lentamente sobre brasas.\n2. Curanto: Preparación ancestral con mariscos, carnes y papas.\n3. Milcao: Preparación de papa rallada típica del sur.\n4. Empanadas de horno: Rellenas de pino con huevo.\n5. Sopaipillas pasadas: Con chancaca caliente, ideales para días de lluvia.',NULL,'articulo',NULL,NULL,NULL,NULL,'publicado','2026-03-01 11:27:14',NULL,1,1,1,2,0,NULL,NULL,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:14:57'),(4,'La historia de los alerces milenarios de Mapu Lahual','alerces-mapu-lahual','Conoce la fascinante historia de los árboles más antiguos de Sudamérica.','Los alerces (Fitzroya cupressoides) del Parque Mapu Lahual son algunos de los árboles más antiguos del planeta, con ejemplares que superan los 3.000 años de edad.\n\nEstos gigantes del bosque valdiviano son protegidos por las comunidades Huilliche que habitan la costa de Purranque desde tiempos ancestrales.\n\nEl alerce fue declarado Monumento Natural en 1976, prohibiéndose su tala. Hoy, los senderos de Mapu Lahual permiten caminar entre estos colosos en un entorno de bosque prístino.',NULL,'articulo',NULL,NULL,NULL,NULL,'publicado','2026-03-01 11:27:14',NULL,0,1,1,1,0,NULL,NULL,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:15:00');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_posts_relacionados`
--

LOCK TABLES `blog_posts_relacionados` WRITE;
/*!40000 ALTER TABLE `blog_posts_relacionados` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts_relacionados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `blog_series`
--

LOCK TABLES `blog_series` WRITE;
/*!40000 ALTER TABLE `blog_series` DISABLE KEYS */;
INSERT INTO `blog_series` (`id`, `titulo`, `slug`, `descripcion`, `imagen`, `activo`, `created_at`) VALUES (1,'Ruta Costera','ruta-costera','Explorando el litoral de Purranque: desde Bahía San Pedro hasta Manquemapu',NULL,1,'2026-02-26 19:50:55'),(2,'Historia de Purranque','historia-de-purranque','La historia de nuestra comuna desde su fundación en 1911',NULL,1,'2026-02-26 19:50:55'),(3,'Conociendo Emprendedores','conociendo-emprendedores','Perfiles de emprendedores locales que mueven Purranque',NULL,1,'2026-02-26 19:50:55'),(4,'Fiestas Costumbristas 2026','fiestas-costumbristas-2026','Cobertura de todas las fiestas costumbristas del verano',NULL,1,'2026-02-26 19:50:55'),(5,'Guía del Trekker: Mapu Lahual','guia-trekker-mapu-lahual','Todo lo que necesitas saber para recorrer la Red de Parques Mapu Lahual',NULL,1,'2026-02-26 19:50:55');
/*!40000 ALTER TABLE `blog_series` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cambios_pendientes`
--

LOCK TABLES `cambios_pendientes` WRITE;
/*!40000 ALTER TABLE `cambios_pendientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `cambios_pendientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` (`id`, `nombre`, `slug`, `descripcion`, `emoji`, `icono`, `imagen`, `color`, `meta_title`, `meta_description`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (1,'Playas y Costa','playas-y-costa','Bahía San Pedro, caletas, playas remotas y el litoral purranquino','🌊',NULL,NULL,'#0ea5e9',NULL,NULL,1,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(2,'Naturaleza y Senderos','naturaleza-y-senderos','Bosques de alerces, Mapu Lahual, trekking y aventura','🌲',NULL,NULL,'#22c55e',NULL,NULL,2,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(3,'Patrimonio e Historia','patrimonio-e-historia','Parroquia San Sebastián, historia de la colonización, patrimonio arquitectónico','🏛',NULL,NULL,'#a855f7',NULL,NULL,3,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(4,'Gastronomía','gastronomia','Empanadas, asados, productos lácteos, sidra y cerveza artesanal','🍽',NULL,NULL,'#f59e0b',NULL,NULL,4,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(5,'Cultura y Tradiciones','cultura-y-tradiciones','Cultura Huilliche, artesanía, costumbres y tradiciones del sur','🎭',NULL,NULL,'#ec4899',NULL,NULL,5,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(6,'Alojamiento','alojamiento','Hospedajes, cabañas, camping y dónde dormir en Purranque','🏨',NULL,NULL,'#6366f1',NULL,NULL,6,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(7,'Transporte','transporte','Cómo llegar, buses, rutas, traslados a la costa','🚌',NULL,NULL,'#64748b',NULL,NULL,7,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(8,'Eventos y Fiestas','eventos-y-fiestas','Fiestas costumbristas, festival aéreo, expo campesina y más','🎵',NULL,NULL,'#ef4444',NULL,NULL,8,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(9,'Fauna y Avistamiento','fauna-y-avistamiento','Pingüineras, loberas, aves, fauna nativa del bosque valdiviano','🐦',NULL,NULL,'#14b8a6',NULL,NULL,9,1,'2026-02-26 19:50:55','2026-02-26 19:50:55'),(10,'Servicios al Visitante','servicios-al-visitante','Información práctica, guías locales, servicios útiles para tu visita','🛍',NULL,NULL,'#f97316',NULL,NULL,10,1,'2026-02-26 19:50:55','2026-02-26 19:50:55');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `compartidos`
--

LOCK TABLES `compartidos` WRITE;
/*!40000 ALTER TABLE `compartidos` DISABLE KEYS */;
INSERT INTO `compartidos` (`id`, `tipo`, `registro_id`, `red_social`, `ip`, `created_at`) VALUES (6,'ficha',7,'whatsapp','127.0.0.1','2026-03-01 23:43:05'),(7,'ficha',10,'facebook','127.0.0.1','2026-03-02 00:09:20');
/*!40000 ALTER TABLE `compartidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` (`clave`, `valor`, `tipo`, `grupo`, `descripcion`, `updated_at`) VALUES ('apariencia_banner_mantenimiento','0','boolean','apariencia','Mostrar banner de mantenimiento en el sitio','2026-03-01 07:17:34'),('apariencia_color_footer','#0f172a','color','apariencia','Color de fondo del footer','2026-03-01 07:17:34'),('apariencia_color_header','#1e293b','color','apariencia','Color de fondo del header','2026-03-01 07:17:34'),('apariencia_color_primario','#1a5632','color','apariencia','Color primario del sitio','2026-03-01 07:23:09'),('apariencia_color_secundario','#0ea5e9','color','apariencia','Color secundario/acento','2026-03-01 07:17:34'),('apariencia_css_custom','','textarea','apariencia','CSS personalizado (se inyecta en todas las páginas)','2026-03-01 07:17:34'),('apariencia_favicon','','image','apariencia','Favicon (32x32 PNG)','2026-03-01 07:17:34'),('apariencia_fuente_cuerpo','Open Sans','text','apariencia','Fuente para texto (Google Fonts)','2026-03-01 07:17:34'),('apariencia_fuente_titulo','Montserrat','text','apariencia','Fuente para títulos (Google Fonts)','2026-03-01 07:17:34'),('apariencia_js_custom','','textarea','apariencia','JavaScript personalizado (se inyecta antes de </body>)','2026-03-01 07:17:34'),('apariencia_logo','','image','apariencia','Logo del sitio (PNG/SVG transparente)','2026-03-01 07:17:34'),('seo_google_analytics','','text','seo','ID de Google Analytics (G-XXXXXXXXXX)','2026-03-01 07:16:38'),('seo_google_verification','','text','seo','Meta tag de verificación Google Search Console','2026-03-01 07:13:28'),('seo_og_image','','image','seo','Imagen OG por defecto (1200x630)','2026-03-01 07:13:28'),('seo_robots_extra','','textarea','seo','Directivas extra para robots.txt','2026-03-01 07:13:28'),('seo_site_description','Guía turística de Purranque. Naturaleza, cultura y tradiciones en la Región de Los Lagos, Chile.','textarea','seo','Meta description por defecto','2026-03-01 07:13:28'),('seo_site_title','Visita Purranque ? Guía Turística de Purranque','text','seo','Título por defecto del sitio','2026-03-01 07:13:28'),('social_facebook','https://facebook.com/visitapurranque','text','redes','URL página de Facebook','2026-03-01 07:13:28'),('social_instagram','https://instagram.com/visitapurranque','text','redes','URL perfil de Instagram','2026-03-01 07:13:28'),('social_tiktok','','text','redes','URL perfil de TikTok','2026-03-01 07:13:28'),('social_twitter','','text','redes','URL perfil de X/Twitter','2026-03-01 07:13:28'),('social_youtube','','text','redes','URL canal de YouTube','2026-03-01 07:16:38');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `contacto_mensajes`
--

LOCK TABLES `contacto_mensajes` WRITE;
/*!40000 ALTER TABLE `contacto_mensajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacto_mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cron_log`
--

LOCK TABLES `cron_log` WRITE;
/*!40000 ALTER TABLE `cron_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cron_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `email_log`
--

LOCK TABLES `email_log` WRITE;
/*!40000 ALTER TABLE `email_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `estadisticas`
--

LOCK TABLES `estadisticas` WRITE;
/*!40000 ALTER TABLE `estadisticas` DISABLE KEYS */;
/*!40000 ALTER TABLE `estadisticas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` (`id`, `titulo`, `slug`, `descripcion`, `descripcion_corta`, `imagen`, `fecha_inicio`, `fecha_fin`, `lugar`, `direccion`, `latitud`, `longitud`, `precio`, `organizador`, `contacto`, `url_externa`, `categoria_id`, `recurrente`, `destacado`, `meta_title`, `meta_description`, `activo`, `eliminado`, `eliminado_at`, `created_at`, `updated_at`) VALUES (1,'Fiesta de San Sebastián 2026','fiesta-san-sebastian-2026','Tradicional fiesta patronal de Purranque con procesión, misa, feria gastronómica y actividades culturales.',NULL,NULL,'2026-01-20 09:00:00','2026-01-20 23:00:00','Iglesia San Sebastián, Purranque',NULL,-40.9110000,-73.1340000,NULL,NULL,NULL,NULL,NULL,0,1,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-01 17:20:12'),(2,'Feria Gastronómica de Verano','feria-gastronomica-verano-2026','Muestra gastronómica con productos locales: quesos, sidra, empanadas, asado al palo y dulces tradicionales.',NULL,NULL,'2026-02-15 11:00:00','2026-02-15 20:00:00','Plaza de Armas, Purranque',NULL,-40.9117000,-73.1347000,NULL,NULL,NULL,NULL,NULL,0,1,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-01 17:19:26'),(3,'Trekking Mapu Lahual — Temporada 2026','trekking-mapu-lahual-2026','Temporada de trekking guiado por senderos ancestrales en bosque de alerce milenario. Guías locales huilliche.',NULL,NULL,'2026-12-01 08:00:00','2027-03-31 18:00:00','Parque Mapu Lahual, Costa de Purranque',NULL,-40.6500000,-73.7200000,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-01 17:19:26'),(4,'Aniversario de Purranque 2026','aniversario-purranque-2026','Celebración del aniversario de la comuna con desfile, actividades culturales y show artístico.',NULL,NULL,'2026-10-15 10:00:00','2026-10-17 23:00:00','Centro de Purranque',NULL,-40.9117000,-73.1347000,NULL,NULL,NULL,NULL,NULL,0,1,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-01 17:19:26');
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
INSERT INTO `faq` (`id`, `pregunta`, `respuesta`, `categoria`, `votos_util`, `votos_no_util`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (1,'Como llegar a Purranque?','Purranque se encuentra a 948 km al sur de Santiago. Puedes llegar en avion hasta Osorno (aeropuerto Canal Bajo) o Puerto Montt (El Tepual) y luego tomar un bus o auto por la Ruta 5 Sur. En bus, las principales empresas (Pullman Bus, Cruz del Sur, Buses ETM) tienen recorridos diarios desde Santiago y ciudades intermedias.','general',0,0,1,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(2,'Cual es la mejor epoca para visitar Purranque?','La mejor epoca es entre diciembre y marzo (verano), cuando las temperaturas son mas agradables (15-25 C) y hay menos lluvias. Sin embargo, cada estacion tiene su encanto: en otono los bosques se tiñen de colores, y en invierno puedes disfrutar de termas y gastronomia local junto a la chimenea.','turismo',0,0,2,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(3,'Que atractivos turisticos tiene Purranque?','Purranque ofrece cascadas, lagos, bosques nativos de alerces milenarios en el Parque Mapu Lahual, playas en la costa como Bahia Mansa, termas naturales, y una rica tradicion gastronómica y cultural. Tambien hay fiestas costumbristas, ferias gastronomicas y trekking en temporada.','turismo',0,0,3,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(4,'Donde puedo alojarme en Purranque?','Existen diversas opciones de alojamiento: hostales, cabañas, camping y turismo rural. Puedes encontrar opciones en nuestro directorio de atractivos buscando por la categoria correspondiente.','servicios',0,0,4,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(5,'Se puede hacer trekking en la zona?','Si, la zona ofrece excelentes rutas de trekking, especialmente en el Parque Mapu Lahual con sus bosques de alerces milenarios. La temporada ideal es de octubre a abril. Se recomienda ir con guia local para las rutas mas exigentes.','turismo',0,0,5,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(6,'Como puedo agregar mi negocio al directorio?','Puedes contactarnos a traves del formulario de contacto o escribirnos directamente. Evaluaremos tu solicitud y te informaremos sobre los planes disponibles para aparecer en nuestro directorio de atractivos turisticos.','servicios',0,0,6,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(7,'El sitio es gratuito para los visitantes?','Si, el acceso a toda la informacion del sitio es completamente gratuito para los visitantes. Nuestro objetivo es promover el turismo en Purranque y facilitar la planificacion de tu viaje.','general',0,0,7,1,'2026-03-01 16:47:52','2026-03-01 16:47:52'),(8,'Puedo dejar una resena de un lugar que visite?','Si, en cada ficha de atractivo turistico encontraras un formulario para dejar tu resena. Tu opinion es muy valiosa para otros viajeros y para los emprendedores locales. Las resenas son moderadas antes de publicarse.','general',0,0,8,1,'2026-03-01 16:47:52','2026-03-01 16:47:52');
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fichas`
--

LOCK TABLES `fichas` WRITE;
/*!40000 ALTER TABLE `fichas` DISABLE KEYS */;
INSERT INTO `fichas` (`id`, `categoria_id`, `subcategoria_id`, `nombre`, `slug`, `descripcion`, `descripcion_corta`, `direccion`, `telefono`, `whatsapp`, `email`, `sitio_web`, `facebook`, `instagram`, `latitud`, `longitud`, `como_llegar`, `info_practica`, `horarios`, `precio_desde`, `precio_hasta`, `precio_texto`, `temporada`, `dificultad`, `duracion_estimada`, `que_llevar`, `imagen_portada`, `logo`, `verificado`, `destacado`, `imperdible`, `vistas`, `clics_telefono`, `clics_whatsapp`, `clics_mapa`, `clics_web`, `promedio_rating`, `total_resenas`, `meta_title`, `meta_description`, `plan_id`, `plan_expira`, `activo`, `eliminado`, `eliminado_at`, `created_at`, `updated_at`) VALUES (7,1,NULL,'Parque Mapu Lahual','parque-mapu-lahual','El Parque Mapu Lahual es una red de áreas protegidas administradas por comunidades Huilliche en la costa de Purranque. Cuenta con senderos a través de bosques de alerce milenario, arrayán y olivillo costero. Es un destino imperdible para el ecoturismo y turismo comunitario.','Red de parques indígenas con bosque de alerce milenario','Costa de Purranque, sector Manquemapu','+56912345678','56912345678',NULL,NULL,NULL,NULL,-40.6500000,-73.7200000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,9,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:04:32'),(8,1,NULL,'Caleta Manquemapu','caleta-manquemapu','Manquemapu es una peque├▒a caleta de pescadores ubicada en la costa sur de la comuna de Purranque. Accesible por camino de ripio (2-3 horas), ofrece paisajes v├¡rgenes, gastronom├¡a marina y la experiencia de una comunidad costera aut├®ntica.','Caleta de pescadores artesanales en la costa de Purranque','Manquemapu, Costa de Purranque','+56912345679','56912345679',NULL,NULL,NULL,NULL,-40.5800000,-73.7400000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-01 15:27:14'),(9,1,NULL,'Bahía San Pedro','bahia-san-pedro','La Bahía San Pedro es uno de los secretos mejor guardados de la costa de Purranque. Con playas de arena oscura rodeadas de bosque nativo, ofrece un paisaje único para quienes buscan naturaleza sin intervención.','Bahía prístina con playas y bosque nativo','Bahía San Pedro, Costa de Purranque',NULL,NULL,NULL,NULL,NULL,NULL,-40.6200000,-73.7600000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,2,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:14:11'),(10,3,NULL,'Plaza de Armas de Purranque','plaza-de-armas-purranque','La Plaza de Armas de Purranque es el corazón de la ciudad. Rodeada de comercio, servicios y la Iglesia Parroquial, es el punto de partida ideal para conocer la zona urbana.','Centro cívico y punto de encuentro de la ciudad','Centro de Purranque',NULL,NULL,NULL,NULL,NULL,NULL,-40.9117000,-73.1347000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,4,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:08:37'),(11,3,NULL,'Iglesia Parroquial San Sebastián','iglesia-san-sebastian','La Iglesia de San Sebastián es el principal templo de Purranque y sede de la tradicional Fiesta de San Sebastián cada 20 de enero. Su arquitectura de madera es representativa de las iglesias del sur de Chile.','Templo histórico y sede de la fiesta patronal','Calle Señoret, Purranque',NULL,NULL,NULL,NULL,NULL,NULL,-40.9110000,-73.1340000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,2,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:13:58'),(12,5,NULL,'Feria Costumbrista de Purranque','feria-costumbrista-purranque','La Feria Costumbrista reúne lo mejor de la tradición rural de Purranque: gastronomía típica, artesanía, música folclórica y juegos campesinos.','Celebración anual de tradiciones campesinas','Sector rural, Purranque',NULL,NULL,NULL,NULL,NULL,NULL,-40.9200000,-73.2000000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,2,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:04:49'),(13,2,NULL,'Cascadas del Río Chanleufú','cascadas-rio-chanle','Las cascadas del río Chanleufú ofrecen un espectáculo natural rodeado de bosque nativo. Accesible por sendero de dificultad media, ideal para fotografía y contemplación.','Saltos de agua en entorno de bosque nativo','Sector Chanleufú, Purranque',NULL,NULL,NULL,NULL,NULL,NULL,-40.8500000,-73.3000000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,0,2,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:05:44'),(14,6,NULL,'Hostal Don Luis','hostal-don-luis','Hostal Don Luis ofrece habitaciones cómodas en el centro de Purranque. Ideal para viajeros que buscan una base para explorar la comuna. Incluye desayuno y estacionamiento.','Hospedaje céntrico con atención familiar','Calle Eleuterio Ramírez 520, Purranque','+56965432100','56965432100',NULL,NULL,NULL,NULL,-40.9120000,-73.1350000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,3,0,0,0,0,0.0,0,NULL,NULL,NULL,NULL,1,0,NULL,'2026-03-01 15:27:14','2026-03-02 00:15:34');
/*!40000 ALTER TABLE `fichas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fichas_atributos`
--

LOCK TABLES `fichas_atributos` WRITE;
/*!40000 ALTER TABLE `fichas_atributos` DISABLE KEYS */;
/*!40000 ALTER TABLE `fichas_atributos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `imagenes`
--

LOCK TABLES `imagenes` WRITE;
/*!40000 ALTER TABLE `imagenes` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `login_intentos`
--

LOCK TABLES `login_intentos` WRITE;
/*!40000 ALTER TABLE `login_intentos` DISABLE KEYS */;
INSERT INTO `login_intentos` (`id`, `email`, `ip`, `user_agent`, `exitoso`, `created_at`) VALUES (1,'contacto@purranque.info','127.0.0.1','curl/8.18.0',0,'2026-02-27 00:51:44'),(2,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-02-27 00:52:32'),(3,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 05:42:03'),(4,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 05:42:11'),(5,'contacto@purranque.info','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0',1,'2026-03-01 05:46:41'),(6,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 06:07:51'),(7,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 06:08:39'),(8,'contacto@purranque.info','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0',1,'2026-03-01 06:36:49'),(9,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 06:52:36'),(10,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 07:09:22'),(11,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 07:22:24'),(12,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 07:28:52'),(13,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 07:33:59'),(14,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 07:39:25'),(15,'gsepulv@gmail.com','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0',0,'2026-03-01 23:13:02'),(16,'contacto@purranque.info','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0',1,'2026-03-01 23:14:05'),(17,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 23:16:45'),(18,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 23:22:42'),(19,'contacto@purranque.info','127.0.0.1','curl/8.18.0',1,'2026-03-01 23:23:03');
/*!40000 ALTER TABLE `login_intentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `medios`
--

LOCK TABLES `medios` WRITE;
/*!40000 ALTER TABLE `medios` DISABLE KEYS */;
/*!40000 ALTER TABLE `medios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` (`id`, `menu`, `parent_id`, `titulo`, `url`, `tipo`, `referencia_id`, `target`, `icono`, `orden`, `activo`, `created_at`) VALUES (1,'principal',NULL,'Inicio','/','enlace',NULL,'_self','🏠',0,1,'2026-03-01 18:52:57'),(2,'principal',NULL,'Categorías','/categorias','enlace',NULL,'_self','📂',1,1,'2026-03-01 18:52:57'),(3,'principal',NULL,'Eventos','/eventos','enlace',NULL,'_self','📅',2,1,'2026-03-01 18:52:57'),(4,'principal',NULL,'Blog','/blog','enlace',NULL,'_self','📝',3,1,'2026-03-01 18:52:57'),(5,'principal',NULL,'Mapa','/mapa','enlace',NULL,'_self','🗺️',4,1,'2026-03-01 18:52:57'),(6,'principal',NULL,'Contacto','/contacto','enlace',NULL,'_self','✉️',5,1,'2026-03-01 18:52:57'),(7,'footer_legal',NULL,'Contacto','/contacto','enlace',NULL,'_self',NULL,0,1,'2026-03-01 18:52:57'),(8,'footer_legal',NULL,'Preguntas frecuentes','/faq','enlace',NULL,'_self',NULL,1,1,'2026-03-01 18:52:57'),(9,'footer_legal',NULL,'Términos de uso','/terminos','enlace',NULL,'_self',NULL,2,1,'2026-03-01 18:52:57'),(10,'footer_legal',NULL,'Privacidad','/privacidad','enlace',NULL,'_self',NULL,3,1,'2026-03-01 18:52:57');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `paginas`
--

LOCK TABLES `paginas` WRITE;
/*!40000 ALTER TABLE `paginas` DISABLE KEYS */;
/*!40000 ALTER TABLE `paginas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `paginas_versiones`
--

LOCK TABLES `paginas_versiones` WRITE;
/*!40000 ALTER TABLE `paginas_versiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `paginas_versiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `planes`
--

LOCK TABLES `planes` WRITE;
/*!40000 ALTER TABLE `planes` DISABLE KEYS */;
INSERT INTO `planes` (`id`, `nombre`, `slug`, `descripcion`, `precio_mensual`, `precio_anual`, `caracteristicas`, `destacado_home`, `max_imagenes`, `tiene_badge`, `orden`, `activo`, `created_at`) VALUES (1,'Básico','basico','Ficha con información básica',0,0,NULL,0,3,0,1,1,'2026-02-26 05:15:53'),(2,'Destacado','destacado','Ficha destacada en su categoria',10000,100000,NULL,0,10,1,2,1,'2026-02-26 05:15:53'),(3,'Premium','premium','Máxima visibilidad + badge premium',25000,250000,NULL,0,20,1,3,1,'2026-02-26 05:15:53');
/*!40000 ALTER TABLE `planes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `popups`
--

LOCK TABLES `popups` WRITE;
/*!40000 ALTER TABLE `popups` DISABLE KEYS */;
/*!40000 ALTER TABLE `popups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_config`
--

LOCK TABLES `proyecto_config` WRITE;
/*!40000 ALTER TABLE `proyecto_config` DISABLE KEYS */;
INSERT INTO `proyecto_config` (`clave`, `valor`, `descripcion`) VALUES ('email_admin','contacto@purranque.info','Correo para reportes'),('hora_reporte_am','07:00','Hora reporte matutino'),('hora_reporte_pm','20:00','Hora reporte nocturno'),('horas_semana_meta','10','Meta de horas por semana'),('proyecto_beta','2026-08-03','Fecha meta lanzamiento BETA'),('proyecto_inicio','2026-03-02','Fecha inicio del proyecto'),('reporte_activo','1','Reportes por correo activos'),('timezone','America/Santiago','Zona horaria');
/*!40000 ALTER TABLE `proyecto_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_fases`
--

LOCK TABLES `proyecto_fases` WRITE;
/*!40000 ALTER TABLE `proyecto_fases` DISABLE KEYS */;
INSERT INTO `proyecto_fases` (`id`, `nombre`, `slug`, `color`, `icono`, `orden`, `semana_inicio`, `semana_fin`, `descripcion`, `created_at`) VALUES (1,'Fundación','fundacion','#a855f7','⚙️',1,1,2,'Setup: Git, BD, estructura, router, layout','2026-02-26 05:15:53'),(2,'Sitio Público','sitio-publico','#3b82f6','🌐',2,3,11,'Home, fichas, mapa, eventos, blog, SEO, PWA','2026-02-26 05:15:53'),(3,'Panel Admin','panel-admin','#22c55e','🔐',3,12,20,'35 módulos del panel de administración','2026-02-26 05:15:53'),(4,'Contenido + BETA','contenido-beta','#f59e0b','🚀',4,20,22,'Contenido real, testing y lanzamiento','2026-02-26 05:15:53');
/*!40000 ALTER TABLE `proyecto_fases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_hitos`
--

LOCK TABLES `proyecto_hitos` WRITE;
/*!40000 ALTER TABLE `proyecto_hitos` DISABLE KEYS */;
INSERT INTO `proyecto_hitos` (`id`, `semana`, `titulo`, `descripcion`, `fecha_objetivo`, `completado`, `fecha_completado`, `created_at`) VALUES (1,2,'Fundación completa','BD, router, layout y estructura funcionando','2026-03-16',0,NULL,'2026-02-26 05:15:53'),(2,4,'Home en producción','Primera versión visible en visitapurranque.cl','2026-03-30',0,NULL,'2026-02-26 05:15:53'),(3,9,'Sitio público navegable','Home, fichas, mapa, eventos y blog','2026-05-04',0,NULL,'2026-02-26 05:15:53'),(4,11,'Sitio público completo','SEO, PWA, contacto, FAQ, legales','2026-05-18',0,NULL,'2026-02-26 05:15:53'),(5,13,'Admin operativo','Login, dashboard y primeros CRUDs','2026-06-01',0,NULL,'2026-02-26 05:15:53'),(6,17,'20 módulos core','Módulos core del admin terminados','2026-06-29',0,NULL,'2026-02-26 05:15:53'),(7,20,'35 módulos completos','Panel admin 100% funcional','2026-07-20',0,NULL,'2026-02-26 05:15:53'),(8,22,'🚀 LANZAMIENTO BETA','Contenido cargado, testing, sitio en vivo','2026-08-03',0,NULL,'2026-02-26 05:15:53');
/*!40000 ALTER TABLE `proyecto_hitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_reportes_log`
--

LOCK TABLES `proyecto_reportes_log` WRITE;
/*!40000 ALTER TABLE `proyecto_reportes_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `proyecto_reportes_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_sesiones`
--

LOCK TABLES `proyecto_sesiones` WRITE;
/*!40000 ALTER TABLE `proyecto_sesiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `proyecto_sesiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `proyecto_tareas`
--

LOCK TABLES `proyecto_tareas` WRITE;
/*!40000 ALTER TABLE `proyecto_tareas` DISABLE KEYS */;
INSERT INTO `proyecto_tareas` (`id`, `fase_id`, `semana`, `titulo`, `descripcion`, `prompt_ref`, `horas_estimadas`, `horas_reales`, `estado`, `prioridad`, `fecha_inicio`, `fecha_completada`, `notas`, `orden`, `created_at`, `updated_at`) VALUES (1,1,1,'Git init + GitHub + .gitignore + .env.example',NULL,'F0',1.0,NULL,'completada','alta',NULL,'2026-02-26 17:31:09',NULL,1,'2026-02-26 05:15:53','2026-02-26 20:31:09'),(2,1,1,'CLAUDE.md + README.md + Laragon symlink',NULL,'F0',0.5,NULL,'completada','alta',NULL,'2026-02-26 17:31:10',NULL,2,'2026-02-26 05:15:53','2026-02-26 20:31:10'),(3,1,1,'Schema SQL: crear 48 tablas con índices',NULL,'F1',3.0,NULL,'completada','alta',NULL,'2026-02-26 17:31:11',NULL,3,'2026-02-26 05:15:53','2026-02-26 20:31:11'),(4,1,1,'Seeders SQL: datos iniciales completos',NULL,'F1',2.0,NULL,'completada','alta',NULL,'2026-02-26 17:31:12',NULL,4,'2026-02-26 05:15:53','2026-02-26 20:31:12'),(5,1,1,'Importar BD en Laragon, verificar tablas',NULL,'F1',0.5,NULL,'completada','alta',NULL,'2026-02-26 17:31:13',NULL,5,'2026-02-26 05:15:53','2026-02-26 20:31:13'),(6,1,1,'Dashboard /proyecto + cron reportes',NULL,'F0-TRACK',3.0,NULL,'completada','media',NULL,'2026-02-26 17:31:14',NULL,6,'2026-02-26 05:15:53','2026-02-26 20:31:14'),(7,1,2,'Estructura de directorios MVC completa',NULL,'F2',2.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,1,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(8,1,2,'Router: index.php + routes.php + .htaccess',NULL,'F2',2.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,2,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(9,1,2,'Conexión PDO + helpers (e, texto, slugify, csrf)',NULL,'F2',2.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,3,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(10,1,2,'Layout público: header + footer + CSS base',NULL,'F2',3.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,4,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(11,1,2,'.htaccess producción + headers seguridad',NULL,'F2',1.0,NULL,'completada','media',NULL,'2026-03-01 01:35:43','Headers seguridad parcial, falta CSP y HSTS en producción',5,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(12,2,3,'Home: hero + countdown + buscador predictivo',NULL,'P1',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(13,2,3,'Home: grid categorías + sección destacados',NULL,'P1',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(14,2,3,'Responsive completo + mobile hamburger',NULL,'P1',2.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,3,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(15,2,3,'Deploy primera versión a VPS',NULL,'P1',1.0,NULL,'pendiente','alta',NULL,NULL,NULL,4,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(16,2,4,'Grid categorías /categorias',NULL,'P1',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(17,2,4,'Listado por categoría /categoria/{slug}',NULL,'P1',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(18,2,4,'Paginación + filtros + ordenamiento',NULL,'P1',2.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(19,2,4,'Breadcrumbs + componente card reutilizable',NULL,'P1',1.0,NULL,'pendiente','media',NULL,NULL,NULL,4,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(20,2,5,'Ficha individual /atractivo/{slug}',NULL,'P2',4.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(21,2,5,'Galería, badges, descripción, contacto, compartir',NULL,'P2',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(22,2,5,'Schema.org TouristAttraction + LocalBusiness',NULL,'P2',1.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(23,2,6,'Mapa interactivo Google Maps /mapa',NULL,'P2',4.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(24,2,6,'Filtros categoría + listado lateral + popup',NULL,'P2',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(25,2,6,'API endpoint /api/fichas-mapa.php',NULL,'P2',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(26,2,7,'Sistema reseñas público + Turnstile',NULL,'P2',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(27,2,7,'Contador vistas/clics por ficha',NULL,'P2',2.0,NULL,'pendiente','media',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(28,2,7,'Botones compartir + atractivos relacionados',NULL,'P2',2.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(29,2,8,'Eventos: calendario + listado + ficha',NULL,'P3',4.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(30,2,8,'Countdown dinámico próximo evento',NULL,'P3',1.0,NULL,'pendiente','media',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(31,2,8,'Schema.org Event + Google Calendar',NULL,'P3',1.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(32,2,9,'Blog: portada + 10 categorías editoriales',NULL,'P3',4.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(33,2,9,'Blog: post individual + comentarios',NULL,'P3',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(34,2,9,'Blog: autor, series, archivo, RSS',NULL,'P3',3.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(35,2,10,'Contacto + Turnstile + auto-respuesta',NULL,'P3',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(36,2,10,'Buscador global /buscar?q=',NULL,'P3',2.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(37,2,10,'FAQ público + Schema.org FAQPage',NULL,'P3',2.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(38,2,10,'Páginas legales dinámicas',NULL,'P3',2.0,NULL,'pendiente','media',NULL,NULL,NULL,4,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(39,2,11,'SEO: meta tags, Open Graph, sitemap.xml',NULL,'P3',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(40,2,11,'PWA: manifest.json + Service Worker + offline',NULL,'P3',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(41,2,11,'Página offline info emergencia',NULL,'P3',1.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(42,2,11,'Cookie banner (Ley 21.719)',NULL,'P3',1.0,NULL,'pendiente','media',NULL,NULL,NULL,4,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(43,3,12,'Admin: login bcrypt + rate limiting + session',NULL,'A1',3.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,1,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(44,3,12,'Admin: layout sidebar 35 módulos + header',NULL,'A1',3.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,2,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(45,3,12,'Admin: dashboard KPIs + Chart.js + timeline',NULL,'A1',3.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,3,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(46,3,13,'Admin: CRUD Fichas/Atractivos completo',NULL,'A2',5.0,NULL,'completada','alta',NULL,'2026-03-01 01:35:43',NULL,1,'2026-02-26 05:15:53','2026-03-01 05:35:43'),(47,3,13,'Admin: CRUD Categorías + Subcategorías',NULL,'A2',3.0,1.0,'completada','alta',NULL,'2026-03-01 01:40:33','Modelo Categoria con subcategorías, AdminCategoriaController CRUD completo, vistas index+form, rutas, sidebar badge',2,'2026-02-26 05:15:53','2026-03-01 05:40:33'),(48,3,14,'Admin: CRUD Eventos + calendario visual',NULL,'A2',3.0,NULL,'completada','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-03-01 06:13:20'),(49,3,14,'Admin: Blog editorial completo',NULL,'A2',5.0,NULL,'completada','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-03-01 06:10:51'),(50,3,15,'Admin: Reseñas moderación',NULL,'A3',2.0,NULL,'completada','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-03-01 06:21:23'),(51,3,15,'Admin: Banners CRUD + A/B + stats CTR',NULL,'A3',3.0,NULL,'completada','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-03-01 06:28:48'),(52,3,15,'Admin: Planes + Suscripciones',NULL,'A3',2.0,NULL,'completada','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-03-01 06:34:52'),(53,3,16,'Admin: Contacto bandeja + Enviar Correo',NULL,'A3',3.0,NULL,'completada','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-03-01 06:44:19'),(54,3,16,'Admin: Reportes + gráficos + CSV',NULL,'A4',3.0,NULL,'completada','alta',NULL,'2026-03-01 02:55:34',NULL,2,'2026-02-26 05:15:53','2026-03-01 06:55:34'),(55,3,16,'Admin: Cambios Pendientes + Renovaciones',NULL,'A3',2.0,NULL,'completada','media',NULL,'2026-03-01 03:12:18',NULL,3,'2026-02-26 05:15:53','2026-03-01 07:12:18'),(56,3,17,'Admin: SEO + Redes Sociales + Compartidos',NULL,'A4',3.0,NULL,'completada','alta',NULL,'2026-03-01 03:17:06',NULL,1,'2026-02-26 05:15:53','2026-03-01 07:17:06'),(57,3,17,'Admin: Apariencia + CSS/JS personalizado',NULL,'A5',2.0,NULL,'completada','media',NULL,'2026-03-01 03:23:49',NULL,2,'2026-02-26 05:15:53','2026-03-01 07:23:49'),(58,3,17,'Integrar audit_log en todos los módulos',NULL,'A4',2.0,NULL,'completada','alta',NULL,'2026-03-01 03:30:27',NULL,3,'2026-02-26 05:15:53','2026-03-01 07:30:27'),(59,3,18,'Admin: Textos Editables + helper texto()',NULL,'A5',4.0,NULL,'completada','alta',NULL,'2026-03-01 03:36:24',NULL,1,'2026-02-26 05:15:53','2026-03-01 07:36:24'),(60,3,18,'Admin: Páginas Estáticas + versionamiento',NULL,'A5',3.0,NULL,'completada','alta',NULL,'2026-03-01 03:41:53',NULL,2,'2026-02-26 05:15:53','2026-03-01 07:41:53'),(61,3,18,'Admin: Editor Menú drag & drop',NULL,'A5',2.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(62,3,19,'Admin: Galería Medios + selector modal',NULL,'A6',4.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(63,3,19,'Admin: Roles y Permisos granulares',NULL,'A6',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(64,3,19,'Admin: Usuarios CRUD + roles',NULL,'A6',2.0,NULL,'pendiente','media',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(65,3,20,'Admin: Backups Google Drive API',NULL,'A7',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(66,3,20,'Admin: Papelera + Redirecciones',NULL,'A7',2.0,NULL,'pendiente','media',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(67,3,20,'Admin: Logs + Salud + Email Templates',NULL,'A7',3.0,NULL,'pendiente','alta',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(68,4,21,'Admin: Tags + FAQ + Popups + Notificaciones',NULL,'A8',4.0,NULL,'pendiente','media',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(69,4,21,'Admin: Búsqueda Ctrl+K + Modo oscuro',NULL,'A8',2.0,NULL,'pendiente','media',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(70,4,21,'Cargar 20 fichas de atractivos reales',NULL,NULL,4.0,NULL,'pendiente','alta',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(71,4,22,'Cargar +10 servicios turísticos',NULL,NULL,3.0,NULL,'pendiente','alta',NULL,NULL,NULL,1,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(72,4,22,'Cargar eventos y fiestas del calendario',NULL,NULL,2.0,NULL,'pendiente','alta',NULL,NULL,NULL,2,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(73,4,22,'Publicar primeros 5 artículos del blog',NULL,NULL,3.0,NULL,'pendiente','alta',NULL,NULL,NULL,3,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(74,4,22,'Testing completo + correcciones',NULL,NULL,3.0,NULL,'pendiente','alta',NULL,NULL,NULL,4,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(75,4,22,'Deploy final + SSL + cron backups',NULL,NULL,2.0,NULL,'pendiente','alta',NULL,NULL,NULL,5,'2026-02-26 05:15:53','2026-02-26 05:15:53'),(76,4,22,'🚀 LANZAMIENTO BETA',NULL,NULL,0.5,NULL,'pendiente','alta',NULL,NULL,NULL,6,'2026-02-26 05:15:53','2026-02-26 05:15:53');
/*!40000 ALTER TABLE `proyecto_tareas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `redirecciones`
--

LOCK TABLES `redirecciones` WRITE;
/*!40000 ALTER TABLE `redirecciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `redirecciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `resenas`
--

LOCK TABLES `resenas` WRITE;
/*!40000 ALTER TABLE `resenas` DISABLE KEYS */;
INSERT INTO `resenas` (`id`, `ficha_id`, `nombre`, `email`, `ciudad_origen`, `rating`, `tipo_experiencia`, `comentario`, `fecha_visita`, `estado`, `ip`, `respuesta_admin`, `respuesta_fecha`, `created_at`) VALUES (14,7,'María González','maria@example.com',NULL,5,'trekking','Increíble experiencia. Los bosques de alerce son impresionantes. Los guías locales son muy conocedores.',NULL,'aprobada',NULL,NULL,NULL,'2026-03-01 15:31:46'),(15,7,'Carlos Muñoz','carlos@example.com',NULL,4,'trekking','Hermoso lugar pero el camino es muy largo y difícil. Llevar vehículo 4x4 obligatorio.',NULL,'aprobada',NULL,NULL,NULL,'2026-03-01 15:31:46'),(16,8,'Ana Pérez','ana@example.com',NULL,5,'gastronomia','La caleta más auténtica que he visitado. Los mariscos frescos son espectaculares.',NULL,'aprobada',NULL,NULL,NULL,'2026-03-01 15:31:46'),(17,11,'Pedro Soto','pedro@example.com',NULL,4,'visita_cultural','Hermosa iglesia de madera. Vale la pena visitarla durante la Fiesta de San Sebastián.',NULL,'aprobada',NULL,NULL,NULL,'2026-03-01 15:31:46'),(18,14,'Laura Díaz','laura@example.com',NULL,3,'alojamiento','Hospedaje sencillo pero limpio y con buena atención. El desayuno es abundante.',NULL,'aprobada',NULL,NULL,NULL,'2026-03-01 15:31:46');
/*!40000 ALTER TABLE `resenas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `rol_permisos`
--

LOCK TABLES `rol_permisos` WRITE;
/*!40000 ALTER TABLE `rol_permisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `rol_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `nombre`, `slug`, `descripcion`, `created_at`) VALUES (1,'Administrador','admin','Acceso total al sistema','2026-02-26 19:50:55'),(2,'Editor','editor','Puede crear y editar contenido','2026-02-26 19:50:55'),(3,'Colaborador','colaborador','Puede crear contenido, necesita aprobación','2026-02-26 19:50:55'),(4,'Visitante','visitante','Solo puede ver contenido público','2026-02-26 19:50:55');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `subcategorias`
--

LOCK TABLES `subcategorias` WRITE;
/*!40000 ALTER TABLE `subcategorias` DISABLE KEYS */;
INSERT INTO `subcategorias` (`id`, `categoria_id`, `nombre`, `slug`, `descripcion`, `orden`, `activo`, `created_at`) VALUES (1,1,'Playas aptas para baño','playas-aptas-para-bano',NULL,1,1,'2026-03-01 05:42:59'),(2,2,'Senderos de trekking','senderos-de-trekking',NULL,1,1,'2026-03-01 05:44:56');
/*!40000 ALTER TABLE `subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `suscripciones`
--

LOCK TABLES `suscripciones` WRITE;
/*!40000 ALTER TABLE `suscripciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `suscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `taggables`
--

LOCK TABLES `taggables` WRITE;
/*!40000 ALTER TABLE `taggables` DISABLE KEYS */;
/*!40000 ALTER TABLE `taggables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `textos_editables`
--

LOCK TABLES `textos_editables` WRITE;
/*!40000 ALTER TABLE `textos_editables` DISABLE KEYS */;
INSERT INTO `textos_editables` (`id`, `clave`, `valor`, `valor_default`, `seccion`, `tipo`, `descripcion`, `updated_at`) VALUES (1,'hero_titulo','Visita Purranque','Visita Purranque','home','text','Titulo principal del hero','2026-03-01 07:34:55'),(2,'hero_subtitulo','Guía del Visitante','Guía del Visitante','home','text','Subtitulo del hero','2026-02-26 23:32:05'),(3,'hero_descripcion','Playas remotas, bosques de alerces milenarios, gastronomía sureña y la calidez de su gente. Descubre todo lo que Purranque tiene para ofrecer.','Playas remotas, bosques de alerces milenarios, gastronomía sureña y la calidez de su gente. Descubre todo lo que Purranque tiene para ofrecer.','home','textarea','Descripcion del hero','2026-02-26 23:32:05'),(4,'buscar_placeholder','Busca playas, senderos, restaurantes...','Busca playas, senderos, restaurantes...','home','text','Placeholder del buscador','2026-02-26 23:32:05'),(5,'hero_cta','Explorar atractivos','Explorar atractivos','home','text','Texto del boton CTA del hero','2026-02-26 23:32:05'),(6,'footer_descripcion','Guía turística de Purranque. Descubre la naturaleza, cultura y tradiciones de la Región de Los Lagos.','Guía turística de Purranque. Descubre la naturaleza, cultura y tradiciones de la Región de Los Lagos.','footer','textarea','Descripcion en el footer','2026-02-26 23:32:05'),(7,'footer_email','contacto@purranque.info','contacto@purranque.info','footer','text','Email de contacto del footer','2026-02-26 23:32:05'),(8,'footer_ciudad','Purranque, Región de Los Lagos','Purranque, Región de Los Lagos','footer','text','Ciudad en el footer','2026-02-26 23:32:05'),(9,'beta_mensaje','Beta','Beta','general','text','Mensaje del badge beta','2026-02-26 23:32:05');
/*!40000 ALTER TABLE `textos_editables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol_id`, `avatar`, `telefono`, `bio`, `activo`, `email_verificado`, `ultimo_login`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Gustavo','contacto@purranque.info','$2y$10$ckGiTgZ81kAauV2rbhBUDO0FHAL9y60d9geVTbSQdBkxOS6Zq68vK',1,NULL,NULL,NULL,1,1,'2026-03-01 19:23:03',NULL,'2026-02-26 19:50:55','2026-03-01 23:23:03');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-01 20:43:46
