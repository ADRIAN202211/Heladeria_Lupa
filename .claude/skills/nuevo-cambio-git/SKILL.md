---
name: nuevo-cambio-git
description: Usa esta skill para preparar un cambio de Heladería Lupa en Git con rama propia desde staging, commits pequeños en formato Conventional Commits en español y sin marca de IA.
---

# Preparar un cambio en Git

## Reglas del proyecto

- Trabaja siempre en una rama propia creada desde `staging`, con nombre `feature/...` o `fix/...`. Nunca trabajes directamente en `production` ni en `staging`.
- No hagas `push` ni abras un Pull Request si la persona responsable no lo pidió.
- Cada commit tiene una sola idea y usa el formato `tipo(área): descripción`, en español e imperativo.
- No agregues `Co-Authored-By` de asistentes, ni "Generated with", ni emojis de herramientas de IA. El autor es la persona que hace el commit.
- No subas `data/*.sqlite`, `public/uploads/*`, `.env` ni contraseñas.

## Pasos

1. Confirma la rama con `git branch --show-current` y que `git status` esté limpio, o que los cambios pendientes sean de este trabajo.
2. Crea la rama desde `staging`: `git switch staging` y luego `git switch -c feature/nombre-corto`.
3. Haz los cambios y verifica con la skill `verificar-cambio`.
4. Agrega solo los archivos de este cambio con `git add <archivo>`. Evita `git add .` si hay archivos ajenos.
5. Revisa lo que entra con `git diff --staged`.
6. Haz el commit con un mensaje claro, por ejemplo `fix(carrito): no permitir cantidades mayores a 99`.
7. Sube la rama solo cuando se pida, con `git push -u origin feature/nombre-corto`.

## Tipos de commit

| Tipo | Cuándo |
|---|---|
| `feat` | Funcionalidad nueva. |
| `fix` | Corrección de un error. |
| `docs` | Solo documentación. |
| `style` | Cambios visuales sin cambiar la lógica. |
| `refactor` | Reorganizar código sin cambiar lo que hace. |
| `test` | Agregar o corregir pruebas. |
| `chore` | Mantenimiento: scripts, configuración y archivos auxiliares. |
