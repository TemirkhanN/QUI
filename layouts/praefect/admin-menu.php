<? use \app\plugins\bootstrap\Bootstrap; ?>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">vforme</a>
    </div>
    <!-- Top Menu Items -->
    <ul class="nav navbar-right top-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <b class="caret"></b></a>
            <ul class="dropdown-menu message-dropdown">
                <li class="message-preview">
                    <a href="#">
                        <div class="media">
                                <span class="pull-left">
                                    <img class="media-object" src="http://placehold.it/50x50" alt="">
                                </span>
                            <div class="media-body">
                                <h5 class="media-heading">
                                    <strong>John Smith</strong>
                                </h5>
                                <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                <p>Lorem ipsum dolor sit amet, consectetur...</p>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="message-footer">
                    <a href="/user/messages/">Read All New Messages</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
            <ul class="dropdown-menu alert-dropdown">
                <li>
                    <a href="#">Alert Name <span class="label label-default">Alert Badge</span></a>
                </li>
                <li>
                    <a href="#">Alert Name <span class="label label-primary">Alert Badge</span></a>
                </li>
                <li>
                    <a href="#">Alert Name <span class="label label-success">Alert Badge</span></a>
                </li>
                <li>
                    <a href="#">Alert Name <span class="label label-info">Alert Badge</span></a>
                </li>
                <li>
                    <a href="#">Alert Name <span class="label label-warning">Alert Badge</span></a>
                </li>
                <li>
                    <a href="#">Alert Name <span class="label label-danger">Alert Badge</span></a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="#">View All</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> John Smith <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li<?=Bootstrap::isActiveLink("/user/profile/")?>>
                    <a href="/user/profile/"><i class="fa fa-fw fa-user"></i> Profile</a>
                </li>
                <li<?=Bootstrap::isActiveLink("/user/messages/")?>>
                    <a href="/user/messages/"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                </li>
                <li<?=Bootstrap::isActiveLink("/user/settings/")?>>
                    <a href="/user/settings/"><i class="fa fa-fw fa-gear"></i> Settings</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="/logout/"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                </li>
            </ul>
        </li>
    </ul>
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li<?=Bootstrap::isActiveLink("/praefect/")?>>
                <a href="/praefect/"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/charts/")?>>
                <a href="/praefect/charts/"><i class="fa fa-fw fa-bar-chart-o"></i> Charts</a>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/tables/")?>>
                <a href="/praefect/tables/"><i class="fa fa-fw fa-table"></i> Tables</a>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/forms/")?>>
                <a href="/praefect/forms/"><i class="fa fa-fw fa-edit"></i> Forms</a>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/bootstrap/")?>>
                <a href="/praefect/bootstrap/"><i class="fa fa-fw fa-desktop"></i> Bootstrap Elements</a>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/grids/")?>>
                <a href="/praefect/grids/"><i class="fa fa-fw fa-wrench"></i> Bootstrap Grid</a>
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo">
                    <i class="fa fa-fw fa-arrows-v"></i> Dropdown <i class="fa fa-fw fa-caret-down"></i>
                </a>
                <ul id="demo" class="collapse">
                    <li>
                        <a href="#">Dropdown Item</a>
                    </li>
                    <li>
                        <a href="#">Dropdown Item</a>
                    </li>
                </ul>
            </li>
            <li<?=Bootstrap::isActiveLink("/praefect/blank/")?>>
                <a href="/praefect/blank/"><i class="fa fa-fw fa-file"></i> Blank Page</a>
            </li>
            <li>
                <a href="index-rtl.html"><i class="fa fa-fw fa-dashboard"></i> RTL Dashboard</a>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>