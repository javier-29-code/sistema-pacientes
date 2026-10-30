# Sistema de ingreso de pacientes

Aplicación desarrollada en PHP para registrar, consultar, editar y eliminar pacientes. El sistema se ejecuta en XAMPP y se conecta a una base de datos MariaDB alojada en una máquina virtual con Rocky Linux.

## Tecnologías utilizadas

- PHP y MySQLi.
- HTML y CSS.
- XAMPP.
- MariaDB.
- Rocky Linux y VirtualBox.
- Git y GitHub.

## Arquitectura

| Componente | Función |
|---|---|
| Windows con XAMPP | Ejecutar la aplicación PHP mediante Apache |
| Máquina virtual Rocky Linux | Alojar la base de datos MariaDB |
| GitHub | Almacenar el código y administrar las ramas |
| Hosting | Publicación pendiente de las credenciales de acceso |

## Funciones del sistema

- Registrar pacientes.
- Consultar los pacientes registrados.
- Editar sus datos.
- Eliminar registros.
- Calcular automáticamente la edad a partir de la fecha de nacimiento.
- Validar campos obligatorios y evitar cédulas duplicadas.

## Datos registrados

- Cédula.
- Nombres y apellidos.
- Fecha de nacimiento.
- Teléfono.
- Dirección.
- Tipo de sangre.
- Alergias.
- Motivo de ingreso.
- Fecha y hora de ingreso.

La edad no se almacena como un campo independiente, ya que se calcula mediante la fecha de nacimiento.

## Pasos realizados

### 1. Preparación del proyecto

Se utilizó como base la práctica `practica3conexionRocky`, que gestionaba estudiantes mediante una conexión remota a MariaDB.

Se adaptó el proyecto para la gestión de pacientes y se ubicó en:

```text
C:\xampp\htdocs\sistema_pacientes
```

### 2. Preparación de la base de datos

En MariaDB de Rocky Linux se utilizó la base de datos `Practica2` y se creó la tabla `pacientes`:

```sql
USE Practica2;

CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    tipo_sangre VARCHAR(12) NOT NULL DEFAULT 'Desconocido',
    alergias VARCHAR(255) DEFAULT NULL,
    motivo_ingreso VARCHAR(255) NOT NULL,
    fecha_ingreso TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Se comprobó la estructura con:

```sql
DESCRIBE pacientes;
```

Para calcular la edad se utiliza:

```sql
TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
```

### 3. Adaptación del código PHP

Se modificaron las operaciones que gestionaban estudiantes para trabajar con pacientes.

Se implementaron las operaciones:

- INSERT: registrar pacientes.
- SELECT: consultar registros.
- UPDATE: actualizar datos.
- DELETE: eliminar pacientes.

Se utilizaron consultas preparadas para las operaciones que reciben datos del usuario y se escapó el texto mostrado en HTML.

### 4. Organización de archivos

| Archivo | Función |
|---|---|
| `index.php` | Redirigir a la página principal |
| `conexion_rocky.php` | Conectar con MariaDB |
| `config.local.php` | Guardar las credenciales locales |
| `config.example.php` | Proporcionar un ejemplo de configuración |
| `crud_remoto.php` | Registrar, listar y eliminar pacientes |
| `editar_remoto.php` | Editar los datos de un paciente |
| `formulario_paciente.php` | Compartir el formulario de registro y edición |
| `funciones.php` | Validar datos y gestionar funciones auxiliares |
| `estilos.css` | Definir la presentación visual |
| `.gitignore` | Excluir archivos del repositorio |

Los archivos antiguos `conexion_remotos.php` y `estudiantes_remotos.php` se eliminaron porque ya no eran necesarios.

### 5. Configuración de la conexión

Se separaron las credenciales en `config.local.php`.

Este archivo se excluyó de Git mediante `.gitignore`:

```gitignore
config.local.php
```

El repositorio incluye un archivo `config.example.php` sin credenciales reales para facilitar la configuración en otro equipo.

### 6. Ejecución local

Se mantuvo encendida la máquina virtual Rocky Linux con MariaDB activo y se inició Apache desde XAMPP.

Se accedió al sistema mediante:

```text
http://localhost/sistema_pacientes/
```

El código PHP se ejecuta en Windows y los registros se almacenan en MariaDB de Rocky Linux.

### 7. Configuración de Git

Desde la carpeta del proyecto se inicializó el repositorio y se configuró la identidad del autor:

```powershell
git init
git config user.name "Tu nombre"
git config user.email "tu-correo@example.com"
git branch -M main
```

Los valores de nombre y correo deben corresponder al autor del proyecto.

### 8. Registro y publicación del código

Se agregaron los archivos y se creó el primer commit:

```powershell
git add .
git commit -m "Implementar registro y gestion de pacientes con MariaDB en Rocky"
```

Se creó un repositorio vacío en GitHub y se vinculó con el proyecto local:

```powershell
git remote add origin https://github.com/TU_USUARIO/sistema-pacientes.git
git push -u origin main
```

La dirección del repositorio debe reemplazarse por la URL real.

### 9. Creación de ramas

Se crearon dos ramas adicionales:

```powershell
git branch desarrollo
git branch pruebas
git push -u origin desarrollo
git push -u origin pruebas
```

| Rama | Propósito |
|---|---|
| `main` | Mantener la versión estable |
| `desarrollo` | Implementar modificaciones |
| `pruebas` | Revisar los cambios antes de integrarlos |

Se verificó la existencia de las ramas locales y remotas mediante:

```powershell
git branch -a
```

## Instalación en otro equipo

1. Clonar el repositorio dentro de la carpeta `htdocs` de XAMPP.
2. Crear o seleccionar una base de datos en MariaDB.
3. Ejecutar el SQL de creación de la tabla incluido en este documento.
4. Copiar `config.example.php` como `config.local.php`.
5. Completar la dirección del servidor, usuario, contraseña, base de datos y puerto.
6. Comprobar que el equipo pueda acceder a MariaDB y que el usuario tenga los permisos necesarios.
7. Iniciar Apache y abrir la aplicación en el navegador.

La instalación de PHP debe tener disponibles las extensiones `mysqli` y `mbstring`.

## Estado del proyecto

- Sistema de pacientes funcionando en XAMPP.
- Base de datos alojada en Rocky Linux.
- Código publicado en GitHub.
- Ramas `main`, `desarrollo` y `pruebas` creadas y publicadas.
- Despliegue al hosting pendiente de las credenciales del docente.

Cuando se disponga del hosting, se transferirán los archivos mediante el método indicado por el proveedor, como FTP o SFTP con FileZilla, y se configurará la base de datos del servidor.

La dirección privada de la máquina virtual Rocky no se utilizará directamente como conexión desde el hosting externo.

## Uso académico

Proyecto desarrollado para la asignatura Aplicaciones de Sistemas Operativos. Para las demostraciones se deben utilizar datos ficticios.