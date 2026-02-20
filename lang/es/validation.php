<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación en español
    |--------------------------------------------------------------------------
    |
    | Estos textos se utilizan para mostrar los errores de validación
    | cuando se usan las reglas estándar como "required", "max", etc.
    | Puedes ir agregando/ajustando mensajes según lo que necesites.
    |
    */

    'required' => 'El campo :attribute es obligatorio.',

    'string' => 'El campo :attribute debe ser una cadena de texto.',

    'numeric' => 'El campo :attribute debe ser un número válido.',

    'date' => 'El campo :attribute debe ser una fecha válida.',

    'max' => [
        'string' => 'El campo :attribute no debe contener más de :max caracteres.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'file' => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
    ],

    'unique' => 'El campo :attribute ya ha sido utilizado.',

    // Aquí puedes definir nombres "bonitos" para los atributos globales.
    // Los específicos de cada formulario también se pueden pasar directamente
    // en el tercer parámetro de $this->validate() o $request->validate().
    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'name' => 'nombre',
        'player_name' => 'nombre del jugador',
    ],

];
