# Despliegue gratis: Supabase + Render (Docker)

## 1) Supabase
1. Crea un proyecto en Supabase.
2. Ve a **SQL Editor** y ejecuta `supabase_schema.sql`.
3. Crea un bucket en **Storage** llamado `profiles` y márcalo como **Public** (para usar URLs públicas).
   - URL pública convencional: `https://<project>.supabase.co/storage/v1/object/public/<bucket>/<path>`.

## 2) Crear usuario admin inicial
Tu app ya no usa JSON. Necesitas al menos 1 usuario en la tabla `users`.

1. Genera un hash con PHP local:
   ```bash
   php -r "echo password_hash('MiPass123!', PASSWORD_DEFAULT), PHP_EOL;"
   ```
2. Inserta el usuario en Supabase SQL Editor reemplazando el hash:
   ```sql
   insert into public.users (name, email, password_hash, must_change_password)
   values ('Admin', 'admin@demo.com', '<PEGAR_HASH>', false);
   ```

## 3) Render
1. Sube este repo a GitHub.
2. En Render: **New +** -> **Web Service** -> conecta tu repo -> elige **Docker**.
3. Agrega variables de entorno (Environment):

### Base de datos (Supabase Postgres)
- `DB_DRIVER=pgsql`
- `DB_HOST=...`
- `DB_PORT=5432`
- `DB_NAME=...`
- `DB_USER=...`
- `DB_PASS=...`
- `DB_SSLMODE=require`

> Usa la cadena de conexión **Direct connection** o **Session pooler** desde Supabase Connect.
> Si usas pooler en modo transaction, puede dar problemas con prepared statements.

### Supabase Storage
- `SUPABASE_URL=https://<project>.supabase.co`
- `SUPABASE_SERVICE_ROLE_KEY=...`  (NO la expongas en frontend)
- `SUPABASE_STORAGE_BUCKET=profiles`

### SMTP (PHPMailer)
- `MAIL_FROM_EMAIL=...`
- `MAIL_FROM_NAME=...`
- `MAIL_HOST=smtp.gmail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME=...`
- `MAIL_PASSWORD=...` (app password recomendado)
- `MAIL_ENCRYPTION=tls`
- `MAIL_AUTH=true`

Render expone el puerto en la variable `PORT` (por defecto 10000). El Dockerfile ya lo usa.

## 4) Notas importantes
- En Render free, el servicio puede “dormirse” si no hay tráfico. La primera petición será lenta.
- En Render el filesystem no es persistente: por eso subimos fotos a Supabase Storage.

### Si usas Pooler en *transaction mode*
- `DB_EMULATE_PREPARES=true`
(Así evitas errores por prepared statements.)
