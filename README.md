<div align="center">
  <img src="public/images/logo.svg" alt="HealthTracker Logo" width="100" height="100">
  <h1>HealthTracker v1</h1>
  <p>
    <strong>Plataforma SaaS para la Gestión Digital de Planes de Cuidado de Salud</strong>
  </p>

  <p>
    <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/CodeIgniter-4-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white" alt="CodeIgniter 4">
    <img src="https://img.shields.io/badge/MySQL-Integrity-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/Frontend-Vanilla_JS-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JS">
  </p>
</div>

---

## 📋 Sobre el Proyecto

**HealthTracker** es una solución integral diseñada para cerrar la brecha entre los profesionales de la salud y el seguimiento diario de sus pacientes. A diferencia de un simple historial clínico, esta plataforma se centra en el **Plan de Cuidado Activo**: tareas, medicación y seguimiento de progreso en tiempo real.

El sistema permite a los médicos diseñar planes personalizados, a los pacientes interactuar con su tratamiento día a día, y a los administradores mantener la gobernanza de los datos maestros.

## ✨ Funcionalidades Principales (MVP)

### 👨‍⚕️ Para el Profesional
* **Gestión de Planes:** Creación de tratamientos personalizados vinculados a diagnósticos.
* **Monitorización:** Visualización del progreso del paciente mediante barras de estado y timelines.
* **Control de Estado:** Capacidad de finalizar o reactivar planes manualmente.
* **Granularidad:** Asignación de tareas específicas con fechas, horas y medicamentos asociados.

### 👤 Para el Paciente
* **Dashboard Personal:** Vista clara de las actividades diarias (pendientes vs. completadas).
* **Feedback:** Posibilidad de dejar comentarios sobre cómo se sintió al realizar una tarea.
* **Historial:** Acceso a sus planes vigentes y pasados.

### 🛠 Para el Administrador
* **Visión Global:** Reporte macro de la plataforma (Planes activos, métricas de ocupación).
* **Gestión de Catálogos:** CRUDs completos para Medicamentos, Diagnósticos y Tipos de Tarea.
* **Seguridad:** Gestión de usuarios y roles (RBAC) con acceso privilegiado de Superusuario.

---

## 🚀 Desafíos Técnicos y Arquitectura

Este proyecto fue construido priorizando la **robustez, la escalabilidad y las buenas prácticas** de ingeniería de software.

### 1. Patrón "Controlador Experto por Entidad"
Nos alejamos de los controladores monolíticos. Cada entidad (`Plan`, `Tarea`, `Usuario`, `Catálogo`) posee su propio controlador que encapsula la lógica de negocio, validaciones y permisos. Los Dashboards actúan como orquestadores que consumen estos servicios.

### 2. Integridad Referencial "Smart" (Base de Datos)
Uno de los mayores desafíos fue manejar la eliminación de datos maestros (ej: borrar un medicamento) sin romper los planes históricos de los pacientes.
* **Solución:** Implementación de **Triggers en MySQL** (`BEFORE DELETE`).
* **Resultado:** Al eliminar un catálogo, el trigger actualiza automáticamente los registros hijos a un estado "Genérico/Histórico" (ej: *"Sin medicamento asociado o eliminado"*), preservando la historia clínica del paciente intacta.

### 3. Seguridad y Sanitización
* **Protección CSRF:** Implementada en todas las peticiones AJAX (Fetch API) mediante meta-tags dinámicos.
* **Manejo de URIs:** Configuración del framework y del frontend para soportar identificadores con caracteres latinos complejos (ej: tildes en diagnósticos como "Celiaquía") sin comprometer la seguridad.

### 4. Frontend Dinámico sin Frameworks Pesados
Se logró una interfaz reactiva y moderna (Modales dinámicos, Timelines visuales, Gráficos de progreso) utilizando **Vanilla JavaScript** y CSS nativo, optimizando el rendimiento de carga y reduciendo dependencias externas.

---

## ⚙️ Instalación Local

1.  **Clonar el repositorio**
    ```bash
    git clone [https://github.com/TU_USUARIO/healthtracker.git](https://github.com/TU_USUARIO/healthtracker.git)
    cd healthtracker
    ```

2.  **Instalar dependencias**
    ```bash
    composer install
    ```

3.  **Configurar Base de Datos**
    * Crea una base de datos llamada `healthtrackerv1`.
    * Importa el archivo `schema.sql` para la estructura y triggers.
    * Importa el archivo `seed_data.sql` para los datos de prueba.
    * Configura tu archivo `.env` con las credenciales.

4.  **Ejecutar el servidor**
    ```bash
    php spark serve
    ```
    Accede a `http://localhost:8080`.

---

## 📄 Credenciales de Acceso (Demo)

| Rol | Email | Contraseña |
| :--- | :--- | :--- |
| **Admin** | `admin@healthtracker.com` | `123456` |
| **Profesional** | `doctor.house@hospital.com` | `123456` |
| **Paciente** | `juan.perez@mail.com` | `123456` |

---

<div align="center">
  <sub>Desarrollado como Proyecto Final de Software. 2025.</sub>
</div>

