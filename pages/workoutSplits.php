<?php
// workoutSplits.php — Workout Split Selection & Management

require_once "../autoload.php";

// Require user to be logged in
if (!SessionManager::isLoggedIn()) {
    header("Location: signInPage.php");
    exit;
}

SessionManager::updateActivity();
$userId = SessionManager::getUserId();

// Set page title for layout.php
$pageTitle = "Workout Splits - Gymly";
include '../template/layout.php';
?>

<div class="container mt-5 py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-light fw-bold mb-2">
                <i class="bi bi-calendar-week text-primary"></i> Choose Your Workout Split
            </h1>
            <p class="text-light-emphasis">
                Select a preset training program or create your own custom split
            </p>
        </div>
    </div>

    <!-- User's Active Split (if exists) -->
    <div id="activeSplitSection" class="row mb-5" style="display: none;">
        <div class="col-12">
            <div class="card bg-dark border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary mb-2">
                                <i class="bi bi-check-circle"></i> Active Split
                            </span>
                            <h4 class="text-light fw-bold mb-2" id="activeSplitName">Loading...</h4>
                            <p class="text-light-emphasis mb-0" id="activeSplitDescription">Loading...</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-light btn-sm" onclick="viewActiveSplit()">
                                <i class="bi bi-eye"></i> View Details
                            </button>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="deactivateSplit()">
                                <i class="bi bi-x-circle"></i> Deactivate
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preset Workout Splits -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="text-light fw-bold mb-3">
                <i class="bi bi-star-fill text-warning"></i> Preset Programs
            </h3>
            <p class="text-light-emphasis mb-4">
                Professionally designed workout splits ready to use
            </p>
        </div>
    </div>

    <div class="row g-4 mb-5" id="presetSplitsContainer">
        <!-- Preset splits will be loaded here via AJAX -->
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- User's Custom Splits -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="text-light fw-bold mb-2">
                        <i class="bi bi-pencil-square text-success"></i> My Custom Splits
                    </h3>
                    <p class="text-light-emphasis mb-0">
                        Your personalized training programs
                    </p>
                </div>
                <button class="btn btn-success" onclick="showCreateSplitModal()">
                    <i class="bi bi-plus-circle"></i> Create New Split
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4" id="customSplitsContainer">
        <!-- Custom splits will be loaded here via AJAX -->
        <div class="col-12">
            <div class="card bg-dark border-secondary">
                <div class="card-body text-center py-5">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No custom splits yet. Create your first one!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Split Details Modal -->
<div class="modal fade" id="splitDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="splitDetailsTitle">Split Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="splitDetailsBody">
                <!-- Split details will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="activateSplitBtn" onclick="activateSplit()">
                    <i class="bi bi-check-circle"></i> Activate This Split
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Split Modal -->
<div class="modal fade" id="createSplitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle text-success"></i> Create Custom Split
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createSplitForm">
                    <div class="mb-3">
                        <label class="form-label">Split Name</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" 
                               name="split_name" placeholder="e.g., My Custom PPL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  name="description" rows="2" 
                                  placeholder="Brief description of your split"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Number of Training Days per Week</label>
                        <select class="form-select bg-dark text-light border-secondary" 
                                name="training_days" id="trainingDaysSelect" required>
                            <option value="3">3 Days</option>
                            <option value="4">4 Days</option>
                            <option value="5">5 Days</option>
                            <option value="6" selected>6 Days</option>
                            <option value="7">7 Days</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        After creating your split, you'll be able to add exercises to each day.
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="createSplit()">
                    <i class="bi bi-check-circle"></i> Create Split
                </button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
let currentSplitId = null;

$(document).ready(function() {
    loadActiveSplit();
    loadPresetSplits();
    loadCustomSplits();
});

// Load user's active split
function loadActiveSplit() {
    $.ajax({
        url: '../handlers/getActiveSplit.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.split) {
                $('#activeSplitSection').show();
                $('#activeSplitName').text(response.split.split_name);
                $('#activeSplitDescription').text(response.split.description || 'No description');
                currentSplitId = response.split.id;
            }
        }
    });
}

// Load preset splits
function loadPresetSplits() {
    $.ajax({
        url: '../handlers/fetchWorkoutSplits.php?type=preset',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.splits.length > 0) {
                renderSplits(response.splits, 'presetSplitsContainer', 'preset');
            } else {
                $('#presetSplitsContainer').html(
                    '<div class="col-12"><p class="text-muted">No preset splits available.</p></div>'
                );
            }
        },
        error: function() {
            $('#presetSplitsContainer').html(
                '<div class="col-12"><p class="text-danger">Error loading preset splits.</p></div>'
            );
        }
    });
}

