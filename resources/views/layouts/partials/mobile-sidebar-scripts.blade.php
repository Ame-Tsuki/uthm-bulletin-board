<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Mobile sidebar script loaded');
    
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    
    console.log('Sidebar element:', sidebar);
    console.log('Mobile toggle element:', mobileToggle);
    
    if (mobileToggle && sidebar) {
        // Create overlay element
        let overlay = document.querySelector('.mobile-sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'mobile-sidebar-overlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:39;display:none;';
            document.body.appendChild(overlay);
            console.log('Overlay created');
        }
        
        // Function to open sidebar
        function openSidebar() {
            sidebar.classList.add('mobile-open');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
            console.log('Sidebar opened');
        }
        
        // Function to close sidebar
        function closeSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
            console.log('Sidebar closed');
        }
        
        // Toggle sidebar on button click
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Toggle button clicked');
            if (sidebar.classList.contains('mobile-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        
        // Close sidebar when clicking overlay
        overlay.addEventListener('click', closeSidebar);
        
        // Close sidebar when clicking a link (on mobile)
        sidebar.querySelectorAll('a, button').forEach(element => {
            element.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeSidebar();
            }
        });
    } else {
        console.log('Elements not found - Sidebar:', sidebar, 'Toggle:', mobileToggle);
    }
});
</script>

<style>
/* Mobile Sidebar Styles */
@media (max-width: 768px) {
    #sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        width: 280px;
        z-index: 40;
    }
    
    #sidebar.mobile-open {
        transform: translateX(0);
    }
}

@media (min-width: 769px) {
    #sidebar {
        transform: translateX(0) !important;
    }
    
    .mobile-sidebar-overlay {
        display: none !important;
    }
}
</style>
