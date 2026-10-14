# Fact+ — Sistema de Facturación e Inventario

Proyecto final de la asignatura **Desarrollo de Proyectos con Software Libre** — Universidad Abierta para Adultos (UAPA).

**Estudiante:** Kelvin Arias

Fact+ es una aplicación web para pequeños negocios dominicanos (colmados, ferreterías, tiendas) que permite llevar el control del inventario y emitir facturas con **Número de Comprobante Fiscal (NCF)** y cálculo de **ITBIS (18%)**.

## Funcionalidades

- **Inicio de sesión** de usuarios.
- **Usuarios y permisos**: CRUD de usuarios con acceso por módulo (facturación, clientes, productos, inventario, NCF, reportes, empresa, usuarios) y un permiso aparte para anular facturas. Los usuarios se pueden desactivar sin borrarlos, y cada uno puede cambiar su propia contraseña.
- **Clientes**: registro con validación de cédula (11 dígitos) y RNC (9 dígitos) usando el dígito verificador.
- **Categorías** y **Productos**: precio de costo y de venta, existencia mínima y productos exentos de ITBIS.
- **Inventario**: entradas, salidas y ajustes, con historial (kardex) de cada producto.
- **Comprobantes fiscales**: secuencias B01 (Crédito Fiscal), B02 (Consumidor Final), B14 y B15, con control de vencimiento y cantidad disponible.
- **Facturación**: asigna el NCF de forma automática, calcula descuento e ITBIS, descuenta el inventario, permite imprimir y anular (al anular devuelve los productos al inventario).
- **Panel de inicio** con ventas del día y del mes, gráfica de los últimos 7 días y productos con existencia baja.
- **Reportes** de ventas por rango de fechas e inventario valorizado.

## Tecnologías (todas libres)

| Componente | Herramienta |
|---|---|
| Sistema operativo | Ubuntu Server 26.04 LTS |
| Servidor web | Apache 2.4 |
| Lenguaje | PHP 8.5 |
| Framework | Laravel 12 |
| Base de datos | PostgreSQL 18 |
| Interfaz | Blade, Bootstrap 5, Bootstrap Icons, Chart.js |
| Control de versiones | Git / GitHub |

## Estructura principal

```
app/
  Http/Controllers/   Controladores (Clientes, Productos, Facturas, Inventario, NCF, Reportes...)
  Models/             Modelos Eloquent
  Rules/              Validación de cédula y RNC
database/
  migrations/         Tablas de la base de datos
  seeders/            Datos de prueba
resources/views/      Vistas Blade
routes/web.php        Rutas del sistema
deploy/factplus.conf  Configuración de Apache
```

## Modelo de datos

- `empresas` — datos del negocio y porcentaje de ITBIS
- `clientes` — clientes con cédula o RNC
- `categorias` / `productos`
- `secuencias_ncf` — secuencias autorizadas por la DGII
- `facturas` / `factura_detalles`
- `movimientos_inventario` — kardex (entradas, salidas y ajustes)
- `users` — usuarios, con los campos `es_admin`, `activo` y `permisos`

## Instalación en el servidor (Ubuntu)

### 1. Instalar dependencias

```bash
sudo apt update
sudo apt install -y apache2 libapache2-mod-php php php-pgsql php-mbstring php-xml php-curl php-zip php-bcmath php-intl composer postgresql git unzip
```

### 2. Crear la base de datos

```bash
sudo -u postgres psql -c "CREATE ROLE factplus LOGIN PASSWORD 'cambiar_esta_clave';"
sudo -u postgres createdb -O factplus factplus
```

### 3. Descargar el proyecto e instalar

```bash
sudo git clone https://github.com/I-Kelvin/FactPlus.git /var/www/factplus
sudo chown -R $USER:www-data /var/www/factplus
cd /var/www/factplus
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Editar el archivo `.env` con los datos de la base de datos y poner:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://IP_DEL_SERVIDOR
DB_PASSWORD=cambiar_esta_clave
```

### 4. Crear las tablas y los datos de prueba

```bash
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Permisos

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 6. Configurar Apache

```bash
sudo cp deploy/factplus.conf /etc/apache2/sites-available/factplus.conf
sudo a2enmod rewrite
sudo a2dissite 000-default.conf
sudo a2ensite factplus.conf
sudo systemctl reload apache2
sudo ufw allow 'Apache'
```

Luego se abre en el navegador `http://IP_DEL_SERVIDOR`.

## Usuarios de prueba

| Correo | Contraseña | Acceso |
|---|---|---|
| admin@factplus.do | admin123 | Administrador (todo) |
| cajero@factplus.do | demo1234 | Facturación y clientes |
| almacen@factplus.do | demo1234 | Productos, inventario y reportes |

> Se recomienda cambiar las contraseñas después de instalar (menú del usuario → Cambiar contraseña). También se pueden definir `ADMIN_PASSWORD` y `DEMO_PASSWORD` en el `.env` antes de ejecutar el seeder.

### Cómo funcionan los permisos

Cada usuario tiene una lista de módulos permitidos (campo `permisos` en la tabla `users`). En `AppServiceProvider` se crea un *Gate* por cada módulo, y las rutas usan el middleware `can:` (por ejemplo `can:clientes`). En las vistas, `@can('clientes')` oculta las opciones del menú que el usuario no puede usar. Si alguien intenta entrar por la URL, el sistema muestra la página de acceso denegado (403).

## Actualizar el sistema en el servidor

```bash
cd /var/www/factplus
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```
