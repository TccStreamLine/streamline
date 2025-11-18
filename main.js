document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('sidebar-toggle');
    const backdrop = document.getElementById('sidebar-backdrop');
    const body = document.body;

    function openSidebar() {
        body.classList.add('sidebar-open');
        if (backdrop) {
            backdrop.classList.add('active');
            backdrop.classList.remove('hidden');
            backdrop.setAttribute('aria-hidden', 'false');
        }
    }

    function closeSidebar() {
        body.classList.remove('sidebar-open');
        if (backdrop) {
            backdrop.classList.remove('active');
            backdrop.classList.add('hidden');
            backdrop.setAttribute('aria-hidden', 'true');
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (body.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            closeSidebar();
        });
    }

    // Fecha a sidebar automaticamente em redimensionamento para desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900 && body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });

    // Fecha a sidebar ao pressionar ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });
});
