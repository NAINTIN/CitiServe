document.addEventListener('DOMContentLoaded', function () {
  const filterBox = document.getElementById('filterBox');
  const filterDropdown = document.getElementById('filterDropdown');
  const selectedRoleText = document.getElementById('selectedRoleText');
  const filterOptions = document.querySelectorAll('.filter-option');
  const searchInput = document.getElementById('searchInput');
  const clearBtn = document.getElementById('clearBtn');
  const tableBody = document.getElementById('requestsTableBody');

  let currentFilter = 'all';
  const originalRows = tableBody ? Array.from(tableBody.querySelectorAll('.request-row')) : [];

  function applyFilters() {
    if (!tableBody) return;

    const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';

    let filteredRows = originalRows.filter(row => {
      const id = (row.dataset.userId || '').toLowerCase();
      const name = (row.dataset.name || '').toLowerCase();
      const email = (row.dataset.email || '').toLowerCase();
      const role = (row.dataset.role || '').toLowerCase();

      const matchesSearch =
        keyword === '' ||
        id.includes(keyword) ||
        name.includes(keyword) ||
        email.includes(keyword);

      const matchesRole =
        currentFilter === 'all' ||
        role === currentFilter;

      return matchesSearch && matchesRole;
    });

    tableBody.innerHTML = '';
    filteredRows.forEach(row => tableBody.appendChild(row));
  }

  if (filterBox && filterDropdown) {
    filterBox.addEventListener('click', function (e) {
      e.stopPropagation();
      filterDropdown.classList.toggle('open');
    });

    filterOptions.forEach(option => {
      option.addEventListener('click', function (e) {
        e.stopPropagation();

        filterOptions.forEach(o => o.classList.remove('active'));
        option.classList.add('active');

        currentFilter = option.dataset.value;
        selectedRoleText.textContent = option.textContent;

        filterDropdown.classList.remove('open');
        applyFilters();
      });
    });

    document.addEventListener('click', function () {
      filterDropdown.classList.remove('open');
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }

  if (clearBtn && searchInput) {
    clearBtn.addEventListener('click', function () {
      searchInput.value = '';
      currentFilter = 'all';
      selectedRoleText.textContent = 'All';

      filterOptions.forEach(o => o.classList.remove('active'));
      document.querySelector('.filter-option[data-value="all"]')?.classList.add('active');

      applyFilters();
    });
  }
});
