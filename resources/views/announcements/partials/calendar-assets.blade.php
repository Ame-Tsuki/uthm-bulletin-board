@once
<style>
    .calendar-dropdown {
        position: relative;
        z-index: 1;
    }
    .calendar-dropdown.is-open {
        z-index: 60;
    }
    .calendar-menu {
        display: none;
        position: absolute;
        right: 0;
        min-width: 11.5rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        padding: 0.25rem 0;
    }
    .calendar-menu.open {
        display: block;
    }
    .calendar-menu--up {
        bottom: calc(100% + 0.375rem);
        top: auto;
    }
    .calendar-menu--down {
        top: calc(100% + 0.375rem);
        bottom: auto;
    }
    .calendar-menu-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.8125rem;
        line-height: 1.25rem;
        color: #374151;
        white-space: nowrap;
        text-decoration: none;
        transition: background-color 0.15s;
    }
    .calendar-menu-item:hover {
        background-color: #f3f4f6;
        color: #111827;
    }
    .calendar-menu-item--action {
        border: none;
        background: transparent;
        cursor: pointer;
        font-family: inherit;
    }
    .calendar-menu-item--action:disabled {
        opacity: 0.6;
        cursor: wait;
    }
    .calendar-menu-item--added {
        font-weight: 500;
    }
    .announcement-card {
        overflow: visible !important;
    }
    .announcement-card-footer {
        position: relative;
        z-index: 0;
    }
    .announcement-actions-cell {
        position: relative;
        overflow: visible;
    }
    .announcement-actions-cell .calendar-menu {
        z-index: 70;
    }
</style>
<script>
    function toggleCalendarMenu(event, menuId) {
        event.preventDefault();
        event.stopPropagation();

        var menu = document.getElementById(menuId);
        if (!menu) return;

        var isOpen = menu.classList.contains('open');
        var dropdown = menu.closest('.calendar-dropdown');
        var button = dropdown ? dropdown.querySelector('.calendar-dropdown-btn') : null;

        document.querySelectorAll('.calendar-menu.open').forEach(function(other) {
            if (other.id !== menuId) {
                other.classList.remove('open');
                var otherDropdown = other.closest('.calendar-dropdown');
                if (otherDropdown) otherDropdown.classList.remove('is-open');
                var otherBtn = otherDropdown ? otherDropdown.querySelector('.calendar-dropdown-btn') : null;
                if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
            }
        });

        if (isOpen) {
            menu.classList.remove('open');
            if (dropdown) dropdown.classList.remove('is-open');
            if (button) button.setAttribute('aria-expanded', 'false');
        } else {
            menu.classList.add('open');
            if (dropdown) dropdown.classList.add('is-open');
            if (button) button.setAttribute('aria-expanded', 'true');
        }
    }

    document.addEventListener('click', function(event) {
        if (event.target.closest('.calendar-dropdown')) {
            return;
        }
        document.querySelectorAll('.calendar-menu.open').forEach(function(menu) {
            menu.classList.remove('open');
            var dropdown = menu.closest('.calendar-dropdown');
            if (dropdown) dropdown.classList.remove('is-open');
            var button = dropdown ? dropdown.querySelector('.calendar-dropdown-btn') : null;
            if (button) button.setAttribute('aria-expanded', 'false');
        });
    });

    function addAnnouncementToCalendar(announcementId, menuId, triggerBtn) {
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var dropdown = document.querySelector('.calendar-dropdown[data-announcement-id="' + announcementId + '"]');
        var addUrl = dropdown ? dropdown.getAttribute('data-add-url') : null;

        if (!csrf || !addUrl) {
            alert('Session expired. Please refresh the page.');
            return;
        }

        if (triggerBtn) {
            triggerBtn.disabled = true;
        }

        fetch(addUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf.content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function(response) {
            return response.json().then(function(data) {
                return { ok: response.ok, data: data };
            });
        })
        .then(function(result) {
            if (!result.ok || !result.data.success) {
                throw new Error(result.data.message || 'Failed to add to calendar.');
            }

            if (dropdown) {
                dropdown.setAttribute('data-in-calendar', '1');
            }

            var menu = document.getElementById(menuId);
            if (menu) {
                menu.classList.remove('open');
            }

            var message = result.data.message || 'Added to your calendar.';

            if (typeof showToast === 'function') {
                showToast(message, 'success');
                setTimeout(function() { location.reload(); }, 1200);
            } else {
                alert(message);
                location.reload();
            }
        })
        .catch(function(error) {
            if (typeof showToast === 'function') {
                showToast(error.message || 'Could not add to calendar.', 'error');
            } else {
                alert(error.message || 'Could not add to calendar. Please try again.');
            }
        })
        .finally(function() {
            if (triggerBtn) {
                triggerBtn.disabled = false;
            }
        });
    }
</script>
@endonce
