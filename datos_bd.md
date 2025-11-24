Carga de datos
USE healthtrackerV1;

-- ===========================
-- 1) Catálogos
-- ===========================
INSERT INTO roles (nombre) VALUES
('Paciente'), ('Profesional'), ('Administrador');

INSERT INTO diagnosticos (nombre, descripcion) VALUES
('Hipertensión arterial','Presión elevada sostenida'),
('Diabetes tipo 2','Hiperglucemia crónica'),
('Asma persistente','Inflamación crónica de vía aérea'),
('Lumbalgia mecánica','Dolor lumbar de origen mecánico'),
('Trastorno de ansiedad','Ansiedad generalizada leve');

INSERT INTO medicamento (nombre) VALUES
('Lisinopril'),('Metformina'),('Salbutamol'),('Ibuprofeno'),('Sertralina');

INSERT INTO tipos_tarea (nombre) VALUES
('medicación'),('ejercicio'),('terapia'),('control');

-- Guardamos los IDs de tipos_tarea en variables para usarlos abajo
SET @tt_medicacion := (SELECT id_tipo_tarea FROM tipos_tarea WHERE nombre='medicación');
SET @tt_ejercicio  := (SELECT id_tipo_tarea FROM tipos_tarea WHERE nombre='ejercicio');
SET @tt_terapia    := (SELECT id_tipo_tarea FROM tipos_tarea WHERE nombre='terapia');
SET @tt_control    := (SELECT id_tipo_tarea FROM tipos_tarea WHERE nombre='control');

-- ===========================
-- 2) Usuarios (IDs explícitos para facilitar referencias)
-- ===========================
INSERT INTO usuarios (id_usuario, email, nombre, apellido, password, nombre_rol, descripcion_perfil) VALUES
-- Profesionales
(1,   'julia.alvarez@clinic.local','Julia','Álvarez','$2y$10$hashjulia','Profesional','Cardióloga. MP 1234.'),
(2,   'martin.rios@clinic.local',  'Martín','Ríos',   '$2y$10$hashmartin','Profesional','Clínico. MP 5678.'),
-- Pacientes
(101, 'ana.sosa@mail.local',  'Ana',   'Sosa', '$2y$10$hashana','Paciente','HTA diagnosticada hace 1 mes.'),
(102, 'carlos.paz@mail.local','Carlos','Paz',  '$2y$10$hashcarlos','Paciente','DM2 en control.'),
(103, 'lucia.mena@mail.local','Lucía', 'Mena', '$2y$10$hashlucia','Paciente','Asma desde la adolescencia.'),
(104, 'pablo.vila@mail.local','Pablo', 'Vila', '$2y$10$hashpablo','Paciente','Dolor lumbar crónico.'),
(105, 'sofia.luna@mail.local','Sofía', 'Luna', '$2y$10$hashsofia','Paciente','Ansiedad leve.'),
-- Admin
(201, 'admin@health.local',   'Admin', 'Root', '$2y$10$hashadmin','Administrador','Superusuario.');

-- ===========================
-- 3) Planes (IDs explícitos)
-- ===========================
INSERT INTO planes (id, nombre, descripcion, id_profesional, id_paciente, nombre_diagnostico, fecha_inicio, fecha_fin) VALUES
(1001,'Plan HTA - Ana','Tratamiento de hipertensión',            1,101,'Hipertensión arterial','2025-11-01','2026-01-31'),
(1002,'Plan DM2 - Carlos','Control y educación para diabetes',    2,102,'Diabetes tipo 2',      '2025-10-20','2026-01-20'),
(1003,'Plan Asma - Lucía','Control de síntomas y medicación',     2,103,'Asma persistente',     '2025-11-02','2026-02-02'),
(1004,'Plan Lumbalgia - Pablo','Rehabilitación y ejercicios',     2,104,'Lumbalgia mecánica',   '2025-11-03','2025-12-15');

