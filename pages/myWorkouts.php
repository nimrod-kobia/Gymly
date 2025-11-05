<?php
// myWorkouts.php — My Workouts Calendar View

require_once "../autoload.php";

// Require user to be logged in
if (!SessionManager::isLoggedIn()) {
    header("Location: signInPage.php");
    exit;
}

SessionManager::updateActivity();
$userId = SessionManager::getUserId();

// Set page title for layout.php
$pageTitle = "My Workouts - Gymly";
include '../template/layout.php';
?>

<!-- My Workouts Page - Calendar View -->
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">💪 My Workouts</h2>
                    <p class="text-muted mb-0">Track your training progress</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" id="changeSplitBtn">
                        <i class="bi bi-arrow-left-right"></i> Change Split
                    </button>
                    <button class="btn btn-primary" id="startWorkoutBtn">
                        <i class="bi bi-play-circle"></i> Start Workout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Split Info -->
    <div class="row mb-4" id="activeSplitInfo" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1" id="splitName">Loading...</h5>
                            <p class="card-text opacity-75 mb-0" id="splitDescription">Loading...</p>
                        </div>
                        <span class="badge bg-white text-primary" id="splitDaysCount">0 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Active Split -->
    <div class="row mb-4" id="noActiveSplit" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h5>No Active Workout Split</h5>
                    <p class="text-muted mb-4">Select a workout split to start tracking your progress</p>
                    <button type="button" class="btn btn-primary" id="chooseSplitBtn">
                        <i class="bi bi-plus-circle"></i> Choose a Split
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row" id="calendarView" style="display: none;">
        <!-- Calendar Header -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" id="prevMonth">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <h5 class="mb-0" id="currentMonth">November 2025</h5>
                        <button class="btn btn-sm btn-outline-secondary" id="nextMonth">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Days Overview -->
    <div class="row mt-4" id="splitDaysOverview" style="display: none;">
        <div class="col-12">
            <h5 class="mb-3">Workout Days</h5>
        </div>
        <div id="splitDaysList">
            <!-- Split days will be rendered here -->
        </div>
    </div>
</div>

<!-- Start Workout Modal -->
<div class="modal fade" id="startWorkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Select Workout Day</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group" id="workoutDaysList">
                    <!-- Workout days will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Day Modal -->
<div class="modal fade" id="manageDayModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="manageDayModalLabel">Manage Workout Day</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3" id="manageDayExtraInfo" style="display: none;">
                    <div class="small text-muted" id="manageDaySummary"></div>
                </div>

                <h6 class="mb-3">Assigned Exercises</h6>
                <div id="manageDayExercisesList" class="mb-4">
                    <!-- Existing exercises will render here -->
                </div>

                <div class="border-top pt-3">
                    <h6 class="mb-3">Add Exercise</h6>
                    <form id="addExerciseForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label" for="exerciseSearchInput">Exercise</label>
                                <input type="text" class="form-control" id="exerciseSearchInput" placeholder="Search by name or muscle group" autocomplete="off">
                                <input type="hidden" id="selectedExerciseId" value="">
                                <div class="list-group mt-2" id="exerciseSearchResults" style="display: none;"></div>
                                <div class="form-text" id="selectedExerciseSummary">No exercise selected.</div>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseSets">Sets</label>
                                <input type="number" class="form-control" id="addExerciseSets" min="1" value="3">
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseReps">Reps</label>
                                <input type="text" class="form-control" id="addExerciseReps" value="8-12">
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseRest">Rest (sec)</label>
                                <input type="number" class="form-control" id="addExerciseRest" min="0" value="90">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="addExerciseNotes">Notes (optional)</label>
                                <textarea class="form-control" id="addExerciseNotes" rows="2" placeholder="Add any cues or reminders..."></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary" id="addExerciseSubmit" disabled>
                                    <i class="bi bi-plus-circle"></i> Add Exercise
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Select Split Modal -->
                <div class="modal fade" id="selectSplitModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content bg-dark text-light">
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title">Select a Workout Split</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="splitPickerList">
                                    <div class="text-center py-4 text-muted">Loading splits...</div>
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <a href="workoutSplits.php" class="btn btn-outline-light">
                                    <i class="bi bi-plus-circle"></i> Manage Splits
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}

