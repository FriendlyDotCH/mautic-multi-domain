<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('MauticMultiDomainBundle:Multidomain:index.html.php');
}
?>

<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered multidomain-list" id="multidomainTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#multidomainTable',
                        'routeBase'       => 'multidomain',
                        'templateButtons' => [
                            'delete' => $permissions['multidomain:items:delete'],
                        ],
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'multidomain',
                        'orderBy'    => 'f.email',
                        'text'       => 'plugin.multidomain.email',
                        'class'      => 'col-multidomain-name',
                        'default'    => true,
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'MauticCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit' => $view['security']->hasEntityAccess(
                                        $permissions['multidomain:items:editown'],
                                        $permissions['multidomain:items:editother'],
                                        $item->getCreatedBy()
                                    ),
                                    'clone'  => $permissions['multidomain:items:create'],
                                    'delete' => $view['security']->hasEntityAccess(
                                        $permissions['multidomain:items:deleteown'],
                                        $permissions['multidomain:items:deleteother'],
                                        $item->getCreatedBy()
                                    ),
                                ],
                                'routeBase' => 'multidomain',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <div>
                            <!-- <?php echo $view->render('MauticCoreBundle:Helper:publishstatus_icon.html.php', ['item' => $item, 'model' => 'multidomain']); ?>-->
                            <a data-toggle="ajax" href="<?php echo $view['router']->path(
                                'mautic_multidomain_action',
                                ['objectId' => $item->getId(), 'objectAction' => 'view']
                            ); ?>">
                                <?php echo $item->getEmail(); ?>
                            </a>
                        </div>
                        <?php if ($description = $item->getDomain()): ?>
                            <div class="text-muted mt-4">
                                <small><?php echo $description; ?></small>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <?php echo $view->render(
            'MauticCoreBundle:Helper:pagination.html.php',
            [
                'totalItems' => count($items),
                'page'       => $page,
                'limit'      => $limit,
                'baseUrl'    => $view['router']->path('mautic_multidomain_index'),
                'sessionVar' => 'multidomain',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php', ['tip' => 'mautic.multidomain.noresults.tip']); ?>
<?php endif; ?>
