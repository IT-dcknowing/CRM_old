<div class="row" id="radiation_table">
    <div class="col-sm-12">
        <div class="content-wrapper">
            <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                <?php echo app('translator')->get('Liste des clients en attente de radiations du CGA'); ?>
            </h4>
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white table-responsive">
                <table class='table table-hover border-0 w-100' id="table-radiation">
                    <thead>
                        <tr>
                            <td align="left">#</td>
                            <td>Identifiant</td>
                            <td>Nom</td>
                            <td width="40%">Motif</td>
                            <td width="20%">Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($clientsAppor->isEmpty()): ?>
                            <tr><td colspan="5" align="center">Aucune données</td></tr>
                        <?php else: ?>
                            <?php $__currentLoopData = $clientsAppor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientsRad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td align="left"><input type="checkbox" name="select_alField as $customField) {
                                        $data[] = [$customField->name => l_table" id="select-all-table" onclick="selectAllTable(this)"></td>
                                    <td><?php echo e($clientsRad->user_id); ?></td>
                                    <td><strong class="text-red"><?php echo e($clientsRad->company_name); ?></strong></td>
                                    <td><?php echo e($clientsRad->electronic_address_scheme); ?></td>  
                                    <td>
                                        <div class="d-grid d-lg-flex d-md-flex action-bar">
                                            <div id="table-actions" class="flex-grow-1 align-items-center">

                                                <a class="btn btn-primary btn-radiation" href="javascript:;" data-user-id="<?php echo e($clientsRad->user_id); ?>">
                                                    <i class="fa fa-check mr-2"></i>
                                                    <?php echo app('translator')->get('Valider'); ?>
                                                </a>

                                                <a class="btn btn-secondary btn-cancel-radiation" href="javascript:;" data-user-id="<?php echo e($clientsRad->user_id); ?>">
                                                    <i class="fa fa-times mr-2"></i>
                                                    <?php echo app('translator')->get('Annuler'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $('body').on('click', '.btn-radiation', function() {
            const id = $(this).data('user-id');
            Swal.fire({
                title: "<?php echo app('translator')->get('messages.sweetAlertTitle'); ?>",
                text: "cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "<?php echo app('translator')->get('app.approve'); ?>",
                cancelButtonText: "<?php echo app('translator')->get('app.cancel'); ?>",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "<?php echo e(route('clients.toggleRadiationValidate', ':id')); ?>";
                    url = url.replace(':id', id);

                    var token = "<?php echo e(csrf_token()); ?>";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

    $('body').on('click', '.btn-cancel-radiation', function() {
        const id = $(this).data('user-id');
        Swal.fire({
            title: "<?php echo app('translator')->get('messages.sweetAlertTitle'); ?>",
            text: "cette action annule la radiation du client.",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "<?php echo app('translator')->get('app.approve'); ?>",
            cancelButtonText: "<?php echo app('translator')->get('app.cancel'); ?>",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "<?php echo e(route('clients.toggleRadiationCancel', ':id')); ?>";
                url = url.replace(':id', id);

                var token = "<?php echo e(csrf_token()); ?>";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            showTable();
                        }
                    }
                });
            }
        });
    });
</script><?php /**PATH C:\laragon\www\CRM\resources\views/clients/ajax/radiation.blade.php ENDPATH**/ ?>