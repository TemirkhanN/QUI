<div id="wrapper">


    <? $this->layout('praefect/admin-menu'); ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Content
                    </h1>
                    <?=\app\plugins\bootstrap\Bootstrap::breadcrumbs([
                        [
                            'title' => 'Dashboard',
                            'href' => '/praefect',
                            'icon' => '<i class="fa fa-dashboard"></i>'
                        ],

                        [
                            'title' => 'Content',
                            'href' => '/praefect/content/'
                        ]
                    ])?>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-6">
                    <h2>Content</h2>
                    <div class="table-responsive">


                        <?=\app\plugins\bootstrap\Bootstrap::table(
                            [
                                'Page', 'Visits', 'New visits', 'Revenue'
                            ],
                            [
                                [
                                    'beauty',
                                    '103',
                                    '14%',
                                    'astrid'
                                ],

                                [
                                    'beauty',
                                    '103',
                                    '14%',
                                    'astrid'
                                ],

                                [
                                    'beauty',
                                    '103',
                                    '14%',
                                    'astrid'
                                ],

                            ],
                            [
                                'class'=>'table-bordered table-hover'
                            ]
                        )?>
                    </div>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
