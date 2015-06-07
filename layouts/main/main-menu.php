<? use \app\plugins\bootstrap\Bootstrap; ?>
<?=Bootstrap::navBar(
    [
        [
            'title'=>'Главная',
            'href'=>'/',
        ],

        [
            'title'=>'О сайте',
            'href'=>'/about',
        ],

        [
            'title'=>'Контакты',
            'href'=>'/contacts',
        ],

        [
            'title'=>'Категории',
            'href'=>'#',
            'child'=>[
                [
                    'title'=>'Красота и здоровье',
                    'href'=>'/beauty',
                ],

                [
                    'title'=>'Фитнес',
                    'href'=>'/fitness',
                ],

                [
                    'decorative'=>true,
                    'class'=>'divider',
                ],

                [
                    'decorative'=>true,
                    'class'=>'dropdown-header',
                    'title'=>'Побалуйте себя'
                ],

                [
                    'title'=>'Кулинария',
                    'href'=>'/cook'
                ]


            ]
        ]
    ],
    'navbar-inverse',
    'vforme'
)?>