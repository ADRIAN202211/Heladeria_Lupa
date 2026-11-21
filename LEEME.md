# Heladería Lupa · Sitio web de pedidos (PHP, local)

Sitio para el cliente (menú, carrito, pedido por WhatsApp, mesas con código QR) y panel para el administrador (pedidos, productos, categorías, mesas, WhatsApp y datos del negocio). Está hecho en PHP con SQLite, así que **no necesita servidor de base de datos ni Composer**.

| Documento | Para qué sirve |
|---|---|
| `LEEME.md` (este archivo) | Instalar, ejecutar, probar, entender el proyecto y usar Git. |
| `docs/manual-usuario.md` | Manual de uso para el cliente y para el administrador del negocio. |
| `CHANGELOG.md` | Lista de cambios visibles por versión. |
| `docs/guia-de-estudio.md` | Guía para estudiar el proyecto y prepararse para sustentarlo. |
| `docs/referencia-del-codigo.md` | Detalle técnico de cada función, con su línea exacta. |
| `AGENTS.md` | Reglas de código y de trabajo para personas y agentes de IA. |

---

## 1. Requisitos

- **PHP 8.1 o superior** con las extensiones `pdo_sqlite` y `mbstring` (vienen activas en la mayoría de instalaciones).
- Un navegador web moderno.
- Git (opcional para usar el sitio; necesario para el flujo de trabajo de la sección 8).

Comprobar en la terminal:

```bash
php -v
php -m | grep -E "pdo_sqlite|mbstring"
```

---

## 2. Ejecutar en local

Desde la carpeta del proyecto:

```bash
bin/iniciar.sh
```

La **primera vez** crea la base de datos (`data/lupa.sqlite`), carga el menú de ejemplo y las mesas, y **muestra la contraseña del panel**. Guárdala: no se vuelve a mostrar.

Luego abre:

| Qué | Dirección |
|---|---|
| Sitio del cliente | http://127.0.0.1:8000/index.php |
| Menú | http://127.0.0.1:8000/index.php?r=menu |
| Panel del administrador | http://127.0.0.1:8000/index.php?r=admin |

- Para otro puerto: `PUERTO=9000 bin/iniciar.sh`.
- Para detener el servidor: `Ctrl+C`.

Si prefieres hacerlo a mano:

```bash
php bin/setup.php                   # crea la base y genera una contraseña
php bin/setup.php --clave=mi-clave  # o pon tu propia contraseña (mínimo 6 caracteres)
php -S 127.0.0.1:8000 -t public
```

### Contraseña del panel

```bash
php bin/setup.php --clave=nueva-clave
```

Cambiar la contraseña **no borra datos**. La contraseña nunca va en el código ni en el repositorio.

### Empezar de cero

```bash
php bin/setup.php --reiniciar       # borra la base y la vuelve a crear
```

Esto borra productos, categorías, mesas, pedidos guardados y ajustes del negocio. Los carritos de los clientes están en la sesión del navegador, así que no se tocan. Las imágenes en `public/uploads/` **no** se borran: bórralas a mano si quieres.

---

## 3. Probar

```bash
bin/verificar.sh
```

Revisa la sintaxis de todos los archivos PHP y corre las pruebas de `tests/run.php`. Esas pruebas usan una base temporal y **no tocan** `data/lupa.sqlite`. El resultado correcto termina con `TODO OK`.

Antes de cada commit, `bin/verificar.sh` debe terminar con `TODO OK` (regla de `AGENTS.md`). Si un cambio corrige un error, agrega primero una prueba que falle con el código anterior.

Además de las pruebas, prueba la página tocada en el navegador con el flujo completo: **menú → carrito → datos → WhatsApp**, y el panel.

---

## 4. Cómo está organizado el proyecto

```
public/          index.php (único punto de entrada), assets/ (CSS, JS, QR), uploads/ (fotos subidas)
src/             lógica: db.php (esquema y ajustes), tienda.php (productos, categorías, mesas),
                 carrito.php, pedidos.php (validación, mensaje y pedidos guardados),
                 ctrl_publico.php / ctrl_admin.php (controladores), rutas.php (tabla de rutas),
                 util.php, bootstrap.php
views/           plantillas: layouts/ (cabecera y pie), publico/ (cliente),
                 admin/ (panel), parciales/ (piezas reutilizables)
config/          config.php: valores iniciales (menú de ejemplo, mesas, datos del negocio)
data/            base SQLite local (lupa.sqlite). NO se versiona.
bin/             setup.php (base y contraseña), iniciar.sh (servidor local), verificar.sh (pruebas)
tests/           run.php: pruebas sin dependencias externas
docs/            manual-usuario.md, guia-de-estudio.md, referencia-del-codigo.md
```

Todas las páginas se abren por `index.php?r=...` (por ejemplo `?r=menu`, `?r=admin/pedidos`). Así funcionan los códigos QR sin configurar el servidor.

### Dónde se cambia cada cosa

