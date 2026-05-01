-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3309
-- Tiempo de generación: 25-05-2025 a las 01:59:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bcs_floragames`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha_planta`
--

CREATE TABLE `ficha_planta` (
  `id` int(11) NOT NULL,
  `nombre_comun` varchar(100) NOT NULL,
  `nombre_cientifico` varchar(150) DEFAULT NULL,
  `distribucion` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `dibujo_animado` varchar(255) DEFAULT NULL,
  `curiosidad` text DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `habitat` varchar(300) DEFAULT NULL,
  `caracteristicas` varchar(500) DEFAULT NULL,
  `usos` varchar(200) NOT NULL,
  `situación` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ficha_planta`
--

INSERT INTO `ficha_planta` (`id`, `nombre_comun`, `nombre_cientifico`, `distribucion`, `foto`, `dibujo_animado`, `curiosidad`, `audio`, `habitat`, `caracteristicas`, `usos`, `situación`) VALUES
(1, 'Cacachila, Tullidora', 'Karwinskia humboldtiana', 'Los Cabos', 'cacachila.png', 'cacachila1.png', 'La floración de una cacachila se presenta al inicio de la temporada de lluvias.', '', 'A lo largo de arroyos y declives rocosos.', 'Árbol pequeño que alcanza entre 2 y 5 metros de altura. Sus flores son pequeñas, de color verdoso, y se agrupan en racimos.', 'Medicinal controlado, Tóxico ', 'Nativa'),
(2, 'Cafecillo, Café de gallina', 'Senna villosa', 'Sierra de la Giganta', 'cafecillo.png', 'cafecillo1.png', 'Las flores del cafecillo son de color amarillo con 5 pétalos.', '', 'Común como maleza en sitios arenosos y rocosos.', 'Hierbas anuales de hasta 1.5 metros de altura. Es una especie adaptada a suelos pobres y secos.', 'Medicinal, Combustible, Maderable.', 'Endémica'),
(3, 'Cardón barbón, Etcho, Hecho', 'Pachycereus pecten-aboriginum', 'Sierra de la Laguna', 'cardonbarbon.png', 'cardonbarbon1.png', 'Durante la primavera el cardón barbón produce flores blancas con tonalidades rojas.', '', 'Lugares rocosos.', 'Cacto columnar que puede alcanzar hasta los 15 metros de altura.', 'Ornamental, Alimenticio, Medicinal y Artesanal', 'Endémica'),
(4, 'Chicura', 'Ambrosía ambrosioides', 'Los Cabos', 'chicura.png', 'chicura1.png', 'La floración de chicura se presenta de febrero a mayo.', '', 'Fondo de cañones y cauces de arroyo.', 'Arbusto de tallos semileñoso, con grandes hojas pecioladas, de 6 a 18 cm de largo.', 'Medicinal, Fijadora de suelos', 'Nativa'),
(5, 'Cholla pelona', 'Cylindropuntia cholla', 'Baja California Sur', 'chollapelona.png', 'chollapelona1.png', 'Las flores de la cholla pelona son de color púrpura o rosa y se presentan de abril a mayo.', '', 'Planicies áridas', 'Cactus de hasta 5 metros de largo. Los frutos son espinosos y carnosos; verdes que se tornan marrones al madurar.', 'Medicinal, Ornamental, Alimenticio, Artesanal, Forraje, Combustible. ', 'Endémica'),
(6, 'Ciruelo cimarrón, Ciruelo silvestre, Chunique', 'Cyrtocarpa edulis', 'Sur de Baja California Sur', 'ciruelocimarron.png', 'ciruelocimarron1.png', 'La fruta chunique fue un recurso alimenticio adicional para los pueblos originarios.', '', 'Arroyos', 'Árbol de tronco robusto de hasta 10 metros de altura. Sus flores, pequeñas y de color blanco‑amarillento, se agrupan en racimos.', 'Medicinal, Ornamental, Alimenticio', 'Endémica'),
(7, 'Guatamote', 'Baccharis salicifolia', 'Nativa de los desiertos', 'guatamote.png', 'guatamote.png', 'Las ramas de guatamote se han usado a veces para elaborar escobas.', '', 'Arroyos', 'Arbusto de hasta 3 metros de altura; florece de marzo a junio. Muy tolerante a la sequía, aunque suele encontrarse cerca de cursos de agua.', 'Medicinal, Combustible, Maderable', 'Nativa'),
(8, 'Incienso', 'Encelia farinosa A.', 'Península de Baja California Sur', 'incienso.png', 'incienso1.png', 'Los indios utilizaban los tallos del incienso como barniz para sus flechas y objetos.', '', 'Llanos desérticos', 'Arbusto que logra alcanzar hasta metro y medio de altura. Se caracteriza por su olor fuerte y en ocasiones desagradable.', 'Medicinal, Resina, Ornamental', 'Nativa'),
(9, 'Istafiate, Estafiate', 'Ambrosia confertiflora DC.', 'Baja California Sur', 'istafiate.png', 'istafiate1.png', 'La infusión de las hojas de Istafiate ayuda a aliviar malestares gripales.', '', 'Campos agrícolas abandonados', 'La floración ocurre de abril a junio. Según la especie presentan pocas o muchas espinas de diferentes tamaños.', 'Medicinal, Repelente', 'Nativa'),
(10, 'Lomboy blanco', 'Jatropha cinerea (Ortega) Müll.Arg.', 'Nor-occidente de México', 'lomboyblanco.png', 'lomboyblanco1.png', 'Se cree que el látex del lomboy blanco es útil para curar heridas y quemaduras.', '', 'Laderas de montañas', 'Arbusto de 1 a 5 metros de alto. Es una de las plantas más características de Baja California Sur.', 'Medicinal ', 'Nativa'),
(12, 'Mesquite', 'Neltuma articulata (S.Watson) Britton & Rose', 'Baja California Sur', 'mesquite.png', 'mesquite1.png', 'El mezquite florece en primavera y verano, produciendo racimos de pequeñas flores amarillas.', '', 'Arroyos', 'Árbol espinoso que puede alcanzar hasta 10 metros de altura, con un tronco grueso y retorcido.', 'Forraje, Combustible, Maderable, Tinte natural, Medicinal', 'Nativa'),
(13, 'Palma real, Palma Colorada', 'Washingtonia robusta H. Wendl.', 'Sur de Baja California Sur', 'palmareal.png', 'palmareal1.png', 'El fruto de la palma real es comestible tanto fresco como seco.', '', 'Arroyos', 'Palmera alta y esbelta; alcanza hasta 30 m, con tronco delgado y liso en la parte superior y cubierto de restos de hojas secas en la parte inferior.', 'Maderable, Ornamental, Artesanal', 'Nativa'),
(14, 'Palo adán', 'Fouquieria diguetii (Tiegh.) I.M.Johnst.', 'Los Cabos, La Paz y Concepción', 'paloadan.png', 'paloadan1.png', 'Las flores de palo adán se presentan de enero a junio y son de color rojo escarlata.', '', 'Colinas rocosas', 'Planta suculenta que puede alcanzar hasta 7 m; tronco delgado y erecto, similar a un cilindro.', 'Ornamental', 'Endémica'),
(15, 'Palo blanco', 'Lysiloma candidum Brandegee', 'Región de Los Cabos', 'paloblanco.png', 'paloblanco1.png', 'El fruto del palo blanco es de color rojo cobrizo.', '', 'Arroyos', 'Árbol de hasta 10 m de alto con corteza de color blanco.', 'Forraje, Combustible, Maderable, Tinte natural ', 'Endémica'),
(16, 'Palo brasil', 'Haematoxylum brasiletto H.Karst.', 'Sur de Baja California Sur', 'palobrasil.png', 'palobrasil1.png', 'La madera del palo brasil se utilizó para colorear vinos.', '', 'Laderas montañosas', 'Árbol pequeño que alcanza hasta 15 m de altura, con pequeñas flores que aparecen en primavera.', 'Medicinal, Maderable, Tinte natural', 'Nativa'),
(17, 'Palo de arco', 'Tecoma stans (L.) Juss. ex Kunth', 'Sierra de la Laguna', 'palodearco.png', 'palodearco1.png', 'Se considera que las hojas de palo de arco se usan como remedio para la diabetes y el dolor de estómago.', '', 'Valles arenosos, arroyos y laderas', 'Árbol pequeño de hasta 8 m de alto, con flores amarillo brillante que aparecen casi todo el año.', 'Medicinal, Artesanal, Ornamental', 'Nativa'),
(18, 'Palo zorrillo', 'Senna atomaria (L.) H.S.Irwin & Barneby', 'Sierra de la Laguna', 'palozorrillo.png', 'palozorrillo1.png', 'El palo zorrillo florece principalmente en la temporada de lluvias.', '', 'Cañones', 'Árbol que puede alcanzar hasta 10 m; sus flores verde‑amarillentas aparecen en primavera.', 'Medicinal, Ornamental, Forraje', 'Nativa'),
(19, 'Papache', 'Randia capitata DC.', 'Sierra de la Giganta a la Región de Los Cabos', 'papache.png', 'papache1.png', 'El fruto del papache es muy consumido por la fauna silvestre.', '', 'Laderas rocosas', 'Arbusto de hasta 3 m, con ramas espinosas y hojas opuestas, ovaladas y de color verde brillante.', 'Maderable, Combustible, Alimenticio', 'Nativa'),
(20, 'Pitaya agria', 'Stenocereus gummosus (Engelm.) A.C.Gibson & K.E.Horak', 'Casi toda la península', 'pitayaagria.png', 'pitayaagria1.png', 'El fruto de la pitaya agria es comestible y se vende en los mercados locales.', '', 'Casi todos los ambientes', 'Cactus columnar de hasta 5 m, con tallo cilíndrico que puede ramificarse.', 'Medicinal, Combustible, Alimenticio', 'Endémica'),
(21, 'Pitaya dulce', 'Stenocereus thurberi (Engelm.) Buxb.', 'Casi toda la península', 'pitayadulce.png', 'pitayadulce1.png', 'De la pulpa fermentada de la pitaya dulce se elabora un vino regional.', '', 'Casi todos los ambientes', 'Cactus columnar de hasta 6 m, con tronco erguido que suele ramificarse.', 'Medicinal, Ornamental, Alimenticio', 'Nativa'),
(22, 'Romerillón', 'Ambrosia monogyra (Torr. & A.Gray) Strother & B.G.Baldwin', 'Baja California Sur', 'romerillon.png', 'romerillon1.png', 'Tras las lluvias, el romerillón libera un aroma característico del campo sudcaliforniano.', '', 'Suelos arenosos', 'Arbusto de hasta 2 m, con follaje grisáceo y hojas estrechas, profundamente divididas.', 'Ornamental, Forraje', 'Nativa'),
(23, 'Salvia', 'Condea tephrodes (A.Gray)', 'Desierto de Vizcaíno', 'salvia.png', 'salvia1.png', 'La salvia es muy demandada por las abejas.', '', 'Márgenes de arroyos', 'Arbusto de hasta 1.5 m con tallos delgados y ramas extendidas.', 'Medicinal, Ornamental, Forraje', 'Endémica'),
(24, 'San Miguelito', 'Antigonon leptopus Hook. & Arn.', 'Baja California Sur', 'sanmiguelito.png', 'sanmiguelito.png', 'Al tostar las semillas de San Miguelito, el sabor es similar al de una nuez.', '', 'Arroyos y laderas', 'Planta trepadora de hasta 10 m, con tallos delgados, flexibles y ramificados.', 'Medicinal, Ornamental', 'Nativa'),
(25, 'Toloache', 'Datura discolor Bernh.', 'Baja California Sur', 'toloache.png', 'toloache.png', 'El toloache, en dosis regulares, produce alucinaciones.', '', 'A los lados del camino', 'Planta de hasta 1.5 m; se desarrolla tras las lluvias de verano y madura en invierno.', 'Medicinal, Alucinógeno, Rituales', 'Nativa'),
(26, 'Torote, Torote colorado', 'Bursera microphylla A.Gray', 'Baja California Sur', 'torotecolorado.png', 'torotecolorado1.png', 'El torote produce un fruto que contiene una semilla amarilla.', '', 'Laderas rocosas', 'Árbol de hasta 5 m con corteza gruesa gris‑marrón que se desprende en láminas finas.', 'Medicinal, Combustible, Tinte natural, Ornamental', 'Nativa'),
(27, 'Viejitos', 'Mammillaria armillata K.Brandegee', 'Baja California Sur', 'viejitos.png', 'viejitos1.png', 'Los frutos de la planta Viejitos son comestibles.', '', 'Bajo árboles y arbustos', 'Cactus que florece con pequeñas flores rosa o blancas en la parte superior del tallo.', 'Ornamental, Alimenticio', 'Endémica'),
(28, 'Vinorama', 'Vachellia farnesiana (L.) Wight & Arn.', 'Región del Cabo', 'vinorama.png', 'vinorama1.png', 'Casi todas las semillas de vinorama son parasitadas por escarabajos.', '', 'Márgenes de brechas y carreteras', 'Arbusto o arbolito de hasta 6 m con corteza espinosa y hojas compuestas.', 'Forraje, Medicinal, Tinte natural', 'Nativa'),
(29, 'Zalate o Amate amarillo', 'Ficus petiolaris Kunth', 'Sur de Baja California Sur', 'zalate.png', 'zalate1.png', 'Los frutos del zalate son consumidos por aves y otros animales.', '', 'Áreas rocosas', 'Árbol de hasta 10 m con grandes raíces superficiales que se extienden sobre las rocas.', 'Medicinal, Ornamental, Alimenticio, Forraje', 'Endémica'),
(30, 'Zantinia, poleo', 'Aloysia barbata (Brandegee) Moldenke', 'Sur de Baja California Sur', 'zantinia.png', 'zantinia1.png', 'La zantinia se utiliza como té para aliviar gripe y tos.', '', 'Laderas', 'Planta arbustiva de hasta 1.5 m con pequeñas flores blancas o lilas y aroma herbal.', 'Medicinal, Infusión', 'Endémica'),
(31, 'Pino salado', 'Tamarix aphylla', 'Baja California Sur', 'pinosalado.png', 'pinosalado1.png', 'Un pino salado se utiliza como árbol de sombra.', '', 'Arroyos', 'Árbol siempreverde de hasta 10 m; flores verdosas o rosadas en la temporada cálida.', 'Ornamental, Sombra, Rompevientos', 'Introducida'),
(32, 'Biznaga', 'Ferocactus townsendianus (Engelm.) Britton & Rose var. townsendianus', 'Baja California Sur', 'biznaga.png', 'biznaga1.png', 'Algunas biznagas pueden llegar a vivir cientos de años.', '', 'Diversos tipos de suelo', 'Cacto globoso o cilíndrico de 30‑70 cm; flores amarillas o rojizas en primavera‑verano.', 'Ornamental, Alimento para ganado', 'Endémica'),
(33, 'Manzanilla', 'Perityle crassifolia', 'Baja California Sur', 'manzanilla.png', 'manzanilla1.png', 'La manzanilla es útil para la producción de leche de cabra de calidad.', '', 'Planicies y laderas', 'Planta anual invernal‑primaveral de 10‑50 cm de altura.', 'Forraje', 'Endémica'),
(34, 'Mauto', 'Lysiloma divaricatum (Jacq.)', 'Sierra de la Laguna', 'mauto.png', 'mauto1.png', 'La corteza del mauto se empleó para curtir pieles.', '', 'Arroyos', 'Árbol de 4‑10 m con corteza casi blanca que se desprende en finas capas.', 'Forraje, Combustible, Maderable, Tinte natural', 'Nativa'),
(35, 'Quelite', 'Amaranthus palmeri', 'Península de Baja California Sur', 'quelite.png', 'quelite1.png', 'En ciertas épocas el quelite puede enfermar al ganado bovino.', '', 'Llanos', 'Maleza de verano con ramas erectas y hojas simples alternas.', 'Alimenticio, Forraje', 'Nativa'),
(36, 'Huizapol', 'Distichlis spicata', 'Baja California Sur', 'huizapol.png', 'huizapol1.png', 'Los frutos del huizapol son espinosos y sus plantas se consideran malezas indeseables.', '', 'Suelos diversos (como maleza)', 'Pastos anuales de 15‑40 cm que brotan tras las lluvias de verano‑invierno.', 'Forraje', 'Nativa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insignias`
--

CREATE TABLE `insignias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insignias`
--

INSERT INTO `insignias` (`id`, `nombre`, `descripcion`, `icono_url`) VALUES
(1, 'Bótanico novato I', 'Primera insignia obtenida al comenzar tu camino en la botánica.', 'Botánico_novato.png'),
(2, 'Botánico novato II', 'Has demostrado mayor interés por la flora; sigue así.', 'Botánico_novatoII.png'),
(3, 'Semilla curiosa I', 'Por descubrir tus primeras plantas y mostrar curiosidad natural.', 'Semilla_curiosa.png'),
(4, 'Semilla curiosa II', 'Tu curiosidad por la flora ha ido creciendo como una semilla saludable.', 'Semilla_curiosaII.png'),
(5, 'Explorador I', 'Por explorar nuevos espacios naturales en busca de especies vegetales.', 'Explorador.png'),
(6, 'Explorador II', 'Has recorrido aún más áreas y conocido mayor diversidad botánica.', 'ExploradorII.png'),
(7, 'Amante de la naturaleza I', 'Muestras aprecio y respeto por el medio ambiente y sus maravillas.', 'Amante_de_la_naturaleza.png'),
(8, 'Amante de la naturaleza II', 'Tu conexión con la naturaleza es cada vez más fuerte y consciente.', 'Amante_de_la_naturalezaII.png'),
(9, 'Botánico aficionado I', 'Has aprendido a identificar varias especies.', 'Botánico_aficionado.png'),
(10, 'Botánico aficionado II', 'Tus conocimientos botánicos van más allá de lo básico.', 'Botánico_aficionadoII.png'),
(11, 'Maestro botánico I', 'Reconocimiento por tu experiencia y conocimientos avanzados en botánica.', 'Maestro_botánico.png'),
(12, 'Maestro botánico II', 'Has alcanzado el nivel más alto como experto en flora y naturaleza.', 'Maestro_botánicoII.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juego_usuario`
--

CREATE TABLE `juego_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `duracion` time NOT NULL,
  `fue_ganado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `juego_usuario`
--

INSERT INTO `juego_usuario` (`id`, `usuario_id`, `fecha`, `duracion`, `fue_ganado`) VALUES
(1, 28, '2025-05-14 15:46:22', '00:04:22', 1),
(2, 28, '2025-05-14 15:47:59', '00:15:23', 1),
(3, 28, '2025-05-14 15:48:12', '00:15:23', 1),
(4, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(5, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(6, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(7, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(8, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(9, 28, '2025-05-14 15:48:26', '00:15:23', 1),
(12, 28, '2025-05-14 15:48:44', '00:15:23', 0),
(14, 28, '2025-05-14 18:03:22', '00:00:55', 1),
(15, 28, '2025-05-14 18:05:02', '00:00:00', 1),
(16, 28, '2025-05-14 18:15:00', '00:00:33', 1),
(17, 28, '2025-05-14 18:15:50', '00:00:42', 1),
(18, 28, '2025-05-14 18:17:39', '00:00:35', 1),
(19, 28, '2025-05-14 19:12:40', '00:00:24', 1),
(20, 28, '2025-05-14 19:12:40', '00:00:24', 1),
(21, 28, '2025-05-14 19:12:40', '00:00:24', 1),
(22, 28, '2025-05-14 19:53:52', '00:00:52', 1),
(23, 28, '2025-05-14 19:53:53', '00:00:52', 1),
(24, 28, '2025-05-14 19:54:19', '00:00:01', 0),
(25, 28, '2025-05-14 19:56:19', '00:00:26', 1),
(26, 28, '2025-05-14 19:56:19', '00:00:26', 1),
(27, 28, '2025-05-14 19:56:19', '00:00:26', 1),
(28, 28, '2025-05-14 20:11:29', '00:00:03', 0),
(29, 28, '2025-05-14 21:15:01', '00:00:35', 1),
(30, 28, '2025-05-14 21:27:20', '00:00:26', 1),
(31, 28, '2025-05-14 21:27:59', '00:00:23', 1),
(32, 28, '2025-05-19 21:41:04', '00:00:27', 1),
(33, 28, '2025-05-22 17:21:53', '00:00:28', 1),
(34, 28, '2025-05-22 17:25:39', '00:00:38', 1),
(35, 28, '2025-05-22 17:30:37', '00:00:23', 1),
(36, 28, '2025-05-22 22:31:34', '00:00:57', 1),
(37, 28, '2025-05-22 22:36:38', '00:00:39', 0),
(38, 28, '2025-05-22 22:40:24', '00:00:00', 0),
(39, 28, '2025-05-22 22:41:01', '00:00:26', 1),
(40, 28, '2025-05-22 22:42:00', '00:00:18', 1),
(41, 28, '2025-05-22 22:43:51', '00:00:00', 1),
(42, 30, '2025-05-23 10:13:25', '00:00:04', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivel_de_usuario`
--

CREATE TABLE `nivel_de_usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nivel_de_usuario`
--

INSERT INTO `nivel_de_usuario` (`id`, `nombre`, `imagen`) VALUES
(1, 'Semilla joven', '1.png'),
(2, 'Brote', '2.png'),
(3, 'Retoño sabio', '3.png'),
(4, 'Raíces firmes', '4.png'),
(5, 'Arbusto', '5.png'),
(6, 'Flor silvestre', '6.png'),
(7, 'Polinizador', '7.png'),
(8, 'Planta medicinal', '8.png'),
(9, 'Árbol pequeño', '9.png'),
(10, 'Árbol milenario', '10.png'),
(11, 'Bosque sabio', '11.png'),
(12, 'Sembrador de conocimiento', '12.png'),
(13, 'Sabio del ecosistema', '13.png'),
(14, 'Guardian del bosque', '14.png'),
(15, 'Maestro de la naturaleza', '15.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ranking`
--

CREATE TABLE `ranking` (
  `usuario_id` int(11) NOT NULL,
  `posicion` int(11) NOT NULL,
  `puntos_ganados` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ranking`
--

INSERT INTO `ranking` (`usuario_id`, `posicion`, `puntos_ganados`) VALUES
(28, 1, 16053),
(29, 2, 6000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasena`
--

CREATE TABLE `recuperacion_contrasena` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `expira_en` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recuperacion_contrasena`
--

INSERT INTO `recuperacion_contrasena` (`id`, `usuario_id`, `token`, `fecha_solicitud`, `expira_en`) VALUES
(15, 30, '568600', '2025-05-23 10:18:20', '2025-05-23 10:33:20'),
(17, 30, '695245', '2025-05-23 10:24:45', '2025-05-23 10:39:45'),
(18, 30, '944820', '2025-05-23 10:28:17', '2025-05-23 10:43:17'),
(19, 30, '532609', '2025-05-23 10:29:36', '2025-05-23 10:44:36'),
(20, 30, '008131', '2025-05-23 10:30:24', '2025-05-23 10:45:24'),
(21, 30, '824561', '2025-05-23 10:31:39', '2025-05-23 10:46:39'),
(22, 30, '032873', '2025-05-23 10:36:05', '2025-05-23 10:51:05'),
(23, 30, '579186', '2025-05-23 10:36:43', '2025-05-23 10:51:43'),
(24, 30, '866749', '2025-05-23 10:38:54', '2025-05-23 10:53:54'),
(25, 30, '000876', '2025-05-23 10:42:38', '2025-05-23 10:57:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_de_nacimiento` date NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `puntos_ganados` int(11) DEFAULT 0,
  `nivel_de_usuario_id` int(11) NOT NULL,
  `juegos_ganados` int(11) DEFAULT 0,
  `plantas_aprendidas` int(11) DEFAULT 0,
  `musica_activada` tinyint(1) DEFAULT 1,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `color_fondo` varchar(20) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `fecha_de_nacimiento`, `correo_electronico`, `contrasena`, `puntos_ganados`, `nivel_de_usuario_id`, `juegos_ganados`, `plantas_aprendidas`, `musica_activada`, `foto_perfil`, `color_fondo`, `fecha_registro`) VALUES
(28, 'Oscar', '2004-02-12', 'oscarist210@gmail.com', '$2y$10$D.HQsT9AVK59SCw8fINkc.iNyvBVeX7uyIfm4soi40bRa2COXnZW.', 16053, 9, 100, 0, 0, 'usuario6.png', '#fff9c4', '2025-05-22 17:23:43'),
(29, 'Jose Torres', '2004-02-01', 'oscarorlopez4@gmail.com', '$2y$10$mBxyQ8g2tMSZxgCXmhCspOghaG0gFACE6p9oqIjncG3EB3XEdiZsS', 6000, 4, 20, 0, 0, 'usuario6.png', '#ffcdd2', '2025-05-22 17:23:43'),
(30, 'HanniaTsui', '2000-06-06', 'hanniamtr09@gmail.com', '$2y$10$855BmswfY7a/XlgJEIX2jeyG2Bkd6IYHi.TvsQN.Ag2uaUKYemr9y', 0, 1, 0, 0, 0, '', '', '2025-05-22 23:03:29'),
(32, 'HanniaTsui', '2005-05-23', 'htsui_22@alu.uabcs.mx', '$2y$10$rPR9LkiAXsVxfQYnxmaDxOUbQAc7R2lPSVjV2K9dFtU4.oPIqwguK', 0, 1, 0, 0, 1, '', '', '2025-05-23 11:36:37');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `trigger_actualizar_nivel_usuario` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    IF NEW.puntos_ganados >= 2000 AND OLD.nivel_de_usuario_id < 15 THEN
        SET NEW.nivel_de_usuario_id = FLOOR(NEW.puntos_ganados / 2000) + 1;

        -- Asegurar que no pase del nivel 15
        IF NEW.nivel_de_usuario_id > 15 THEN
            SET NEW.nivel_de_usuario_id = 15;
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_actualizar_ranking` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
  IF NEW.puntos_ganados <> OLD.puntos_ganados THEN
    CALL actualizar_ranking();
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trigger_asignar_insignias_por_victorias` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
    -- Solo ejecutar si el campo juegos_ganados ha cambiado
    IF NEW.juegos_ganados <> OLD.juegos_ganados THEN
        -- Llamar al procedimiento para asignar insignias
        CALL asignar_insignias_por_victorias(NEW.id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_insignias`
--

CREATE TABLE `usuario_insignias` (
  `usuario_id` int(11) NOT NULL,
  `insignia_id` int(11) NOT NULL,
  `fecha_obtenida` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_insignias`
--

INSERT INTO `usuario_insignias` (`usuario_id`, `insignia_id`, `fecha_obtenida`) VALUES
(28, 1, '2025-05-12'),
(28, 2, '2025-05-13'),
(28, 3, '2025-05-14'),
(28, 4, '2025-05-14'),
(28, 5, '2025-05-14'),
(28, 6, '2025-05-14'),
(28, 7, '2025-05-14'),
(28, 8, '2025-05-14'),
(28, 9, '2025-05-14'),
(28, 10, '2025-05-14'),
(29, 1, '2025-05-14'),
(29, 2, '2025-05-14'),
(29, 3, '2025-05-14');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ficha_planta`
--
ALTER TABLE `ficha_planta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `insignias`
--
ALTER TABLE `insignias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `juego_usuario`
--
ALTER TABLE `juego_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `nivel_de_usuario`
--
ALTER TABLE `nivel_de_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`),
  ADD KEY `nivel_de_usuario_id` (`nivel_de_usuario_id`);

--
-- Indices de la tabla `usuario_insignias`
--
ALTER TABLE `usuario_insignias`
  ADD PRIMARY KEY (`usuario_id`,`insignia_id`),
  ADD KEY `insignia_id` (`insignia_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ficha_planta`
--
ALTER TABLE `ficha_planta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `insignias`
--
ALTER TABLE `insignias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `juego_usuario`
--
ALTER TABLE `juego_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `nivel_de_usuario`
--
ALTER TABLE `nivel_de_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `juego_usuario`
--
ALTER TABLE `juego_usuario`
  ADD CONSTRAINT `juego_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ranking`
--
ALTER TABLE `ranking`
  ADD CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recuperacion_contrasena`
--
ALTER TABLE `recuperacion_contrasena`
  ADD CONSTRAINT `recuperacion_contrasena_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`nivel_de_usuario_id`) REFERENCES `nivel_de_usuario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_insignias`
--
ALTER TABLE `usuario_insignias`
  ADD CONSTRAINT `usuario_insignias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_insignias_ibfk_2` FOREIGN KEY (`insignia_id`) REFERENCES `insignias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
