📋 Sistema de Gestión de Marcaciones de Empleados
API REST para registrar y consultar marcaciones de asistencia con autenticación JWT y validaciones de flujo.

🚀 Características Principales
✅ Autenticación segura con tokens JWT
✅ Registro inteligente de marcaciones (ingreso/salida/almuerzo)
✅ Validación de secuencias lógicas (ej: no permite 2 ingresos seguidos)
✅ Historial completo por empleado
✅ Base de datos MySQL/PostgreSQL
✅ Generación de reportes en CSV (script Python incluido)

🔧 Requisitos Técnicos
PHP 8.0+

Composer

MySQL 5.7+ o PostgreSQL 12+

Extensión PHP para JWT

⚙️ Configuración Inicial
Clonar repositorio

Instalar dependencias:

bash
Copy
composer install
Configurar archivo .env (copiar de .env.example)

Ejecutar migraciones:

bash
Copy
php artisan migrate
🔐 Endpoints de Autenticación
Método	Endpoint	Descripción
POST	/login	Iniciar sesión (obtener token)
POST	/register	Registrar nuevo usuario
POST	/logout	Cerrar sesión
📅 Endpoints de Marcaciones
Registrar Marcación
POST /api/marcaciones

json
Copy
{
  "empleado_id": 1,
  "tipo_marcacion": "ingreso"
}
Obtener Historial
GET /api/marcaciones/{empleado_id}

🔄 Flujo Válido de Marcaciones
mermaid
Copy
graph LR
  A[Ingreso] --> B[Almuerzo Inicio]
  B --> C[Almuerzo Fin]
  C --> D[Salida]
  D --> A
🛡️ Validaciones Implementadas
No se puede repetir la misma marcación consecutivamente

Secuencia obligatoria:

Ingreso → (Almuerzo Inicio → Almuerzo Fin) → Salida

Primera marcación debe ser siempre de ingreso

📊 Script de Reportes (Python)
Genera reportes CSV con filtros por fecha:

bash
Copy
python reporte_marcaciones.py --inicio 2023-01-01 --fin 2023-01-31
Formato del CSV:

Copy
empleado_id, nombre, fecha, hora, tipo_marcacion
📦 Estructura del Proyecto
Copy
├── app/
│   ├── Http/Controllers/   # Lógica de endpoints
│   ├── Models/             # Modelos de datos
├── database/
│   ├── migrations/         # Esquema de BD
├── routes/
│   └── api.php            # Definición de rutas
└── scripts/
    └── reporte_marcaciones.py # Script Python
📌 Notas Adicionales
CodeIgniter Alternative: Uso de librería firebase/php-jwt para autenticación

Seguridad: Todos los endpoints protegidos requieren token JWT válido

Logs: El sistema registra todas las operaciones para auditoría