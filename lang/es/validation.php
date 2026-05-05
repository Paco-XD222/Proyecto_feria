<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validación Genérica
    |--------------------------------------------------------------------------
    | Aquí se definen los mensajes para todas las reglas de validación.
    */

    'unique' => 'El :attribute ya ha sido registrado.',
    // ... puedes agregar más reglas comunes si es necesario, pero para tu caso,
    // nos centraremos en 'custom' y 'attributes'.

    /*
    |--------------------------------------------------------------------------
    | Mensajes de Validación Personalizados (Custom)
    |--------------------------------------------------------------------------
    | Esta sección te permite anular mensajes para atributos/reglas específicos.
    | Usaremos esto para los mensajes amigables con emojis.
    */

    'custom' => [
        'email' => [
            // Sobreescribir el mensaje de la regla 'unique' para el campo 'email'
            'unique' => '❌ **¡Error de Validación!** El correo electrónico que intentas registrar ya está asociado a otra cuenta. Por favor, usa una dirección diferente.',
        ],
        'nro_kardex' => [
            // Sobreescribir el mensaje de la regla 'unique' para el campo 'nro_kardex'
            'unique' => '🛑 **¡Error de Validación!** El número de Kardex (Ficha de Afiliado) ingresado ya se encuentra registrado. Por favor, verifica el número y su unicidad.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres de Atributos Personalizados
    |--------------------------------------------------------------------------
    | Estos nombres se usan cuando no se define un mensaje 'custom' y se usa
    | el mensaje genérico (ej. ':attribute').
    */

    'attributes' => [
        'email' => 'correo electrónico',
        'nro_kardex' => 'Número de Kardex',
    ],
    
];