| Si quieres cambiar… | Dónde |
|---|---|
| Un precio, una foto o un producto | Panel → **Productos** (no en el código) |
| Categorías | Panel → **Categorías** |
| Mesas y QR | Panel → **Mesas** |
| Número de WhatsApp, domicilio o mensaje | Panel → **WhatsApp y mensaje** |
| Nombre, dirección, horario, contacto, "Sobre nosotros" | Panel → **Negocio y promoción** |
| Colores, tamaños y diseño | `public/assets/style.css` |
| Textos de una página | `views/publico/` o `views/admin/` |
| Reglas del pedido o del carrito | `src/pedidos.php` y `src/carrito.php` (con pruebas) |

---

## 5. Flujo del pedido

1. El cliente elige productos en el menú y los agrega al carrito. El carrito se guarda en su sesión del navegador.
2. Llena **"Datos para tu pedido"**: nombre, teléfono, forma de entrega (domicilio, recoger en tienda o en su mesa), método de pago (Efectivo, Nequi o Transferencia) y una nota opcional. Si elige domicilio, también dirección y barrio.
3. Al enviar, el sitio **valida los datos** (el precio se lee de la base en cada petición; nunca se confía en el precio que llega del navegador).
4. El sitio **guarda el pedido en la base de datos** con estado *pendiente*. Así aparece en el panel, en **Pedidos**.
5. Luego arma el mensaje con la plantilla del panel y abre WhatsApp con el número del negocio.
6. Si WhatsApp no abre, el carrito **se conserva** y el cliente puede volver a intentarlo. El pedido ya quedó guardado en el panel.

**Mesas:** cada QR apunta a `index.php?mesa=Mesa+5`. Al escanearlo, el sitio recuerda la mesa en la sesión y el pedido sale con *"Entrega: En mi mesa (Mesa 5)"*. Si el cliente elige "En mi mesa" sin haber escaneado el QR ni elegido mesa en el menú, el formulario se lo pide.

---

## 6. Códigos QR

- Se generan en el panel, en **Mesas y códigos QR**, usando la dirección que tengas abierta en ese momento (por ejemplo `http://127.0.0.1:8000/...`).
- Los QR se dibujan en el navegador con la librería incluida, así que **no hace falta internet** para verlos o imprimirlos.
- Para que el QR funcione desde un celular, el sitio debe estar publicado o abierto con la IP del equipo en la misma red Wi‑Fi. No funciona con `127.0.0.1`/`localhost` desde otro teléfono.
- **Guardar mesas reemplaza la lista completa.** Si quitas una mesa del texto, desaparece con su QR. Si solo quieres añadir mesas, usa **Generar rápido**, que no duplica las que ya existen.
- Si cambias el dominio al publicar el sitio, vuelve a generar e imprimir los QR.

---

## 7. Pendientes y puntos a revisar

- **Fotos reales de los productos.** Se suben desde el panel, en cada producto. Sin foto, el cliente ve un cono de ejemplo.
- **Productos.** Solo se cargan los 8 productos de ejemplo de `config/config.php`. Los demás se crean desde el panel.
- **Promociones.** La sección existe en el panel pero está pendiente de confirmar su funcionamiento.
- **Aviso de pago.** El texto del sitio dice "Pagas al recibir o por transferencia", pero el formulario también ofrece **Nequi**. Conviene decidir el texto correcto.
- **Publicación en internet.** El proyecto está pensado para uso local. Antes de publicarlo hay que poner HTTPS, cambiar la contraseña, revisar los permisos de `data/` y `public/uploads/`, y regenerar los QR con el dominio nuevo.
- **Estados de pedido.** Los estados son *pendiente, confirmado, entregado* y *cancelado*. Se cambian a mano desde el panel; no hay avisos automáticos.

---

## 8. Control de versiones (Git)

El proyecto es un repositorio Git local. Este apartado explica cómo se trabaja con ramas, commits y cambios.

### 8.1 Ramas

| Rama | Para qué sirve |
|---|---|
| `production` | Versión que está en producción. Solo recibe cambios ya probados en `staging`. |
| `staging` | Pruebas. Lo que se va a publicar se prueba aquí primero. |
| `feature/nombre-corto` | Trabajo de una funcionalidad. Se crea desde `staging`. |
| `fix/nombre-corto` | Corrección de un error. Se crea desde `staging`. |

Nunca se trabaja directamente sobre `production` ni `staging`.

### 8.2 Flujo recomendado

```bash
git switch staging                          # ir a pruebas
git switch -c feature/horario-domingo       # rama propia para el cambio

# ... editar archivos ...

bin/verificar.sh                            # debe terminar con TODO OK
git status                                  # qué cambió
git diff                                    # cómo cambió (sin guardar aún)

git add views/publico/info.php              # agregar solo lo que es de este cambio
git commit -m "fix(info): corregir horario de domingo"
```

Después:

1. Se publica la rama y se abre un **Pull Request a `staging`** (solo cuando la persona responsable lo pida).
2. Se prueba en `staging` (local o servidor de pruebas).
3. Se abre un **Pull Request de `staging` a `production`** cuando todo esté bien probado.

