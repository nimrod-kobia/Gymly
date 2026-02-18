<?php
require_once "../autoload.php";

$pageTitle = "Document Hub";
include "../template/layout.php";
?>

<div class="container py-5 mt-5 text-light">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-file-earmark-pdf-fill text-danger"></i> Company & Employee Document Hub</h1>
            <p class="text-secondary mb-0">Publish and search downloadable PDFs across companies and employees.</p>
        </div>
    </div>

    <?php if (SessionManager::isLoggedIn()): ?>
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card bg-black border-secondary h-100">
                <div class="card-body">
                    <h2 class="h5">Create Company</h2>
                    <form id="createCompanyForm">
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required maxlength="150">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Website URL</label>
                            <input type="url" name="website_url" class="form-control" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Create Company</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-black border-secondary h-100">
                <div class="card-body">
                    <h2 class="h5">Assign Employee</h2>
                    <form id="assignEmployeeForm">
                        <div class="mb-2">
                            <label class="form-label">Company</label>
                            <select name="company_id" id="assignCompanyId" class="form-select" required></select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Find User</label>
                            <input type="text" id="userLookup" class="form-control" placeholder="Type username/email">
                            <select name="user_id" id="userLookupResults" class="form-select mt-2" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position_title" class="form-control" maxlength="120" value="Employee">
                        </div>
                        <button class="btn btn-outline-light w-100" type="submit">Assign</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-black border-secondary h-100">
                <div class="card-body">
                    <h2 class="h5">Publish PDF</h2>
                    <form id="uploadPublicationForm" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required maxlength="200">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Company (optional)</label>
                            <select name="company_id" id="publicationCompanyId" class="form-select"></select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="keywords" class="form-control" placeholder="shipping, compliance, policy">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Summary</label>
                            <textarea name="summary" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PDF (max 25MB)</label>
                            <input type="file" name="pdf_file" class="form-control" accept="application/pdf" required>
                        </div>
                        <button class="btn btn-danger w-100" type="submit">Publish PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card bg-black border-secondary">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-lg-8">
                    <input type="search" id="searchInput" class="form-control" placeholder="Search title, summary, keywords, company...">
                </div>
                <div class="col-lg-4">
                    <select id="filterCompanyId" class="form-select"></select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle" id="publicationsTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Publisher</th>
                            <th>Company</th>
                            <th>Published</th>
                            <th>Downloads</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <small id="resultMeta" class="text-secondary"></small>
                <div class="d-flex gap-2">
                    <button id="prevPage" class="btn btn-outline-light btn-sm">Prev</button>
                    <button id="nextPage" class="btn btn-outline-light btn-sm">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let offset = 0;
const limit = 10;
let total = 0;

function showMessage(message, type = 'info') {
    const cls = type === 'error' ? 'alert-danger' : 'alert-success';
    const alert = document.createElement('div');
    alert.className = `alert ${cls} position-fixed top-0 end-0 m-3`;
    alert.style.zIndex = 9999;
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 2500);
}

async function loadCompanies() {
    const res = await fetch('../handlers/fetchCompanies.php');
    const payload = await res.json();
    if (!payload.success) return;

    const options = ['<option value="">All companies</option>'].concat(
        payload.data.map(c => `<option value="${c.id}">${c.name}</option>`)
    ).join('');

    const optionalOptions = '<option value="">Independent publication (no company)</option>' + payload.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

    const filter = document.getElementById('filterCompanyId');
    const assign = document.getElementById('assignCompanyId');
    const publication = document.getElementById('publicationCompanyId');

    if (filter) filter.innerHTML = options;
    if (assign) assign.innerHTML = '<option value="">Select company</option>' + payload.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    if (publication) publication.innerHTML = optionalOptions;
}

function renderPublications(rows) {
    const tbody = document.querySelector('#publicationsTable tbody');
    tbody.innerHTML = rows.map((row) => {
        const title = row.title || '';
        const summary = row.summary || '';
        return `
            <tr>
                <td>
                    <div class="fw-semibold">${title}</div>
                    <small class="text-secondary">${summary.slice(0, 120)}</small>
                </td>
                <td>${row.publisher_username}</td>
                <td>${row.company_name || '<span class="text-secondary">Independent</span>'}</td>
                <td>${new Date(row.published_at).toLocaleDateString()}</td>
                <td>${row.downloads_count}</td>
                <td><a class="btn btn-sm btn-danger" href="../handlers/downloadPublication.php?id=${row.id}">Download PDF</a></td>
            </tr>
        `;
    }).join('');

    document.getElementById('resultMeta').textContent = `Showing ${Math.min(offset + 1, total)} - ${Math.min(offset + limit, total)} of ${total}`;
    document.getElementById('prevPage').disabled = offset === 0;
    document.getElementById('nextPage').disabled = offset + limit >= total;
}

async function loadPublications() {
    const q = encodeURIComponent(document.getElementById('searchInput').value.trim());
    const companyId = document.getElementById('filterCompanyId').value;
    const res = await fetch(`../handlers/fetchPublications.php?q=${q}&company_id=${companyId}&limit=${limit}&offset=${offset}`);
    const payload = await res.json();

    if (!payload.success) {
        showMessage(payload.message || 'Failed to fetch publications', 'error');
        return;
    }

    total = payload.total;
    renderPublications(payload.data);
}

async function postForm(form, endpoint, isMultipart = false) {
    const body = isMultipart ? new FormData(form) : new URLSearchParams(new FormData(form));
    const res = await fetch(endpoint, { method: 'POST', body });
    const payload = await res.json();

    if (!payload.success) {
        showMessage(payload.message || 'Request failed', 'error');
        return;
    }

    showMessage(payload.message || 'Saved');
    form.reset();
    await loadCompanies();
    await loadPublications();
}

document.addEventListener('DOMContentLoaded', async () => {
    await loadCompanies();
    await loadPublications();

    document.getElementById('searchInput').addEventListener('input', async () => {
        offset = 0;
        await loadPublications();
    });

    document.getElementById('filterCompanyId').addEventListener('change', async () => {
        offset = 0;
        await loadPublications();
    });

    document.getElementById('prevPage').addEventListener('click', async () => {
        offset = Math.max(0, offset - limit);
        await loadPublications();
    });

    document.getElementById('nextPage').addEventListener('click', async () => {
        if (offset + limit < total) {
            offset += limit;
            await loadPublications();
        }
    });

    const companyForm = document.getElementById('createCompanyForm');
    if (companyForm) {
        companyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await postForm(companyForm, '../handlers/createCompany.php');
        });
    }

    const assignForm = document.getElementById('assignEmployeeForm');
    if (assignForm) {
        assignForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await postForm(assignForm, '../handlers/assignEmployee.php');
        });

        const userLookup = document.getElementById('userLookup');
        const userLookupResults = document.getElementById('userLookupResults');

        userLookup.addEventListener('input', async () => {
            const q = userLookup.value.trim();
            if (q.length < 2) {
                userLookupResults.innerHTML = '';
                return;
            }

            const res = await fetch(`../handlers/fetchUserLookup.php?q=${encodeURIComponent(q)}`);
            const payload = await res.json();
            if (!payload.success) return;

            userLookupResults.innerHTML = payload.data
                .map(user => `<option value="${user.id}">${user.username} (${user.email})</option>`)
                .join('');
        });
    }

    const uploadForm = document.getElementById('uploadPublicationForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await postForm(uploadForm, '../handlers/uploadPublication.php', true);
        });
    }
});
</script>

<?php include "../template/layout-footer.php"; ?>
