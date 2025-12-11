<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentación API de Tareas</title>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@7.11.0/styles.min.css">
    <style>
        body {
            margin: 0;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at 20% 20%, #f7faff 0, #eef2ff 35%, #e0f2fe 100%);
        }
        header {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        header h1 {
            margin: 0;
            font-size: 1rem;
            letter-spacing: 0.03em;
            color: #111827;
        }
        header span {
            color: #6b7280;
            font-size: 0.85rem;
        }
        main {
            height: calc(100vh - 64px);
        }
        elements-api {
            height: 100%;
            --theme-sidebar-background: #0f172a;
            --theme-sidebar-text: #e2e8f0;
            --theme-primary: #2563eb;
            --theme-text: #0f172a;
        }
    </style>
</head>
<body>
    <header>
        <h1>API de Tareas</h1>
        <span>Stoplight Elements · OpenAPI 3</span>
    </header>
    <main>
        <elements-api
            id="api-docs"
            router="hash"
            layout="sidebar"
        ></elements-api>
    </main>
    <script type="module" src="https://unpkg.com/@stoplight/elements@7.11.0/web-components.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('api-docs');
            if (el) {
                // Set via property to avoid attribute parsing issues
                el.apiDescriptionUrl = "{{ url('/openapi.yaml') }}";
            }
        });
    </script>
</body>
</html>
