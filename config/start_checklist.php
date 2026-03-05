<?php

return [
    // Preguntas fijas del checklist de inicio (Líder de producción).
    // Puedes editar los textos/keys aquí sin tocar el componente.
    'questions' => [
        [
            'key' => 'pregunta_1',
            'label' => '¿Recursos humano completo?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'pregunta_2',
            'label' => '¿Cuentas con la materia prima optima para tu trabajo?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'pregunta_3',
            'label' => '¿Los equipos rectificadores y las conexiones eléctricas están en buen estado?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'pregunta_4',
            'label' => '¿Las tinas y arcos están bien conectados?',
            'type' => 'boolean',
            'required' => true,
        ],
        [
            'key' => 'pregunta_5',
            'label' => 'Si usas polipasto o grúa, ¿esta funcionando correctamente?',
            'type' => 'boolean',
            'required' => true,
        ],

        [
            'key' => 'pregunta_6',
            'label' => '¿Los ventiladores y sopladores están en buen estado?',
            'type' => 'boolean',
            'required' => true,
        ],
    ],
];
