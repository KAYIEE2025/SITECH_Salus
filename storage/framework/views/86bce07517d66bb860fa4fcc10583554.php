<?php $__env->startSection('title', 'Manage Grades'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <a href="<?php echo e(route('teacher.classes.index')); ?>" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to My Classes
        </a>
    </div>

    <!-- Class Information Card -->
    <div class="tc-card mb-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 flex-1">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">School Year</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->school_year ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Grade Level</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e(optional($classSchedule->section?->yearLevel)->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Section</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->section->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subject</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->subject->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subject Code</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->subject->code ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Teacher Name</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e(auth()->user()->name); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Students</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($students->count()); ?></p>
                </div>
            </div>
            <?php if($gradeStatus): ?>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        <?php echo e($gradeStatus === 'draft' ? 'bg-gray-100 text-gray-700' : 
                           ($gradeStatus === 'submitted' ? 'bg-yellow-100 text-yellow-700' : 
                           ($gradeStatus === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'))); ?>">
                        <?php echo e(ucfirst($gradeStatus)); ?>

                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($gradeStatus === 'rejected'): ?>
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-red-800">Grades Rejected</h3>
                    <p class="text-xs text-red-600 mt-1">
                        The Registrar has rejected your grades. Please review the feedback and make necessary corrections before resubmitting.
                    </p>
                    <?php if($existingGrades && $existingGrades->isNotEmpty() && $existingGrades->first()->rejection_reason): ?>
                        <div class="mt-2 p-2 bg-white rounded border border-red-200">
                            <p class="text-xs text-gray-600"><strong>Rejection Reason:</strong> <?php echo e($existingGrades->first()->rejection_reason); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($gradeTimeline && $gradeTimeline->isNotEmpty()): ?>
        <div class="tc-card mb-6 p-6">
            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-800">Grade Timeline</h3>
                <p class="text-xs text-gray-500 mt-1">Track the history of grade changes for this class</p>
            </div>
            <div class="space-y-4">
                <?php $__currentLoopData = $gradeTimeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full
                                <?php echo e($event['status'] === 'Approved' ? 'bg-green-500' : 
                                   ($event['status'] === 'Rejected' ? 'bg-red-500' : 
                                   ($event['status'] === 'Submitted' ? 'bg-yellow-500' : 'bg-gray-400'))); ?>">
                            </div>
                            <?php if(!$loop->last): ?>
                                <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 pb-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-800"><?php echo e($event['action']); ?></span>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    <?php echo e($event['status'] === 'Approved' ? 'bg-green-100 text-green-700' : 
                                       ($event['status'] === 'Rejected' ? 'bg-red-100 text-red-600' : 
                                       ($event['status'] === 'Submitted' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600'))); ?>">
                                    <?php echo e($event['status']); ?>

                                </span>
                            </div>
                            <p class="text-xs text-gray-500"><?php echo e($event['timestamp']->format('M d, Y - g:i A')); ?></p>
                            <p class="text-xs text-gray-600 mt-1"><?php echo e($event['description']); ?></p>
                            <p class="text-xs text-gray-400 mt-1">By: <?php echo e($event['user']); ?></p>
                            <?php if($event['rejection_reason']): ?>
                                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                                    <p class="text-xs text-red-600"><strong>Rejection Reason:</strong> <?php echo e($event['rejection_reason']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($submissionSchedule && $submissionStatus): ?>
        <div class="tc-card mb-6 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <?php if($submissionStatus === 'open'): ?>
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    <?php elseif($submissionStatus === 'closed'): ?>
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    <?php else: ?>
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Grade Submission Status</h3>
                    <div class="flex items-center gap-2 mb-3">
                        <?php if($submissionStatus === 'open'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                🟢 OPEN
                            </span>
                        <?php elseif($submissionStatus === 'closed'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                🔴 CLOSED
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                🟡 SCHEDULED
                            </span>
                        <?php endif; ?>
                        <span class="text-sm text-gray-600"><?php echo e($submissionStatusMessage); ?></span>
                    </div>
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 w-32">School Year:</span>
                            <span class="font-medium text-gray-800"><?php echo e($classSchedule->school_year); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 w-32">Grading Period:</span>
                            <span class="font-medium text-gray-800">Term <?php echo e($currentGradingPeriod); ?></span>
                        </div>
                        <?php if($submissionSchedule->start_at): ?>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 w-32">Submission Opens:</span>
                            <span class="font-medium text-gray-800"><?php echo e($submissionSchedule->start_at->format('F j, g:i A')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($submissionSchedule->end_at): ?>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 w-32">Deadline:</span>
                            <span class="font-medium text-gray-800"><?php echo e($submissionSchedule->end_at->format('F j, g:i A')); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($submissionStatus === 'closed'): ?>
                        <div class="mt-4" id="reopening-request-section">
                            <?php if(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isPending()): ?>
                                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-3">
                                    <p class="text-sm font-medium text-yellow-800">Status: Pending Approval</p>
                                    <p class="text-xs text-yellow-600 mt-1">Your reopening request is being reviewed by the Registrar.</p>
                                </div>
                            <?php elseif(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isRejected()): ?>
                                <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                                    <p class="text-sm font-medium text-red-800">Request Rejected</p>
                                    <p class="text-xs text-red-600 mt-1">Your reopening request was rejected. You may submit a new request.</p>
                                </div>
                                <button type="button" id="request-reopening-btn" 
                                    class="mt-3 text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Request Reopening Again
                                </button>
                            <?php elseif(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isApproved() && $reopeningRequest->hasTemporaryAccess()): ?>
                                <div class="rounded-lg bg-green-50 border border-green-200 p-3">
                                    <p class="text-sm font-medium text-green-800">Request Approved</p>
                                    <p class="text-xs text-green-600 mt-1">Temporary access granted until <?php echo e($reopeningRequest->temporary_deadline->format('F j, g:i A')); ?></p>
                                </div>
                            <?php else: ?>
                                <button type="button" id="request-reopening-btn" 
                                    class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Request Reopening
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($allTermsApproved): ?>
        <div class="tc-card mb-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-800">All Grading Periods Completed</h3>
                    <p class="text-xs text-gray-500 mt-1">All grading periods for this class have already been approved by the Registrar.</p>
                </div>
            </div>
        </div>
    <?php elseif(!$allTermsApproved): ?>
        <div class="tc-card mb-6 p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Import Official SALUS Grading Sheet</h2>
                <p class="mt-1 text-sm text-gray-500">Upload the official SALUS/DepEd grading sheet for this assigned class.</p>
            </div>

            <!-- Upload Form -->
            <form id="upload-form" action="<?php echo e(route('teacher.classes.process-import', $classSchedule)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <div class="mb-6 rounded-lg bg-gray-50 border border-gray-200 p-4">
                    <h3 class="text-sm font-medium text-gray-800 mb-3">Grading Periods</h3>
                    <div class="space-y-2">
                        <?php
                            $term1Disabled = $approvedTerms['term_1'];
                            $term1Status = $termStatus['term_1'] ?? null;
                            $term1Class = $term1Status === 'approved' ? 'border-green-200 bg-green-50' : ($term1Status === 'rejected' ? 'border-red-200 bg-red-50' : ($term1Status === 'submitted' ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-white'));
                            $term1Opacity = $term1Disabled ? 'opacity-60' : '';
                        ?>
                        <label class="flex items-center gap-3 p-2 rounded-lg border <?php echo e($term1Class); ?> cursor-pointer <?php echo e($term1Disabled ? 'opacity-60' : 'hover:border-green-300'); ?>">
                            <input type="radio" name="grading_period" value="1" <?php echo e($term1Disabled ? 'disabled' : ''); ?> required class="h-4 w-4 text-green-600 focus:ring-green-500 <?php echo e($term1Disabled ? 'cursor-not-allowed' : 'cursor-pointer'); ?>">
                            <span class="flex items-center gap-2">
                                <?php if($term1Status === 'approved'): ?>
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-700">Term 1 (Approved)</span>
                                <?php elseif($term1Status === 'rejected'): ?>
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-red-700">Term 1 (Rejected)</span>
                                <?php elseif($term1Status === 'submitted'): ?>
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-700">Term 1 (Submitted)</span>
                                <?php else: ?>
                                    <span class="text-sm font-medium text-gray-700">Term 1</span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <?php
                            $term2Disabled = $approvedTerms['term_2'];
                            $term2Status = $termStatus['term_2'] ?? null;
                            $term2Class = $term2Status === 'approved' ? 'border-green-200 bg-green-50' : ($term2Status === 'rejected' ? 'border-red-200 bg-red-50' : ($term2Status === 'submitted' ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-white'));
                        ?>
                        <label class="flex items-center gap-3 p-2 rounded-lg border <?php echo e($term2Class); ?> cursor-pointer <?php echo e($term2Disabled ? 'opacity-60' : 'hover:border-green-300'); ?>">
                            <input type="radio" name="grading_period" value="2" <?php echo e($term2Disabled ? 'disabled' : ''); ?> required class="h-4 w-4 text-green-600 focus:ring-green-500 <?php echo e($term2Disabled ? 'cursor-not-allowed' : 'cursor-pointer'); ?>">
                            <span class="flex items-center gap-2">
                                <?php if($term2Status === 'approved'): ?>
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-700">Term 2 (Approved)</span>
                                <?php elseif($term2Status === 'rejected'): ?>
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-red-700">Term 2 (Rejected)</span>
                                <?php elseif($term2Status === 'submitted'): ?>
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-700">Term 2 (Submitted)</span>
                                <?php else: ?>
                                    <span class="text-sm font-medium text-gray-700">Term 2</span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <?php
                            $term3Disabled = $approvedTerms['term_3'];
                            $term3Status = $termStatus['term_3'] ?? null;
                            $term3Class = $term3Status === 'approved' ? 'border-green-200 bg-green-50' : ($term3Status === 'rejected' ? 'border-red-200 bg-red-50' : ($term3Status === 'submitted' ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-white'));
                        ?>
                        <label class="flex items-center gap-3 p-2 rounded-lg border <?php echo e($term3Class); ?> cursor-pointer <?php echo e($term3Disabled ? 'opacity-60' : 'hover:border-green-300'); ?>">
                            <input type="radio" name="grading_period" value="3" <?php echo e($term3Disabled ? 'disabled' : ''); ?> required class="h-4 w-4 text-green-600 focus:ring-green-500 <?php echo e($term3Disabled ? 'cursor-not-allowed' : 'cursor-pointer'); ?>">
                            <span class="flex items-center gap-2">
                                <?php if($term3Status === 'approved'): ?>
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-700">Term 3 (Approved)</span>
                                <?php elseif($term3Status === 'rejected'): ?>
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-red-700">Term 3 (Rejected)</span>
                                <?php elseif($term3Status === 'submitted'): ?>
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-700">Term 3 (Submitted)</span>
                                <?php else: ?>
                                    <span class="text-sm font-medium text-gray-700">Term 3</span>
                                <?php endif; ?>
                            </span>
                        </label>
                    </div>
                    <?php if($approvedTerms['term_1'] || $approvedTerms['term_2'] || $approvedTerms['term_3']): ?>
                        <p class="text-xs text-gray-500 mt-2">Approved grading periods cannot be modified. Rejected grading periods can be resubmitted.</p>
                    <?php endif; ?>
                </div>

                <?php if(session('error')): ?>
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3">
                        <p class="text-sm text-red-700"><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>

                <?php $__sessionArgs = ['success'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3">
                        <p class="text-sm text-green-700"><?php echo e($value); ?></p>
                    </div>
                <?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>

                <!-- Upload Area -->
                <div id="upload-area" class="mb-4">
                    <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 p-8 text-center transition-colors hover:border-green-500 hover:bg-green-50 cursor-pointer">
                        <div class="mb-3">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">📄 Drag & Drop Excel File Here</p>
                        <p class="text-xs text-gray-500 mt-1">OR</p>
                        <p class="text-sm text-green-700 font-medium mt-1">Browse File</p>
                        <p class="text-xs text-gray-400 mt-3">Accepted: .xlsx, .xls (Max 10 MB)</p>
                        <input type="file" id="file-input" name="excel_file" accept=".xlsx,.xls" class="hidden">
                    </div>
                </div>

                <!-- Error Messages -->
                <div id="error-message" class="hidden mb-4 rounded-lg bg-red-50 border border-red-200 p-3">
                    <p class="text-sm text-red-700" id="error-text"></p>
                </div>

                <!-- File Selection Display -->
                <div id="file-display" class="hidden mb-4 rounded-lg bg-green-50 border border-green-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800" id="file-name"></p>
                                <p class="text-xs text-gray-500" id="file-size"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <button type="button" id="remove-file-btn" class="hidden px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Remove File
                    </button>
                    <div class="ml-auto">
                        <button type="submit" id="continue-btn" class="px-6 py-2 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                            Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Locked Status Message -->
        <div class="tc-card mb-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-800">Grades <?php echo e($gradeStatus === 'submitted' ? 'Submitted to Registrar' : 'Approved'); ?></h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo e($gradeStatus === 'submitted' ? 'Waiting for Registrar approval. Editing is locked.' : 'Grades have been approved by the Registrar.'); ?>

                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('upload-form');
            const continueBtn = document.getElementById('continue-btn');

            if (uploadForm && continueBtn) {
                // Continue button is disabled by default until file is selected
                // No submission window restriction on upload
            }
        });
    </script>

    <!-- Submit Grades Button -->
    <?php if($hasDraftGradesForCurrentPeriod && $currentGradingPeriod): ?>
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-800">Ready to Submit</h3>
                    <p class="text-xs text-gray-500 mt-1">Submit your Term <?php echo e($currentGradingPeriod); ?> draft grades to the Registrar for approval.</p>
                </div>
                <?php if($submissionStatus === 'open'): ?>
                    <button type="button" id="submit-grades-btn" class="px-6 py-2 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition">
                        Submit Grades
                    </button>
                <?php else: ?>
                    <button type="button" disabled class="px-6 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                        Submit Grades (<?php echo e($submissionStatusMessage); ?>)
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <?php if($submissionStatus === 'open'): ?>
        <div id="submit-confirmation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Submit Grades?</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Once submitted, you can no longer edit these grades unless the Registrar rejects them.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-submit-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <form id="submit-grades-form" action="<?php echo e(route('teacher.classes.submit-grades', $classSchedule)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="grading_period" value="<?php echo e($currentGradingPeriod); ?>">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 transition">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Class Roster (Hidden for now, will be shown in Phase 2) -->
    <div class="tc-card hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-800">Class Roster</h3>
        </div>

        <?php if($students->isEmpty()): ?>
            <div class="px-6 py-10 text-center text-sm text-gray-400">
                No students enrolled in this subject.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Student Number</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Gender</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studyLoad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-mono text-gray-700">
                                    <?php echo e($studyLoad->student->student_number); ?>

                                </td>
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    <?php echo e($studyLoad->student->last_name); ?>, <?php echo e($studyLoad->student->first_name); ?>

                                    <?php echo e($studyLoad->student->middle_name ? $studyLoad->student->middle_name[0] . '.' : ''); ?>

                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    <?php echo e($studyLoad->student->gender ?? 'Not specified'); ?>

                                </td>
                                <td class="px-6 py-3">
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        Ready
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const fileDisplay = document.getElementById('file-display');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const removeFileBtn = document.getElementById('remove-file-btn');
            const continueBtn = document.getElementById('continue-btn');
            const errorMessage = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            const MAX_SIZE = 10 * 1024 * 1024; // 10 MB
            const ACCEPTED_TYPES = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
            const ACCEPTED_EXTENSIONS = ['.xlsx', '.xls'];

            let selectedFile = null;

            const formatFileSize = (bytes) => {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            };

            const showError = (message) => {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
            };

            const hideError = () => {
                errorMessage.classList.add('hidden');
            };

            const validateFile = (file) => {
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                
                if (!ACCEPTED_EXTENSIONS.includes(fileExtension)) {
                    showError('Invalid file type. Please upload an Excel (.xlsx or .xls) grading sheet.');
                    return false;
                }

                if (file.size > MAX_SIZE) {
                    showError('File size exceeds the maximum limit of 10 MB.');
                    return false;
                }

                hideError();
                return true;
            };

            const handleFileSelect = (file) => {
                if (validateFile(file)) {
                    selectedFile = file;
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    fileDisplay.classList.remove('hidden');
                    removeFileBtn.classList.remove('hidden');
                    continueBtn.disabled = false;
                }
            };

            const resetFileSelection = () => {
                selectedFile = null;
                fileInput.value = '';
                fileDisplay.classList.add('hidden');
                removeFileBtn.classList.add('hidden');
                continueBtn.disabled = true;
                hideError();
            };

            // Click to browse
            dropZone.addEventListener('click', () => {
                fileInput.click();
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Drag and drop events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-green-500', 'bg-green-50');
            });

            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-green-500', 'bg-green-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-green-500', 'bg-green-50');
                
                if (e.dataTransfer.files.length > 0) {
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });

            // Remove file button
            removeFileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                resetFileSelection();
            });

            // Continue button - Submit form
            continueBtn.addEventListener('click', () => {
                if (selectedFile) {
                    // Set the selected grading period before submitting
                    const selectedGradingPeriod = document.querySelector('input[name="grading_period"]:checked');
                    if (selectedGradingPeriod) {
                        document.getElementById('selected-grading-period').value = selectedGradingPeriod.value;
                    }
                    document.getElementById('upload-form').submit();
                }
            });

            // Submit Grades confirmation dialog
            const submitGradesBtn = document.getElementById('submit-grades-btn');
            const submitConfirmationModal = document.getElementById('submit-confirmation-modal');
            const cancelSubmitBtn = document.getElementById('cancel-submit-btn');

            if (submitGradesBtn) {
                submitGradesBtn.addEventListener('click', () => {
                    submitConfirmationModal.classList.remove('hidden');
                });
            }

            if (cancelSubmitBtn) {
                cancelSubmitBtn.addEventListener('click', () => {
                    submitConfirmationModal.classList.add('hidden');
                });
            }

            // Close modal when clicking outside
            if (submitConfirmationModal) {
                submitConfirmationModal.addEventListener('click', (e) => {
                    if (e.target === submitConfirmationModal) {
                        submitConfirmationModal.classList.add('hidden');
                    }
                });
            }

            // Reopening Request Modal
            const requestReopeningBtn = document.getElementById('request-reopening-btn');
            const reopeningRequestModal = document.getElementById('reopening-request-modal');
            const cancelReopeningBtn = document.getElementById('cancel-reopening-btn');
            const submitReopeningBtn = document.getElementById('submit-reopening-btn');

            if (requestReopeningBtn) {
                requestReopeningBtn.addEventListener('click', () => {
                    if (reopeningRequestModal) {
                        reopeningRequestModal.classList.remove('hidden');
                    }
                });
            }

            if (cancelReopeningBtn) {
                cancelReopeningBtn.addEventListener('click', () => {
                    if (reopeningRequestModal) {
                        reopeningRequestModal.classList.add('hidden');
                    }
                });
            }

            if (submitReopeningBtn) {
                submitReopeningBtn.addEventListener('click', () => {
                    const reason = document.getElementById('reopening-reason').value;
                    if (!reason || reason.trim() === '') {
                        alert('Please provide a reason for your reopening request.');
                        return;
                    }

                    submitReopeningBtn.disabled = true;
                    submitReopeningBtn.textContent = 'Submitting...';

                    const formData = new FormData();
                    formData.append('grading_period', '<?php echo e($currentGradingPeriod ?? 1); ?>');
                    formData.append('reason', reason);

                    fetch('<?php echo e(route('teacher.grade-reopening-requests.create', $classSchedule)); ?>', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                            submitReopeningBtn.disabled = false;
                            submitReopeningBtn.textContent = 'Submit Request';
                        }
                    })
                    .catch(error => {
                        alert('Error submitting request: ' + error.message);
                        submitReopeningBtn.disabled = false;
                        submitReopeningBtn.textContent = 'Submit Request';
                    });
                });
            }

            // Close reopening modal when clicking outside
            if (reopeningRequestModal) {
                reopeningRequestModal.addEventListener('click', (e) => {
                    if (e.target === reopeningRequestModal) {
                        reopeningRequestModal.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    
    <div id="reopening-request-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Request Grade Submission Reopening</h3>
            <p class="text-sm text-gray-600 mb-4">
                Please provide a reason for requesting to reopen the grade submission period.
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea id="reopening-reason" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Explain why you need to submit grades after the deadline..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-reopening-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" id="submit-reopening-btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Submit Request
                </button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/teacher/classes/grades.blade.php ENDPATH**/ ?>