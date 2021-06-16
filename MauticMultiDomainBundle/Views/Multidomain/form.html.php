<?php
$view->extend('MauticCoreBundle:Default:content.html.php');


$view['slots']->set('mauticContent', 'multidomain');

$header = ($entity->getId())
    ?
    $view['translator']->trans(
        'mautic.multidomain.edit',
        ['%title%' => $view['translator']->trans($entity->getEmail())]
    )
    :
    $view['translator']->trans('mautic.multidomain.new');
$view['slots']->set('headerTitle', $header);

//$attr = $form->vars['attr'];
echo $view['form']->start($form);
?>

<!-- start: box layout -->
<div class="box-layout">
     <!-- container -->
     <div class="col-md-9 bg-auto height-auto bdr-r pa-md">
            <!-- Warning text to mapp domain-->
            <div class="alert alert-warning">
                <p> 
                    <?php echo $view['translator']->trans('mautic.multidomain.add.warning'); ?> 
                </p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['email']); ?>
                </div>
                
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['domain']); ?>
                </div>
                
            </div>
     </div>     
</div>
<div class="modal-form-buttons" style="margin-left: 15px;">
    
</div>
<?php echo $view['form']->end($form); ?>