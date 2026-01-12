# Reglas de UI (WTS Cotizador)

## Prioridad de Componentes
1. **Primera regla:** usar componentes nativos de **Filament** siempre que sea posible.
2. **Segunda regla:** si Filament no cubre el caso, usar una **librería/biblioteca compatible con Laravel/Filament** que resuelva ese componente (ej: chat).

## Consistencia de Librerías
- Si ya existe una librería para un tipo de componente, **usar esa misma** en nuevas iteraciones.
- **No agregar una librería nueva** para el mismo tipo de componente sin revisar primero las que ya usamos.
- Mantener consistencia visual y técnica.

> Solo usar componentes 100% custom cuando no exista alternativa viable en Filament o en librerías compatibles.
