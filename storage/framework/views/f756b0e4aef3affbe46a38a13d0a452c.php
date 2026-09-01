<?php if(in_array('week_timelog', $activeWidgets) && $sidebarUserPermissions['view_timelogs'] != 5 && $sidebarUserPermissions['view_timelogs'] != 'none' && in_array('timelogs', user_modules())): ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['mb-3', 'col-md-6' => (in_array('lead', $activeWidgets) && $leadAgent), 'col-md-12' => !(in_array('lead', $activeWidgets) && $leadAgent)]); ?>">
        <div
            class="bg-white p-20 rounded b-shadow-4 d-flex justify-content-between align-items-center">
            <div class="d-block  w-100">
                <?php
                    $totalWeekTime = $weekWiseTimelogs - $weekWiseTimelogBreak;
                    // Convert total minutes to hours and minutes
                    $hours = intdiv($totalWeekTime, 60);
                    $minutes = $totalWeekTime % 60;

                    // Format output based on hours and minutes
                    $weekTimeLog = $hours > 0
                        ? $hours . 'h' . ($minutes > 0 ? ' ' . sprintf('%02dm', $minutes) : '')
                        : ($minutes > 0 ? sprintf('%dm', $minutes) : '0s');
                ?>
                <h5 class="f-15 f-w-500 mb-20 text-darkest-grey"><?php echo app('translator')->get('modules.dashboard.weekTimelog'); ?> <span class="badge badge-secondary ml-1 f-10"><?php echo e($weekTimeLog . ' ' . __('modules.timeLogs.thisWeek')); ?></span></h5>

                <div id="weekly-timelogs">
                    <nav class="mb-3">
                        <ul class="pagination pagination-sm week-pagination">
                            <?php $__currentLoopData = $weekPeriod->toArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'page-item',
                                        'week-timelog-day',
                                        'active' => (now(company()->timezone)->toDateString() == $date->toDateString()),
                                    ]); ?>"
                                    data-toggle="tooltip" data-original-title="<?php echo e($date->translatedFormat(company()->date_format)); ?>" data-date="<?php echo e($date->toDateString()); ?>">
                                    <a class="page-link" href="javascript:;"><?php echo e($date->isoFormat('dd')); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </nav>

                    <?php
                        $totalDayMinutesPercent = ($dayMinutes > 0) ? floatval((floatval($dayMinutes - $dayBreakMinutes)/$dayMinutes) * 100) : 0;
                    ?>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e($totalDayMinutesPercent); ?>%" aria-valuenow="<?php echo e($totalDayMinutesPercent); ?>" aria-valuemin="0" aria-valuemax="100" data-toggle="tooltip" data-original-title="<?php echo e($totalDayMinutes); ?>"></div>

                        <div class="progress-bar bg-grey" role="progressbar" style="width: <?php echo e((100 - $totalDayMinutesPercent)); ?>%" aria-valuenow="<?php echo e($totalDayMinutesPercent); ?>" aria-valuemin="0" aria-valuemax="100" data-toggle="tooltip" data-original-title="<?php echo e($totalDayBreakMinutes); ?>"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-1 text-dark-grey f-12">
                        <small><?php echo app('translator')->get('app.duration'); ?>: <?php echo e($totalDayMinutes); ?></small>
                        <small><?php echo app('translator')->get('modules.timeLogs.break'); ?>: <?php echo e($totalDayBreakMinutes); ?></small>
                    </div>
                </div>

            </div>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/cp2255957p00/public_html/tacheflux/resources/views/dashboard/employee/widgets/week_timelog.blade.php ENDPATH**/ ?>