> Regla del proyecto: no hacer `push` ni abrir PR sin que la persona responsable lo pida (ver `AGENTS.md`).

### 8.3 Mensajes de commit

Se usa **Conventional Commits en español**: `tipo(área): descripción`.

| Tipo | Cuándo |
|---|---|
| `feat` | Funcionalidad nueva. |
| `fix` | Corrección de un error. |
| `docs` | Solo documentación (LEEME, manual, CHANGELOG). |
| `style` | Cambios visuales o de formato sin cambiar la lógica. |
| `refactor` | Reorganizar código sin cambiar lo que hace. |
| `test` | Agregar o corregir pruebas. |
| `chore` | Mantenimiento: scripts, configuración, archivos auxiliares. |

Áreas habituales: `sitio`, `menu`, `carrito`, `pedido`, `panel`, `mesas`, `qr`, `whatsapp`, `estilo`, `manual`.

Ejemplos reales del estilo del proyecto:

```
feat(panel): agregar pantalla de pedidos con estado y CSV
fix(carrito): no permitir cantidades mayores a 99
docs(manual): describir la sección de pedidos del panel
style(menu): mejorar tarjetas y foco del teclado
```

Reglas (ver `AGENTS.md`):

- **Commits pequeños**, cada uno con una sola idea.
- Descripción **corta y en imperativo**, en español.
- **Sin marcas de herramientas de IA** en commits ni en Pull Requests: ni `Co-Authored-By` de asistentes, ni "Generated with…". El autor es la persona que hace el commit.

### 8.4 Ver el historial y los cambios

| Qué quieres ver | Comando |
|---|---|
| Rama actual y archivos modificados | `git status` |
| Cambios que aún no están en un commit | `git diff` |
| Cambios ya agregados (listos para commit) | `git diff --staged` |
| Historial resumido | `git log --oneline` |
| Historial con archivos cambiados | `git log --stat` |
| Qué cambió en un commit concreto | `git show <hash>` |
| Historia de un archivo | `git log --follow -- views/publico/menu.php` |
| Qué hay en `staging` y no en `production` | `git log --oneline production..staging` |
| Diferencias entre ramas | `git diff production staging --stat` |
| Quién cambió cada línea | `git blame <archivo>` |

El historial actual tiene un commit inicial: `7fda0f0 feat(sitio): sitio de pedidos de Heladería Lupa`. Los cambios que se van agregando se resumen en `CHANGELOG.md`.

### 8.5 Deshacer cambios (con cuidado)

| Situación | Comando | Efecto |
|---|---|---|
| Quiero descartar un cambio **sin guardar** | `git restore <archivo>` | **Irreversible**: pierdes esos cambios. Revisa `git diff` antes. |
| Quiero sacar un archivo de un commit ya agregado (sin borrar el cambio) | `git restore --staged <archivo>` | Lo quita del commit siguiente; no pierdes el contenido. |
| Quiero deshacer un commit que ya está compartido | `git revert <hash>` | Crea un commit nuevo que lo deshace. Es seguro. |
| Quiero rehacer el último commit local (no publicado) | `git commit --amend` | Reescribe el último commit. Solo si no se ha compartido. |

**No usar** `git reset --hard` ni `git push --force` sobre `production` o `staging`.

### 8.6 Qué se versiona y qué no

Versionado: código, vistas, estilos, pruebas, documentación, `config/config.php`, imágenes de ejemplo (`public/assets/productos/`) y la librería de QR con su licencia.

**No se versiona** (ya está en `.gitignore`):

- `data/*.sqlite` y `data/*.sqlite-*`: la base con pedidos, clientes y ajustes reales.
- `public/uploads/*`: fotos subidas desde el panel (se conserva `.gitkeep`).
- `.env` y cualquier archivo con contraseñas.
- `.DS_Store`.

Los datos de clientes y los teléfonos nunca deben entrar a un commit.

### 8.7 Configuración y finales de línea

- Tu identidad de commit debe ser tuya: `git config user.name "Tu nombre"` y `git config user.email "tu@correo"`. Revisa con `git config user.name`.
- `.gitattributes` fuerza finales de línea **LF** en texto y marca los PNG como binarios.
- Antes de cambiar de rama, revisa `git status`. Si hay cambios sin guardar, guárdalos en un commit o usa `git stash` (`git stash push -m "descripción"` y `git stash pop` para recuperarlos).

---

## 9. Notas técnicas

- La librería de QR (`qrcode` 1.5.4, licencia MIT) va en `public/assets/vendor/`, con su `LICENSE-qrcode.txt`.
- El sitio funciona **sin JavaScript** para comprar. El JS solo agrega el botón de volver arriba, la confirmación antes de borrar, el cálculo del total al elegir domicilio y la generación de los QR en el panel.
- No hay dependencias de Composer ni de npm. Agregar una dependencia requiere acuerdo del equipo.
- Dinero siempre en **pesos enteros** (`int`), mostrado con `dinero()`.
- Las fotos subidas se validan con `getimagesize()` (JPG, PNG o WEBP, máximo 2 MB).
