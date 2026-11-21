# Planeación del proyecto · Heladería Lupa

Plan de trabajo del sitio de pedidos de Heladería Lupa: qué ya está hecho, qué sigue, quién responde por cada parte y qué riesgos hay que vigilar. Se actualiza cada vez que se cierra una fase.

## 1. Objetivo y alcance

**Objetivo:** que el cliente pueda ver el menú, armar su pedido y enviarlo por WhatsApp, y que el negocio lo gestione desde un panel, sin depender de mensajes manuales.

**Incluido:** menú, carrito, pedido con WhatsApp, mesas con QR, panel del administrador (pedidos, productos, categorías, mesas, WhatsApp y datos del negocio), página de información y de contacto.

**No incluido por ahora:** pagos en línea, cuentas de cliente, avisos automáticos por WhatsApp o correo, varios administradores con roles, publicación en internet.

## 2. Fases

| Fase | Contenido | Estado |
|---|---|---|
| **F0 · Sitio base** | Menú, carrito, pedido por WhatsApp, panel, pruebas y `bin/verificar.sh`. Versión inicial en `production`. | Cerrada |
| **F1 · Estética y documentación** | Mejoras visuales, móvil y botón de WhatsApp. Manual, LEEME, CHANGELOG y AGENTS. Rama `feature/estetica-y-documentacion`. | Subida a GitHub |
| **F2 · Correcciones** | Sección del menú que no se cerraba. Aviso cuando se cambia el estado de un pedido que ya no existe. | Subida a GitHub |
| **F3 · Contacto** | Página de contacto con formulario que abre WhatsApp. Rama `feature/pestana-contacto`. | Pendiente de revisión |
| **F4 · Documentación de estudio y planeación** | Guía de estudio, referencia del código, este plan y las skills del agente. | En curso |
| **F5 · Pendientes del negocio** | Ver sección 4. | Pendiente |

## 3. Desglose del trabajo (EDT)

| Paquete | Entregables | Verificación |
|---|---|---|
| 3.1 Pedido y carrito | `src/carrito.php`, `src/pedidos.php`, `views/publico/` | `bin/verificar.sh` y flujo menú → carrito → datos → WhatsApp |
| 3.2 Panel | `src/ctrl_admin.php`, `views/admin/` | Entrar, cambiar un estado, descargar CSV |
| 3.3 Diseño | `public/assets/style.css`, `views/layouts/` | Revisión en escritorio y celular |
| 3.4 Documentación | `LEEME.md`, `docs/`, `CHANGELOG.md` | Cada cambio visible actualiza el manual y el CHANGELOG |
| 3.5 Control de versiones | Ramas, commits y Pull Requests | Commits pequeños, sin datos reales ni marca de IA |

## 4. Pendientes

| Pendiente | Por qué importa | Propuesta |
|---|---|---|
| Texto de pago inconsistente | El sitio dice "al recibir o por transferencia" y ofrece Nequi. | Decidir el texto correcto y ajustarlo. |
| Promociones | La sección existe pero no aplica descuentos. | Definir la regla antes de programarla. |
| Límite de intentos al entrar al panel | Cualquiera podría probar contraseñas sin freno. | Bloquear tras varios intentos fallidos. |
| HTTPS y publicación | El proyecto solo funciona en local. | Definir hosting y certificado antes de publicar. |
| Guardar mesas reemplaza toda la lista | Una mesa quitada pierde su QR. | Cambiar a agregar o quitar mesas por separado. |
| Listado de pedidos limitado a 300 | Con mucho volumen no se ven los pedidos antiguos. | Agregar paginación. |

## 5. Riesgos

| Riesgo | Probabilidad | Impacto | Respuesta |
|---|---|---|---|
| Subir datos reales (pedidos, teléfonos, contraseñas) a Git | Baja | Alto | `.gitignore` excluye la base, las fotos y `.env`. Revisar `git status` antes de cada commit. |
| Cambios del agente que no se revisan | Media | Alto | Cada cambio pasa por `bin/verificar.sh`, una prueba que falle antes de la corrección y una revisión humana. |
| Ramas mal creadas o copias de trabajo desalineadas | Media | Medio | Crear ramas desde `staging`, revisar `git log` antes de subir y usar una sola copia de trabajo. |
| Permisos de los scripts (`bin/`) perdidos en Windows | Media | Bajo | Restaurar el permiso de ejecución con `git update-index --chmod=+x` y verificarlo en el repositorio. |
| Documentación que no coincide con el código | Media | Medio | Revisar los números (por ejemplo, las pruebas) en `LEEME.md` y en `docs/` al cerrar cada fase. |

## 6. Control de cambios

1. Cada cambio nace de una rama propia creada desde `staging`.
2. Antes del commit, `bin/verificar.sh` debe terminar en `TODO OK`.
3. Si el cambio altera lo que ve el cliente o el panel, se actualizan el manual y el `CHANGELOG.md` en el mismo commit.
4. Se sube la rama y se abre un Pull Request a `staging` solo cuando la persona responsable lo pide.
5. `staging` pasa a `production` cuando se prueba el flujo completo.

## 7. Criterios de aceptación

- El flujo completo funciona: menú → carrito → datos → WhatsApp, y el pedido aparece en el panel.
- Las pruebas pasan y el commit tiene un mensaje claro en formato `tipo(área): descripción`.
- La documentación coincide con el comportamiento actual.
- No hay datos reales, contraseñas ni marcas de herramientas de IA en el repositorio.
