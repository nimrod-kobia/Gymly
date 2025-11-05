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
                    <h2 class="mb-1">My Workouts</h2>
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
                    <a href="workoutSplits.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Choose a Split
                    </a>
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

<script>
$(document).ready(function() {
    let activeSplit = null;
    let currentDate = new Date();
    let workoutSessions = [];

    // Load active split and initialize
    loadActiveSplit();

    function loadActiveSplit() {
        console.log('Loading active split...');
        $.ajax({
            url: '../handlers/getActiveSplit.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Active split response:', response);
                if (response.success && response.data) {
                    activeSplit = response.data;
                    displayActiveSplit();
                    loadSplitDetails();
                } else {
                    console.log('No active split found');
                    showNoActiveSplit();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading active split:', error);
                console.error('Response:', xhr.responseText);
                showNoActiveSplit();
            }
        });
    }

    function loadSplitDetails() {
        console.log('Loading split details for split:', activeSplit.id);
        $.ajax({
            url: '../handlers/getSplitDetails.php',
            method: 'GET',
            data: { split_id: activeSplit.id },
            dataType: 'json',
            success: function(response) {
                console.log('Split details response:', response);
                if (response.success) {
                    activeSplit.days = response.data.days;
                    console.log('Loaded days:', activeSplit.days);
                    renderCalendar();
                    renderSplitDays();
                    $('#calendarView').show();
                    $('#splitDaysOverview').show();
                } else {
                    console.error('Failed to load split details:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading split details:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    function displayActiveSplit() {
        $('#splitName').text(activeSplit.split_name);
        $('#splitDescription').text(activeSplit.description || 'No description');
        $('#activeSplitInfo').show();
        $('#noActiveSplit').hide();
    }

    function showNoActiveSplit() {
        $('#activeSplitInfo').hide();
        $('#calendarView').hide();
        $('#splitDaysOverview').hide();
        $('#noActiveSplit').show();
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Update month display
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                          'July', 'August', 'September', 'October', 'November', 'December'];
        $('#currentMonth').text(`${monthNames[month]} ${year}`);

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();

        let html = '';

        // Day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Previous month's days
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonthLastDay - i;
            html += `<div class="calendar-day other-month">
                        <div class="calendar-day-number">${day}</div>
                    </div>`;
        }

        // Current month's days
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = date.toDateString() === today.toDateString();
            const dayOfWeek = date.getDay();
            
            // Check if this day has a scheduled workout based on split
            const workoutDay = getWorkoutForDay(dayOfWeek);
            
            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (workoutDay) classes += ' has-workout';

            html += `<div class="${classes}" data-date="${date.toISOString().split('T')[0]}">
                        <div class="calendar-day-number">${day}</div>
                        <div class="calendar-day-content">`;
            
            if (workoutDay) {
                html += `<div class="workout-badge">${workoutDay.day_name}</div>`;
            }
            
            html += `</div></div>`;
        }

        // Next month's days
        const remainingDays = 42 - (startingDayOfWeek + daysInMonth); // 6 rows * 7 days
        for (let day = 1; day <= remainingDays; day++) {
            html += `<div class="calendar-day other-month">
                        <div class="calendar-day-number">${day}</div>
                    </div>`;
        }

        $('#calendarGrid').html(html);

        // Add click handlers to calendar days
        $('.calendar-day:not(.other-month)').click(function() {
            const date = $(this).data('date');
            if (date) {
                showWorkoutForDate(date);
            }
        });
    }

    function getWorkoutForDay(dayOfWeek) {
        if (!activeSplit || !activeSplit.days) return null;
        
        // dayOfWeek: 0 = Sunday, 1 = Monday, etc.
        // day_of_week in DB: 1 = Monday, 7 = Sunday
        const dbDayOfWeek = dayOfWeek === 0 ? 7 : dayOfWeek;
        
        return activeSplit.days.find(day => day.day_of_week === dbDayOfWeek);
    }

    function renderSplitDays() {
        if (!activeSplit || !activeSplit.days) return;

        let html = '';
        const dayNames = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        activeSplit.days.forEach(day => {
            const exerciseCount = day.exercises ? day.exercises.length : 0;
            const dayName = dayNames[day.day_of_week] || 'Day ' + day.day_of_week;
            
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm split-day-card h-100" data-day-id="${day.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">${day.day_name}</h6>
                                <span class="badge exercise-count-badge">${exerciseCount} exercises</span>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-calendar-event"></i> ${dayName}
                            </p>
                            ${day.exercises && day.exercises.length > 0 ? `
                                <div class="small text-muted">
                                    ${day.exercises.slice(0, 3).map(ex => 
                                        `<div>• ${ex.name}</div>`
                                    ).join('')}
                                    ${day.exercises.length > 3 ? `<div class="text-primary">+${day.exercises.length - 3} more...</div>` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        $('#splitDaysList').html(html);
        $('#splitDaysCount').text(`${activeSplit.days.length} days`);

        // Add click handlers
        $('.split-day-card').click(function() {
            const dayId = $(this).data('day-id');
            showDayDetails(dayId);
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
        const day = activeSplit.days.find(d => d.id == dayId);
        if (!day) return;

        let html = '';
        if (day.exercises && day.exercises.length > 0) {
            day.exercises.forEach(exercise => {
                html += `
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${exercise.name}</h6>
                                <p class="mb-1 text-muted small">
                                    ${exercise.muscle_group} | ${exercise.equipment}
                                </p>
                                <div class="badge bg-primary-subtle text-primary me-1">
                                    ${exercise.target_sets} sets
                                </div>
                                <div class="badge bg-primary-subtle text-primary me-1">
                                    ${exercise.target_reps} reps
                                </div>
                                <div class="badge bg-primary-subtle text-primary">
                                    ${exercise.target_rest_seconds}s rest
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
        $('#startWorkoutModal .modal-title').text(day.day_name + ' - Exercises');
        $('#startWorkoutModal').modal('show');
    }

    // Calendar navigation
    $('#prevMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    $('#nextMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Button handlers
    $('#changeSplitBtn').click(function() {
        window.location.href = 'workoutSplits.php';
    });

    $('#startWorkoutBtn').click(function() {
        if (!activeSplit || !activeSplit.days || activeSplit.days.length === 0) {
            alert('No workout days available');
            return;
        }

        // Show today's workout or let them choose
        const today = new Date().getDay();
        const todaysWorkout = getWorkoutForDay(today);
        
        if (todaysWorkout) {
            showDayDetails(todaysWorkout.id);
        } else {
            // Show first available workout
            showDayDetails(activeSplit.days[0].id);
        }
    });
});
</script>
