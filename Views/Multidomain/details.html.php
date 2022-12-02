<?php

    $view->extend('MauticCoreBundle:Default:content.html.php');
    $view['slots']->set('mauticContent', 'multidomain');
    $view['slots']->set('headerTitle', $item->getEmail());

    $view['slots']->set(
        'actions',
        $view->render(
            'MauticCoreBundle:Helper:page_actions.html.php',
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
                    'close' => $view['security']->isGranted('multidomain:items:view'),
                ],
                'routeBase' => 'multidomain',
                'langVar'   => 'multidomain',
            ]
        )
    );

?>

<!-- start: box layout -->
<div class="box-layout">
    
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-r pa-md">
        <div class="row">
            <div class="col-md-2">
                <b><?php echo $view['translator']->trans('plugin.multidomain.email'); ?>:</b>
            </div>
            <div class="col-md-6">
                <?php echo $item->getEmail();?>
            </div>
        </div> 

        <div class="row">
            <div class="col-md-2">
                <b><?php echo $view['translator']->trans('plugin.multidomain.domain'); ?>:</b>
            </div>
            <div class="col-md-6">
                <p><?php echo $item->getDomain();?></p>
            </div>
        </div>        
    </div>
</div>