<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    if (!sidebar || !mainContent) return;

    const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
    if (isSidebarExpanded) {
        expandSidebar();
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('sidebar-expanded')) {
                collapseSidebar();
            } else {
                expandSidebar();
            }
        });
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
            if (!sidebar.classList.contains('sidebar-expanded')) {
                sidebar.classList.add('sidebar-expanded');
                sidebar.classList.remove('sidebar-collapsed');
            }
        });
    }

    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function() {
            userMenu.classList.add('hidden');
        });
    }

    document.querySelectorAll('#sidebar a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 &&
            sidebar.classList.contains('mobile-open') &&
            mobileMenuToggle &&
            !sidebar.contains(e.target) &&
            !mobileMenuToggle.contains(e.target)) {
            sidebar.classList.remove('mobile-open');
        }
    });

    function expandSidebar() {
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('sidebar-expanded');
        mainContent.classList.remove('content-collapsed');
        mainContent.classList.add('content-expanded');
        if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
        localStorage.setItem('sidebarExpanded', 'true');
    }

    function collapseSidebar() {
        sidebar.classList.remove('sidebar-expanded');
        sidebar.classList.add('sidebar-collapsed');
        mainContent.classList.remove('content-expanded');
        mainContent.classList.add('content-collapsed');
        if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
        localStorage.setItem('sidebarExpanded', 'false');
    }
});
</script>
