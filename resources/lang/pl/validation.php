<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute musi być akceptowany.',
    'active_url' => ":attribute nie jest poprawnym URL'em.",
    'after' => ':attribute musi być datą po :date.',
    'after_or_equal' => ':attribute musi być datą po albo tą samą datą co :date.',
    'alpha' => ':attribute może zawierać tylko litery.',
    'alpha_dash' => ':attribute może zawierać litery, liczby, kreski i podkreślenia.',
    'alpha_num' => ':attribute może zawierać litery i liczby.',
    'array' => ':attribute musi być tablicą.',
    'before' => ':attribute musi być datą przed :date.',
    'before_or_equal' => ':attribute musi być datą przed albo tą samą datą co :date.',
    'between' => [
        'numeric' => ':attribute musi być między :min i :max.',
        'file' => ':attribute musi mieć między :min i :max kilobajtów.',
        'string' => ':attribute musi mieć między :min i :max znaków.',
        'array' => ':attribute musi mieć między :min i :max elementów.',
    ],
    'boolean' => 'Pole :attribute musi być true albo false.',
    'confirmed' => 'Potwierdzenie :attribute nie pasuje.',
    'date' => ':attribute nie jest poprawną datą.',
    'date_format' => ':attribute nie pasuje do formatu daty :format.',
    'different' => ':attribute i :other muszą być różne.',
    'digits' => ':attribute musi mieć :digits cyfr.',
    'digits_between' => ':attribute musi mieć między :min i :max  cyfr.',
    'dimensions' => ':attribute ma niepoprawne rozmiary obrazu.',
    'distinct' => ':attribute pole ma zdublowaną wartość.',
    'email' => ':attribute musi być poprawnym adresem email.',
    'exists' => 'Wybrany :attribute jest niepoprawny.',
    'file' => ':attribute musi być plikiem.',
    'filled' => 'Pole :attribute musi mieć wartość.',
    'gt' => [
        'numeric' => ':attribute musi być większy niż :value.',
        'file' => ':attribute musi być większy niż :value kilobajtów.',
        'string' => ':attribute musi mieć więcej niż :value znaków.',
        'array' => ':attribute musi mieć więcej niż :value elementów.',
    ],
    'gte' => [
        'numeric' => ':attribute musi być większy lub równy :value.',
        'file' => ':attribute musi być większy niż lub równy :value kilobajtów.',
        'string' => ':attribute musi mieć więcej lub :value znaków.',
        'array' => ':attribute musi mieć więcej lub :value elementów.',
    ],
    'image' => ':attribute musi być obrazem.',
    'in' => 'Wybrany :attribute jest niepoprawny.',
    'in_array' => 'Pole :attribute nie istnieje w :other.',
    'integer' => ':attribute musi być liczbą całkowitą.',
    'ip' => ':attribute musi być poprawnym adresem IP.',
    'ipv4' => ':attribute musi być poprawnym adresem IPv4.',
    'ipv6' => ':attribute musi być poprawnym adresem IPv6.',
    'json' => ':attribute musi być poprawnym łańcuchem JSON.',
    'lt' => [
        'numeric' => ':attribute musi być mniejszy niż :value.',
        'file' => 'The :attribute musi być mniejszy niż :value kilobajtów.',
        'string' => 'The :attribute musi mieć mniej niż :value znaków.',
        'array' => 'The :attribute musi mieć mniej niż :value elementów.',
    ],
    'lte' => [
        'numeric' => ':attribute musi być mniejszy lub równy :value.',
        'file' => ':attribute musi być mniejszy niż lub równy :value kilobajtów.',
        'string' => ':attribute musi mieć mniej lub :value znaków.',
        'array' => ':attribute musi mieć mniej lub :value elementów.',
    ],
    'max' => [
        'numeric' => ':attribute nie może być większy niż :max.',
        'file' => ':attribute nie może być większy niż :max kilobajtów.',
        'string' => ':attribute nie może mieć więcej niż :max znaków.',
        'array' => ':attribute nie może mieć więcej niż :max elementów.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attribute musi być nie mniejszy niż :min.',
        'file' => ':attribute musi być nie mniejszy niż :min kilobajtów.',
        'string' => ':attribute musi być nie mniej niż :min znaków.',
        'array' => ':attribute musi być nie mniej niż :min elementów.',
    ],
    'not_in' => 'Wybrany :attribute jest niepoprawny.',
    'not_regex' => 'Format :attribute jest niepoprawny.',
    'numeric' => ':attribute musi być liczbą.',
    'present' => 'Pole :attribute musi być podany.',
    'regex' => 'Format :attribute jest niepoprawny.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other ma :value.',
    'required_unless' => 'Pole :attribute jest wymagane chyba, że :other jest w :values.',
    'required_with' => 'Pole :attribute jest wymagane gdy :values jest podane.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy :values są podane.',
    'required_without' => 'Pole :attribute jest wymagane gdy :values nie jest podane.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy :values nie są podane.',
    'same' => ':attribute i :other muszą się zgadzać.',
    'size' => [
        'numeric' => ':attribute musi by :size.',
        'file' => ':attribute musi mieć :size kilobajtów.',
        'string' => ':attribute musi mieć :size znaków.',
        'array' => ':attribute musi zawierać :size elementów.',
    ],
    'string' => ':attribute musi być łańcuchem.',
    'timezone' => ':attribute musi być poprawną strefą czasową.',
    'unique' => 'Taki :attribute został już podany.',
    'uploaded' => 'Wgranie :attribute nie powiodło się.',
    'url' => 'Format :attribute jest niepoprawny.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
