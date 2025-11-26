# BASE DE DATOS

DROP DATABASE IF EXISTS healthtrackerV1;
CREATE DATABASE healthtrackerV1;
USE healthtrackerV1;

-- 1. Tablas de catálogo
CREATE TABLE roles (
    nombre VARCHAR(100) NOT NULL PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE diagnosticos (
    nombre VARCHAR(255) NOT NULL PRIMARY KEY,
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE medicamento (
    nombre VARCHAR(255) NOT NULL PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE tipos_tarea (
    id_tipo_tarea INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 2. Tablas con dependencias
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_rol VARCHAR(100),
    descripcion_perfil TEXT,
    -- CORRECCIÓN: Ya estaba bien, permite cambiar nombre del rol y si se borra el rol, el usuario queda "sin rol" (NULL)
    FOREIGN KEY (nombre_rol) REFERENCES roles(nombre)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    id_profesional INT NOT NULL,
    id_paciente INT NOT NULL,
    nombre_diagnostico VARCHAR(255),
    fecha_inicio DATE,
    fecha_fin DATE,
    -- Integridad para usuarios (IDs numéricos raramente cambian, RESTRICT es seguro por defecto)
    FOREIGN KEY (id_profesional) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    FOREIGN KEY (id_paciente) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    
    -- CORRECCIÓN CRÍTICA: Diagnóstico es VARCHAR.
    -- Si corriges "Gripe" a "Influenza" en la tabla diagnosticos,
    -- esto actualizará automáticamente todos los planes relacionados.
    FOREIGN KEY (nombre_diagnostico) REFERENCES diagnosticos(nombre)
        ON UPDATE CASCADE
        ON DELETE RESTRICT -- No permite borrar un diagnóstico si hay planes activos usándolo
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE tareas (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    id_plan INT NOT NULL,
    id_tipo_tarea INT NOT NULL,
    num_tarea INT,
    descripcion TEXT NOT NULL,
    fecha_programada DATETIME,
    fecha_fin_programada DATETIME,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    comentarios_paciente TEXT,
    fecha_realizacion DATETIME,
    
    -- CORRECCIÓN: Si se borra un plan, se borran sus tareas (Cascada). Esto estaba bien.
    FOREIGN KEY (id_plan) REFERENCES planes(id) ON DELETE CASCADE,
    
    -- Tipos de tarea son catálogo numérico, RESTRICT está bien.
    FOREIGN KEY (id_tipo_tarea) REFERENCES tipos_tarea(id_tipo_tarea)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;

## CARGA DE DATOS EN LA BASE DE DATOS
USE healthtrackerV1;

-- ========================================================
-- 1. CARGA DE CATÁLOGOS (Tablas sin dependencias FK)
-- ========================================================

-- Roles del sistema
INSERT INTO roles (nombre) VALUES 
('Administrador'),
('Profesional'),
('Paciente');

-- Diagnósticos médicos comunes
INSERT INTO diagnosticos (nombre, descripcion) VALUES 
('Diabetes Mellitus Tipo 2', 'Enfermedad crónica que afecta la forma en que el cuerpo procesa el azúcar en sangre.'),
('Hipertensión Arterial', 'Afección frecuente en la que la fuerza que ejerce la sangre contra las paredes de las arterias es alta.'),
('Asma Bronquial', 'Afección en la que las vías respiratorias se estrechan e hinchan, lo que puede producir mayor mucosidad.');

-- Medicamentos disponibles
INSERT INTO medicamento (nombre) VALUES 
('Metformina 850mg'),
('Losartán 50mg'),
('Ibuprofeno 400mg'),
('Salbutamol Aerosol'),
('Insulina Glargina');

-- Tipos de tareas para los planes
INSERT INTO tipos_tarea (nombre) VALUES 
('Toma de Medicamento'),
('Actividad Física'),
('Registro de Signos Vitales'),
('Cita de Control'),
('Dieta / Alimentación');


-- ========================================================
-- 2. CARGA DE USUARIOS (Dependen de Roles)
-- ========================================================

-- NOTA: La contraseña para todos es '123456'.
-- El hash generado es un ejemplo válido de BCRYPT para pruebas.
INSERT INTO usuarios (email, nombre, apellido, password, nombre_rol, descripcion_perfil) VALUES 
-- 1. Administrador
('admin@healthtracker.com', 'Admin', 'General', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.u.E9.p.q.r.s.t.u.v.w.x.y.z.A.B', 'Administrador', 'Superusuario del sistema.'),

-- 2. Profesional (Médico)
('doctor@healthtracker.com', 'Gregory', 'House', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.u.E9.p.q.r.s.t.u.v.w.x.y.z.A.B', 'Profesional', 'Especialista en Medicina Interna y Diagnóstico.'),

-- 3. Paciente 1 (Con Diabetes)
('pepe@healthtracker.com', 'Pepe', 'Argento', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.u.E9.p.q.r.s.t.u.v.w.x.y.z.A.B', 'Paciente', 'Paciente con antecedentes de hiperglucemia.'),

-- 4. Paciente 2 (Con Asma)
('moni@healthtracker.com', 'Moni', 'Argento', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.u.E9.p.q.r.s.t.u.v.w.x.y.z.A.B', 'Paciente', 'Paciente asmática estacional.');


-- ========================================================
-- 3. CARGA DE PLANES (Dependen de Usuarios y Diagnósticos)
-- ========================================================

INSERT INTO planes (nombre, descripcion, id_profesional, id_paciente, nombre_diagnostico, fecha_inicio, fecha_fin) VALUES 
-- Plan para Pepe (Diabetes) asignado por Dr. House
('Control Glucémico Q1 2024', 'Plan inicial para estabilizar niveles de glucosa mediante dieta y medicación oral.', 2, 3, 'Diabetes Mellitus Tipo 2', '2024-01-01', '2024-03-31'),

-- Plan para Moni (Asma) asignado por Dr. House
('Manejo Crisis Asmática', 'Plan de acción para temporada de otoño.', 2, 4, 'Asma Bronquial', '2024-03-20', '2024-06-20');


-- ========================================================
-- 4. CARGA DE TAREAS (Dependen de Planes y Tipos de Tarea)
-- ========================================================

-- Tareas para el Plan de Pepe (ID 1)
INSERT INTO tareas (id_plan, id_tipo_tarea, num_tarea, descripcion, fecha_programada, fecha_fin_programada, estado) VALUES 
-- Tarea 1: Medicamento
(1, 1, 1, 'Tomar Metformina 850mg con el desayuno', '2024-01-02 08:00:00', '2024-01-02 08:30:00', 'Pendiente'),
-- Tarea 2: Registro
(1, 3, 2, 'Medir glucosa en ayunas y registrar valor', '2024-01-02 07:00:00', '2024-01-02 07:15:00', 'Completada'),
-- Tarea 3: Ejercicio
(1, 2, 3, 'Caminata ligera de 30 minutos', '2024-01-02 18:00:00', '2024-01-02 19:00:00', 'Pendiente');

-- Tareas para el Plan de Moni (ID 2)
INSERT INTO tareas (id_plan, id_tipo_tarea, num_tarea, descripcion, fecha_programada, fecha_fin_programada, estado) VALUES 
(2, 1, 1, 'Dos disparos de Salbutamol si hay sibilancias', '2024-03-21 09:00:00', '2024-03-21 21:00:00', 'Pendiente');

COMMIT;


# Historias de usuario:

HU: CU-01 — Registro de usuario
Descripción: Como Usuario (Administrador/Profesional/Paciente) quiero registrarme para crear mi cuenta y acceder según mi rol.
Criterios de aceptación:
Dado un formulario con campos obligatorios, cuando ingreso datos válidos entonces se crea la cuenta y quedo habilitado por rol
Debe validar campos requeridos y formatos (ej.: email)
Debe poder registrarse cualquiera de los tres perfiles previstos.
Mostrar mensajes claros de éxito/errores.
Puntos: (promedio de 1, 3, 5)
Valor: Onboarding autónomo que facilita adopción y reduce carga administrativa
Riesgo: Bajo (flujo estándar de alta con validaciones).

HU: CU-02 — Login/Logout
Descripción: Como Usuario quiero iniciar/cerrar sesión (login/logout) para acceder de forma segura a mi dashboard y permisos.
Criterios de aceptación:
Autenticación por email/clave; al fallar muestra error sin revelar detalles.
Sesión mantiene el rol y permisos; logout invalida sesión.
Validaciones de formulario activas.
Puntos: ( promedio 2, 5, 8)
Valor:  Acceso controlado y personalizado que garantiza seguridad y experiencia contextual.
Riesgo: Bajo (seguridad básica y manejo de sesión).

HU: CU-03 — ABMC Usuarios (Admin)
Descripción: Como Administrador quiero crear, editar, eliminar y consultar (ABMC) usuarios para gestionar cuentas y permisos.
Criterios de aceptación:
Alta/edición con asignación de rol (Administrador/Profesional/Paciente).
Baja lógica y búsqueda por filtros.
Validaciones de campos y mensajes de confirmación.
Puntos:  2, 3, 3
Valor: Control centralizado de usuarios que mejora gobernanza y cumplimiento institucional.
Riesgo: Medio (consistencia y gobernanza de permisos).


HU: CU-04 — Restablecer contraseña
Descripción: Como Usuario quiero restablecer mi contraseña de forma segura sin asistencia manual.
Criterios de aceptación:
Solicitud por email y envío de enlace temporal con expiración.
Cambio exitoso invalida sesiones previas.
Validaciones y feedback de estado.
Puntos: ( ?, ?, ?)
Valor: Reduce fricción y tickets de soporte, mejora seguridad con enlaces temporales
Riesgo: Medio (gestión segura de tokens y expiraciones).

HU: CU-05 — Crear Plan de Cuidado (Profesional)
Descripción: Como Profesional quiero crear un plan para un paciente, vinculado a un diagnóstico, con fechas y metas/tareas.
Criterios de aceptación:
El plan se asocia a paciente y a un “Tipo de Diagnóstico” existente; fechas inicio/fin son obligatorias.
Permite definir metas/tareas iniciales del plan.
(Restricción del curso) La lista de tipos de diagnóstico está limitada (máx. 5).
Validaciones y confirmación de creación.
Puntos: (5, 8, 8)
Valor: Permite intervenciones individualizadas y registro clínico estructurado.
Riesgo: Medio (modelado de dominio y reglas de negocio).

HU: CU-05a — ABMC de tareas/metas del plan (Profesional)
 Descripción: Como Profesional quiero crear/editar/eliminar/consultar metas/tareas del plan para detallar acciones del paciente.
 Criterios de aceptación:
Alta con nombre, frecuencia/periodicidad, notas y objetivo.
Edición y baja lógica; historial visible.
Debe validar campos requeridos.
 Puntos: (5,8,5)
 Valor: Granularidad en el seguimiento que habilita trazabilidad y métricas precisas
 Riesgo: Medio (trazabilidad de cambios en componentes del plan).

HU: CU-06 — ABMC de Planes estandarizados (Admin)
Descripción: Como Administrador quiero gestionar plantillas de planes de cuidado para uso homogéneo.
Criterios de aceptación:
Crear plantilla con metas/tareas predefinidas y tipo de diagnóstico asignable.
Editar/bajar/consultar plantillas; clonarlas a un paciente (cuando el Profesional las use).
Validaciones de formularios
Puntos: (???)
Valor: : Facilita capacitación, control de calidad y uso homogéneo entre profesionales
Riesgo: Medio (versionado/impacto de cambios en plantillas).

HU: CU-07 — Consultar planes (Profesional/Paciente)
Descripción: Como Profesional/Paciente quiero ver planes activos y pasados para conocer estado e historial.
Criterios de aceptación:
Listado filtrable (activo/finalizado) con paginación.
Detalle muestra metas, progreso y estado.
Acceso al historial de tratamientos del paciente.
Puntos: 1,3,3
Valor: Transparencia y flexibilidad en la evolución del tratamiento.
Riesgo: Bajo (consultas y vistas).

HU: CU-08 — Registrar progreso (Paciente)
Descripción: Como Paciente quiero marcar tareas como cumplidas y agregar fecha/duración/comentarios.
Criterios de aceptación:
Registro por tarea con fecha requerida; opcional duración/comentarios.
Solo sobre tareas vigentes del plan activo.
Validaciones y feedback de guardado.
Puntos: 3,1,3
Valor:  Empoderamiento del paciente y datos directos para evaluación clínica
Riesgo: Bajo (captura simple con validaciones).


HU: CU-09 — Validar cumplimiento (Profesional)
Descripción: Como Profesional quiero validar/aprobar y/o puntuar los reportes del paciente y, de ser necesario, actualizar el estado del plan.
Criterios de aceptación:
Vista de pendientes de validación con filtros.
Acción de validar/rechazar con nota y puntuación opcional.
Puede cambiar el estado del plan según criterio clínico.
Puntos: ???
Valor: Control clínico sobre la veracidad del cumplimiento y mejora de la toma de decisiones
Riesgo: Medio (reglas de estado y auditoría).

HU: CU-10 — Métricas y estadísticas
Descripción: Como Profesional/Paciente/Administrador quiero ver métricas de cumplimiento y efectividad para analizar progreso y tendencias.
Criterios de aceptación:
Panel con KPIs (p. ej., % cumplimiento, racha, tareas/semana) e históricos.
Gráficos de tendencia y comparativas por plan/diagnóstico.
Filtros por fecha/estado/rol
Puntos: ???
Valor: Inteligencia operacional que permite mejorar protocolos y medir resultados poblacionales e individuales
Riesgo: Alto (agregaciones, rendimiento y diseño de métricas).

HU: CU-11 — Documentación médica (Paciente)
Descripción: Como Paciente quiero subir y organizar estudios/recetas/informes para centralizar mi historial y compartirlo.
Criterios de aceptación:
Subir archivos (tipos permitidos), ver/descargar, categorizar por tipo.
Asociar documentos a planes/diagnósticos opcionalmente.
Validaciones de tamaño/formatos y mensajes claros.
Puntos: ???
Valor: Digitalización del expediente que agiliza consultas y reduce pérdida de información
Riesgo: Medio (manejo de archivos y permisos).

HU: CU-12 — Vista global de planes (Admin)
Descripción: Como Administrador quiero ver el total de planes por estado (activos, finalizados, pendientes) para monitorear uso y carga.
Criterios de aceptación:
Tablero con conteos por estado y tendencia semanal/mensual.
Filtros por entidad/médico/diagnóstico.
Exportar tabla/resumen.
Puntos: 3
Valor: 8,5,3
Riesgo: Bajo (consultas agregadas simples).


HISTORIAS DEL PROYECTO MVP (DEBEN GENERARSE PRIMERO): CU-01, CU-02, CU-03, CU-04, CU-05, CU-05a, CU-07, CU-08, CU-12.

REGLAS A SEGUIR (VITALES):

1. Patrón de "experto en conocimiento": Existe un controller por entidad del sistema, el mismo gestiona TODO lo relacionado a la entidad, principalmente ABMC. Los roles consumen dichos controllers para llevar adelante sus funcionalidades.
2. Rutas explícitas en el archivo Routes.php: Manejo semántico y limpio de las rutas, sencillez de acceso y sin acoplamiento. Los roles pueden consumir distintos controllers según la funcionalidad que llevan a cabo.
3. Archivo script.js único, que define comportamientos claves y generalizados de las vistas pero nada más. Cada dashboard tendrá un bloque <script></script> que gestionará la lógica propia del dashboard.

HU: ID
Descripción:
Criterios de aceptación:
Puntos:
Valor:
Riesgo:


