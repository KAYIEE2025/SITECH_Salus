
<?php $__env->startSection('title', 'Grade Approval'); ?>
<?php $__env->startSection('content'); ?>

    <?php $__sessionArgs = ['success'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            <?php echo e($value); ?>

        </div>
    <?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>

    
    <div class="ra-card mb-6 overflow-hidden">
        <div class="ra-card-header flex flex-col items-start justify-between gap-3 sm:gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-2 md:gap-3">
                <input type="checkbox"
                       id="select-all-checkbox"
                       class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                       onchange="toggleSelectAll()">
                <h2 class="text-sm md:text-base font-semibold text-gray-800">Pending Grade Submissions</h2>
            </div>
            <div class="flex gap-1.5 md:gap-2">
                <button id="approve-selected-btn" disabled onclick="approveSelected()" class="text-[10px] md:text-xs bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Approve Selected
                </button>
                <button id="reject-selected-btn" disabled onclick="showRejectModal()" class="text-[10px] md:text-xs bg-red-600 hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Reject Selected
                </button>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $pendingGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classScheduleId => $grades): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $first = $grades->first(); ?>
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2 md:mb-3">
                    <div class="flex items-center gap-2 md:gap-3">
                        <input type="checkbox"
                               class="class-checkbox w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                               name="class_schedule_ids[]"
                               value="<?php echo e($first->classSchedule->id); ?>"
                               data-grading-period="<?php echo e($first->grading_period ?? ''); ?>"
                               onchange="updateButtons()">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">
                                <?php echo e($first->classSchedule->subject->code ?? '—'); ?> —
                                <?php echo e($first->classSchedule->subject->name ?? ''); ?>

                            </p>
                            <p class="text-[10px] md:text-xs text-gray-500">
                                Teacher: <?php echo e($first->classSchedule->teacher->name ?? '—'); ?> ·
                                <?php echo e($first->classSchedule->section->yearLevel->name ?? ''); ?>

                                Sec <?php echo e($first->classSchedule->section->name ?? ''); ?>

                            </p>
                        </div>
                    </div>
                    <div class="flex gap-1.5 md:gap-2">
                        <a href="<?php echo e(route('registrar.grade-approval.view', $first->classSchedule)); ?>" class="text-[10px] md:text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                            View Details
                        </a>
                        <?php if($first->status === 'submitted'): ?>
                            <form method="POST" action="<?php echo e(route('registrar.grade-approval.approve-class', $first->classSchedule)); ?>" class="inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="text-[10px] md:text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                    Approve All
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-form-<?php echo e($first->classSchedule->id); ?>').classList.toggle('hidden')" class="text-[10px] md:text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                Reject All
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($first->status === 'submitted'): ?>
                    <div id="reject-form-<?php echo e($first->classSchedule->id); ?>" class="hidden mb-3 p-2 md:p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form method="POST" action="<?php echo e(route('registrar.grade-approval.reject-class', $first->classSchedule)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <div class="mb-1.5 md:mb-2">
                                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                                    Reason for Rejection <span class="text-red-600">*</span>
                                </label>
                                <textarea name="rejection_reason" required rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1 text-[10px] md:text-xs focus:outline-none focus:ring-2 focus:ring-red-400"
                                    placeholder="Please provide a reason for rejecting these grades..."></textarea>
                            </div>
                            <div class="flex gap-1.5 md:gap-2">
                                <button type="submit" class="text-[10px] md:text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 md:px-3 md:py-1 rounded-lg transition whitespace-nowrap">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="document.getElementById('reject-form-<?php echo e($first->classSchedule->id); ?>').classList.add('hidden')" class="text-[10px] md:text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 md:px-3 md:py-1 rounded-lg transition whitespace-nowrap">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="overflow-x-auto"><table class="w-full min-w-max text-xs md:text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                        <tr>
                            <th class="text-left px-2 py-2 md:px-4 md:py-2">Student Name</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 1</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 2</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 3</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Final Rating</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                // Calculate Final Rating only if all three terms are approved
                                $finalRating = '-';
                                if ($grade->term_1 && $grade->term_2 && $grade->term_3) {
                                    // Check if all terms are approved (status is approved and term fields are not null)
                                    $term1Approved = $grade->status === 'approved' && $grade->term_1;
                                    $term2Approved = $grade->status === 'approved' && $grade->term_2;
                                    $term3Approved = $grade->status === 'approved' && $grade->term_3;
                                    
                                    if ($term1Approved && $term2Approved && $term3Approved) {
                                        $finalRating = round(($grade->term_1 + $grade->term_2 + $grade->term_3) / 3, 2);
                                    }
                                }
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 md:px-4 md:py-2 text-gray-800">
                                    <?php echo e($grade->student->last_name); ?>, <?php echo e($grade->student->first_name); ?>

                                </td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700"><?php echo e($grade->term_1 ?? '-'); ?></td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700"><?php echo e($grade->term_2 ?? '-'); ?></td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700"><?php echo e($grade->term_3 ?? '-'); ?></td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center font-semibold text-gray-800"><?php echo e($finalRating); ?></td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center">
                                    <span class="text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full
                                        <?php echo e($grade->status === 'approved' ? 'bg-green-50 text-green-700' : 
                                           ($grade->status === 'rejected' ? 'bg-red-50 text-red-600' : 
                                           ($grade->status === 'submitted' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600'))); ?>">
                                        <?php echo e(ucfirst($grade->status)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-4 py-6 md:px-6 md:py-8 text-center text-gray-400">
                No grade submissions pending approval.
            </div>
        <?php endif; ?>
    </div>

    
    <div class="ra-card overflow-hidden">
        <div class="ra-card-header">
            <h2 class="text-sm md:text-base font-semibold text-gray-800">Review History</h2>
        </div>
        <div class="overflow-x-auto"><table class="w-full min-w-max text-xs md:text-sm">
            <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                <tr>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Student</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Subject</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Grade</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Status</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Reviewed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-800"><?php echo e($grade->student->last_name); ?>, <?php echo e($grade->student->first_name); ?></td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-600"><?php echo e($grade->classSchedule->subject->code ?? '—'); ?></td>
                    <td class="px-2 py-2 md:px-6 md:py-3 font-semibold text-green-800"><?php echo e($grade->final_grade); ?></td>
                    <td class="px-2 py-2 md:px-6 md:py-3">
                        <span class="text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full
                            <?php echo e($grade->status == 'approved' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'); ?>">
                            <?php echo e(ucfirst($grade->status)); ?>

                        </span>
                        <?php if($grade->status == 'rejected'): ?>
                            <p class="text-[10px] md:text-xs text-gray-400 mt-1"><?php echo e($grade->rejection_reason); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-400 text-[10px] md:text-xs">
                        <?php echo e($grade->reviewed_at?->format('M d, Y h:i A')); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400">No reviewed grades yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table></div>
    </div>

    
    <div id="reject-selected-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md mx-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4">Reject Selected Classes</h3>
            <div class="mb-3 md:mb-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    Reason for Rejection <span class="text-red-600">*</span>
                </label>
                <textarea id="rejection-reason" required rows="3"
                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                    placeholder="Please provide a reason for rejecting these grades..."></textarea>
            </div>
            <div class="flex gap-1.5 md:gap-2 justify-end">
                <button onclick="hideRejectModal()" class="text-xs md:text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Cancel
                </button>
                <button onclick="rejectSelected()" class="text-xs md:text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const classCheckboxes = document.querySelectorAll('.class-checkbox');

            classCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateButtons();
        }

        function updateButtons() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');
            const approveBtn = document.getElementById('approve-selected-btn');
            const rejectBtn = document.getElementById('reject-selected-btn');

            approveBtn.disabled = checkboxes.length === 0;
            rejectBtn.disabled = checkboxes.length === 0;
        }

        function getSelectedData() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');
            const data = {
                class_schedule_ids: []
            };

            checkboxes.forEach(checkbox => {
                const classId = checkbox.value;
                data.class_schedule_ids.push(classId);
            });

            return data;
        }

        function approveSelected() {
            const data = getSelectedData();

            console.log('APPROVE SELECTED - Data being sent:', data);

            if (data.class_schedule_ids.length === 0) {
                alert('Please select at least one class to approve.');
                return;
            }

            if (!confirm(`Are you sure you want to approve ${data.class_schedule_ids.length} selected class(es)?`)) {
                return;
            }

            console.log('APPROVE SELECTED - Sending to route:', '<?php echo e(route('registrar.grade-approval.approve-selected')); ?>');

            fetch('<?php echo e(route('registrar.grade-approval.approve-selected')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('APPROVE SELECTED - Response received:', data);
                if (data.success) {
                    console.log('APPROVE SELECTED - Success, reloading page');
                    window.location.href = '<?php echo e(route('registrar.grade-approval')); ?>';
                } else {
                    console.error('APPROVE SELECTED - Server error:', data.message);
                    alert(data.message || 'Error approving selected classes.');
                }
            })
            .catch(error => {
                console.error('APPROVE SELECTED - Network error:', error);
                alert('Error approving selected classes.');
            });
        }

        function showRejectModal() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');

            if (checkboxes.length === 0) {
                alert('Please select at least one class to reject.');
                return;
            }

            document.getElementById('reject-selected-modal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('reject-selected-modal').classList.add('hidden');
            document.getElementById('rejection-reason').value = '';
        }

        function rejectSelected() {
            const data = getSelectedData();
            const rejectionReason = document.getElementById('rejection-reason').value.trim();

            console.log('REJECT SELECTED - Data being sent:', data);
            console.log('REJECT SELECTED - Rejection reason:', rejectionReason);

            if (!rejectionReason) {
                alert('Please provide a rejection reason.');
                return;
            }

            data.rejection_reason = rejectionReason;

            console.log('REJECT SELECTED - Sending to route:', '<?php echo e(route('registrar.grade-approval.reject-selected')); ?>');

            fetch('<?php echo e(route('registrar.grade-approval.reject-selected')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('REJECT SELECTED - Response received:', data);
                if (data.success) {
                    console.log('REJECT SELECTED - Success, reloading page');
                    window.location.href = '<?php echo e(route('registrar.grade-approval')); ?>';
                } else {
                    console.error('REJECT SELECTED - Server error:', data.message);
                    alert(data.message || 'Error rejecting selected classes.');
                }
            })
            .catch(error => {
                console.error('REJECT SELECTED - Network error:', error);
                alert('Error rejecting selected classes.');
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/registrar/grade-approval/index.blade.php ENDPATH**/ ?>