// Load user's custom splits
function loadCustomSplits() {
    $.ajax({
        url: '../handlers/fetchWorkoutSplits.php?type=custom',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.splits.length > 0) {
                renderSplits(response.splits, 'customSplitsContainer', 'custom');
            }
        }
    });
}

// Render splits cards
function renderSplits(splits, containerId, type) {
    let html = '';
    splits.forEach(split => {
        const isActive = split.is_active == 1;
        const badgeClass = type === 'preset' ? 'bg-warning' : 'bg-success';
        const badgeText = type === 'preset' ? 'Preset' : 'Custom';
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="card bg-dark border-secondary h-100 ${isActive ? 'border-primary' : ''}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge ${badgeClass}">${badgeText}</span>
                            ${isActive ? '<span class="badge bg-primary"><i class="bi bi-check-circle"></i> Active</span>' : ''}
                        </div>
                        <h5 class="text-light fw-bold mb-2">${split.split_name}</h5>
                        <p class="text-light-emphasis small mb-3">
                            ${split.description || 'No description available'}
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-light flex-fill" 
                                    onclick="viewSplitDetails(${split.id})">
                                <i class="bi bi-eye"></i> View
                            </button>
                            ${!isActive ? `
                                <button class="btn btn-sm btn-primary flex-fill" 
                                        onclick="viewSplitDetails(${split.id})">
                                    <i class="bi bi-check-circle"></i> Activate
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    $('#' + containerId).html(html);
}

// View split details
function viewSplitDetails(splitId) {
    currentSplitId = splitId;
    const modal = new bootstrap.Modal(document.getElementById('splitDetailsModal'));
    
    // Reset modal content
    $('#splitDetailsBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    modal.show();
    
    // Load split details
    $.ajax({
        url: '../handlers/getSplitDetails.php?id=' + splitId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderSplitDetails(response.split, response.days);
            } else {
                $('#splitDetailsBody').html('<p class="text-danger">Error loading split details.</p>');
            }
        },
        error: function() {
            $('#splitDetailsBody').html('<p class="text-danger">Error loading split details.</p>');
        }
    });
}

// Render split details
function renderSplitDetails(split, days) {
    $('#splitDetailsTitle').text(split.split_name);
    
    let html = `
        <div class="mb-4">
            <h6 class="text-light fw-bold">Description</h6>
            <p class="text-light-emphasis">${split.description || 'No description'}</p>
        </div>
        <div>
            <h6 class="text-light fw-bold mb-3">Training Schedule</h6>
    `;
    
    if (days && days.length > 0) {
        days.forEach(day => {
            const dayOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][day.day_of_week || 0];
            const exerciseCount = day.exercises ? day.exercises.length : 0;
            
            html += `
                <div class="card bg-black border-secondary mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2">${dayOfWeek}</span>
                                <strong class="text-light">${day.day_name}</strong>
                            </div>
                            <span class="text-light-emphasis small">
                                ${exerciseCount} exercise${exerciseCount !== 1 ? 's' : ''}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        html += '<p class="text-muted">No days configured yet.</p>';
    }
    
    html += '</div>';
    $('#splitDetailsBody').html(html);
}

// Activate split
function activateSplit() {
    if (!currentSplitId) return;
    
    console.log('Activating split:', currentSplitId);
    
    $.ajax({
        url: '../handlers/activateSplit.php',
        method: 'POST',
        data: { split_id: currentSplitId },
        dataType: 'json',
        success: function(response) {
            console.log('Activate response:', response);
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('splitDetailsModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (response.message || 'Failed to activate split'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Activate error:', error);
            console.error('Response text:', xhr.responseText);
            alert('Error activating split: ' + error + '\n\nCheck console for details.');
        }
    });
}

// Show create split modal
function showCreateSplitModal() {
    const modal = new bootstrap.Modal(document.getElementById('createSplitModal'));
    $('#createSplitForm')[0].reset();
    modal.show();
}

// Create new split
function createSplit() {
    const formData = $('#createSplitForm').serialize();
    
    $.ajax({
        url: '../handlers/createSplit.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('createSplitModal')).hide();
                loadCustomSplits();
                alert('Split created successfully!');
            } else {
                alert('Error: ' + (response.message || 'Failed to create split'));
            }
        }
    });
}

// View active split
function viewActiveSplit() {
    if (currentSplitId) {
        viewSplitDetails(currentSplitId);
    }
}

// Deactivate split
function deactivateSplit() {
    if (!confirm('Are you sure you want to deactivate your current split?')) return;
    
    $.ajax({
        url: '../handlers/deactivateSplit.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + (response.message || 'Failed to deactivate split'));
            }
        }
    });
}
</script>

<?php include '../template/footer.php'; ?>
