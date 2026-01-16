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

    'accepted' => ':attribute должен быть принят.',
    'accepted_if' => ':attribute должен быть принят, когда :other равен :value.',
    'active_url' => ':attribute не является допустимым URL.',
    'after' => ':attribute должен быть датой после :date.',
    'after_or_equal' => ':attribute должен быть датой после или равной :date.',
    'alpha' => ':attribute должен содержать только буквы.',
    'alpha_dash' => ':attribute должен содержать только буквы, цифры, дефисы и подчеркивания.',
    'alpha_num' => ':attribute должен содержать только буквы и цифры.',
    'array' => ':attribute должен быть массивом.',
    'ascii' => ':attribute должен содержать только однобайтовые буквы и символы.',
    'before' => ':attribute должен быть датой до :date.',
    'before_or_equal' => ':attribute должен быть датой до или равной :date.',
    'between' => [
        'array' => ':attribute должен содержать от :min до :max элементов.',
        'file' => ':attribute должен быть размером от :min до :max килобайт.',
        'numeric' => ':attribute должен быть между :min и :max.',
        'string' => ':attribute должен быть длиной от :min до :max символов.',
    ],
    'boolean' => 'Поле :attribute должно быть истинным или ложным.',
    'confirmed' => 'Подтверждение поля «:attribute» не совпадает.',
    'current_password' => 'Пароль неверный.',
    'date' => ':attribute не является допустимой датой.',
    'date_equals' => ':attribute должен быть датой, равной :date.',
    'date_format' => ':attribute не соответствует формату :format.',
    'decimal' => ':attribute должен иметь :decimal десятичных знаков.',
    'declined' => ':attribute должен быть отклонен.',
    'declined_if' => ':attribute должен быть отклонен, когда :other равен :value.',
    'different' => ':attribute и :other должны быть разными.',
    'digits' => ':attribute должен состоять из :digits цифр.',
    'digits_between' => ':attribute должен быть длиной от :min до :max цифр.',
    'dimensions' => ':attribute имеет недопустимые размеры изображения.',
    'distinct' => 'Поле :attribute содержит повторяющееся значение.',
    'doesnt_end_with' => ':attribute не должен заканчиваться одним из следующих значений: :values.',
    'doesnt_start_with' => ':attribute не должен начинаться с одного из следующих значений: :values.',
    'email' => ':attribute должен быть допустимым адресом электронной почты.',
    'ends_with' => ':attribute должен заканчиваться одним из следующих значений: :values.',
    'enum' => 'Выбранный :attribute недопустим.',
    'exists' => 'Выбранный :attribute недопустим.',
    'file' => ':attribute должен быть файлом.',
    'filled' => 'Поле :attribute должно иметь значение.',
    'gt' => [
        'array' => ':attribute должен содержать более :value элементов.',
        'file' => ':attribute должен быть больше :value килобайт.',
        'numeric' => ':attribute должен быть больше :value.',
        'string' => ':attribute должен быть длиной больше :value символов.',
    ],
    'gte' => [
        'array' => ':attribute должен содержать :value элементов или больше.',
        'file' => ':attribute должен быть больше или равен :value килобайтам.',
        'numeric' => ':attribute должен быть больше или равен :value.',
        'string' => ':attribute должен быть длиной больше или равной :value символов.',
    ],
    'image' => ':attribute должен быть изображением.',
    'in' => 'Выбранный :attribute недопустим.',
    'in_array' => 'Поле :attribute не существует в :other.',
    'integer' => ':attribute должен быть целым числом.',
    'ip' => ':attribute должен быть допустимым IP-адресом.',
    'ipv4' => ':attribute должен быть допустимым IPv4-адресом.',
    'ipv6' => ':attribute должен быть допустимым IPv6-адресом.',
    'json' => ':attribute должен быть допустимой строкой JSON.',
    'lowercase' => ':attribute должен быть в нижнем регистре.',
    'lt' => [
        'array' => ':attribute должен содержать менее :value элементов.',
        'file' => ':attribute должен быть меньше :value килобайт.',
        'numeric' => ':attribute должен быть меньше :value.',
        'string' => ':attribute должен быть длиной меньше :value символов.',
    ],
    'lte' => [
        'array' => ':attribute не должен содержать более :value элементов.',
        'file' => ':attribute должен быть меньше или равен :value килобайтам.',
        'numeric' => ':attribute должен быть меньше или равен :value.',
        'string' => ':attribute должен быть длиной меньше или равной :value символов.',
    ],
    'mac_address' => ':attribute должен быть допустимым MAC-адресом.',
    'max' => [
        'array' => ':attribute не должен содержать более :max элементов.',
        'file' => ':attribute не должен быть больше :max килобайт.',
        'numeric' => ':attribute не должен быть больше :max.',
        'string' => ':attribute не должен содержать больше :max символов.',
    ],
    'max_digits' => ':attribute не должен содержать больше :max цифр.',
    'mimes' => ':attribute должен быть файлом типа: :values.',
    'mimetypes' => ':attribute должен быть файлом типа: :values.',
    'min' => [
        'array' => ':attribute должен содержать как минимум :min элементов.',
        'file' => ':attribute должен быть размером не менее :min килобайт.',
        'numeric' => ':attribute должен быть как минимум :min.',
        'string' => ':attribute должен содержать как минимум :min символов.',
    ],
    'min_digits' => ':attribute должен содержать как минимум :min цифр.',
    'missing' => 'Поле :attribute должно отсутствовать.',
    'missing_if' => 'Поле :attribute должно отсутствовать, когда :other равно :value.',
    'missing_unless' => 'Поле :attribute должно отсутствовать, если :other не равно :value.',
    'missing_with' => 'Поле :attribute должно отсутствовать, когда :values присутствует.',
    'missing_with_all' => 'Поле :attribute должно отсутствовать, когда все значения :values присутствуют.',
    'multiple_of' => ':attribute должен быть кратным :value.',
    'not_in' => 'Выбранный :attribute недопустим.',
    'not_regex' => 'Формат :attribute недопустим.',
    'numeric' => ':attribute должен быть числом.',
    'password' => [
        'letters' => ':attribute должен содержать хотя бы одну букву.',
        'mixed' => ':attribute должен содержать хотя бы одну заглавную и одну строчную букву.',
        'numbers' => ':attribute должен содержать хотя бы одну цифру.',
        'symbols' => ':attribute должен содержать хотя бы один символ.',
        'uncompromised' => 'Указанный :attribute встречается в утечке данных. Пожалуйста, выберите другой :attribute.',
    ],
    'present' => 'Поле :attribute должно присутствовать.',
    'prohibited' => 'Поле :attribute запрещено.',
    'prohibited_if' => 'Поле :attribute запрещено, когда :other равно :value.',
    'prohibited_unless' => 'Поле :attribute запрещено, если :other не содержится в :values.',
    'prohibits' => 'Поле :attribute запрещает присутствие :other.',
    'regex' => 'Формат :attribute недопустим.',
    'required' => 'Поле :attribute обязательно для заполнения.',
    'required_array_keys' => 'Поле :attribute должно содержать записи для: :values.',
    'required_if' => 'Поле :attribute обязательно для заполнения, когда :other равно :value.',
    'required_if_accepted' => 'Поле :attribute обязательно для заполнения, когда :other принято.',
    'required_unless' => 'Поле :attribute обязательно для заполнения, если :other не содержится в :values.',
    'required_with' => 'Поле :attribute обязательно для заполнения, когда :values присутствует.',
    'required_with_all' => 'Поле :attribute обязательно для заполнения, когда все значения :values присутствуют.',
    'required_without' => 'Поле :attribute обязательно для заполнения, когда :values отсутствует.',
    'required_without_all' => 'Поле :attribute обязательно для заполнения, когда ни одно из значений :values не присутствует.',
    'same' => ':attribute и :other должны совпадать.',
    'size' => [
        'array' => ':attribute должен содержать :size элементов.',
        'file' => ':attribute должен быть размером :size килобайт.',
        'numeric' => ':attribute должен быть равен :size.',
        'string' => ':attribute должен содержать :size символов.',
    ],
    'starts_with' => ':attribute должен начинаться с одного из следующих значений: :values.',
    'string' => ':attribute должен быть строкой.',
    'timezone' => ':attribute должен быть допустимой временной зоной.',
    'unique' => ':attribute уже занят.',
    'uploaded' => ':attribute не удалось загрузить.',
    'uppercase' => ':attribute должен быть в верхнем регистре.',
    'url' => ':attribute должен быть допустимым URL-адресом.',
    'ulid' => ':attribute должен быть допустимым ULID.',
    'uuid' => ':attribute должен быть допустимым UUID.',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Наименование',
        'description' => 'Описание',
        'order' => 'Порядок',
        'access_date' => 'Дата доступа до',
        'access_days' => 'Количество дней доступа',
        'lessons' => 'Уроки',
        'lessons.*.name' => 'Наименование урока',
        'lessons.*.description' => 'Описание урока',
        'lessons.*.link' => 'Ссылка на урок',
        'lessons.*.link' => 'Ссылка на урок',
        'price' => 'Цена',
        'discount_scedule' => 'Расписание скидок',
        'discount_scedule.*.price' => 'Стоимость со скидкой',
        'discount_scedule.*.dateUntil' => 'Срок действия скидки',
        'renew_price_discount' => 'Стоимость продления со скидкой',
        'renew_price' => 'Стоимость продления без скидки',
        'password' => 'Пароль',
        'email' => 'Email',
        'phone' => 'Телефон',
    ],

];
