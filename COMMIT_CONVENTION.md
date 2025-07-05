# Convención de Commits – PHPost 2025

Este proyecto sigue una convención de commits basada en [Conventional Commits](https://www.conventionalcommits.org/es/v1.0.0/), con algunas personalizaciones para adaptarse a PHPost 2025.

## 🎯 Estructura general
tipo(modulo): descripción breve en tiempo presente

- Todo en minúsculas
- Usar el infinitivo si es más natural en español
- La descripción debe ser corta pero clara

---

## 🧾 Tipos de commit permitidos
| Tipo        | Propósito                                         | Ejemplo                                          |
|-------------|--------------------------------------------------|--------------------------------------------------|
| `feat`      | Nueva funcionalidad                               | `feat(user): agregar sistema de reputación`      |
| `fix`       | Corrección de errores                             | `fix(posts): corregir error de paginación`       |
| `refactor`  | Reorganizar código sin cambiar funcionalidad      | `refactor(core): dividir métodos de tsCore`      |
| `style`     | Cambios visuales o de formato (no funcionalidad)  | `style(ui): nuevo color para botón publicar`     |
| `docs`      | Cambios en documentación                          | `docs: actualizar sección de instalación`        |
| `test`      | Agregado o ajuste de pruebas                      | `test(user): test para validación de emails`     |
| `chore`     | Cambios menores sin impacto funcional             | `chore: borrar archivos antiguos de prueba`      |
| `perf`      | Mejoras de rendimiento                            | `perf(db): optimizar consulta de comentarios`    |
| `build`     | Cambios en dependencias o configuración externa   | `build: configurar composer.json y autoload`     |
| `ci`        | Cambios en integración continua o workflows       | `ci: configurar GitHub Actions`                  |
| `revert`    | Reversión de un commit anterior                   | `revert: volver a versión estable del login`     |

---

## 📌 Ejemplos comunes
- feat(core): añadir soporte para PHP 8.3
- fix(theme): corregir bug al cargar portada de post
- refactor(db): simplificar método de inserción
- docs(readme): agregar lista de módulos en progreso
- style(user): aplicar animación a notificaciones