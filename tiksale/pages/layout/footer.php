</div> <!-- dashboard-container -->

<script>
document.addEventListener('DOMContentLoaded', () => {
    const page = location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item').forEach(link => {
        if (link.getAttribute('href') === page) {
            link.classList.add('active');
        }
    });
});
</script>

</body>
</html>
