<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tareas fijas de la propuesta de Workana
    |--------------------------------------------------------------------------
    | Lista fija que ofrece el formulario de Workana. Cada tarea arranca como
    | "no_incluido" y puede pasar a "incluido" o "costo_adicional" con una nota.
    */
    'workana_tasks' => [
        ['key' => 'archivo_fuente',    'label' => 'Archivo fuente'],
        ['key' => 'hosting',           'label' => 'Hosting'],
        ['key' => 'dominio',           'label' => 'Dominio'],
        ['key' => 'ssl',               'label' => 'SSL'],
        ['key' => 'chat',              'label' => 'Chat'],
        ['key' => 'subir_contenido',   'label' => 'Subir el contenido'],
        ['key' => 'pasarela_pago',     'label' => 'Integración con pasarela de pago'],
        ['key' => 'kit_redes',         'label' => 'Kit para redes sociales'],
        ['key' => 'whatsapp_redes',    'label' => 'WhatsApp and Social Networks links'],
        ['key' => 'revision_extra',    'label' => 'Revisión extra'],
        ['key' => 'diseno_adaptativo', 'label' => 'Diseño adaptativo'],
        ['key' => 'api_integration',   'label' => 'API Integration'],
        ['key' => 'speed_optimized',   'label' => 'Speed optimized'],
        ['key' => 'seo_structure',     'label' => 'SEO structure'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Estados de las tareas
    |--------------------------------------------------------------------------
    | El formulario real de Workana solo ofrece dos opciones por tarea:
    | "Incluido" o "Extra" (con un valor en COP obligatorio). Las tareas que no
    | aplican NO se dejan en un tercer estado: se quitan de la propuesta (en
    | Workana se borra la fila con la ×).
    */
    'task_statuses' => [
        'incluido' => 'Incluido',
        'extra' => 'Extra',
    ],

    /*
    |--------------------------------------------------------------------------
    | Estados de seguimiento de la propuesta
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'borrador' => 'Borrador',
        'enviada' => 'Enviada',
        'en_conversacion' => 'En conversación',
        'ganada' => 'Ganada',
        'perdida' => 'Perdida',
    ],
];