.calendar-day-header {
    text-align: center;
    font-weight: 600;
    padding: 10px;
    color: var(--bs-secondary);
    font-size: 0.85rem;
}

.calendar-day {
    aspect-ratio: 1;
    border: 1px solid var(--bs-border-color);
    border-radius: 8px;
    padding: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--bs-body-bg);
    display: flex;
    flex-direction: column;
    position: relative;
}

.calendar-day:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.calendar-day.other-month {
    opacity: 0.3;
}

.calendar-day.today {
    border-color: var(--bs-primary);
    border-width: 2px;
    background: rgba(102, 126, 234, 0.1);
}

.calendar-day.has-workout {
    background: rgba(102, 126, 234, 0.2);
    border-color: var(--bs-primary);
}

.calendar-day.completed {
    background: rgba(40, 167, 69, 0.2);
    border-color: var(--bs-success);
}

.calendar-day-number {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.calendar-day-content {
    flex: 1;
    font-size: 0.75rem;
    overflow: hidden;
}

.workout-badge {
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    background: var(--bs-primary);
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}

.completed-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    background: var(--bs-success);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
}

.split-day-card {
    transition: all 0.2s;
    cursor: pointer;
}

.split-day-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.exercise-count-badge {
    background: rgba(102, 126, 234, 0.1);
    color: var(--bs-primary);
    font-weight: 600;
}

.exercise-manage-card {
    border: 1px solid var(--bs-border-color);
    border-radius: 10px;
}

#exerciseSearchResults {
    max-height: 220px;
    overflow-y: auto;
}

#exerciseSearchResults .active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #fff;
}

