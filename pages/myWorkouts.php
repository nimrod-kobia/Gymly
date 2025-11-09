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

// Fetch workout data server-side for instant page load
$db = (new Database())->connect();
$workoutData = [
    'active_split' => null,
    'splits' => [],
    'sessions' => []
];

if ($db) {
    try {
        // Fetch user's splits (custom + copies of presets)
        $splitsStmt = $db->prepare("SELECT id, split_name, split_type, description, is_active, created_at, updated_at
            FROM workout_splits
            WHERE user_id = :user_id
            ORDER BY is_active DESC, updated_at DESC");
        $splitsStmt->execute([':user_id' => $userId]);
        $splits = $splitsStmt->fetchAll(PDO::FETCH_ASSOC);

        $splitIds = array_map(fn($split) => (int)$split['id'], $splits);

        $dayCounts = [];
        $exerciseCounts = [];

        if (count($splitIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($splitIds), '?'));

            $dayStmt = $db->prepare("SELECT split_id, COUNT(*) AS day_count
                FROM split_days
                WHERE split_id IN ($placeholders)
                GROUP BY split_id");
            $dayStmt->execute($splitIds);
            foreach ($dayStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $dayCounts[(int)$row['split_id']] = (int)$row['day_count'];
            }

            $exerciseStmt = $db->prepare("SELECT sd.split_id, COUNT(*) AS exercise_count
                FROM split_day_exercises sde
                INNER JOIN split_days sd ON sde.split_day_id = sd.id
                WHERE sd.split_id IN ($placeholders)
                GROUP BY sd.split_id");
            $exerciseStmt->execute($splitIds);
            foreach ($exerciseStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $exerciseCounts[(int)$row['split_id']] = (int)$row['exercise_count'];
            }
        }

        $activeSplitId = null;
        foreach ($splits as &$split) {
            $splitId = (int)$split['id'];
            $split['id'] = $splitId;
            $split['is_active'] = filter_var($split['is_active'], FILTER_VALIDATE_BOOLEAN);
            $split['day_count'] = $dayCounts[$splitId] ?? 0;
            $split['exercise_count'] = $exerciseCounts[$splitId] ?? 0;

            if ($split['is_active']) {
                $activeSplitId = $splitId;
            }
        }
        unset($split);

        $workoutData['splits'] = $splits;

        // Fetch active split details with days and exercises
        if ($activeSplitId !== null) {
            $activeSplitStmt = $db->prepare("SELECT * FROM workout_splits WHERE id = :id AND user_id = :user_id LIMIT 1");
            $activeSplitStmt->execute([':id' => $activeSplitId, ':user_id' => $userId]);
            $activeSplit = $activeSplitStmt->fetch(PDO::FETCH_ASSOC);

            if ($activeSplit) {
                $activeSplit['id'] = (int)$activeSplit['id'];
                $activeSplit['is_active'] = true;

                $daysStmt = $db->prepare("SELECT * FROM split_days WHERE split_id = :split_id ORDER BY display_order ASC");
                $daysStmt->execute([':split_id' => $activeSplitId]);
                $days = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($days as &$day) {
                    $day['id'] = (int)$day['id'];
                    $day['split_id'] = (int)$day['split_id'];
                    $day['day_of_week'] = $day['day_of_week'] !== null ? (int)$day['day_of_week'] : null;
                    $day['display_order'] = $day['display_order'] !== null ? (int)$day['display_order'] : null;
                    $day['is_rest_day'] = filter_var($day['is_rest_day'], FILTER_VALIDATE_BOOLEAN);

                    $exerciseStmt = $db->prepare("SELECT sde.*, e.name, e.muscle_group, e.equipment,
                        ec.completed AS is_completed
                        FROM split_day_exercises sde
                        INNER JOIN exercises e ON sde.exercise_id = e.id
                        LEFT JOIN exercise_completions ec ON ec.exercise_id = sde.exercise_id 
                            AND ec.split_day_id = sde.split_day_id 
                            AND ec.user_id = :user_id 
                            AND ec.completion_date = CURRENT_DATE
                        WHERE sde.split_day_id = :day_id
                        ORDER BY sde.display_order ASC");
                    $exerciseStmt->execute([':day_id' => $day['id'], ':user_id' => $userId]);
                    $exercises = $exerciseStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($exercises as &$exercise) {
                        $exercise['id'] = (int)$exercise['id'];
                        $exercise['split_day_id'] = (int)$exercise['split_day_id'];
                        $exercise['exercise_id'] = (int)$exercise['exercise_id'];
                        $exercise['target_sets'] = $exercise['target_sets'] !== null ? (int)$exercise['target_sets'] : null;
                        $exercise['target_reps'] = $exercise['target_reps'] !== null ? (int)$exercise['target_reps'] : null;
                        $exercise['target_rest_seconds'] = $exercise['target_rest_seconds'] !== null ? (int)$exercise['target_rest_seconds'] : null;
                        $exercise['is_completed'] = filter_var($exercise['is_completed'], FILTER_VALIDATE_BOOLEAN);
                    }
                    unset($exercise);

                    $day['exercises'] = $exercises;
                }
                unset($day);

                $activeSplit['days'] = $days;
                $workoutData['active_split'] = $activeSplit;
            }
        }
    } catch (PDOException $e) {
        error_log('myWorkouts.php data fetch error: ' . $e->getMessage());
    }
}

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
                    <button type="button" class="btn btn-primary" id="chooseSplitBtn">
                        <i class="bi bi-plus-circle"></i> Choose a Split
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row g-4 align-items-stretch" id="calendarView" style="display: none;">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm calendar-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button class="btn btn-sm btn-outline-secondary" id="prevMonth">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <h5 class="mb-0" id="currentMonth">November 2025</h5>
                        <button class="btn btn-sm btn-outline-secondary" id="nextMonth">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100 calendar-sidebar">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Workout Days</h5>
                        <span class="badge bg-primary-subtle text-primary" id="splitDaysCountBadge">0</span>
                    </div>
                    <p class="text-muted small mb-3">Manage your recurring workouts and jump into a day with a single click.</p>
                    <div id="splitDaysList" class="flex-grow-1 overflow-auto">
                        <!-- Split days will be rendered here -->
                    </div>
                </div>
            </div>
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
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-success" id="markDayCompleteBtn">
                    <i class="bi bi-check-circle"></i> Done - Mark All Complete
                </button>
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
                            <div class="col-md-3">
                                <label class="form-label" for="muscleGroupSelect">Muscle Group</label>
                                <select class="form-select" id="muscleGroupSelect" required>
                                    <option value="">Select muscle group...</option>
                                    <option value="chest">Chest</option>
                                    <option value="back">Back</option>
                                    <option value="shoulders">Shoulders</option>
                                    <option value="arms">Arms</option>
                                    <option value="legs">Legs</option>
                                    <option value="core">Core</option>
                                    <option value="glutes">Glutes</option>
                                    <option value="full_body">Full Body</option>
                                    <option value="cardio">Cardio</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="exerciseSelect">Exercise</label>
                                <select class="form-select" id="exerciseSelect" required disabled>
                                    <option value="">Select muscle group first...</option>
                                </select>
                                <input type="hidden" id="selectedExerciseId" value="">
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseSets">Sets</label>
                                <input type="number" class="form-control" id="addExerciseSets" min="1" value="3" required>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseReps">Reps</label>
                                <input type="text" class="form-control" id="addExerciseReps" value="8-12" required>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="addExerciseRest">Rest (sec)</label>
                                <input type="number" class="form-control" id="addExerciseRest" min="0" value="90" required>
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
:root {
    --calendar-card-bg: rgba(255, 255, 255, 0.04);
    --calendar-card-border: rgba(255, 255, 255, 0.12);
    --calendar-day-bg: rgba(255, 255, 255, 0.07);
    --calendar-day-hover-border: rgba(102, 126, 234, 0.65);
    --calendar-text-muted: rgba(255, 255, 255, 0.65);
    --calendar-badge-bg: rgba(102, 126, 234, 0.26);
    --calendar-success-bg: rgba(25, 135, 84, 0.25);
    --calendar-danger-bg: rgba(220, 53, 69, 0.22);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.calendar-card,
.calendar-sidebar {
    background: var(--calendar-card-bg);
    border: 1px solid var(--calendar-card-border);
    border-radius: 18px;
}

.calendar-card .btn-outline-secondary {
    border-color: rgba(255, 255, 255, 0.25);
    color: var(--calendar-text-muted);
}

.calendar-card .btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.4);
    color: #fff;
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
    color: var(--calendar-text-muted);
    font-size: 0.85rem;
}

.calendar-day {
    aspect-ratio: 1;
    border: 1px solid var(--calendar-card-border);
    border-radius: 12px;
    padding: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: var(--calendar-day-bg);
    display: flex;
    flex-direction: column;
    position: relative;
}

.calendar-day:hover {
    border-color: var(--calendar-day-hover-border);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}

.calendar-day.other-month {
    opacity: 0.35;
}

.calendar-day.today {
    border-color: var(--calendar-day-hover-border);
    border-width: 2px;
    background: rgba(102, 126, 234, 0.32);
}

.calendar-day.has-workout {
    background: var(--calendar-badge-bg);
    border-color: rgba(102, 126, 234, 0.6);
}

.calendar-day.day-complete {
    background: var(--calendar-success-bg) !important;
    border-color: rgba(25, 135, 84, 0.7) !important;
}

.calendar-day.day-complete .workout-badge {
    background: rgba(25, 135, 84, 0.85) !important;
}

.calendar-day.day-incomplete {
    background: var(--calendar-danger-bg) !important;
    border-color: rgba(220, 53, 69, 0.65) !important;
}

.calendar-day.day-incomplete .workout-badge {
    background: rgba(220, 53, 69, 0.85) !important;
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
    color: var(--calendar-text-muted);
}

.workout-badge {
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(102, 126, 234, 0.75);
    color: #fff;
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

.calendar-sidebar .text-muted {
    color: var(--calendar-text-muted) !important;
}

.calendar-day-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.calendar-day-list-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--calendar-card-border);
    border-radius: 14px;
    padding: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.calendar-day-list-item:hover {
    border-color: var(--calendar-day-hover-border);
    background: rgba(102, 126, 234, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.28);
}

.calendar-day-list-item .day-meta {
    color: var(--calendar-text-muted);
    font-size: 0.8rem;
}

.calendar-day-list-item .exercise-preview {
    color: var(--calendar-text-muted);
    font-size: 0.78rem;
}

.calendar-manage-btn {
    border-color: rgba(255, 255, 255, 0.28);
    color: #fff;
    font-size: 0.78rem;
    border-radius: 999px;
    padding: 0.35rem 0.8rem;
}

.calendar-manage-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.45);
    color: #fff;
}

