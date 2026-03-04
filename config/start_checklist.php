<?php

return [
    // Preguntas fijas del checklist de inicio (Líder de producción).
    // Puedes editar los textos/keys aquí sin tocar el componente.
    'questions' => [
        [
            'key' => 'area_limpia',
            'label' => '¿El área de trabajo está limpia y ordenada?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'epp_completo',
            'label' => '¿Cuentas con EPP completo y en buen estado?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'material_listo',
            'label' => '¿El material requerido está disponible para iniciar?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'equipo_ok',
            'label' => '¿El equipo/máquina está en condiciones para operar?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'seguridad_ok',
            'label' => '¿Se verificaron condiciones de seguridad (guardas, paros, señalización)?',
            'type' => 'boolean',
            'required' => true,
        ],
    ],
];