@media (max-width: 768px) {
    .calendar-grid {
        gap: 5px;
    }
    
    .calendar-day {
        padding: 4px;
    }
    
    .calendar-day-number {
        font-size: 0.8rem;
    }
    
    .calendar-day-content {
        font-size: 0.65rem;
    }
}
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    const dayNames = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    let activeSplit = null;
    let userSplits = [];
    let currentDate = new Date();
    let workoutSessions = [];
    let manageDayModalInstance = null;
    let manageDaySelectedId = null;
    let selectedExerciseIdForAdd = null;
    let exerciseSearchTimeout = null;
    let lastExerciseSearchQuery = '';

    const manageDayModalElement = document.getElementById('manageDayModal');

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function pluralize(count, singular, plural) {
        const resolvedPlural = plural || `${singular}s`;
        return `${count} ${count === 1 ? singular : resolvedPlural}`;
    }

    if (manageDayModalElement) {
        manageDayModalElement.addEventListener('hidden.bs.modal', function() {
            manageDaySelectedId = null;
            resetAddExerciseForm();
        });
        manageDayModalElement.addEventListener('shown.bs.modal', function() {
            $('#exerciseSearchInput').trigger('focus');
        });
    }

    loadOverview();

    function loadOverview(callback, options) {
        const opts = options || {};
        if (!opts.skipLoadingState) {
            showLoadingState();
        }

        $.ajax({
            url: '../handlers/fetchMyWorkoutsOverview.php',
            method: 'GET',
            dataType: 'json',
            timeout: 45000,
            success: function(response) {
                if (!response.success) {
                    showNoActiveSplit(response.message);
                    return;
                }

                const data = response.data || {};
                userSplits = Array.isArray(data.splits) ? data.splits : [];
                activeSplit = data.active_split || null;
                workoutSessions = Array.isArray(data.sessions) ? data.sessions : [];

                renderSplitPicker();

                if (activeSplit) {
                    displayActiveSplit();
                    renderCalendar();
                    renderSplitDays();
                    $('#calendarView').show();
                    $('#splitDaysOverview').show();
                } else {
                    showNoActiveSplit();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading workouts overview:', error);
                console.error('Response:', xhr.responseText);
                showNoActiveSplit('Unable to load workout data.');
            },
            complete: function() {
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    function showLoadingState() {
        $('#calendarGrid').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#splitDaysList').html('<div class="text-center py-5 text-muted">Loading workout days...</div>');
        $('#workoutDaysList').html('');
    }

    function renderSplitPicker() {
        const container = $('#splitPickerList');

        if (!userSplits || userSplits.length === 0) {
            container.html('<div class="text-center py-4 text-muted">No splits yet. Create one first.</div>');
            return;
        }

        let html = '<div class="list-group">';
        userSplits.forEach(split => {
            const isActive = !!split.is_active;
            const dayCountLabel = pluralize(split.day_count || 0, 'day');
            const exerciseCountLabel = pluralize(split.exercise_count || 0, 'exercise');

            html += `
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center split-picker-item" data-split-id="${split.id}">
                    <div>
                        <h6 class="mb-1">${escapeHtml(split.split_name)}</h6>
                        <p class="mb-0 text-muted small">${escapeHtml(split.description || 'No description')}</p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary-subtle text-primary mb-1">${escapeHtml(dayCountLabel)}</div>
                        <div class="badge bg-secondary-subtle text-secondary">${escapeHtml(exerciseCountLabel)}</div>
                        ${isActive ? '<div class="badge bg-success ms-2">Active</div>' : ''}
                    </div>
                </button>
            `;
        });
        html += '</div>';

        container.html(html);

        $('.split-picker-item').click(function() {
            const splitId = parseInt($(this).data('split-id'), 10);
            if (!splitId) {
                return;
            }

            if (activeSplit && activeSplit.id === splitId) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('selectSplitModal'));
                if (modal) {
                    modal.hide();
                }
                return;
            }

            activateSplit(splitId, function(success) {
                if (success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('selectSplitModal'));
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        });
    }

    function displayActiveSplit() {
        $('#splitName').text(activeSplit.split_name);
        $('#splitDescription').text(activeSplit.description || 'No description');
        $('#splitDaysCount').text(`${activeSplit.days ? activeSplit.days.length : 0} days`);
        $('#activeSplitInfo').show();
        $('#noActiveSplit').hide();
    }

    function showNoActiveSplit(message) {
        if (message) {
            $('#noActiveSplit .text-muted').text(message);
        } else {
            $('#noActiveSplit .text-muted').text('Select a workout split to start tracking your progress');
        }
        $('#activeSplitInfo').hide();
        $('#calendarView').hide();
        $('#splitDaysOverview').hide();
        $('#noActiveSplit').show();
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        $('#currentMonth').text(`${monthNames[month]} ${year}`);

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();

        let html = '';

        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonthLastDay - i;
            html += `<div class="calendar-day other-month">
                        <div class="calendar-day-number">${day}</div>
                    </div>`;
        }

        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = date.toDateString() === today.toDateString();
            const dayOfWeek = date.getDay();
            const workoutDay = getWorkoutForDay(dayOfWeek);
            const workoutLabel = workoutDay ? escapeHtml(workoutDay.day_name || 'Workout') : '';

            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (workoutDay) classes += ' has-workout';

            html += `<div class="${classes}" data-date="${date.toISOString().split('T')[0]}">
                        <div class="calendar-day-number">${day}</div>
                        <div class="calendar-day-content">`;

            if (workoutDay) {
                html += `<div class="workout-badge">${workoutLabel}</div>`;
            }

            html += `</div></div>`;
        }

        const remainingDays = 42 - (startingDayOfWeek + daysInMonth);
        for (let day = 1; day <= remainingDays; day++) {
            html += `<div class="calendar-day other-month">
                        <div class="calendar-day-number">${day}</div>
                    </div>`;
        }

        $('#calendarGrid').html(html);

        $('.calendar-day:not(.other-month)').click(function() {
            const date = $(this).data('date');
            if (date) {
                showWorkoutForDate(date);
            }
        });
    }

    function getWorkoutForDay(dayOfWeek) {
        if (!activeSplit || !Array.isArray(activeSplit.days)) return null;

        const dbDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
        return activeSplit.days.find(day => day.day_of_week === dbDayOfWeek);
    }

    function renderSplitDays() {
        if (!activeSplit || !Array.isArray(activeSplit.days)) return;

        let html = '';

        activeSplit.days.forEach(day => {
            const exerciseCount = Array.isArray(day.exercises) ? day.exercises.length : 0;
            const dayOfWeekLabel = day.day_of_week ? (dayNames[day.day_of_week] || `Day ${day.day_of_week}`) : 'Flexible day';

            const exercisesPreview = Array.isArray(day.exercises) ? day.exercises.slice(0, 3).map(ex => `<div>• ${escapeHtml(ex.name)}</div>`).join('') : '';

            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm split-day-card h-100" data-day-id="${day.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">${escapeHtml(day.day_name)}</h6>
                                <span class="badge exercise-count-badge">${exerciseCount} exercises</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-calendar-event"></i> ${escapeHtml(dayOfWeekLabel)}
                            </p>
                            ${exerciseCount > 0 ? `<div class="small text-muted">${exercisesPreview}${exerciseCount > 3 ? `<div class="text-primary">+${exerciseCount - 3} more...</div>` : ''}</div>` : '<div class="small text-muted">No exercises assigned yet.</div>'}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm manage-exercises-btn" data-day-id="${day.id}">
                                    <i class="bi bi-sliders"></i> Manage exercises
                                </button>
                                ${exerciseCount === 0 ? '<span class="text-muted small">Add your first exercise</span>' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#splitDaysList').html(html);
        $('#splitDaysCount').text(`${activeSplit.days.length} days`);

        $('.split-day-card').click(function(event) {
            const dayId = $(this).data('day-id');
            showDayDetails(dayId);
        });

        $('.manage-exercises-btn').click(function(event) {
            event.stopPropagation();
            const dayId = parseInt($(this).data('day-id'), 10);
            openManageDayModal(dayId);
        });
    }

    function showWorkoutForDate(date) {
        const dateObj = new Date(date);
        const dayOfWeek = dateObj.getDay();
        const workoutDay = getWorkoutForDay(dayOfWeek);

        if (workoutDay) {
            showDayDetails(workoutDay.id);
        }
    }

    function showDayDetails(dayId) {
        const day = activeSplit && activeSplit.days ? activeSplit.days.find(d => d.id == dayId) : null;
        if (!day) return;

        let html = '';
        if (day.exercises && day.exercises.length > 0) {
            day.exercises.forEach(exercise => {
                html += `
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${escapeHtml(exercise.name)}</h6>
                                <p class="mb-1 text-muted small">
                                    ${escapeHtml(exercise.muscle_group)} | ${escapeHtml(exercise.equipment)}
                                </p>
                                <div class="badge bg-primary-subtle text-primary me-1">
                                    ${escapeHtml(`${exercise.target_sets} sets`)}
                                </div>
                                <div class="badge bg-primary-subtle text-primary me-1">
                                    ${escapeHtml(`${exercise.target_reps} reps`)}
                                </div>
                                <div class="badge bg-primary-subtle text-primary">
                                    ${escapeHtml(`${exercise.target_rest_seconds}s rest`)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="text-center py-4 text-muted">No exercises assigned</div>';
        }

        $('#workoutDaysList').html(html);
        $('#startWorkoutModal .modal-title').text(`${day.day_name} - Exercises`);
        $('#startWorkoutModal').modal('show');
    }

    function openManageDayModal(dayId) {
        if (!activeSplit || !Array.isArray(activeSplit.days)) {
            return;
        }

        const targetDay = activeSplit.days.find(day => day.id === dayId || day.id == dayId);
        if (!targetDay) {
            alert('Workout day not found.');
            return;
        }

        manageDaySelectedId = targetDay.id;
        $('#manageDayModalLabel').text(targetDay.day_name || 'Workout Day');

        const summaryParts = [];
        if (targetDay.day_of_week) {
            summaryParts.push(dayNames[targetDay.day_of_week] || `Day ${targetDay.day_of_week}`);
        }
        const exerciseCount = Array.isArray(targetDay.exercises) ? targetDay.exercises.length : 0;
        summaryParts.push(pluralize(exerciseCount, 'exercise'));
        $('#manageDaySummary').text(summaryParts.map(escapeHtml).join(' • '));
        $('#manageDayExtraInfo').toggle(summaryParts.length > 0);

        renderManageDayExercises(targetDay);
        resetAddExerciseForm();
        performExerciseSearch(lastExerciseSearchQuery || '');

        if (manageDayModalElement) {
            manageDayModalInstance = bootstrap.Modal.getOrCreateInstance(manageDayModalElement);
            manageDayModalInstance.show();
        }
    }

    function renderManageDayExercises(day) {
        const container = $('#manageDayExercisesList');

        if (!day.exercises || day.exercises.length === 0) {
            container.html('<div class="text-center py-4 text-muted">No exercises assigned yet. Use the form below to add one.</div>');
            return;
        }

        let html = '';
        day.exercises.forEach(exercise => {
            const setsValue = exercise.target_sets !== null && exercise.target_sets !== undefined ? exercise.target_sets : '';
            const repsValue = exercise.target_reps !== null && exercise.target_reps !== undefined ? exercise.target_reps : '';
            const restValue = exercise.target_rest_seconds !== null && exercise.target_rest_seconds !== undefined ? exercise.target_rest_seconds : '';
            const notesValue = exercise.notes ? exercise.notes : '';

            html += `
                <div class="exercise-manage-card p-3 mb-3" data-entry-id="${exercise.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${escapeHtml(exercise.name)}</h6>
                            <div class="text-muted small">${escapeHtml(exercise.muscle_group)} • ${escapeHtml(exercise.equipment)}</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-exercise-btn" data-entry-id="${exercise.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-sm-4">
                            <label class="form-label small mb-1">Sets</label>
                            <input type="number" class="form-control form-control-sm exercise-sets-input" min="1" value="${escapeHtml(setsValue)}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small mb-1">Reps</label>
                            <input type="text" class="form-control form-control-sm exercise-reps-input" value="${escapeHtml(repsValue)}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small mb-1">Rest (sec)</label>
                            <input type="number" class="form-control form-control-sm exercise-rest-input" min="0" value="${escapeHtml(restValue)}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label small mb-1">Notes</label>
                        <textarea class="form-control form-control-sm exercise-notes-input" rows="2">${escapeHtml(notesValue)}</textarea>
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-sm btn-primary save-exercise-btn" data-entry-id="${exercise.id}">
                            <i class="bi bi-save"></i> Save changes
                        </button>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    function resetAddExerciseForm() {
        selectedExerciseIdForAdd = null;
        lastExerciseSearchQuery = '';
        $('#exerciseSearchInput').val('');
        $('#selectedExerciseId').val('');
        $('#exerciseSearchResults').hide().empty();
        $('#selectedExerciseSummary').text('No exercise selected.');
        $('#addExerciseSets').val(3);
        $('#addExerciseReps').val('8-12');
        $('#addExerciseRest').val(90);
        $('#addExerciseNotes').val('');
        updateAddExerciseSubmitState();
    }

    function performExerciseSearch(query) {
        const trimmedQuery = query || '';
        lastExerciseSearchQuery = trimmedQuery;

        const resultsContainer = $('#exerciseSearchResults');
        resultsContainer.show().html('<div class="list-group-item text-muted">Searching...</div>');

        $.ajax({
            url: '../handlers/searchExercises.php',
            method: 'GET',
            dataType: 'json',
            data: {
                q: trimmedQuery,
                limit: 15
            },
            success: function(response) {
                if (!response.success) {
                    resultsContainer.html('<div class="list-group-item text-danger">Unable to load exercises.</div>');
                    return;
                }

                renderExerciseSearchResults(response.data || []);
            },
            error: function() {
                resultsContainer.html('<div class="list-group-item text-danger">Unable to load exercises.</div>');
            }
        });
    }

    function renderExerciseSearchResults(list) {
        const resultsContainer = $('#exerciseSearchResults');
        if (!Array.isArray(list) || list.length === 0) {
            resultsContainer.html('<div class="list-group-item text-muted">No exercises found.</div>');
            return;
        }

        let html = '';
        list.forEach(item => {
            const exerciseId = parseInt(item.id, 10);
            const muscleGroup = item.muscle_group ? item.muscle_group : 'General';
            const equipment = item.equipment ? item.equipment : 'Mixed';
            const isActive = selectedExerciseIdForAdd === exerciseId;

            html += `
                <button type="button" class="list-group-item list-group-item-action exercise-search-result ${isActive ? 'active' : ''}" data-exercise-id="${exerciseId}" data-exercise-name="${escapeHtml(item.name)}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">${escapeHtml(item.name)}</div>
                            <div class="text-muted small">${escapeHtml(muscleGroup)} • ${escapeHtml(equipment)}</div>
                        </div>
                        ${isActive ? '<i class="bi bi-check-lg"></i>' : ''}
                    </div>
                </button>
            `;
        });

        resultsContainer.html(html);
    }

    function updateAddExerciseSubmitState() {
        const setsVal = parseInt($('#addExerciseSets').val(), 10);
        const repsVal = ($('#addExerciseReps').val() || '').trim();
        const restVal = parseInt($('#addExerciseRest').val(), 10);

        const isValid = selectedExerciseIdForAdd !== null
            && !Number.isNaN(setsVal) && setsVal > 0
            && repsVal !== ''
            && !Number.isNaN(restVal) && restVal >= 0;

        $('#addExerciseSubmit').prop('disabled', !isValid);
    }

    function refreshAndReopenManageModal() {
        if (manageDaySelectedId) {
            const reopenId = manageDaySelectedId;
            loadOverview(function() {
                openManageDayModal(reopenId);
            }, { skipLoadingState: true });
        } else {
            loadOverview();
        }
    }

    $('#prevMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    $('#nextMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    $('#changeSplitBtn, #chooseSplitBtn').click(function() {
        const modal = new bootstrap.Modal(document.getElementById('selectSplitModal'));
        modal.show();
    });

    $('#startWorkoutBtn').click(function() {
        if (!activeSplit || !activeSplit.days || activeSplit.days.length === 0) {
            alert('No workout days available');
            return;
        }

        const today = new Date().getDay();
        const todaysWorkout = getWorkoutForDay(today);

        if (todaysWorkout) {
            showDayDetails(todaysWorkout.id);
        } else {
            showDayDetails(activeSplit.days[0].id);
        }
    });

    $('#exerciseSearchInput').on('input', function() {
        const query = $(this).val();
        if (exerciseSearchTimeout) {
            clearTimeout(exerciseSearchTimeout);
        }
        exerciseSearchTimeout = setTimeout(function() {
            performExerciseSearch(query);
        }, 250);
    });

    $('#addExerciseSets, #addExerciseReps, #addExerciseRest').on('input', updateAddExerciseSubmitState);

    $('#exerciseSearchResults').on('click', '.exercise-search-result', function() {
        const exerciseId = parseInt($(this).data('exercise-id'), 10);
        const exerciseName = $(this).data('exercise-name');

        if (!exerciseId) {
            return;
        }

        selectedExerciseIdForAdd = exerciseId;
        $('#selectedExerciseId').val(exerciseId);
        $('#exerciseSearchInput').val(exerciseName);
        $('#selectedExerciseSummary').text(`Selected: ${exerciseName}`);
        $('#exerciseSearchResults .exercise-search-result').removeClass('active');
        $(this).addClass('active');
        updateAddExerciseSubmitState();
    });

    $('#addExerciseForm').on('submit', function(event) {
        event.preventDefault();

        if (!manageDaySelectedId || selectedExerciseIdForAdd === null) {
            return;
        }

        const payload = {
            split_day_id: manageDaySelectedId,
            exercise_id: selectedExerciseIdForAdd,
            target_sets: $('#addExerciseSets').val(),
            target_reps: ($('#addExerciseReps').val() || '').trim(),
            target_rest_seconds: $('#addExerciseRest').val(),
            notes: ($('#addExerciseNotes').val() || '').trim()
        };

        const submitBtn = $('#addExerciseSubmit');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Adding...');

        $.ajax({
            url: '../handlers/addSplitDayExercise.php',
            method: 'POST',
            dataType: 'json',
            data: payload,
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to add exercise.');
                    return;
                }
                refreshAndReopenManageModal();
            },
            error: function() {
                alert('Failed to add exercise. Please try again.');
            },
            complete: function() {
                submitBtn.html(originalHtml);
                resetAddExerciseForm();
            }
        });
    });

    $('#manageDayExercisesList').on('click', '.save-exercise-btn', function() {
        const button = $(this);
        const entryId = parseInt(button.data('entry-id'), 10);
        if (!entryId) {
            return;
        }

        const card = button.closest('.exercise-manage-card');
        const setsVal = parseInt(card.find('.exercise-sets-input').val(), 10);
        const repsVal = (card.find('.exercise-reps-input').val() || '').trim();
        const restVal = parseInt(card.find('.exercise-rest-input').val(), 10);
        const notesVal = (card.find('.exercise-notes-input').val() || '').trim();

        if (Number.isNaN(setsVal) || setsVal < 1) {
            alert('Please enter a valid number of sets (minimum 1).');
            return;
        }
        if (repsVal === '') {
            alert('Please enter target reps.');
            return;
        }
        if (Number.isNaN(restVal) || restVal < 0) {
            alert('Please enter rest time in seconds (0 or greater).');
            return;
        }

        const originalHtml = button.html();
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: '../handlers/updateSplitDayExercise.php',
            method: 'POST',
            dataType: 'json',
            data: {
                split_day_exercise_id: entryId,
                target_sets: setsVal,
                target_reps: repsVal,
                target_rest_seconds: restVal,
                notes: notesVal
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to update exercise.');
                    return;
                }
                refreshAndReopenManageModal();
            },
            error: function() {
                alert('Failed to update exercise. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('#manageDayExercisesList').on('click', '.delete-exercise-btn', function() {
        const button = $(this);
        const entryId = parseInt(button.data('entry-id'), 10);
        if (!entryId) {
            return;
        }

        if (!confirm('Remove this exercise from the day?')) {
            return;
        }

        const originalHtml = button.html();
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '../handlers/deleteSplitDayExercise.php',
            method: 'POST',
            dataType: 'json',
            data: {
                split_day_exercise_id: entryId
            },
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Unable to remove exercise.');
                    return;
                }
                refreshAndReopenManageModal();
            },
            error: function() {
                alert('Failed to remove exercise. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    function activateSplit(splitId, callback) {
        const target = userSplits.find(split => split.id === splitId);
        if (!target) {
            alert('Split not found.');
            return;
        }

        $.ajax({
            url: '../handlers/activateSplit.php',
            method: 'POST',
            data: { split_id: splitId },
            dataType: 'json',
            timeout: 45000,
            success: function(response) {
                if (response.success) {
                    loadOverview(function() {
                        if (typeof callback === 'function') {
                            callback(true);
                        }
                    });
                } else {
                    alert('Error: ' + (response.message || 'Failed to activate split'));
                    if (typeof callback === 'function') {
                        callback(false);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error activating split from workouts:', error);
                console.error('Response:', xhr.responseText);
                alert('Error activating split: ' + (error || 'Unknown error'));
                if (typeof callback === 'function') {
                    callback(false);
                }
            }
        });
    }
});
</script>