-- ===========================
-- 4) Tareas por plan (IDs explícitos)
-- ===========================
INSERT INTO tareas
(id_tarea, id_plan, id_tipo_tarea, num_tarea, descripcion,                      fecha_programada,         fecha_fin_programada,   estado,      comentarios_paciente,  fecha_realizacion)
VALUES
-- Plan 1001 (Ana - HTA)
(20001, 1001, @tt_medicacion, 1, 'Tomar Lisinopril 10 mg cada mañana',          '2025-11-06 09:00:00',    '2025-11-06 09:05:00',  'Pendiente', NULL,                 NULL),
(20002, 1001, @tt_control,    2, 'Medir presión arterial y registrar',          '2025-11-06 20:00:00',    '2025-11-06 20:10:00',  'Pendiente', NULL,                 NULL),
(20003, 1001, @tt_ejercicio,  3, 'Caminata 30 minutos',                         '2025-11-06 18:00:00',    '2025-11-06 18:30:00',  'Completada','Hecho sin dolor',    '2025-11-06 18:30:00'),
-- Plan 1002 (Carlos - DM2)
(20004, 1002, @tt_medicacion, 1, 'Tomar Metformina 850 mg con la cena',         '2025-11-06 21:00:00',    '2025-11-06 21:05:00',  'Pendiente', NULL,                 NULL),
(20005, 1002, @tt_control,    2, 'Control de glucemia en ayunas',               '2025-11-07 07:30:00',    '2025-11-07 07:40:00',  'Pendiente', NULL,                 NULL),
(20006, 1002, @tt_ejercicio,  3, 'Bicicleta fija 20 minutos',                   '2025-11-07 19:00:00',    '2025-11-07 19:20:00',  'Pendiente', NULL,                 NULL),
(20011, 1002, @tt_control,    4, 'Control glucemia postprandial',               '2025-11-05 14:00:00',    '2025-11-05 14:10:00',  'Completada','120 mg/dL',          '2025-11-05 14:05:00'),
-- Plan 1003 (Lucía - Asma)
(20007, 1003, @tt_medicacion, 1, 'Inhalador de rescate (Salbutamol) según síntomas','2025-11-06 00:00:00','2026-02-02 23:59:00','Pendiente', NULL,                 NULL),
(20008, 1003, @tt_control,    2, 'Peak flow diario y registro',                 '2025-11-07 08:00:00',    '2025-11-07 08:10:00',  'Pendiente', NULL,                 NULL),
-- Plan 1004 (Pablo - Lumbalgia)
(20009, 1004, @tt_terapia,    1, 'Sesión de kinesiología',                       '2025-11-08 10:00:00',    '2025-11-08 11:00:00',  'Pendiente', NULL,                 NULL),
(20010, 1004, @tt_ejercicio,  2, 'Estiramientos lumbares 15 min',               '2025-11-06 19:00:00',    '2025-11-06 19:15:00',  'Pendiente', NULL,                 NULL);










Pruebas
-- Tareas por plan (con tipo)
SELECT p.id, p.nombre AS plan, t.id_tarea, tt.nombre AS tipo, t.descripcion, t.estado
FROM planes p
JOIN tareas t      ON t.id_plan = p.id
JOIN tipos_tarea tt ON tt.id_tipo_tarea = t.id_tipo_tarea
ORDER BY p.id, t.num_tarea;

-- Tareas próximas de un paciente (ej.: Ana = id 101)
SELECT t.*
FROM tareas t
JOIN planes p ON p.id = t.id_plan
WHERE p.id_paciente = 101
  AND t.fecha_programada >= NOW()
ORDER BY t.fecha_programada;

-- Planes por profesional
SELECT u.nombre, u.apellido, COUNT(*) AS cant_planes
FROM usuarios u
JOIN planes p ON p.id_profesional = u.id_usuario
GROUP BY u.id_usuario;


-- ===========================
-- Script de prueba para paciente "queSeYo"
-- ===========================
-- Este script agrega el paciente queSeYo con un plan y tareas para pruebas

USE healthtrackerV1;

-- Paciente queSeYo (ID 106)
INSERT INTO usuarios (id_usuario, email, nombre, apellido, password, nombre_rol, descripcion_perfil) VALUES
(106, 'queseyo@test.local', 'queSeYo', 'Test', '$2y$10$hashqueseyo', 'Paciente', 'Paciente de prueba para desarrollo.');

-- Plan para queSeYo (ID 1005) - Vinculado con profesional Julia Álvarez (ID 1)
INSERT INTO planes (id, nombre, descripcion, id_profesional, id_paciente, nombre_diagnostico, fecha_inicio, fecha_fin) VALUES
(1005, 'Plan Prueba - queSeYo', 'Plan de prueba para desarrollo y testing', 1, 106, 'Hipertensión arterial', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 MONTH));

-- Tareas para el plan de queSeYo
-- Usamos las variables de tipos_tarea ya definidas arriba
INSERT INTO tareas
(id_tarea, id_plan, id_tipo_tarea, num_tarea, descripcion, fecha_programada, fecha_fin_programada, estado, comentarios_paciente, fecha_realizacion)
VALUES
-- Tareas pendientes para probar
(20012, 1005, @tt_medicacion, 1, 'Tomar medicamento según prescripción médica', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 'Pendiente', NULL, NULL),
(20013, 1005, @tt_control, 2, 'Medir presión arterial y registrar valores', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 'Pendiente', NULL, NULL),
(20014, 1005, @tt_ejercicio, 3, 'Realizar caminata de 30 minutos', DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 'Pendiente', NULL, NULL),
-- Tarea completada para ver en historial
(20015, 1005, @tt_control, 4, 'Control de presión arterial matutina', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Completada', 'Presión normal: 120/80', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Verificación: Ver datos del paciente queSeYo
-- SELECT u.id_usuario, u.nombre, u.email, p.id as plan_id, p.nombre as plan_nombre, 
--        COUNT(t.id_tarea) as total_tareas,
--        SUM(CASE WHEN t.estado = 'Pendiente' THEN 1 ELSE 0 END) as tareas_pendientes,
--        SUM(CASE WHEN t.estado = 'Completada' THEN 1 ELSE 0 END) as tareas_completadas
-- FROM usuarios u
-- JOIN planes p ON p.id_paciente = u.id_usuario
-- LEFT JOIN tareas t ON t.id_plan = p.id
-- WHERE u.nombre = 'queSeYo'
-- GROUP BY u.id_usuario, p.id;