.exercise-count-badge {
    background: rgba(102, 126, 234, 0.2);
    color: #cfd5ff;
    font-weight: 600;
}

.exercise-manage-card {
    border: 1px solid var(--bs-border-color);
    border-radius: 10px;
    transition: all 0.3s;
}

.exercise-manage-card.completed {
    background-color: rgba(25, 135, 84, 0.05);
    border-color: rgba(25, 135, 84, 0.3);
}

.exercise-completion-checkbox {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.75rem;
    cursor: pointer;
}

.exercise-completion-checkbox:checked {
    background-color: #198754;
    border-color: #198754;
}

.exercise-day-checkbox {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.75rem;
    cursor: pointer;
}

.exercise-day-checkbox:checked {
    background-color: #198754;
    border-color: #198754;
}

.exercise-completed {
    background-color: rgba(25, 135, 84, 0.05);
    border-left: 3px solid #198754 !important;
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

<script>
// Embed server-side data for instant page load (no AJAX delay!)
const INITIAL_WORKOUT_DATA = <?php echo json_encode($workoutData, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

$(document).ready(function() {
    const dayNames = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    let activeSplit = null;
    let userSplits = [];
    let currentDate = new Date();
    let workoutSessions = [];
    let manageDayModalInstance = null;
    let manageDaySelectedId = null;
    let selectedExerciseIdForAdd = null;
    let currentViewingDayId = null; // Track which day is currently being viewed

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

    function formatDateLocal(dateObj) {
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    if (manageDayModalElement) {
        manageDayModalElement.addEventListener('hidden.bs.modal', function() {
            manageDaySelectedId = null;
            resetAddExerciseForm();
        });
        manageDayModalElement.addEventListener('shown.bs.modal', function() {
            $('#muscleGroupSelect').trigger('focus');
        });
    }

    // Muscle group dropdown change handler
    $('#muscleGroupSelect').on('change', function() {
        const muscleGroup = $(this).val();
        console.log('Muscle group selected:', muscleGroup);
        selectedExerciseIdForAdd = null;
        loadExercisesForMuscleGroup(muscleGroup);
        updateAddExerciseSubmitState();
    });

    // Exercise dropdown change handler
    $('#exerciseSelect').on('change', function() {
        selectedExerciseIdForAdd = $(this).val() || null;
        updateAddExerciseSubmitState();
    });


    // Load data instantly from embedded JSON (no AJAX needed!)
    loadInitialData();

    function loadInitialData() {
        userSplits = Array.isArray(INITIAL_WORKOUT_DATA.splits) ? INITIAL_WORKOUT_DATA.splits : [];
        activeSplit = INITIAL_WORKOUT_DATA.active_split || null;
        workoutSessions = Array.isArray(INITIAL_WORKOUT_DATA.sessions) ? INITIAL_WORKOUT_DATA.sessions : [];

        renderSplitPicker();

        if (activeSplit) {
            displayActiveSplit();
            renderCalendar();
            renderSplitDays();
            $('#calendarView').show();
        } else {
            showNoActiveSplit();
        }
    }

    function loadOverview(callback, options) {
        // Fallback AJAX loader for refresh actions (after activate/deactivate)
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
        const dayCount = activeSplit.days ? activeSplit.days.length : 0;
        $('#splitDaysCount').text(`${dayCount} days`);
        $('#splitDaysCountBadge').text(dayCount);
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
        $('#splitDaysList').html('<div class="text-center text-muted py-4">Choose a split to manage your workouts.</div>');
        $('#splitDaysCountBadge').text('0');
        $('#noActiveSplit').show();
    }

    function renderCalendar() {
        console.log('renderCalendar called. Active split:', activeSplit);
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
            
            // Check completion status for this date
            const dateStr = formatDateLocal(date);
            const completionStatus = getCompletionStatusForDate(workoutDay, dateStr);

            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (workoutDay) classes += ' has-workout';
            if (completionStatus === 'complete') classes += ' day-complete';
            else if (completionStatus === 'incomplete' && date < today) classes += ' day-incomplete';

            html += `<div class="${classes}" data-date="${dateStr}">
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

    function getCompletionStatusForDate(workoutDay, dateStr) {
        if (!workoutDay || !workoutDay.exercises || workoutDay.exercises.length === 0) {
            return null; // No workout scheduled
        }

        // Note: This checks today's completion only since we're querying CURRENT_DATE in SQL
        // For past dates, we'd need to fetch historical completion data
        const today = formatDateLocal(new Date());
        if (dateStr !== today) {
            return null; // Only check today for now
        }

        const totalExercises = workoutDay.exercises.length;
        const completedExercises = workoutDay.exercises.filter(ex => ex.is_completed).length;

        console.log('Completion check for', dateStr, ':', {
            totalExercises,
            completedExercises,
            exercises: workoutDay.exercises.map(ex => ({ name: ex.name, completed: ex.is_completed }))
        });

        if (completedExercises === 0) {
            return 'incomplete';
        } else if (completedExercises === totalExercises) {
            return 'complete';
        } else {
            return 'partial';
        }
    }

    function renderSplitDays() {
        if (!activeSplit || !Array.isArray(activeSplit.days)) {
            $('#splitDaysList').html('<div class="text-center text-muted py-4">No workout days yet. Create one to begin scheduling.</div>');
            $('#splitDaysCount').text('0 days');
            $('#splitDaysCountBadge').text('0');
            return;
        }

        if (!activeSplit.days.length) {
            $('#splitDaysList').html('<div class="text-center text-muted py-4">No workout days yet. Create one to begin scheduling.</div>');
            $('#splitDaysCount').text('0 days');
            $('#splitDaysCountBadge').text('0');
            return;
        }

        let html = '<div class="calendar-day-list">';

        activeSplit.days.forEach(day => {
            const exerciseCount = Array.isArray(day.exercises) ? day.exercises.length : 0;
            const dayOfWeekLabel = day.day_of_week ? (dayNames[day.day_of_week] || `Day ${day.day_of_week}`) : 'Flexible day';
            const exerciseLabel = exerciseCount === 1 ? '1 exercise' : `${exerciseCount} exercises`;

            let previewHtml = '';
            if (Array.isArray(day.exercises) && day.exercises.length > 0) {
                const previewItems = day.exercises.slice(0, 2).map(ex => `<span class="d-block">• ${escapeHtml(ex.name)}</span>`).join('');
                const moreCount = day.exercises.length > 2 ? `<span class="text-primary d-block small">+${day.exercises.length - 2} more</span>` : '';
                previewHtml = `<div class="exercise-preview mt-2">${previewItems}${moreCount}</div>`;
            }

            html += `
                <div class="calendar-day-list-item" data-day-id="${day.id}">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-semibold mb-1">${escapeHtml(day.day_name)}</div>
                            <div class="day-meta">${escapeHtml(dayOfWeekLabel)} • ${exerciseLabel}</div>
                            ${previewHtml}
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <button type="button" class="btn btn-outline-light btn-sm calendar-manage-btn d-inline-flex align-items-center gap-1" data-day-id="${day.id}" title="Manage exercises">
                                <i class="bi bi-sliders"></i>
                                <span>Manage</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';

        $('#splitDaysList').html(html);
        $('#splitDaysCount').text(`${activeSplit.days.length} days`);
        $('#splitDaysCountBadge').text(activeSplit.days.length);

        $('#splitDaysList .calendar-day-list-item').on('click', function() {
            const dayId = $(this).data('day-id');
            showDayDetails(dayId);
        });

        $('#splitDaysList .calendar-manage-btn').on('click', function(event) {
            event.stopPropagation();
            const dayId = parseInt($(this).data('day-id'), 10);
            openManageDayModal(dayId);
        });
    }

    function showWorkoutForDate(date) {
        const [yearRaw, monthRaw, dayRaw] = date.split('-');
        const year = parseInt(yearRaw, 10);
        const month = parseInt(monthRaw, 10);
        const day = parseInt(dayRaw, 10);

        if (Number.isNaN(year) || Number.isNaN(month) || Number.isNaN(day)) {
            console.warn('Invalid date provided to showWorkoutForDate:', date);
            return;
        }

        const dateObj = new Date(year, month - 1, day);
        const dayOfWeek = dateObj.getDay();
        const workoutDay = getWorkoutForDay(dayOfWeek);

        if (workoutDay) {
            showDayDetails(workoutDay.id);
        }
    }

    function showDayDetails(dayId) {
        const day = activeSplit && activeSplit.days ? activeSplit.days.find(d => d.id == dayId) : null;
        if (!day) return;

        currentViewingDayId = dayId; // Store current day ID

        let html = '';
        if (day.exercises && day.exercises.length > 0) {
            day.exercises.forEach(exercise => {
                const isCompleted = exercise.is_completed || false;
                html += `
                    <div class="list-group-item list-group-item-action ${isCompleted ? 'exercise-completed' : ''}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="form-check flex-grow-1">
                                <input class="form-check-input exercise-day-checkbox" type="checkbox" 
                                       id="day-exercise-${exercise.exercise_id}" 
                                       data-split-day-id="${exercise.split_day_id}"
                                       data-exercise-id="${exercise.exercise_id}"
                                       ${isCompleted ? 'checked' : ''}>
                                <label class="form-check-label w-100" for="day-exercise-${exercise.exercise_id}">
                                    <h6 class="mb-1 ${isCompleted ? 'text-decoration-line-through text-muted' : ''}">${escapeHtml(exercise.name)}</h6>
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
                                </label>
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
            const isCompleted = exercise.is_completed || false;

            html += `
                <div class="exercise-manage-card p-3 mb-3 ${isCompleted ? 'completed' : ''}" data-entry-id="${exercise.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="form-check">
                            <input class="form-check-input exercise-completion-checkbox" type="checkbox" 
                                   id="exercise-${exercise.exercise_id}" 
                                   data-split-day-id="${exercise.split_day_id}"
                                   data-exercise-id="${exercise.exercise_id}"
                                   ${isCompleted ? 'checked' : ''}>
                            <label class="form-check-label" for="exercise-${exercise.exercise_id}">
                                <h6 class="mb-1 ${isCompleted ? 'text-decoration-line-through text-muted' : ''}">${escapeHtml(exercise.name)}</h6>
                                <div class="text-muted small">${escapeHtml(exercise.muscle_group)} • ${escapeHtml(exercise.equipment)}</div>
                            </label>
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
        $('#muscleGroupSelect').val('');
        $('#exerciseSelect').prop('disabled', true).html('<option value="">Select muscle group first...</option>');
        $('#selectedExerciseId').val('');
        $('#addExerciseSets').val(3);
        $('#addExerciseReps').val('8-12');
        $('#addExerciseRest').val(90);
        $('#addExerciseNotes').val('');
        updateAddExerciseSubmitState();
    }

    function loadExercisesForMuscleGroup(muscleGroup) {
        if (!muscleGroup) {
            $('#exerciseSelect').prop('disabled', true).html('<option value="">Select muscle group first...</option>');
            return;
        }

        $('#exerciseSelect').prop('disabled', true).html('<option value="">Loading...</option>');

        $.ajax({
            url: '../handlers/searchExercises.php',
            method: 'GET',
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            data: {
                muscle_group: muscleGroup,
                limit: 50
            },
            success: function(response) {
                console.log('Exercise search response:', response);
                
                if (!response || !response.success) {
                    $('#exerciseSelect').html('<option value="">Error: ' + (response?.message || 'Unknown error') + '</option>');
                    return;
                }

                if (!Array.isArray(response.data) || response.data.length === 0) {
                    $('#exerciseSelect').html('<option value="">No exercises found for this muscle group</option>');
                    return;
                }

                let html = '<option value="">Select an exercise...</option>';
                response.data.forEach(exercise => {
                    html += `<option value="${exercise.id}" data-name="${escapeHtml(exercise.name)}" data-equipment="${escapeHtml(exercise.equipment || 'N/A')}">${escapeHtml(exercise.name)}${exercise.equipment ? ' (' + escapeHtml(exercise.equipment) + ')' : ''}</option>`;
                });

                $('#exerciseSelect').prop('disabled', false).html(html);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading exercises:', status, error, xhr.responseText);
                $('#exerciseSelect').html('<option value="">Error loading exercises</option>');
            }
        });
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
            console.log('Refreshing data and reopening modal for day:', reopenId);
            loadOverview(function() {
                console.log('Data reloaded, reopening modal. Active split:', activeSplit);
                openManageDayModal(reopenId);
            }, { skipLoadingState: true });
        } else {
            console.log('Refreshing data without reopening modal');
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

    $('#addExerciseSets, #addExerciseReps, #addExerciseRest').on('input', updateAddExerciseSubmitState);

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

    // Handle exercise completion checkbox
    $('#manageDayExercisesList').on('change', '.exercise-completion-checkbox', function() {
        const checkbox = $(this);
        const splitDayId = parseInt(checkbox.data('split-day-id'), 10);
        const exerciseId = parseInt(checkbox.data('exercise-id'), 10);
        const completed = checkbox.prop('checked');
        const card = checkbox.closest('.exercise-manage-card');
        const label = checkbox.siblings('label').find('h6');

        $.ajax({
            url: '../handlers/toggleExerciseCompletion.php',
            method: 'POST',
            dataType: 'json',
            data: {
                split_day_id: splitDayId,
                exercise_id: exerciseId,
                completed: completed ? 1 : 0,
                completion_date: formatDateLocal(new Date())
            },
            success: function(response) {
                if (response.success) {
                    // Update UI
                    if (completed) {
                        card.addClass('completed');
                        label.addClass('text-decoration-line-through text-muted');
                    } else {
                        card.removeClass('completed');
                        label.removeClass('text-decoration-line-through text-muted');
                    }
                    // Reload data to update calendar colors
                    loadOverview();
                } else {
                    // Revert checkbox on error
                    checkbox.prop('checked', !completed);
                    alert(response.message || 'Failed to update completion status');
                }
            },
            error: function() {
                // Revert checkbox on error
                checkbox.prop('checked', !completed);
                alert('Failed to update completion status. Please try again.');
            }
        });
    });

    // Handle exercise completion checkbox on day details modal
    $('#workoutDaysList').on('change', '.exercise-day-checkbox', function() {
        const checkbox = $(this);
        const splitDayId = parseInt(checkbox.data('split-day-id'), 10);
        const exerciseId = parseInt(checkbox.data('exercise-id'), 10);
        const completed = checkbox.prop('checked');
        const listItem = checkbox.closest('.list-group-item');
        const label = checkbox.siblings('label').find('h6');

        $.ajax({
            url: '../handlers/toggleExerciseCompletion.php',
            method: 'POST',
            dataType: 'json',
            data: {
                split_day_id: splitDayId,
                exercise_id: exerciseId,
                completed: completed ? 1 : 0,
                completion_date: formatDateLocal(new Date())
            },
            success: function(response) {
                if (response.success) {
                    // Update UI
                    if (completed) {
                        listItem.addClass('exercise-completed');
                        label.addClass('text-decoration-line-through text-muted');
                    } else {
                        listItem.removeClass('exercise-completed');
                        label.removeClass('text-decoration-line-through text-muted');
                    }
                    // Reload data to update calendar colors
                    loadOverview();
                } else {
                    // Revert checkbox on error
                    checkbox.prop('checked', !completed);
                    alert(response.message || 'Failed to update completion status');
                }
            },
            error: function() {
                // Revert checkbox on error
                checkbox.prop('checked', !completed);
                alert('Failed to update completion status. Please try again.');
            }
        });
    });

    // Handle "Done - Mark All Complete" button
    $('#markDayCompleteBtn').on('click', function() {
        if (!currentViewingDayId) {
            return;
        }

        const day = activeSplit && activeSplit.days ? activeSplit.days.find(d => d.id == currentViewingDayId) : null;
        if (!day || !day.exercises || day.exercises.length === 0) {
            alert('No exercises to complete');
            return;
        }

        const button = $(this);
        const originalHtml = button.html();
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Marking complete...');

        // Mark all exercises as complete
        const completionPromises = day.exercises.map(exercise => {
            return $.ajax({
                url: '../handlers/toggleExerciseCompletion.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    split_day_id: exercise.split_day_id,
                    exercise_id: exercise.exercise_id,
                    completed: 1,
                    completion_date: formatDateLocal(new Date())
                }
            });
        });

        $.when.apply($, completionPromises).then(
            function() {
                // All completed successfully
                console.log('All exercises marked complete, reloading calendar...');
                
                // Check all checkboxes and update UI
                $('#workoutDaysList .exercise-day-checkbox').each(function() {
                    $(this).prop('checked', true);
                    const listItem = $(this).closest('.list-group-item');
                    const label = $(this).siblings('label').find('h6');
                    listItem.addClass('exercise-completed');
                    label.addClass('text-decoration-line-through text-muted');
                });

                // Reload data to update calendar colors - force a full reload
                loadOverview(function() {
                    console.log('Calendar reloaded after completion');
                });
                
                // Close modal after brief delay
                setTimeout(function() {
                    $('#startWorkoutModal').modal('hide');
                }, 800);
            },
            function() {
                alert('Failed to mark all exercises as complete. Please try again.');
            }
        ).always(function() {
            button.prop('disabled', false).html(originalHtml);
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

</main>

<?php include '../template/footer.php'; ?>
