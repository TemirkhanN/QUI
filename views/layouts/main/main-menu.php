<? use \app\plugins\bootstrap\Bootstrap; ?>

<?
if(\app\models\user\User::hasRights('admin')):
    $praefect = [
        'title'=>'Админка',
        'href'=>'/praefect/'
    ];
else:
    $praefect = [];
endif;
?>
<?=Bootstrap::navBar(
    [
        $praefect,
        [
            'title'=>'Главная',
            'href'=>'/',
        ],

        [
            'title'=>'Категории',
            'href'=>'#',
            'child'=>[
                [
                    'title'=>'Красота и здоровье',
                    'href'=>'/beauty/',
                ],

                [
                    'title'=>'Фитнес',
                    'href'=>'/fitness/',
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
                    'href'=>'/cook/'
                ]


            ]
        ],

        [
            'title'=>'О сайте',
            'href'=>'/about/',
        ],

        [
            'title'=>'Контакты',
            'href'=>'/contacts/',
        ],
    ],
    'navbar-inverse',
    'vforme'
)?>