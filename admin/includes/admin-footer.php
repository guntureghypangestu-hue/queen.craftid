        </main>
    </div>
</div>

<!-- Script untuk Toggle Sidebar (Mobile) dan lainnya -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        // Reset scroll ke atas saat halaman dimuat
        window.scrollTo(0, 0);

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        // Buka sidebar ketika hamburger di-klik
        sidebarToggle.addEventListener('click', openSidebar);

        // Tutup sidebar ketika overlay di-klik
        sidebarOverlay.addEventListener('click', closeSidebar);

        // (Opsional) Tutup sidebar ketika window di-resize ke ukuran desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { // 1024px adalah breakpoint 'lg' di Tailwind
                closeSidebar();
            }
        });
    });
</script>

</body>
</html>