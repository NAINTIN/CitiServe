/**
 * admin_staff.js
 * Handles all frontend interactivity for the Staff Management page.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // HELPERS
    // ============================================================
    function show(el) { if (el) el.classList.add('show'); }
    function hide(el) { if (el) el.classList.remove('show'); }
    function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
    function qsa(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }

    // ============================================================
    // SEARCH & FILTER
    // ============================================================
    const searchInput  = document.getElementById('searchInput');
    const clearBtn     = document.getElementById('clearBtn');
    const tableBody    = document.getElementById('requestsTableBody');
    const filterBtn    = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const selectedFilterText = document.getElementById('selectedFilterText');

    let currentFilter = 'all';
    let currentSearch = '';

    function applyFilters() {
        if (!tableBody) return;

        const rows = qsa('.request-row', tableBody);
        const search = currentSearch.trim().toLowerCase();

        // Separate filter vs sort
        const isSort = currentFilter === 'az' || currentFilter === 'za';

        let visible = rows.filter(row => {
            const name    = (row.dataset.name    || '').toLowerCase();
            const email   = (row.dataset.email   || '').toLowerCase();
            const staffId = (row.dataset.staffId || '').toLowerCase();
            const status  = (row.dataset.status  || '').toLowerCase();

            const matchSearch = !search ||
            name.includes(search) ||
            email.includes(search) ||
            staffId.includes(search);

            const matchFilter =
            currentFilter === 'all' ||
            isSort ||
            (currentFilter === 'active'   && status === 'active') ||
            (currentFilter === 'inactive' && status === 'inactive');

            return matchSearch && matchFilter;
        });

        if (currentFilter === 'az') {
            visible.sort((a, b) =>
            (a.dataset.name || '').localeCompare(b.dataset.name || ''));
        } else if (currentFilter === 'za') {
            visible.sort((a, b) =>
            (b.dataset.name || '').localeCompare(a.dataset.name || ''));
        }

        // Hide all rows then show + reorder matching ones
        rows.forEach(r => { r.style.display = 'none'; });
        visible.forEach(r => {
            r.style.display = '';
            tableBody.appendChild(r);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value;
            applyFilters();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            currentSearch = '';
            currentFilter = 'all';
            qsa('.filter-option', filterDropdown).forEach(o => o.classList.remove('active'));
            const allOpt = qs('.filter-option[data-value="all"]', filterDropdown);
            if (allOpt) allOpt.classList.add('active');
            if (selectedFilterText) selectedFilterText.textContent = 'All';
            applyFilters();
        });
    }

    // Filter dropdown toggle
    if (filterBtn && filterDropdown) {
        filterBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            filterDropdown.classList.toggle('open');
        });

        qsa('.filter-option', filterDropdown).forEach(opt => {
            opt.addEventListener('click', function () {
                currentFilter = this.dataset.value;
                if (selectedFilterText) selectedFilterText.textContent = this.textContent.trim();
                qsa('.filter-option', filterDropdown).forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                filterDropdown.classList.remove('open');
                applyFilters();
            });
        });
    }

    // ============================================================
    // STAFF PROFILE MODAL
    // ============================================================
    const staffProfileModal  = document.getElementById('staffProfileModal');
    const staffProfileCloseX = document.getElementById('staffProfileCloseX');
    const staffProfileCloseBtn = document.getElementById('staffProfileCloseBtn');
    const toggleStaffStatusBtn = document.getElementById('toggleStaffStatusBtn');
    const toggleStaffStatusImg = document.getElementById('toggleStaffStatusImg');
    const profileStatusImg     = document.getElementById('profileStatusImg');

    // Hidden toggle form (injected by PHP — see note below)
    const toggleForm   = document.getElementById('toggleStaffForm');
    const toggleUserId = document.getElementById('toggleStaffUserId');

    let activeUserId   = null;
    let activeStatus   = null;

    function openStaffProfile(row) {
        const name     = row.dataset.name    || '-';
        const email    = row.dataset.email   || '-';
        const contact  = row.dataset.contact || '-';
        const role     = row.dataset.role    || '-';
        const joined   = row.dataset.joined  || '-';
        const status   = row.dataset.status  || '-';
        const initials = row.dataset.initials|| '?';
        const staffId  = row.dataset.staffId || '-';
        const userId   = row.dataset.userId  || '';

        activeUserId = userId;
        activeStatus = status;

        qs('#profileInitials').textContent = initials;
        qs('#profileName').textContent     = name;
        qs('#profileId').textContent       = staffId;
        qs('#profileRole').textContent     = role;
        qs('#profileEmail').textContent    = email;
        qs('#profileContact').textContent  = contact;
        qs('#profileJoined').textContent   = joined;
        qs('#profileStatus').textContent   = status;

        // Status badge image
        if (profileStatusImg) {
            profileStatusImg.src = status === 'Active'
            ? '/CitiServe/frontend/admin_dashboard/images/resident_active_staff.png'
            : '/CitiServe/frontend/admin_dashboard/images/resident_inactive_staff.png';
            profileStatusImg.alt = status;
        }

        // Toggle button image
        if (toggleStaffStatusImg) {
            toggleStaffStatusImg.src = status === 'Active'
            ? '/CitiServe/frontend/admin_dashboard/images/resident_mark_inactive.png'
            : '/CitiServe/frontend/admin_dashboard/images/resident_mark_active.png';
            toggleStaffStatusImg.alt = status === 'Active' ? 'Mark as Inactive' : 'Mark as Active';
        }

        show(staffProfileModal);
    }

    function closeStaffProfile() {
        hide(staffProfileModal);
        activeUserId = null;
        activeStatus = null;
    }

    if (staffProfileCloseX)  staffProfileCloseX.addEventListener('click',  closeStaffProfile);
    if (staffProfileCloseBtn) staffProfileCloseBtn.addEventListener('click', closeStaffProfile);

    if (staffProfileModal) {
        staffProfileModal.addEventListener('click', function (e) {
            if (e.target === staffProfileModal) closeStaffProfile();
        });
    }

    // Submit toggle_status via the hidden form
    if (toggleStaffStatusBtn) {
        toggleStaffStatusBtn.addEventListener('click', function () {
            if (!activeUserId || !toggleForm || !toggleUserId) return;
            toggleUserId.value = activeUserId;
            toggleForm.submit();
        });
    }

    // ============================================================
    // REMOVE STAFF MODAL
    // ============================================================
    const removeStaffModal  = document.getElementById('removeStaffModal');
    const cancelRemoveStaff = document.getElementById('cancelRemoveStaff');
    const removeStaffIdInput = document.getElementById('removeStaffId');

    function openRemoveModal(userId) {
        if (removeStaffIdInput) removeStaffIdInput.value = userId;
        show(removeStaffModal);
    }

    function closeRemoveModal() {
        hide(removeStaffModal);
    }

    if (cancelRemoveStaff) cancelRemoveStaff.addEventListener('click', closeRemoveModal);

    if (removeStaffModal) {
        removeStaffModal.addEventListener('click', function (e) {
            if (e.target === removeStaffModal) closeRemoveModal();
        });
    }

    // ============================================================
    // ADD STAFF MODAL
    // ============================================================
    const addStaffBtn    = document.getElementById('addStaffBtn');
    const addStaffModal  = document.getElementById('addStaffModal');
    const addStaffCloseX = document.getElementById('addStaffCloseX');
    const addStaffCancel = document.getElementById('addStaffCancel');
    const addStaffRoleDropdown  = document.getElementById('addStaffRoleDropdown');
    const addStaffRoleInput     = document.getElementById('addStaffRoleInput');
    const addStaffSelectedRole  = document.getElementById('addStaffSelectedRole');

    function openAddStaffModal() { show(addStaffModal); }
    function closeAddStaffModal() { hide(addStaffModal); }

    if (addStaffBtn)    addStaffBtn.addEventListener('click', openAddStaffModal);
    if (addStaffCloseX) addStaffCloseX.addEventListener('click', closeAddStaffModal);
    if (addStaffCancel) addStaffCancel.addEventListener('click', closeAddStaffModal);

    if (addStaffModal) {
        addStaffModal.addEventListener('click', function (e) {
            if (e.target === addStaffModal) closeAddStaffModal();
        });
    }

    // Role dropdown inside Add Staff modal
    const addStaffRoleBox = document.getElementById('addStaffRoleBox');
    if (addStaffRoleBox && addStaffRoleDropdown) {
        const roleToggleBtn = addStaffRoleBox.querySelector('.add-staff-select-btn');
        if (roleToggleBtn) {
            roleToggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                addStaffRoleDropdown.classList.toggle('open');
            });
        }

        qsa('.add-staff-option', addStaffRoleDropdown).forEach(opt => {
            opt.addEventListener('click', function () {
                const val = this.dataset.value;
                if (addStaffRoleInput)    addStaffRoleInput.value      = val;
                if (addStaffSelectedRole) addStaffSelectedRole.textContent = val;
                qsa('.add-staff-option', addStaffRoleDropdown)
                .forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                addStaffRoleDropdown.classList.remove('open');
            });
        });
    }

    // ============================================================
    // DELEGATED CLICK — "View Profile" and "Remove" buttons in rows
    // ============================================================
    document.addEventListener('click', function (e) {
        // View Profile
        const viewBtn = e.target.closest('.view-profile-btn');
        if (viewBtn) {
            const row = viewBtn.closest('.request-row');
            if (row) openStaffProfile(row);
            return;
        }

        // Remove (opens confirmation modal)
        const removeBtn = e.target.closest('.remove-staff-btn');
        if (removeBtn) {
            const row = removeBtn.closest('.request-row');
            if (row) openRemoveModal(row.dataset.userId || '');
            return;
        }

        // Close dropdowns when clicking elsewhere
        if (filterDropdown && !filterBtn?.contains(e.target)) {
            filterDropdown.classList.remove('open');
        }
        if (addStaffRoleDropdown && !addStaffRoleBox?.contains(e.target)) {
            addStaffRoleDropdown.classList.remove('open');
        }
    });

    // ============================================================
    // SUCCESS / ERROR NOTIFICATION BANNER
    // ============================================================
    const successNotif = document.getElementById('staffSuccessNotif');
    // PHP renders a <p style="color:#15803d"> when $message is set
    const successP = qs('p[style*="#15803d"]');

    if (successP && successNotif) {
        successNotif.style.display = 'block';
        setTimeout(function () {
            successNotif.style.display = 'none';
        }, 4000);
    }

    // Auto-open the correct section if PHP redirected back after a POST
    // (PHP sets the active tab via $activeTab which renders section classes — no extra JS needed)

});
